<?php
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
?>
<div class="negosiasi-form">
    <?php $form = ActiveForm::begin([
        'id'=>'negosiasi-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
    <?= $form->field($model, 'penawaran_id')->widget(Select2::class,[
        'data' => $penawaran,
        'options' => ['placeholder' => 'Pilih Penawaran'],
        'pluginOptions' => [
            'allowClear' => true
        ]
    ]) ?>
      <?= $form->field($model, 'ammount')->textInput() ?>
      <?php if($this->context->isVendor()):?>
      <?= $form->field($model, 'accept')->widget(SwitchInput::class,[
          'pluginOptions' => [
              'onText' => 'Ya',
              'offText' => 'Tidak',
          ]
      ]) ?>
      <?php endif; ?>
    <?php if (!Yii::$app->request->isAjax){ ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>
