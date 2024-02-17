<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use kartik\date\DatePicker;
$this->title="Reject DPP -> Paket Pengadaan";
?>
<div class="dpp-form">
    <?php $form = ActiveForm::begin([
        'id' => 'dpp-formreject',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 col-md-3 control-label text-sm-left text-md-right'],
        ],
    ]); ?>
    <?= $form->field($model, 'nomor')->textInput(['maxlength' => true, 'readonly' => true]) ?>
    <?= $form->field($model, 'nama_paket')->textInput(['maxlength' => true, 'readonly' => true]) ?>
    <?= $form->field($model, 'alasan_reject')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'tanggal_reject')->widget(DatePicker::class, [
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
            'autoclose' => true
        ],
    ]) ?>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>