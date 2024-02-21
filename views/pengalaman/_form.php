<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="pengalaman-penyedia-form">
    <?php $form = ActiveForm::begin([
        'id'=>'pengalaman-penyedia-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
          <?= $form->field($model, 'penyedia_id')->textInput() ?>
      <?= $form->field($model, 'paket_pengadaan_id')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'link')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'pekerjaan')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'lokasi')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'instansi_pemberi_tugas')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'alamat_instansi')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'tanggal_kontrak')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'tanggal_selesai_kontrak')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'nilai_kontrak')->textInput() ?>
      <?= $form->field($model, 'file')->textarea(['rows' => 6]) ?>
    <?php if (!Yii::$app->request->isAjax){ ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>
