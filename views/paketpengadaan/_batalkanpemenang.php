<?php
use yii\helpers\Html;
use kartik\date\DatePicker;
use yii\bootstrap4\ActiveForm;
\app\assets\AppAsset::register($this);
$this->registerJs('
jQuery(function ($) {
    setupImagePreview($("#imageInput"), $("#imagePreview"), $("#bast"));
});', \yii\web\View::POS_END);
?>
<?php $form = ActiveForm::begin([
    'id' => 'pembatalan-form',
    'enableAjaxValidation' => false,
    'fieldConfig' => [
        'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
        'labelOptions' => ['class' => 'col-sm-3 col-md-3 control-label text-sm-left text-md-right'],
    ],
]); ?>
<?= $form->field($model, 'tanggal_dibatalkan')->widget(DatePicker::class, [
    'pluginOptions' => [
        'format' => 'yyyy-mm-dd',
        'todayHighlight' => true,
        'autoclose' => true
    ]
]) ?>
<?= $form->field($model, 'berita_acara_pembatalan')->hiddenInput(['id' => 'bast'])->label(false) ?>
<div class="form-group ">
    <div class="row">
        <label class="control-label right col-sm-3" for="reviewdpp-bast">File berita acara pembatalan (images/pdf)</label>
        <div class="col-sm-9">
            <input type="file" accept=".pdf, .png, .jpg, .jpeg, .gif" id="imageInput">
            <div id="imagePreview"></div>
        </div>
    </div>
</div>
<?= $form->field($model, 'alasan_dibatalkan')->textInput(['maxlength' => true]) ?>
<?php if (!Yii::$app->request->isAjax) { ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
<?php } ?>
<?php ActiveForm::end(); ?>