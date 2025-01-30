<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="penilaian-penyedia-form">
    <?php $form = ActiveForm::begin([
        'id'=>'penilaian-penyedia-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
          <?= $form->field($model, 'unit_kerja')->textInput(['maxlength' => true]) ?>
      <?= $form->field($model, 'nama_perusahaan')->textInput(['maxlength' => true]) ?>
      <?= $form->field($model, 'alamat_perusahaan')->textInput(['maxlength' => true]) ?>
      <?= $form->field($model, 'paket_pekerjaan')->textInput(['maxlength' => true]) ?>
      <?= $form->field($model, 'lokasi_pekerjaan')->textInput(['maxlength' => true]) ?>
      <?= $form->field($model, 'nomor_kontrak')->textInput(['maxlength' => true]) ?>
      <?= $form->field($model, 'jangka_waktu')->textInput(['maxlength' => true]) ?>
      <?= $form->field($model, 'tanggal_kontrak')->textInput() ?>
      <?= $form->field($model, 'metode_pemilihan')->textInput(['maxlength' => true]) ?>
      <?= $form->field($model, 'details')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'pengguna_anggaran')->textInput(['maxlength' => true]) ?>
      <?= $form->field($model, 'pejabat_pembuat_komitmen')->textInput(['maxlength' => true]) ?>
      <?= $form->field($model, 'nilai_kontrak')->textInput(['maxlength' => true]) ?>
      <?= $form->field($model, 'dpp_id')->textInput() ?>
    <?php if (!Yii::$app->request->isAjax){ ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>
