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
        <h4>Data Item Produk</h4>
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