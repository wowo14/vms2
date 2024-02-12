<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="draft-rab-form">
    <?php $form = ActiveForm::begin([
        'id'=>'draft-rab-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
          <?= $form->field($model, 'tahun_anggaran')->textInput() ?>
      <?= $form->field($model, 'kode_program')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'nama_program')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'kode_kegiatan')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'nama_kegiatan')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'kode_rekening')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'uraian_anggaran')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'jumlah_anggaran')->textInput() ?>
      <?= $form->field($model, 'sisa_anggaran')->textInput() ?>
      <?= $form->field($model, 'sumber_dana')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'is_completed')->textInput() ?>
    <?php if (!Yii::$app->request->isAjax){ ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>
