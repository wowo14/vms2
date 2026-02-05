<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
?>

<div class="report-penyedia-import">
    <div class="alert alert-info">
        <h5><i class="icon fas fa-info"></i> Petunjuk Import</h5>
        <p>Silakan upload file <strong>Excel (.xlsx / .xls)</strong> atau <strong>CSV (.csv)</strong> dengan format kolom sebagai berikut:</p>
        <ul>
            <li>Kolom A: Nama Penyedia</li>
            <li>Kolom B: Alamat</li>
            <li>Kolom C: Kota</li>
            <li>Kolom D: Telepon</li>
            <li>Kolom E: Produk ditawarkan</li>
            <li>Kolom F: Jenis Pekerjaan (Konstruksi, Alat Kesehatan, dll)</li>
            <li>Kolom G: Nama Paket</li>
            <li>Kolom H: Bidang</li>
            <li>Kolom I: Nilai Evaluasi</li>
        </ul>
        <p>Baris pertama dianggap sebagai header dan akan dilewati.</p>
        <p class="text-danger"><small><strong>Catatan:</strong> Jika mengalami error "ZipArchive not found", gunakan format <strong>.csv</strong> atau aktifkan extension zip di php.ini.</small></p>
    </div>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= Html::fileInput('file', null, ['class' => 'form-control', 'required' => true]) ?>

    <?php ActiveForm::end(); ?>
</div>