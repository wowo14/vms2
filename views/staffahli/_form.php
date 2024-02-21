<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="staff-ahli-form">
    <?php $form = ActiveForm::begin([
        'id'=>'staff-ahli-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
          <?= $form->field($model, 'penyedia_id')->textInput() ?>
      <?= $form->field($model, 'nama')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'tanggal_lahir')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'alamat')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'email')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'jenis_kelamin')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'pendidikan')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'warga_negara')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'lama_pengalaman')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'file')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'keahlian')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'spesifikasi_pekerjaan')->textarea(['rows' => 6]) ?>
    <?php if (!Yii::$app->request->isAjax){ ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>
