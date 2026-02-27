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
                <?php if (!$model->isNewRecord): ?>
                    <?= Html::a('<i class="fas fa-file-upload"></i> Import dari Excel', ['import-item-form', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm']) ?>
                <?php endif; ?>
            </small>
        </h4>



        <?= MultipleInput::widget([
            'name' => 'MinikompetisiItem',
            'data' => $model->minikompetisiItems,
            'addButtonOptions' => [
                'class' => 'btn btn-success',
                'label' => '+'
            ],
            'removeButtonOptions' => [
                'label' => 'x'
            ],
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
            'addButtonOptions' => [
                'class' => 'btn btn-success',
                'label' => '+'
            ],
            'removeButtonOptions' => [
                'label' => 'x'
            ],
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

<?php if (!$model->isNewRecord): ?>
    <!-- Modal Import Item — di luar ActiveForm utama agar tidak nested -->
    <div class="modal fade" id="modalImportItem" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-file-upload mr-1"></i> Import Item dari Excel</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="<?= \yii\helpers\Url::to(['import-item', 'id' => $model->id]) ?>" method="post"
                    enctype="multipart/form-data">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                        value="<?= Yii::$app->request->csrfToken ?>">
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
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$js = <<<JS
$('#metode-select').change(function() {
    if($(this).val() == 2) {
        $('#bobot-container').show();
    } else {
        $('#bobot-container').hide();
    }
}).trigger('change');

// Trigger modal import item — kompatibel Bootstrap 4 & 5
$('#btn-open-import-item').on('click', function() {
    var el = document.getElementById('modalImportItem');
    if (!el) return;
    // Bootstrap 5
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        (new bootstrap.Modal(el)).show();
    } else if (typeof $ !== 'undefined' && $.fn.modal) {
        // Bootstrap 4 / jQuery
        $(el).modal('show');
    } else {
        // Fallback: tampilkan manual
        el.style.display = 'block';
        el.classList.add('show');
        document.body.classList.add('modal-open');
    }
});
JS;
$this->registerJs($js);
?>