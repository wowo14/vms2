<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="peralatankerja-form">
    <?php $form = ActiveForm::begin([
        'id'=>'peralatankerja-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
          <?= $form->field($model, 'penyedia_id')->textInput() ?>
      <?= $form->field($model, 'nama_alat')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'jumlah')->textInput() ?>
      <?= $form->field($model, 'kapasitas')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'merk_tipe')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'tahun_pembuatan')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'kondisi')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'lokasi_sekarang')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'status_kepemilikan')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'bukti_kepemilikan')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'file')->textarea(['rows' => 6]) ?>
    <?php if (!Yii::$app->request->isAjax){ ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>
