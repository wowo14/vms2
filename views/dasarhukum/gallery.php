<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\LinkPager;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $categories array */

$this->title = 'Galeri Dasar Hukum';
$models = $dataProvider->getModels();
$currentCategory = Yii::$app->request->get('kategori');
?>

<style>
    .hero-section {
        background: linear-gradient(135deg, #0d6efd 0%, #003d99 100%);
        padding: 80px 0;
        color: white;
        margin-bottom: 50px;
        clip-path: ellipse(150% 100% at 50% 0%);
    }
    .search-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        margin-top: -60px;
        border: none;
    }
    .doc-card {
        border: none;
        border-radius: 20px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        overflow: hidden;
        background: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        height: 100%;
    }
    .doc-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }
    .doc-img-container {
        height: 200px;
        overflow: hidden;
        position: relative;
    }
    .doc-img {
        width: 100%;
        height: 100%;
        object-cover: cover;
        transition: transform 0.5s;
    }
    .doc-card:hover .doc-img {
        transform: scale(1.1);
    }
    .category-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: rgba(255,255,255,0.9);
        color: #0d6efd;
        font-weight: 700;
        padding: 5px 15px;
        border-radius: 50px;
        font-size: 0.75rem;
        text-transform: uppercase;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .btn-category {
        border-radius: 50px;
        padding: 8px 20px;
        margin: 5px;
        font-weight: 500;
        transition: all 0.3s;
        border: 1px solid #dee2e6;
    }
    .btn-category.active {
        background: #0d6efd;
        color: white;
        border-color: #0d6efd;
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
    }
</style>

<div class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 font-weight-bold mb-3">Pusat Dasar Hukum</h1>
        <p class="lead opacity-75">Kumpulan regulasi terbaru Pengadaan Barang dan Jasa Pemerintah</p>
    </div>
</div>

<div class="container mb-5">
    <!-- Search & Filter Area -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="search-card mb-5">
                <form action="<?= Url::to(['dasarhukum/gallery']) ?>" method="GET">
                    <div class="input-group input-group-lg mb-4">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-transparent border-right-0 rounded-left" style="border-radius: 15px 0 0 15px;">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                        </div>
                        <input type="text" name="search" value="<?= Html::encode(Yii::$app->request->get('search')) ?>" class="form-control border-left-0" placeholder="Cari judul atau nomor peraturan..." style="border-radius: 0 15px 15px 0;">
                        <div class="input-group-append ml-2">
                            <button type="submit" class="btn btn-primary px-4" style="border-radius: 15px;">Cari</button>
                        </div>
                    </div>
                </form>

                <div class="text-center">
                    <p class="text-muted small font-weight-bold text-uppercase mb-3">Filter Kategori</p>
                    <div class="d-flex flex-wrap justify-content-center">
                        <a href="<?= Url::to(['dasarhukum/gallery']) ?>" class="btn btn-light btn-category <?= !$currentCategory ? 'active' : '' ?>">Semua</a>
                        <?php foreach ($categories as $cat): ?>
                            <?php if (!$cat) continue; ?>
                            <a href="<?= Url::to(['dasarhukum/gallery', 'kategori' => $cat]) ?>" class="btn btn-light btn-category <?= $currentCategory === $cat ? 'active' : '' ?>">
                                <?= Html::encode($cat) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="row">
        <?php if (!empty($models)): ?>
            <?php foreach ($models as $model): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card doc-card">
                        <div class="doc-img-container">
                            <?php if ($model->foto): ?>
                                <img src="<?= $model->foto ?>" class="doc-img" alt="<?= Html::encode($model->judul) ?>">
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light text-muted">
                                    <i class="fas fa-file-alt fa-4x opacity-25"></i>
                                </div>
                            <?php endif; ?>
                            <span class="category-badge"><?= Html::encode($model->kategori ?: 'Regulasi') ?></span>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="far fa-calendar-alt text-primary mr-2 small"></i>
                                <span class="text-muted small font-weight-bold">
                                    <?= ($model->tanggal_ditetapkan && strtotime($model->tanggal_ditetapkan)) ? date('d M Y', strtotime($model->tanggal_ditetapkan)) : 'No Date' ?>
                                </span>
                            </div>
                            <h5 class="card-title font-weight-bold text-dark mb-3 line-clamp-2" style="min-height: 3rem;">
                                <?= Html::encode($model->judul) ?>
                            </h5>
                            <p class="card-text text-muted small mb-4 line-clamp-3">
                                <?= Html::encode(strip_tags($model->summary)) ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <span class="text-muted extra-small">Oleh: <?= Html::encode($model->penerbit ?: 'Portal PBJ') ?></span>
                                <div class="btn-group">
                                    <?php if ($model->file_pdf): ?>
                                        <a href="<?= $model->file_pdf ?>" target="_blank" class="btn btn-outline-danger btn-sm rounded-circle mr-2" title="Download PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= Url::to(['dasarhukum/detail', 'id' => $model->id]) ?>" class="btn btn-primary btn-sm rounded-pill px-3">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-folder-open fa-5x text-light"></i>
                </div>
                <h3>Dokumen tidak ditemukan</h3>
                <p class="text-muted">Gunakan kata kunci lain atau reset filter untuk mencarian lebih luas.</p>
                <a href="<?= Url::to(['dasarhukum/gallery']) ?>" class="btn btn-primary mt-3">Reset Pencarian</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-5">
        <?= LinkPager::widget([
            'pagination' => $dataProvider->pagination,
            'listOptions' => ['class' => 'pagination pagination-lg'],
            'linkContainerOptions' => ['class' => 'page-item'],
            'linkOptions' => ['class' => 'page-link rounded-circle mx-1 border-0 shadow-sm'],
            'activePageCssClass' => 'active',
            'disabledPageCssClass' => 'disabled',
        ]) ?>
    </div>
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .extra-small {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .pagination .page-link {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
    }
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        color: white;
    }
</style>

