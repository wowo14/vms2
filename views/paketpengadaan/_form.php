<?php
use app\assets\AppAsset;
use app\widgets\FilePreview;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
AppAsset::register($this);
$this->registerJs('
jQuery(function ($) {
    setupImagePreview($("#imageInput"), $("#imagePreview"), $("#file_tanggapan"));
});', View::POS_END);
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
    <?= $form->field($model, 'nomor')->textInput() ?>
    <?= $form->field($model, 'tanggal_paket')->widget(DatePicker::class, [
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
            'autoclose' => true
        ],
    ]) ?>
    <?= $form->field($model, 'nama_paket')->textArea() ?>
    <?= $form->field($model, 'kode_program')->textInput() ?>
    <?= $form->field($model, 'kode_kegiatan')->textInput() ?>
    <?= $form->field($model, 'kode_rekening')->textInput() ?>
    <?= $form->field($model, 'ppkom')->textInput() ?>
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
    <?= $form->field($model, 'tahun_anggaran')->widget(Select2::class, [
        'data' => $model::optiontahunanggaran(),
        'options' => [
            'placeholder' => 'Pilih tahun anggaran',
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