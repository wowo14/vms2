<?php
use kartik\date\DatePicker;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use kartik\switchinput\SwitchInput;
?>
<div class="galery-dasarhukum-form">
    <?php $form = ActiveForm::begin([
        'id' => 'galery-dasarhukum-form',
        'enableAjaxValidation' => false,
        'options' => ['enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'judul')->textarea(['rows' => 1]) ?>
            <?= $form->field($model, 'summary')->textarea(['rows' => 1]) ?>
            <?= $form->field($model, 'tags')->widget(Select2::class, [
                'data' => $model->tags ?? []
            ]) ?>
            <?= $form->field($model, 'is_active')->widget(SwitchInput::class, [
                'pluginOptions' => ['size' => 'mini'],
            ]); ?>
            <?= $form->field($model, 'kategori')->textInput() ?>
            <?= $form->field($model, 'nomor')->textInput() ?>
            <?= $form->field($model, 'tanggal_ditetapkan')->widget(DatePicker::class, [
                'options' => ['placeholder' => 'Select date ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'autoclose' => true
                ]
            ]) ?>
            <?= $form->field($model, 'penerbit')->textInput() ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'foto')->widget(\kartik\file\FileInput::classname(), ['options' => ['accept' => 'image/*']]) ?>
            <?= $form->field($model, 'file_pdf')->widget(\kartik\file\FileInput::classname(), ['options' => ['accept' => 'application/pdf']]) ?>
        </div>
    </div>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>