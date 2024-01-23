<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="template-checklist-evaluasi-form">
    <?php $form = ActiveForm::begin([
        'id' => 'template-checklist-evaluasi-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-8'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-4 col-md-4 control-label text-sm-left text-md-right'],
        ],
    ]); ?>
    <?= $form->field($model, 'template')->textInput() ?>
    <?= $form->field($model, 'jenis_evaluasi')->textInput() ?>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>