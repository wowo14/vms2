<?php
use app\assets\AppAsset;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
AppAsset::register($this);
$arpenyedia = [];
    $url = Yii::$app->request->getUrl();
    $query = parse_url($url, PHP_URL_QUERY);
    parse_str($query, $params);
    if ($params) {
        @$_GET['uid'] = $params['id'];
    }
$this->registerJs('
jQuery(function ($) {
    setupImagePreview($("#imageInput"), $("#imagePreview"), $("#file_ijinusaha"));
});', View::POS_END);
?>
<div class="ijinusaha-form">
    <?php $form = ActiveForm::begin([
        'id' => 'ijinusaha-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
    <?php if (Yii::$app->tools->isAdmin()) :
        $arpenyedia = isset($_GET['uid'])
            ? collect($model->vendors)
            ->filter(fn ($vendor) => $vendor->id == $this->context->decodeurl($_GET['uid'])->id)
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
    <?= $form->field($model, 'instansi_pemberi')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'nomor_ijinusaha')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'tanggal_ijinusaha')->widget(DatePicker::class, [
        'options' => ['placeholder' => 'Select date ...'],
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
            'autoclose' => true
        ]
    ]) ?>
    <?= $form->field($model, 'tanggal_berlaku_sampai')->widget(DatePicker::class, [
        'options' => ['placeholder' => 'Select date ...'],
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
            'autoclose' => true
        ]
    ]) ?>
    <?= $form->field($model, 'jenis_ijin')->widget(Select2::class, [
        'data' => $model->jenis,
        'options' => ['placeholder' => 'Select jenis Ijin ...'],
    ]) ?>
    <?= $form->field($model, 'kualifikasi')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'klasifikasi')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'tags')->textarea(['rows' => 2]) ?>
    <?= $form->field($model, 'is_active')->widget(SwitchInput::class, [
        'pluginOptions' => ['size' => 'mini'],
    ]); ?>
    <?= $form->field($model, 'file_ijinusaha')->hiddenInput(['id' => 'file_ijinusaha'])->label(false) ?>
    <div class="form-group ">
        <div class="row">
            <label class="control-label right col-sm-3" for="ijinusaha-file_ijinusaha">File IjinUsaha (images/pdf)</label>
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