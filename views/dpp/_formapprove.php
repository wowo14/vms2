<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="approvedpp-form">
    <?php $form = ActiveForm::begin([
        'id' => 'approvedpp-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 col-md-3 control-label text-sm-left text-md-right'],
        ],
    ]); ?>
    <?= $form->field($model, 'nomor_dpp')->textInput(['readonly' => true]) ?>
    <?= $form->field($model, 'nomor_persetujuan')->textInput(['maxlength' => true]) ?>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>