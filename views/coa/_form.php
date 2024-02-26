<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
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
    <?= $form->field($model, 'kode')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'rekening')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'parent')->textInput() ?>
    <?= $form->field($model, 'is_active')->textInput() ?>
    <?= $form->field($model, 'tahun_anggaran')->textInput() ?>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>