<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'Import Paket Pengadaan';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="paket-pengadaan-import">

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-file-excel"></i> Form Import Paket dari Excel</h3>
        </div>
        <div class="card-body">
            
            <div class="alert alert-info">
                <h5><i class="icon fas fa-info"></i> Panduan Import</h5>
                Fitur ini digunakan untuk memasukkan (import) paket pengadaan yang tidak sempat dientry di project VMS agar dapat dinilai pada modul evaluasi penyedia.<br>
                <b>Langkah-langkah:</b>
                <ol>
                    <li>Download template excel melalui tombol dibawah.</li>
                    <li>Isi data paket pengadaan pada sheet <strong>Form Import</strong>. Anda bisa merujuk ke sheet <strong>Data Master</strong> untuk melihat ID Vendor dan ID Pegawai.</li>
                    <li>Pastikan tidak ada baris yang terlewat, lalu simpan file Excel.</li>
                    <li>Upload file Excel yang sudah diisi melalui form di bawah ini lalu klik tombol "Mulai Import".</li>
                </ol>
                <?= Html::a('<i class="fas fa-download"></i> Download Template', ['template'], ['class' => 'btn btn-sm btn-success', 'data-pjax' => 0]) ?>
            </div>

            <?php if (Yii::$app->session->hasFlash('success')): ?>
                <div class="alert alert-success alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <?= Yii::$app->session->getFlash('success') ?>
                </div>
            <?php endif; ?>

            <?php if (Yii::$app->session->hasFlash('error')): ?>
                <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <?= Yii::$app->session->getFlash('error') ?>
                </div>
            <?php endif; ?>

            <div class="row mt-4">
                <div class="col-md-6">
                    <?php $form = ActiveForm::begin([
                        'options' => ['enctype' => 'multipart/form-data']
                    ]); ?>

                    <div class="form-group">
                        <label>Pilih File Excel (.xlsx)</label>
                        <?= Html::fileInput('file', null, ['class' => 'form-control', 'accept' => '.xlsx', 'required' => true]) ?>
                    </div>

                    <div class="form-group mt-3">
                        <?= Html::submitButton('<i class="fas fa-upload"></i> Mulai Import', ['class' => 'btn btn-primary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
            
        </div>
    </div>
</div>
