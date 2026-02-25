<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use unclead\multipleinput\MultipleInput;

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

        <?php if ($model->isNewRecord): ?>
            <hr>
            <h4>Data Item Produk</h4>
            <?= MultipleInput::widget([
                'name' => 'MinikompetisiItem',
                'columns' => [
                    [
                        'name' => 'nama_produk',
                        'title' => 'Nama Produk',
                        // 'enableError' => true,
                    ],
                    [
                        'name' => 'qty',
                        'title' => 'Kuantitas',
                        // 'enableError' => true,
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
                'columns' => [
                    [
                        'name' => 'nama_vendor',
                        'title' => 'Nama Vendor',
                    ],
                    [
                        'name' => 'email_vendor',
                        'title' => 'Email Vendor',
                    ],
                ]
            ]) ?>
        <?php else: ?>
            <p class="text-muted"><i>Untuk mengubah item dan vendor, fitur diupdate menyusul atau dari menu detail
                    aslinya.</i></p>
        <?php endif; ?>

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