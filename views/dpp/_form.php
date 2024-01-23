<?php

use app\models\PaketPengadaan;
use app\models\Unit;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
?>
<div class="dpp-form">
    <?php $form = ActiveForm::begin([
        'id' => 'dpp-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 col-md-3 control-label text-sm-left text-md-right'],
        ],
    ]); ?>
    <?= $form->field($model, 'nomor_dpp')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'tanggal_dpp')->widget(DatePicker::class, [
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
            'autoclose' => true
        ],
    ]) ?>
    <?= $form->field($model, 'bidang_bagian')->widget(Select2::class, [
        'data' => Unit::collectAll()->pluck('unit', 'id')->toArray(),
        'options' => [
            'placeholder' => 'Pilih Bidang/Bagian...',
        ]
    ]) ?>
    <?= $form->field($model, 'paket_id')->widget(Select2::class, [
        'data' => PaketPengadaan::collectAll(['approval_by' => null])->pluck('nomornamapaket', 'id')->toArray(),
        'options' => [
            'placeholder' => 'Pilih Paket...',
        ]
    ]) ?>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>