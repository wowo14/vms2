<?php
use app\assets\AppAsset;
use app\models\Unit;
use app\widgets\FilePreview;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\{Html, Url};
use yii\web\View;
AppAsset::register($this);
$this->registerJs('
jQuery(function ($) {
    setupImagePreview($("#imageInput"), $("#imagePreview"), $("#file_tanggapan"));
});', View::POS_END);
$js = <<< JS
function rabid(){
    var programnya=$('#paketpengadaan-kode_program').find(':selected').text().split('- ').pop();
    var kegiatannya=$('#paketpengadaan-kode_kegiatan').find(':selected').text().split('- ').pop();
    var rekeningnya=$('#paketpengadaan-kode_rekening').find(':selected').text().split('- ').pop();
    if(programnya && programnya.trim() !== ''){
        $('#paketpengadaan-nama_program').val(programnya);
    }
    if(kegiatannya && kegiatannya.trim() !== ''){
        $('#paketpengadaan-nama_kegiatan').val(kegiatannya);
    }
    if(rekeningnya && rekeningnya.trim() !== ''){
        $('#paketpengadaan-uraian_anggaran').val(rekeningnya);
    }
}
JS;
$this->registerJs($js, \yii\web\View::POS_HEAD);
$datakodeprogram = $model->isNewRecord ? \app\models\ProgramKegiatan::optionprogram() : \app\models\ProgramKegiatan::optionprogram($model->kode_program, $model->tahun_anggaran);
?>
<div class="paket-pengadaan-form">
    <?php $form = ActiveForm::begin([
        'id' => 'paket-pengadaan-form',
        'enableAjaxValidation' => false,
        'options' => ['enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 col-md-3 control-label text-sm-left text-md-right'],
        ],
    ]); ?>
    <div class="row">
    <div class="col-md-7">
        <?= $form->field($model, 'nomor')->textInput() ?>
        <?= $form->field($model, 'nomor_persetujuan')->textInput() ?>
        <?= $form->field($model, 'nama_paket')->textArea() ?>
    </div>
    <div class="col-md-5">
        <?= $form->field($model, 'tanggal_dpp')->widget(DatePicker::class, [
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
            'autoclose' => true
        ],
    ])?>
        <?= $form->field($model, 'tanggal_persetujuan')->widget(DatePicker::class, [
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
            'autoclose' => true
        ],
    ])?>
    <?= $form->field($model, 'tanggal_paket')->widget(DatePicker::class, [
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
            'autoclose' => true
        ],
    ]) ?>
    </div>
    </div>
    <?= $form->field($model, 'tahun_anggaran')->widget(Select2::class, [
        'data' => $model::optiontahunanggaran(),
        'options' => [
            'placeholder' => 'Pilih tahun anggaran',
        ]
    ]) ?>
    <?= $form->field($model, 'kode_program')->widget(DepDrop::class, [
        'type' => DepDrop::TYPE_SELECT2,
        'options' => ['placeholder' => 'Select ...'],
        'select2Options' => [
            'data' => $datakodeprogram,
            // 'options' => ['placeholder' => 'Select ...'],
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
            'depends' => ['paketpengadaan-tahun_anggaran'],
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
            'depends' => ['paketpengadaan-tahun_anggaran'],
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
            'depends' => ['paketpengadaan-tahun_anggaran'],
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
    <?= $form->field($model, 'unit')->widget(Select2::class, [
        'data' => Unit::collectAll()->pluck('unit', 'id')->toArray(),
        'options' => [
            'placeholder' => 'Pilih Unit',
        ]
    ]) ?>
    <?= $form->field($model, 'ppkom')->widget(Select2::class, [
        'data' => $model::optionppkom(),
        'options' => [
            'placeholder' => 'Pilih PPKom',
        ]
    ]) ?>
    <?= $form->field($model, 'admin_ppkom')->widget(Select2::class, [
        'data' => $model::optionadminppkom(),
        'options' => [
            'placeholder' => 'Pilih PPKom',
        ]
    ]) ?>
    <?= $form->field($model, 'pagu')->textInput() ?>
    <?= $form->field($model, 'metode_pengadaan')->widget(Select2::class, [
        'data' => $model::optionmetodepengadaan(),
        'options' => [
            'placeholder' => 'Pilih metode pengadaan',
        ]
    ]) ?>
    <?= $form->field($model, 'kategori_pengadaan')->widget(Select2::class, [
        'data' => $model::optionkategoripengadaan(),
        'options' => [
            'placeholder' => 'Pilih kategori pengadaan',
        ]
    ]) ?>
    <?php if (!$model->isNewRecord && ($model->tanggal_reject && $model->alasan_reject)) :
        $reviews = $model->dpp->reviews;
    ?>
        <?= $form->field($model, 'tanggal_reject')->textInput() ?>
        <?= $form->field($model, 'alasan_reject')->textInput() ?>
        <?= $form->field($reviews, 'tanggapan_ppk')->textInput() ?>
        <?= $form->field($reviews, 'tgl_dikembalikan')->widget(DatePicker::class, [
            'options' => ['placeholder' => 'Select date ...'],
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'autoclose' => true
            ]
        ]) ?>
        <?= $form->field($reviews, 'file_tanggapan')->hiddenInput(['id' => 'file_tanggapan'])->label(false) ?>
        <div class="form-group ">
            <div class="row">
                <label class="control-label right col-sm-3" for="reviewdpp-file_tanggapan">File Tanggapan PPK (images/pdf)</label>
                <div class="col-sm-9">
                    <input type="file" accept=".pdf, .png, .jpg, .jpeg, .gif" id="imageInput">
                    <div id="imagePreview"></div>
                </div>
            </div>
        </div>
        <?php echo $reviews->file_tanggapan ? Html::a(
            FilePreview::widget([
                'model' => $reviews,
                'attribute' => 'file_tanggapan',
            ]),
            Yii::getAlias('@web/uploads/') . $reviews->file_tanggapan,
            ['target' => '_blank']
        ) : '';
        ?>
    <?php endif; ?>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>