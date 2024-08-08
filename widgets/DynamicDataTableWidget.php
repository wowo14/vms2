<?php
namespace app\widgets;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\bootstrap4\Modal;
class DynamicDataTableWidget extends InputWidget
{
    public $ajaxUrl;
    public $columns = [];
    public $filterFields = [];
    public $multiple = false;
    public $authToken = null;
    public function run(){
        return $this->renderInput() . $this->renderModal();
    }
    protected function renderInput(){
        $inputId = $this->options['id'];
        $inputName = $this->attribute;
        return Html::activeTextInput($this->model, $this->attribute, [
            'readonly' => true,
            'value' => $this->value,
            'id' => $inputId,
            'class' => 'form-control'
        ]);
    }
    protected function renderModal()
    {
        $modalId = 'data-modal-' . $this->id;
        $tableId = 'data-table-' . $this->id;
        $columnsJson = json_encode($this->columns);
        $ajaxUrl = $this->ajaxUrl;
        $authToken = $this->authToken ? json_encode($this->authToken) : 'null';
        $multiple = $this->multiple ? 'true' : 'false';
        $filters = '';
        foreach ($this->filterFields as $field) {
            $filters .= Html::input('text', $field, '', [
                'class' => 'form-control filter-input',
                'placeholder' => $field,
                'name' => $field
            ]);
        }
        $initFunctionName = 'inittb' . $this->id;
        $js = <<<JS
        var table;
        $('#{$this->options['id']}').on('click', function() {
            $('#$modalId').modal('show')
                .find('.modal-body')
                .html('<table id="$tableId" class="table table-bordered" width="100%"></table>');
            $initFunctionName();
        });
        function $initFunctionName() {
            table = $('#$tableId').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '$ajaxUrl',
                    beforeSend: function (request) {
                        if ($authToken) {
                            request.setRequestHeader("Authorization", "Bearer " + $authToken);
                        }
                    },
                    data: function(d) {
                        $('.filter-input').each(function() {
                            d[$(this).attr('name')] = $(this).val();
                        });
                    },
                    dataSrc: 'data'
                },
                columns: $columnsJson,
                destroy: true, // Allows reinitializing the table on modal open
                select: $multiple ? { style: 'multi' } : true
            });
            $('#$tableId tbody').on('click', 'tr', function() {
                if ($multiple) {
                    $(this).toggleClass('selected');
                } else {
                    table.$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                    var selectedData = table.row(this).data();
                    $('#{$this->options['id']}').val(selectedData[Object.keys(selectedData)[1]]);
                    $('#$modalId').modal('hide');
                }
            });
            $('#apply-filters-{$this->id}').on('click', function() {
                table.ajax.reload();
            });
        }
        $('#apply-selection-{$this->id}').on('click', function() {
            var selectedData = table.rows('.selected').data().toArray();
            var selectedValues = selectedData.map(function(row) {
                return row[Object.keys(row)[1]];
            });
            $('#{$this->options['id']}').val(selectedValues.join(', '));
            $('#{$this->options['id']}-hidden').val(selectedValues.join(', '));
            $('#$modalId').modal('hide');
        });
        JS;
        $this->view->registerJs($js);
        ob_start();
        Modal::begin([
            'id' => $modalId,
            'size' => Modal::SIZE_LARGE,
            'title' => $this->options['title'] ?? '',
            'footer' => Html::button('Apply', ['class' => 'btn btn-primary', 'id' => 'apply-selection-' . $this->id]),
        ]);
        echo '<div class="modal-body">' . $filters . '</div>';
        Modal::end();
        return ob_get_clean();
    }
}