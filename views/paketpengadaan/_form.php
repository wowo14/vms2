<?php
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
?>
<div class="paket-pengadaan-form">
    <?php $form = ActiveForm::begin([
        'id' => 'paket-pengadaan-form',
        'enableAjaxValidation' => false,
        'options' => ['enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 col-md-3 control-label text-sm-left text-md-right'],
        ],
    ]); ?>
    <?= $form->field($model, 'nomor')->textInput() ?>
    <?= $form->field($model, 'tanggal_paket')->widget(DatePicker::class, [
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
            'autoclose' => true
        ],
    ]) ?>
    <?= $form->field($model, 'nama_paket')->textArea() ?>
    <?= $form->field($model, 'kode_program')->textInput() ?>
    <?= $form->field($model, 'kode_kegiatan')->textInput() ?>
    <?= $form->field($model, 'kode_rekening')->textInput() ?>
    <?= $form->field($model, 'ppkom')->textInput() ?>
    <?= $form->field($model, 'pagu')->textInput() ?>
    <?= $form->field($model, 'metode_pengadaan')->textInput() ?>
    <?= $form->field($model, 'tahun_anggaran')->widget(Select2::class, [
        'data' => $model::optiontahunanggaran(),
        'options' => [
            'placeholder' => 'Pilih tahun anggaran',
        ]
    ]) ?>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>