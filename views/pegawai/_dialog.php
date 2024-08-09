<?php
use app\assets\AppAsset;
use app\widgets\DynamicDataTableWidget;
use app\widgets\DynamicDTCWidget;
use yii\helpers\{Url,Html};
use yii\widgets\ActiveForm;
AppAsset::register($this);
$this->title = 'Input with Data Modal';
?>
<div class="input-with-modal">
    <?php $form = ActiveForm::begin([
        'id' => 'input-with-modal-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
    <?= $form->field($model, 'nama')->widget(DynamicDataTableWidget::class, [
        'options'=>[
            'title'=>'data Contact',
        ],
        'authToken'=>'dsas123',
        'ajaxUrl' => Url::to(['/pegawai/list_datatable']),
        'columns' => [
            ['data' => 'id', 'title' => 'ID'],
            ['data' => 'nama', 'title' => 'Nama'],
        ],
        // 'filterFields' => [ 'nama'],
        'defaultOrder' => [
            [0, 'asc'],
            [1, 'asc'],
        ],
        'multiple' => true,
    ]) ?>
    <?= $form->field($model, 'nik')->widget(DynamicDTCWidget::class, [
        'options'=>[
            'title'=>'data pegawai',
        ],
        'ajaxUrl' => Url::to(['/pegawai/listpegawai_datatable']),
        'columns' => [
            // col 0 is checkbox
            ['data' => 'id', 'title' => 'ID'],
            // ['data' => 'nik', 'title' => 'NIK'],
            ['data' => 'nama', 'title' => 'Nama'],
        ],
        // 'filterFields' => [ 'nik'],
        'defaultOrder' => [
            [1, 'asc'],
            [2, 'asc'],
        ],
        'multiple' => false,
    ]) ?>
    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary',]);?>
    <?php ActiveForm::end(); ?>
</div>
