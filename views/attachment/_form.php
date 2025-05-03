<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="attachment-form">
    <?php $form = ActiveForm::begin([
        'id'=>'attachment-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
          <?= $form->field($model, 'user_id')->textInput() ?>
      <?= $form->field($model, 'name')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'uri')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'mime')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'size')->textInput() ?>
      <?= $form->field($model, 'type')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'jenis_dokumen')->textInput() ?>
    <?php if (!Yii::$app->request->isAjax){ ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>
