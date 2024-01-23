<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="akta-penyedia-form">
    <?php $form = ActiveForm::begin([
        'id' => 'akta-penyedia-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 col-md-3 control-label text-sm-left text-md-right'],
        ],
    ]); ?>
    <?= $form->field($model, 'penyedia_id')->textInput() ?>
    <?= $form->field($model, 'jenis_akta')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'nomor_akta')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'tanggal_akta')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'notaris')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'file_akta')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'created_at')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'updated_at')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'created_by')->textInput() ?>
    <?= $form->field($model, 'updated_by')->textInput() ?>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>