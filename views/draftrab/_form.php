<?php
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\helpers\{Html, ArrayHelper, Url};
use yii\widgets\ActiveForm;
$js = <<< JS
function rabid(){
    var programnya=$('#draftrab-kode_program').find(':selected').text().split('- ').pop();
    var kegiatannya=$('#draftrab-kode_kegiatan').find(':selected').text().split('- ').pop();
    var rekeningnya=$('#draftrab-kode_rekening').find(':selected').text().split('- ').pop();
    if(programnya && programnya.trim() !== ''){
        $('#draftrab-nama_program').val(programnya);
    }
    if(kegiatannya && kegiatannya.trim() !== ''){
        $('#draftrab-nama_kegiatan').val(kegiatannya);
    }
    if(rekeningnya && rekeningnya.trim() !== ''){
        $('#draftrab-uraian_anggaran').val(rekeningnya);
    }
}
JS;
$this->registerJs($js, \yii\web\View::POS_HEAD);
?>
<div class="draft-rab-form">
    <?php $form = ActiveForm::begin(
        [
            'id' => 'draft-rab-form',
            'enableAjaxValidation' => false,
            'fieldConfig' => [
                'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
                'labelOptions' => ['class' => 'col-sm-3 control-label right'],
            ],
            'options' => ['class' => 'row clear-fix']
        ]
    ); ?>
    <div class="col-md-6">
        <?= $form->field($model, 'tahun_anggaran')->widget(Select2::class, [
            'data' => $model->isNewRecord ? $model::optiontahunanggaran() : $model::optiontahunanggaran($model->tahun_anggaran),
            'options' => ['placeholder' => 'Select tahun'],
            'pluginOptions' => [
                'allowClear' => true
            ]
        ]) ?>
        <?= $form->field($model, 'kode_program')->widget(DepDrop::class, [
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
                    console.log(el);
                    if(el !=='' || el !==null || el !=='undefined'){
                        rabid(el);
                    }
                }",
                ],
            ],
            'pluginOptions' => [
                'depends' => ['draftrab-tahun_anggaran'],
                'placeholder' => 'Select...',
                'url' => Url::to(['/programkegiatan/child?param=tahun'])
            ],
            'pluginEvents' => [
                "depdrop:change" => "function(){
                var el=$(this).val();
                    if(el !=='' || el !==null || el !=='undefined'){
                            rabid(el);
                    }
            }",
            ],
        ]) ?>
        <?= $form->field($model, 'kode_kegiatan')->widget(DepDrop::class, [
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
                    if(el !=='' || el !==null || el !=='undefined'){
                        rabid(el);
                    }
                }",
                ],
            ],
            'pluginOptions' => [
                'depends' => ['draftrab-tahun_anggaran'],
                'placeholder' => 'Select...',
                'url' => Url::to(['/programkegiatan/child?param=program'])
            ],
            'pluginEvents' => [
                "depdrop:change" => "function(){
                var el=$(this).val();
                    if(el !=='' || el !==null || el !=='undefined'){
                            rabid(el);
                    }
            }",
            ],
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'kode_rekening')->widget(DepDrop::class, [
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
                    if(el !=='' || el !==null || el !=='undefined'){
                        rabid(el);
                    }
                }",
                ],
            ],
            'pluginOptions' => [
                'depends' => ['draftrab-tahun_anggaran'],
                'placeholder' => 'Select...',
                'url' => Url::to(['/programkegiatan/child?param=koderekening'])
            ],
            'pluginEvents' => [
                "depdrop:change" => "function(){
                var el=$(this).val();
                    if(el !=='' || el !==null || el !=='undefined'){
                            rabid();
                    }
            }",
            ],
        ]) ?>
        <?= $form->field($model, 'uraian_anggaran')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'jumlah_anggaran')->textInput() ?>
        <?= $form->field($model, 'sumber_dana')->widget(Select2::class, [
            'data' => $model::optionsSettingtype('sumberdana', 'value'),
            'options' => ['placeholder' => 'Select Sumber Dana'],
            'pluginOptions' => [
                'allowClear' => true
            ]
        ]) ?>
    </div>
    <?= $form->field($model, 'nama_program')->hiddenInput(['maxlength' => true])->label(false) ?>
    <?= $form->field($model, 'nama_kegiatan')->hiddenInput(['maxlength' => true])->label(false) ?>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>