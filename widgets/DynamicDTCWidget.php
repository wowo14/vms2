<?php
namespace app\widgets;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\bootstrap4\Modal;
class DynamicDTCWidget extends InputWidget
{
    public $ajaxUrl;
    public $columns = [];
    public $filterFields = [];
    public $multiple = false;
    public $authToken = null;
    public $defaultOrder = [[1, 'asc']];
    public function run()
    {
        return $this->renderInput() . $this->renderModal();
    }
    protected function renderInput()
    {
        $inputId = $this->options['id'];
        return Html::activeTextInput($this->model, $this->attribute, [
            'readonly' => true,
            'value' => $this->value,
            'id' => $inputId,
            'class' => 'form-control'
        ]) . Html::hiddenInput($this->attribute, $this->value, ['id' => $inputId . '-hidden']);
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
        $defaultOrderJson = json_encode($this->defaultOrder);
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
                columns: [
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return '<input type="checkbox" class="row-select" value="' + full[Object.keys(full)[1]] + '">';
                        },
                        title: '<input type="checkbox" id="checkAll"> Select All'
                    }
                ].concat($columnsJson),
                order: $defaultOrderJson,
                destroy: true
            });
            $('#$tableId tbody').on('click', 'tr', function() {
                var checkbox = $(this).find('.row-select');
                var isChecked = checkbox.prop('checked');
                checkbox.prop('checked', !isChecked);
                $(this).toggleClass('selected', !isChecked);
            });
            $('#checkAll').on('click', function() {
                var isChecked = $(this).is(':checked');
                $('#$tableId tbody input.row-select').prop('checked', isChecked);
                $('#$tableId tbody tr').toggleClass('selected', isChecked);
            });
            $('#apply-filters-{$this->id}').on('click', function() {
                table.ajax.reload();
            });
        }
        $('#apply-selection-{$this->id}').on('click', function() {
            var selectedValues = [];
            $('#$tableId tbody input.row-select:checked').each(function() {
                selectedValues.push($(this).val());
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
