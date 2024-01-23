<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="validasi-kualifikasi-penyedia-form">
    <?php $form = ActiveForm::begin([
        'id'=>'validasi-kualifikasi-penyedia-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
          <?= $form->field($model, 'penyedia_id')->textInput() ?>
      <?= $form->field($model, 'paket_pengadaan_id')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'keperluan')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'is_active')->textInput() ?>
    <?php if (!Yii::$app->request->isAjax){ ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>
