<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Katalog Produk — Normalisasi Nama';
$this->params['breadcrumbs'][] = ['label' => 'Minikompetisi', 'url' => ['/minikompetisi/index']];
$this->params['breadcrumbs'][] = ['label' => 'Price Intelligence', 'url' => ['price-intelligence']];
$this->params['breadcrumbs'][] = 'Katalog Produk';
?>

<div class="d-flex align-items-center mb-3" style="gap:12px;">
    <div>
        <h3 class="mb-0"><i class="fas fa-building text-secondary mr-2"></i>Katalog Produk</h3>
        <small class="text-muted">Kelola nama produk resmi dan alias untuk normalisasi data harga</small>
    </div>
    <div class="ml-auto">
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalAddCatalog">
            <i class="fas fa-plus mr-1"></i> Tambah Produk
        </button>
    </div>
</div>

<!-- Search -->
<form method="GET" action="<?= Url::to(['product-catalog']) ?>" class="mb-3">
    <div class="input-group">
        <input type="text" name="q" class="form-control" placeholder="Cari nama produk..."
            value="<?= Html::encode($q) ?>">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
        </div>
    </div>
</form>

<!-- Catalog Table -->
<div class="card" style="border-radius:10px;border:none;box-shadow:0 2px 10px rgba(0,0,0,.08);">
    <div class="card-body p-0">
        <?php if (empty($catalog)): ?>
            <div class="py-5 text-center text-muted">
                <i class="fas fa-box-open fa-3x mb-3 d-block" style="opacity:.3;"></i>
                <p>Belum ada katalog produk. Klik <strong>Tambah Produk</strong> untuk memulai.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama Resmi (Canonical)</th>
                            <th>Kategori</th>
                            <th>Satuan Default</th>
                            <th>Alias</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($catalog as $pc): ?>
                            <tr>
                                <td><strong>
                                        <?= Html::encode($pc->canonical_name) ?>
                                    </strong></td>
                                <td>
                                    <?= Html::encode($pc->category ?: '-') ?>
                                </td>
                                <td>
                                    <?= Html::encode($pc->default_unit ?: '-') ?>
                                </td>
                                <td>
                                    <?php foreach ($pc->aliases as $alias): ?>
                                        <span class="badge badge-light border mr-1">
                                            <?= Html::encode($alias->alias_name) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Add Catalog -->
<div class="modal fade" id="modalAddCatalog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= Url::to(['product-catalog']) ?>">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                    value="<?= Yii::$app->request->csrfToken ?>">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle mr-1"></i> Tambah Katalog Produk</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Resmi (Canonical) <span class="text-danger">*</span></label>
                        <input type="text" name="canonical_name" class="form-control"
                            placeholder="Nama baku produk, misal: Susu Bubuk Full Cream" required>
                        <small class="text-muted">Nama ini akan menjadi acuan normalisasi semua varian nama
                            lain.</small>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <input type="text" name="category" class="form-control"
                            placeholder="Makanan & Minuman, Bahan Bangunan, dll.">
                    </div>
                    <div class="form-group">
                        <label>Satuan Default</label>
                        <input type="text" name="default_unit" class="form-control"
                            placeholder="kg, liter, pcs, batang, dll.">
                    </div>
                    <div class="form-group">
                        <label>Alias / Nama Lain</label>
                        <input type="text" name="aliases" class="form-control"
                            placeholder="Pisahkan dengan koma: susu bubuk, milk powder, susu bubuk fc">
                        <small class="text-muted">Semua nama lain yang merujuk ke produk yang sama.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>