<?php
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use yii\helpers\{Html, ArrayHelper, Url};
use yii\widgets\ActiveForm;
?>
<div class="program-kegiatan-form">
    <?php $form = ActiveForm::begin(
        [
            'id' => 'program-kegiatan-form',
            'enableAjaxValidation' => false,
            'fieldConfig' => [
                'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
                'labelOptions' => ['class' => 'col-sm-3 control-label right'],
            ],
        ]
    ); ?>
    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'desc')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'parent')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'type')->widget(Select2::class, [
            'data' => ['program' => 'Program', 'kegiatan' => 'Kegiatan'],
            'options' => ['placeholder' => 'Select type'],
            'pluginOptions' => [
                'allowClear' => true
            ]
        ]) ?>
    <?= $form->field($model, 'tahun_anggaran')->widget(Select2::class, [
            'data' => $model->isNewRecord ? $model::optiontahunanggaran() : $model::optiontahunanggaran($model->tahun_anggaran),
            'options' => ['placeholder' => 'Select tahun'],
            'pluginOptions' => [
                'allowClear' => true
            ]
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