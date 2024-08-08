<?php
use yii2ajaxcrud\ajaxcrud\CrudAsset;
use yii\bootstrap4\Modal;
use yii\helpers\{Html,Url};
use yii\widgets\ActiveForm;
CrudAsset::register($this);
?>
<div class="input-with-modal">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'nama')->textInput(['readonly' => true, 'id' => 'selected-data-input']) ?>
    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary',]);?>
    <?php ActiveForm::end(); ?>
    <?php
    Modal::begin([
        'id' => 'data-modal',
        'size' => Modal::SIZE_LARGE,
    ]);
    echo '<div id="data-modal-content"></div>';
    Modal::end();
    ?>
</div>
<?php
$this->registerCssFile('https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css', ['depends' => [CrudAsset::class]]);
$this->registerCssFile('https://cdn.datatables.net/buttons/1.2.3/css/buttons.dataTables.min.css', ['depends' => [CrudAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js', ['depends' => [CrudAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/1.2.3/js/dataTables.buttons.min.js', ['depends' => [CrudAsset::class]]);
$this->registerJs("
  $('#selected-data-input').on('click', function() {
    $('#data-modal').modal('show')
        .find('#data-modal-content')
        .html('<table id=\"data-table\" class=\"table table-bordered\" width=\"100%\"></table>');
    initializeDataTable(); // Initialize the DataTable
});
function initializeDataTable() {
    $('#data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/pegawai/list',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id', title: 'ID' },
            { data: 'nama', title: 'Data Field' }
        ],
        destroy: true // Allows reinitializing the table on modal open
    });
}
$(document).on('click', '#data-table tbody tr', function() {
    var selectedData = $(this).find('td').eq(1).text();
    $('#selected-data-input').val(selectedData);
    $('#data-modal').modal('hide');
});
");
?>
