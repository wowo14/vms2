<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="penyedia-form">
    <?php $form = ActiveForm::begin([
        'id'=>'penyedia-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
        <?= $form->field($model, 'npwp')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'nama_perusahaan')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'alamat_perusahaan')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'nomor_telepon')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'email_perusahaan')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'tanggal_pendirian')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'kategori_usaha')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'akreditasi')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'active')->textInput() ?>

    <?= $form->field($model, 'propinsi')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'kota')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'kode_pos')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'mobile_phone')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'website')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'is_cabang')->textInput() ?>

    <?= $form->field($model, 'alamat_kantorpusat')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'telepon_kantorpusat')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'fax_kantorpusat')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'email_kantorpusat')->textarea(['rows' => 6]) ?>

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