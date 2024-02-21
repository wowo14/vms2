<?php
use app\assets\AppAsset;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use yii\bootstrap4\ActiveForm;
use yii\helpers\{ArrayHelper,Html,Url};
AppAsset::register($this);
$js = <<< JS
function depdropchange(){
    var propinsinya=$('#penyedia-propinsi').find(':selected').text().split('- ').pop();
    var kotanya=$('#penyedia-kota').find(':selected').text().split('- ').pop();
    if(kotanya && kotanya.trim() !== ''){
        //$('#penyedia-kota').val(kotanya);
        console.log(kotanya);
    }
    if(propinsinya && propinsinya.trim() !== ''){
        //$('#penyedia-propinsi').val(propinsinya);
        console.log(propinsinya);
    }
}
JS;
    $this->registerJs($js);
?>
<div class="penyedia-form">
    <?php $form = ActiveForm::begin([
        'id' => 'penyedia-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 col-md-3 control-label text-sm-left text-md-right'],
        ],
    ]); ?>
    <?= $form->field($model, 'npwp')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'nama_perusahaan')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'alamat_perusahaan')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'propinsi')->widget(Select2::class, [
        'data' => ArrayHelper::map(Yii::$app->regions->getData('propinsi'), 'id', 'name'),
        'options' => ['placeholder' => 'Select propinsi penyedia'],
        'pluginOptions' => [
            'allowClear' => true
        ]
    ]) ?>
    <?= $form->field($model, 'kota')->widget(DepDrop::class, [
        'data' => !$model->isNewRecord && isset($model->kota) ? $model->kab : [],
        'type' => DepDrop::TYPE_SELECT2,
        'options' => ['placeholder' => 'Select ...'],
        'select2Options' => [
            'options' => ['placeholder' => 'Select ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
            'pluginEvents' => [
                "change" => "function(){
                            var el=$(this).val();
                            if(el !=='' && el !==null){
                                depdropchange(el);
                            }
                        }",
            ],
        ],
        'pluginOptions' => [
            'depends' => ['penyedia-propinsi'],
            'placeholder' => 'Select...',
            'url' => Url::to(['site/depdropregion?param=kabupaten'])
        ],
        // 'pluginEvents' => [
        //     "depdrop:change" => "function(){
        //     }",
        // ],
    ]) ?>
    <?= $form->field($model, 'nomor_telepon')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'email_perusahaan')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'tanggal_pendirian')->widget(DatePicker::class, [
        'options' => ['placeholder' => 'Select date ...'],
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
            'autoclose' => true
        ]
    ]) ?>
    <?= $form->field($model, 'kategori_usaha')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'akreditasi')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'kode_pos')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'active')->widget(SwitchInput::class, [
        'pluginOptions' => ['size' => 'mini'],
    ]); ?>
    <?= $form->field($model, 'is_cabang')->widget(SwitchInput::class, [
        'pluginOptions' => ['size' => 'mini'],
    ]); ?>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>