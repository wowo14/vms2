<?php
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use yii\bootstrap4\ActiveForm;
use yii\helpers\{Html, ArrayHelper};
$js = <<<JS
function extractKtp(nik){
    $.ajax({url: '/pemohon/ktp',type: 'POST',
        data: {nik: nik}
    }).done(function(data){
        $('#pegawai-nama').val(data.nama);
    })
}
    $("#pegawai-nik").on("input", function() {
        var inputValue = $(this).val();
        if (inputValue.length === 16) {
            extractKtp(inputValue);
        }
    });
JS;
$this->registerJs($js);
?>
<div class="pegawai-form">
    <?php $form = ActiveForm::begin([
        'id' => 'pegawai-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
    <?= $form->field($model, 'nik')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'nip')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'nama')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'alamat')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'telp')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'status')->widget(SwitchInput::class, [
        'pluginOptions' => ['size' => 'mini'],
    ]); ?>
    <?= $form->field($model, 'hak_akses')->widget(Select2::class, [
        'data' => collect(Yii::$app->authManager->getRoles())->filter(fn ($e) => $e->name != 'Admin')->pluck('name', 'name'),
        'options' => ['placeholder' => 'Select hak akses...'],
        'pluginOptions' => [
            'allowClear' => true
        ]
    ]) ?>
    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>