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
    public function run()
    {
        // $this->registerAssets();
        return $this->renderInput() . $this->renderModal();
    }
    protected function renderInput()
    {
        $inputId = $this->options['id'];
        $inputName = $this->attribute;
        return Html::activeTextInput($this->model, $this->attribute, [
            'readonly' => true,'value'=>$this->value,
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
        $multiple = $this->multiple ? 'true' : 'false';
        $filters = '';
        foreach ($this->filterFields as $field) {
            $filters .= Html::input('text', $field, '', [
                'class' => 'form-control filter-input',
                'placeholder' => $field
            ]);
        }
        $js = <<<JS
var table;
$('#{$this->options['id']}').on('click', function() {
    $('#$modalId').modal('show')
        .find('.modal-body')
        .html('<table id="$tableId" class="table table-bordered" width="100%"></table>');
    initializeDataTable(); // Initialize the DataTable
});
function initializeDataTable() {
    table = $('#$tableId').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '$ajaxUrl',
            data: function(d) {
                $('.filter-input').each(function() {
                    d[$(this).attr('name')] = $(this).val();
                });
            },
            dataSrc: 'data'
        },
        columns: $columnsJson,
        destroy: true, // Allows reinitializing the table on modal open
        select: $multiple ? {
            style: 'multi'
        } : true
    });
    $('#$tableId tbody').on('click', 'tr', function() {
        if ($multiple) {
            $(this).toggleClass('selected');
        } else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
            var selectedData = table.row(this).data();
            console.log(selectedData);
            console.log(selectedData[Object.keys(selectedData)[1]]);
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
            'title' => 'Select Data',
            'footer' => Html::button('Apply', ['class' => 'btn btn-primary', 'id' => 'apply-selection-' . $this->id]),
        ]);
        echo '<div class="modal-body">' . $filters . '</div>';
        Modal::end();
        return ob_get_clean();
    }
    // protected function registerAssets()
    // {
    //     $this->view->registerCssFile('https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css', ['depends' => [\yii\web\JqueryAsset::class]]);
    //     $this->view->registerCssFile('https://cdn.datatables.net/buttons/1.2.3/css/buttons.dataTables.min.css', ['depends' => [\yii\web\JqueryAsset::class]]);
    //     $this->view->registerJsFile('https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
    //     $this->view->registerJsFile('https://cdn.datatables.net/buttons/1.2.3/js/dataTables.buttons.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
    //     $this->view->registerJsFile('https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
    // }
}
