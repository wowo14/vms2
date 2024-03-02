<?php

use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
?>
<div class="kode-rekening-form">
    <?php $form = ActiveForm::begin([
        'id' => 'kode-rekening-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 col-md-3 control-label text-sm-left text-md-right'],
        ],
    ]); ?>
    <?= $form->field($model, 'tahun_anggaran')->widget(Select2::class, [
        'data' => $model->isNewRecord ? $model::optiontahunanggaran() : $model::optiontahunanggaran($model->tahun_anggaran),
        'options' => ['placeholder' => 'Select tahun'],
    ]) ?>
    <?= $form->field($model, 'kode')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'rekening')->textarea(['maxlength' => true]) ?>
    <?= $form->field($model, 'parent')->widget(Select2::class, [
        'data' => collect($model->coacode)->map(function ($e) {
            $e->kode = $e->kode . '||' . $e->rekening;
            return $e;
        })->pluck('kode', 'id')->toArray(),
        'language' => 'id',
        'options' => ['placeholder' => 'Select parent'],
        'pluginOptions' => [
            // 'escapeMarkup' => $escape,
            // 'templateResult' => new JsExpression('format'),
            // 'templateSelection' => new JsExpression('format'),
            'allowClear' => true
        ],
    ]) ?>

    <?= $form->field($model, 'is_active')->widget(SwitchInput::class, [
        'pluginOptions' => ['size' => 'mini'],
    ]); ?>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>