<?php

use app\models\PaketPengadaan;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

\app\assets\AppAsset::register($this);
$url = Yii::$app->request->getUrl();
$query = parse_url($url, PHP_URL_QUERY);
parse_str($query, $params);
$where = '';
$dpp = collect(PaketPengadaan::find()->all())->pluck('nomornamapaket', 'id')->toArray();
if ($params) {
    @$_GET['uid'] = $params['id'];
    // print_r(PaketPengadaan::where(['pemenang' => $params['id']])->all());
    $dpp = collect(PaketPengadaan::where(['pemenang' => $params['id']])->all())->pluck('nomornamapaket', 'id')->toArray();
}
$this->registerJs('
jQuery(function ($) {
    setupImagePreview($("#imageInput"), $("#imagePreview"), $("#file_akta"));
});', View::POS_END);
?>
<div class="pengalaman-penyedia-form">
    <?php $form = ActiveForm::begin([
        'id' => 'pengalaman-penyedia-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
    <?php if (Yii::$app->tools->isAdmin()) :
        $arpenyedia = isset($_GET['uid'])
            ? collect($model->vendors)
            ->filter(fn($vendor) => $vendor->id == $this->context->decodeurl($_GET['uid'])->id)
            ->pluck('nama_perusahaan', 'id')
            ->toArray()
            : ArrayHelper::map($model->vendors, 'id', 'nama_perusahaan');
    ?>
        <?= $form->field($model, 'penyedia_id')->widget(Select2::class, [
            'data' => $arpenyedia,
        ]); ?>
    <?php endif; ?>
    <?php if ($vendordisabled = $this->context->isVendor()) : ?>
        <?php
        $companyId = Yii::$app->session->get('companygroup');
        if ($vendordisabled) {
            echo $form->field($model, 'penyedia_id')->hiddenInput(['value' => $companyId])->label(false);
        }
        ?>
    <?php endif; ?>
    <?= $form->field($model, 'paket_pengadaan_id')->widget(Select2::class, [
        'data' => $dpp,
        'options' => ['placeholder' => 'Pilih Paket Pengadaan...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]) ?>
    <?= $form->field($model, 'link')->textInput() ?>
    <?= $form->field($model, 'pekerjaan')->textarea(['rows' => 2]) ?>
    <?= $form->field($model, 'lokasi')->textarea(['rows' => 1]) ?>
    <?= $form->field($model, 'instansi_pemberi_tugas')->textInput() ?>
    <?= $form->field($model, 'alamat_instansi')->textInput() ?>
    <?= $form->field($model, 'tanggal_kontrak')->widget(DatePicker::class, [
        'options' => ['placeholder' => 'Select date ...'],
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
            'autoclose' => true
        ]
    ]) ?>
    <?= $form->field($model, 'tanggal_selesai_kontrak')->widget(DatePicker::class, [
        'options' => ['placeholder' => 'Select date ...'],
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
            'autoclose' => true
        ]
    ]) ?>
    <?= $form->field($model, 'nilai_kontrak')->textInput() ?>
    <?= $form->field($model, 'file')->hiddenInput(['id' => 'file'])->label(false) ?>
    <div class="form-group ">
        <div class="row">
            <label class="control-label right col-sm-3" for="pengalamanPenyedia-file">File (images/pdf)</label>
            <div class="col-sm-9">
                <input type="file" accept=".pdf, .png, .jpg, .jpeg, .gif" id="imageInput">
                <div id="imagePreview"></div>
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