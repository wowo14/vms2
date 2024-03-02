<?php
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
$paket=$model->isNewRecord?[]:ArrayHelper::map($model->allpaketpengadaan,'id','nomornamapaket');
$penyedia=$model->isNewRecord?[]:ArrayHelper::map($model->vendor,'id','nama_perusahaan');
?>
<div class="penawaran-pengadaan-form">
    <?php $form = ActiveForm::begin([
        'id'=>'penawaran-pengadaan-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
          <?= $form->field($model, 'paket_id')->widget(Select2::class,[
            'data' => $paket,
            'options' => ['placeholder' => 'Pilih Paket'],
            'pluginOptions' => [
                'allowClear' => true
            ],
          ]) ?>
      <?= $form->field($model, 'penyedia_id')->widget(Select2::class,[
            'data' => $penyedia,
            'options' => ['placeholder' => 'Pilih Penyedia'],
            'pluginOptions' => [
                'allowClear' => true
            ],
      ]) ?>
      <?= $form->field($model, 'kode')->textInput(['maxlength' => true]) ?>
      <?= $form->field($model, 'tanggal_mendaftar')->widget(DatePicker::class, [
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                    ],
                ]) ?>
      <?= $form->field($model, 'masa_berlaku')->textInput(['maxlength' => true]) ?>
      <?= $form->field($model, 'lampiran_penawaran')->textarea(['rows' => 6]) ?>
      <?= $form->field($model, 'lampiran_penawaran_harga')->textarea(['rows' => 6]) ?>
    <?php if (!Yii::$app->request->isAjax){ ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>
