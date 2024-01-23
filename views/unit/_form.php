<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="unit-form">
    <?php $form = ActiveForm::begin([
        'id' => 'unit-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 col-md-3 control-label text-sm-left text-md-right'],
        ],
    ]); ?>
    <?= $form->field($model, 'kode')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'unit')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'fk_instalasi')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'is_vip')->textInput() ?>

    <?= $form->field($model, 'aktif')->textInput() ?>

    <?= $form->field($model, 'logo')->textarea(['rows' => 6]) ?>

    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>