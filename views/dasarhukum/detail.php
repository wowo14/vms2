<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\GaleryDasarhukum */

$this->title = $model->judul;
?>

<style>
    .detail-hero {
        background: #2d3436;
        color: white;
        padding: 60px 0;
        position: relative;
    }
    .badge-category {
        background: #0d6efd;
        color: white;
        padding: 5px 15px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 15px;
        display: inline-block;
    }
    .info-strip {
        background: #f1f3f5;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 30px;
    }
    .info-item i {
        color: #0d6efd;
        width: 30px;
        font-size: 1.2rem;
    }
    .doc-content-card {
        background: white;
        border-radius: 25px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        border: none;
    }
    .sidebar-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: none;
        margin-bottom: 25px;
    }
    .pdf-viewer-container {
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #dee2e6;
        height: 800px;
        background: #e9ecef;
    }
</style>

<div class="detail-hero">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-4">
                <li class="breadcrumb-item"><a href="<?= Url::to(['dasarhukum/gallery']) ?>" class="text-primary">Galeri</a></li>
                <li class="breadcrumb-item active text-white opacity-50" aria-current="page"><?= Html::encode($model->kategori) ?></li>
            </ol>
        </nav>
        <div class="badge-category"><?= Html::encode($model->kategori) ?></div>
        <h1 class="font-weight-bold"><?= Html::encode($model->judul) ?></h1>
    </div>
</div>

<div class="container mt-n4">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card doc-content-card mb-4">
                <div class="info-strip d-flex flex-wrap justify-content-between">
                    <div class="info-item mb-2 mb-md-0">
                        <i class="fas fa-hashtag"></i>
                        <span class="text-muted small font-weight-bold">NOMOR:</span>
                        <div class="ml-4 font-weight-bold text-dark"><?= Html::encode($model->nomor ?: '-') ?></div>
                    </div>
                    <div class="info-item mb-2 mb-md-0">
                        <i class="far fa-calendar-check"></i>
                        <span class="text-muted small font-weight-bold">DITETAPKAN:</span>
                        <div class="ml-4 font-weight-bold text-dark"><?= ($model->tanggal_ditetapkan && strtotime($model->tanggal_ditetapkan)) ? date('d F Y', strtotime($model->tanggal_ditetapkan)) : '-' ?></div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-landmark"></i>
                        <span class="text-muted small font-weight-bold">PENERBIT:</span>
                        <div class="ml-4 font-weight-bold text-dark"><?= Html::encode($model->penerbit ?: '-') ?></div>
                    </div>
                </div>

                <h4 class="font-weight-bold mb-4">Ringkasan Dokumen</h4>
                <div class="text-muted leading-relaxed" style="font-size: 1.05rem;">
                    <?= nl2br(Html::encode($model->summary)) ?>
                </div>

                <?php if ($model->tags): ?>
                <div class="mt-5 pt-4 border-top">
                    <p class="text-muted small font-weight-bold text-uppercase mb-2">Kata Kunci</p>
                    <?php foreach (explode(',', $model->tags) as $tag): ?>
                        <span class="badge badge-light p-2 mr-2 border">#<?= trim($tag) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- PDF Preview Section -->
            <?php if ($model->file_pdf): ?>
            <div class="doc-content-card p-0 mb-5 overflow-hidden">
                <div class="bg-primary text-white p-3 d-flex justify-content-between align-items-center">
                    <span class="font-weight-bold"><i class="far fa-file-pdf mr-2"></i>Pratinjau Dokumen</span>
                    <a href="<?= $model->file_pdf ?>" target="_blank" class="btn btn-light btn-sm font-weight-bold">Buka Fullscreen</a>
                </div>
                <div class="pdf-viewer-container">
                    <object data="<?= Url::to($model->file_pdf, true) ?>" type="application/pdf" width="100%" height="100%">
                        <iframe src="<?= Url::to($model->file_pdf, true) ?>" width="100%" height="100%" style="border: none;">
                            <p>Browser Anda tidak mendukung preview PDF. <a href="<?= Url::to($model->file_pdf, true) ?>">Klik di sini untuk mengunduh</a>.</p>
                        </iframe>
                    </object>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="sidebar-card">
                <h5 class="font-weight-bold mb-4">Unduh Berkas</h5>
                <?php if ($model->file_pdf): ?>
                    <a href="<?= $model->file_pdf ?>" target="_blank" class="btn btn-danger btn-block btn-lg mb-3 shadow">
                        <i class="fas fa-file-pdf mr-2"></i>Unduh PDF
                    </a>
                    <p class="text-muted small text-center italic mb-0">Format: Portable Document Format</p>
                <?php else: ?>
                    <div class="alert alert-light border text-center">
                        <i class="fas fa-info-circle mb-2 d-xl-block"></i>
                        <span class="small">Berkas belum tersedia</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="sidebar-card bg-primary text-white">
                <h5 class="font-weight-bold mb-3">Bagikan</h5>
                <p class="small opacity-75 mb-4">Bagikan dasar hukum ini ke kolega Anda untuk mempermudah koordinasi.</p>
                <div class="d-flex">
                    <button onclick="copyToClipboard()" class="btn btn-light flex-grow-1 mr-2 font-weight-bold">
                        <i class="fas fa-link mr-2 text-primary"></i>Copy Link
                    </button>
                    <a href="https://wa.me/?text=<?= urlencode($model->judul . ' - ' . Url::to(['dasarhukum/detail', 'id' => $model->id], true)) ?>" class="btn btn-success font-weight-bold">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="<?= Url::to(['dasarhukum/gallery']) ?>" class="btn btn-link text-muted font-weight-bold">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Galeri
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard() {
    var dummy = document.createElement('input'),
    text = window.location.href;
    document.body.appendChild(dummy);
    dummy.value = text;
    dummy.select();
    document.execCommand('copy');
    document.body.removeChild(dummy);
    alert('Link berhasil disalin ke clipboard!');
}
</script>
