<?php
use app\assets\AppAsset;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\{ArrayHelper, Html};
use yii\web\View;
AppAsset::register($this);
$paket = ArrayHelper::map($model->allpaketpengadaan, 'id', 'nomornamapaket');
$penyedia = ArrayHelper::map($model->vendors, 'id', 'nama_perusahaan');
$this->registerJs('
jQuery(function ($) {
    setupImagePreview($("#imageInput"), $("#imagePreview"), $("#lampiran_penawaran"));
    setupImagePreview($("#imageInput2"), $("#imagePreview2"), $("#lampiran_penawaran_harga"));
});', View::POS_END);
?>
<div class="penawaran-pengadaan-form">
    <?php $form = ActiveForm::begin([
        'id' => 'penawaran-pengadaan-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
    <?= $form->field($model, 'paket_id')->widget(Select2::class, [
        'data' => $paket,
        'options' => ['placeholder' => 'Pilih Paket'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]) ?>
    <?= $form->field($model, 'penyedia_id')->widget(Select2::class, [
        'data' => $penyedia,
        'options' => ['placeholder' => 'Pilih Penyedia'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]) ?>
    <?= $form->field($model, 'kode')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'tanggal_mendaftar')->widget(DatePicker::class, [
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
            'autoclose' => true
        ],
    ]) ?>
    <?= $form->field($model, 'masa_berlaku')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'lampiran_penawaran')->hiddenInput(['id' => 'lampiran_penawaran'])->label(false) ?>
    <div class="form-group ">
        <div class="row">
            <label class="control-label right col-sm-3" for="PenawaranPengadaan-lampiran_penawaran">File Penawaran(images/pdf)</label>
            <div class="col-sm-9">
                <input type="file" accept=".pdf, .png, .jpg, .jpeg, .gif" id="imageInput">
                <div id="imagePreview"></div>
            </div>
        </div>
    </div>
    <?= $form->field($model, 'lampiran_penawaran_harga')->hiddenInput(['id' => 'lampiran_penawaran_harga'])->label(false) ?>
    <div class="form-group ">
        <div class="row">
            <label class="control-label right col-sm-3" for="PenawaranPengadaan-lampiran_penawaran_harga">File Penawaran harga(images/pdf)</label>
            <div class="col-sm-9">
                <input type="file" accept=".pdf, .png, .jpg, .jpeg, .gif" id="imageInput2">
                <div id="imagePreview2"></div>
            </div>
        </div>
    </div>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>