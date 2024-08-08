<?php
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use kartik\typeahead\Typeahead;
use yii\bootstrap4\ActiveForm;
use yii\helpers\{Html, ArrayHelper,Url};
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

    $('#pegawai-nama').on('select2:select', function (e) {
        var data = e.params.data;
        $('#pegawai-nik').val(data.nik);
        $('#pegawai-nip').val(data.nip);
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
    <?=$form->field($model, 'nama')->widget(Select2::class, [
    'options' => ['placeholder' => 'Cari atau tambah nama...'],
    'pluginOptions' => [
        'tags' => true,
        'tokenSeparators' => [',', ' '],
        // 'maximumInputLength' => 100,
        'ajax' => [
            'url' => Url::to(['/pegawai/nama']),
            'dataType' => 'json',
            'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }'),
            'processResults' => new \yii\web\JsExpression('function(data) {
                return {results: data.results};
            }'),
        ],
    ],
    ])?>
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