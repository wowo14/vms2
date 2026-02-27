<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Minikompetisi */

$this->title = 'Import Item dari Excel';
$this->params['breadcrumbs'][] = ['label' => 'Minikompetisi', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->judul, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row justify-content-center">
    <div class="col-md-7">

        <!-- Info Card -->
        <div class="card card-outline card-info mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-1"></i>
                    Paket:
                    <?= Html::encode($model->judul) ?>
                </h3>
            </div>
            <div class="card-body p-2">
                <p class="mb-1">
                    <strong>Langkah-langkah:</strong>
                </p>
                <ol class="mb-0 pl-4" style="font-size:14px;">
                    <li>Download template Excel terlebih dahulu</li>
                    <li>Isi data item mulai dari <strong>baris ke-4</strong> (jangan ubah baris header)</li>
                    <li>Upload file yang sudah diisi</li>
                </ol>
                <div class="mt-2 p-2 bg-light rounded" style="font-size:13px;">
                    <strong>Format kolom:</strong><br>
                    <span class="badge badge-secondary">A</span> Nama Produk &nbsp;
                    <span class="badge badge-secondary">B</span> Qty &nbsp;
                    <span class="badge badge-secondary">C</span> Satuan &nbsp;
                    <span class="badge badge-secondary">D</span> Harga HPS (Satuan) &nbsp;
                    <span class="badge badge-secondary">E</span> Harga Beli Existing
                </div>
            </div>
        </div>

        <!-- Download Template -->
        <div class="mb-3">
            <?= Html::a(
                '<i class="fas fa-file-excel mr-1"></i> Download Template Excel',
                ['download-template-item'],
                ['class' => 'btn btn-success btn-block', 'target' => '_blank']
            ) ?>
        </div>

        <!-- Upload Form -->
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-upload mr-1"></i> Upload File Excel
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <strong>Perhatian:</strong> Import akan <strong>mengganti semua item yang ada</strong> pada paket
                    ini.
                </div>

                <form action="<?= Url::to(['import-item', 'id' => $model->id]) ?>" method="post"
                    enctype="multipart/form-data">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                        value="<?= Yii::$app->request->csrfToken ?>">

                    <div class="form-group">
                        <label class="font-weight-bold">
                            Pilih File Excel <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="file_item_excel" class="form-control" accept=".xlsx,.xls" required>
                        <small class="form-text text-muted">
                            Format yang diterima: <code>.xlsx</code> atau <code>.xls</code>
                        </small>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <?= Html::a(
                                '<i class="fas fa-arrow-left mr-1"></i> Kembali',
                                ['view', 'id' => $model->id],
                                ['class' => 'btn btn-secondary btn-block']
                            ) ?>
                        </div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-cloud-upload-alt mr-1"></i> Proses Import
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>