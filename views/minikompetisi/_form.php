<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use unclead\multipleinput\MultipleInput;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\Minikompetisi */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="minikompetisi-form card card-outline card-info">
    <div class="card-header">
        <h3 class="card-title">
            <?= Html::encode($this->title) ?>
        </h3>
    </div>
    <div class="card-body">
        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'judul')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'tanggal')->textInput(['type' => 'date']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'metode')->dropDownList([
                    1 => 'Harga Terendah (Hanya Harga)',
                    2 => 'Kualitas & Harga (Kombinasi)',
                    3 => 'Lumpsum (Total Keseluruhan)',
                ], ['id' => 'metode-select']) ?>

                <div id="bobot-container" style="display:none;">
                    <?= $form->field($model, 'bobot_kualitas')->textInput(['type' => 'number']) ?>
                    <?= $form->field($model, 'bobot_harga')->textInput(['type' => 'number']) ?>
                </div>
            </div>
        </div>

        <hr>
        <h4>
            Data Item Produk
            <small class="ml-2">
                <?= Html::a('<i class="fas fa-file-excel"></i> Download Template', ['download-template-item'], [
                    'class' => 'btn btn-outline-success btn-sm',
                    'target' => '_blank',
                    'title' => 'Download template Excel untuk import item produk',
                ]) ?>
                <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal"
                    data-target="#modalImportItem">
                    <i class="fas fa-file-upload"></i> Import dari Excel
                </button>
            </small>
        </h4>

        <?php if (!$model->isNewRecord): ?>
            <!-- Modal Import Item -->
            <div class="modal fade" id="modalImportItem" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><i class="fas fa-file-upload mr-1"></i> Import Item dari Excel</h5>
                            <button type="button" class="close text-white"
                                data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <?php $importForm = \yii\bootstrap4\ActiveForm::begin([
                            'action' => ['import-item', 'id' => $model->id],
                            'options' => ['enctype' => 'multipart/form-data'],
                            'id' => 'form-import-item',
                        ]); ?>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                <strong>Perhatian:</strong> Import Excel akan <strong>mengganti semua item</strong> yang ada
                                saat ini.
                            </div>
                            <div class="form-group">
                                <label>File Excel <span class="text-danger">*</span></label>
                                <input type="file" name="file_item_excel" class="form-control-file" accept=".xlsx,.xls"
                                    required>
                                <small class="form-text text-muted">Format: .xlsx atau .xls sesuai template sistem</small>
                            </div>
                            <div class="alert alert-info p-2 mb-0" style="font-size:12px;">
                                <strong>Format kolom (mulai baris ke-4):</strong><br>
                                A: Nama Produk &nbsp;|&nbsp; B: Qty &nbsp;|&nbsp; C: Satuan &nbsp;|&nbsp; D: Harga HPS
                                &nbsp;|&nbsp; E: Harga Existing
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-upload mr-1"></i> Proses
                                Import</button>
                        </div>
                        <?php \yii\bootstrap4\ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info py-2 mb-2" style="font-size:13px;">
                <i class="fas fa-info-circle mr-1"></i> Simpan data terlebih dahulu sebelum menggunakan fitur Import Excel.
            </div>
        <?php endif; ?>

        <?= MultipleInput::widget([
            'name' => 'MinikompetisiItem',
            'data' => $model->minikompetisiItems,
            'columns' => [
                [
                    'name' => 'id',
                    'type' => 'hiddenInput',
                ],
                [
                    'name' => 'nama_produk',
                    'title' => 'Nama Produk',
                ],
                [
                    'name' => 'qty',
                    'title' => 'Kuantitas',
                ],
                [
                    'name' => 'satuan',
                    'title' => 'Satuan',
                ],
                [
                    'name' => 'harga_hps',
                    'title' => 'Harga HPS (Satuan)',
                ],
                [
                    'name' => 'harga_existing',
                    'title' => 'Harga Beli Existing',
                ],
            ],
        ]) ?>

        <hr>
        <h4>Daftar Vendor Diundang</h4>
        <?= MultipleInput::widget([
            'name' => 'MinikompetisiVendor',
            'data' => $model->minikompetisiVendors,
            'columns' => [
                [
                    'name' => 'id',
                    'type' => 'hiddenInput',
                ],
                [
                    'name' => 'nama_vendor',
                    'title' => 'Nama Vendor',
                    'type' => Select2::class,
                    'options' => [
                        'pluginOptions' => [
                            'allowClear' => true,
                            'tags' => true,
                            'ajax' => [
                                'url' => Url::to(['penyedia-list']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(res) { return res.text; }'),
                            'templateSelection' => new JsExpression('function (res) { return res.text; }'),
                        ],
                        'pluginEvents' => [
                            "select2:select" => "function(e) { 
                                var data = e.params.data;
                                if(data.email) {
                                    $(this).closest('tr').find('input[name*=\"email_vendor\"]').val(data.email);
                                }
                            }",
                        ]
                    ]
                ],
                [
                    'name' => 'email_vendor',
                    'title' => 'Email Vendor',
                ],
            ]
        ]) ?>

        <div class="form-group mb-0 mt-3">
            <?= Html::submitButton('<i class="fas fa-save"></i> Simpan', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$js = <<<JS
$('#metode-select').change(function() {
    if($(this).val() == 2) {
        $('#bobot-container').show();
    } else {
        $('#bobot-container').hide();
    }
}).trigger('change');
JS;
$this->registerJs($js);
?>