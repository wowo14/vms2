<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="pengurusperusahaan-form">
    <?php $form = ActiveForm::begin([
        'id'=>'pengurusperusahaan-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
          <?= $form->field($model, 'nama')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'nik')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'alamat')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'email')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'telepon')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'nip')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'jabatan')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'instansi')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'is_vendor')->textInput() ?>
      <?= $form->field($model, 'is_active')->textInput() ?>
      <?= $form->field($model, 'user_id')->textInput() ?>
      <?= $form->field($model, 'penyedia_id')->textInput() ?>
      <?= $form->field($model, 'password')->textarea(['rows' => 6]) ?>
    <?php if (!Yii::$app->request->isAjax){ ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>
