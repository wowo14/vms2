<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="produk-form">
    <?php $form = ActiveForm::begin([
        'id'=>'produk-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
        <?= $form->field($model, 'kode_kbki')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'nama_produk')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'merk')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status_merk')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'nama_pemilik_merk')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'nomor_produk_penyedia')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'unit_pengukuran')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'jenis_produk')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'nilai_tkdn')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'nomor_sni')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'garansi_produk')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'spesifikasi_produk')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'layanan_lain')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'komponen_biaya')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'lokasi_tempat_usaha')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'keterangan_lainya')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'active')->textInput() ?>

    <?= $form->field($model, 'hargapasar')->textInput() ?>

    <?= $form->field($model, 'hargabeli')->textInput() ?>

    <?= $form->field($model, 'hargahps')->textInput() ?>

    <?= $form->field($model, 'hargalainya')->textInput() ?>

    <?= $form->field($model, 'barcode')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'created_at')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'updated_at')->textarea(['rows' => 6]) ?>

    <?php if (!Yii::$app->request->isAjax){ ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>