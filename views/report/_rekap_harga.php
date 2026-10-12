<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\Modal;

$this->title = 'Rekap Perbandingan Harga';
$this->params['breadcrumbs'][] = ['label' => 'Report', 'url' => ['report/index']];
$this->params['breadcrumbs'][] = $this->title;

// Generate unique modal ID
$idmodal = 'paketModal-' . uniqid();

// Get all unique years from the data
$allYears = [];
foreach ($reportData as $kategori => $products) {
    foreach ($products as $product) {
        foreach ($product['riwayat_tahun'] as $tahun => $data) {
            if (!in_array($tahun, $allYears)) {
                $allYears[] = $tahun;
            }
        }
    }
}
sort($allYears);
?>

<div class="report-rekap-harga">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">
                <i class="fas fa-chart-line"></i> Rekap Perbandingan Harga
            </h4>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <a href="<?= Url::to(['report/rekap-harga']) ?>" class="btn btn-secondary">
                    <i class="fas fa-filter"></i> Filter
                </a>
            </div>
            
            <?php if (!empty($reportData)): ?>
                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Hasil Rekap</h5>
                        <?= Html::beginForm(['report/rekap-harga'], 'post', ['id' => 'export-form']) ?>
                        <?= Html::hiddenInput('type', 'pdf') ?>
                        <?php foreach ($params as $key => $value): ?>
                            <?php if ($value !== null && $value !== ''): ?>
                                <?= Html::hiddenInput($key, $value) ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?= Html::submitButton(
                            '<i class="fas fa-file-pdf"></i> Export PDF',
                            ['class' => 'btn btn-danger btn-sm']
                        ) ?>
                        <?= Html::endForm() ?>
                    </div>
                    
                    <?php foreach ($reportData as $kategori => $products): ?>
                        <div class="produk-category mb-4">
                            <h5 class="bg-primary text-white p-2"><?= Html::encode($kategori) ?></h5>
                            
                            <table class="table table-striped table-bordered table-produk">
                                <thead>
                                    <tr>
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">Nama Produk</th>
                                        <th rowspan="2">Satuan</th>
                                        <?php foreach ($allYears as $tahun): ?>
                                            <th colspan="2"><?= $tahun ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <?php foreach ($allYears as $tahun): ?>
                                            <th>Harga Negosiasi</th>
                                            <th>Fluktuasi</th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr data-nama-produk="<?= strtolower($product['nama_produk']) ?>">
                                            <td><?= $no++ ?></td>
                                            <td><?= Html::encode($product['nama_produk']) ?></td>
                                            <td><?= Html::encode($product['satuan']) ?></td>
                                            
                                            <?php foreach ($allYears as $tahun): ?>
                                                <?php 
                                                $data = $product['riwayat_tahun'][$tahun] ?? null;
                                                if ($data): 
                                                ?>
                                                    <td class="text-right">
                                                        <?= number_format($data['harga_negosiasi'] ?? 0, 0, ',', '.') ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php 
                                                        $fluktuasi = $data['persentase_fluktuasi'];
                                                        if ($fluktuasi === null): ?>
                                                            <span class="text-muted">–</span>
                                                        <?php elseif ($fluktuasi > 0): ?>
                                                            <span class="text-danger">+<?= number_format($fluktuasi, 2) ?>%</span>
                                                        <?php elseif ($fluktuasi < 0): ?>
                                                            <span class="text-success"><?= number_format($fluktuasi, 2) ?>%</span>
                                                        <?php else: ?>
                                                            <span class="text-muted">0%</span>
                                                        <?php endif; ?>
                                                    </td>
                                                <?php else: ?>
                                                    <td class="text-center text-muted">-</td>
                                                    <td class="text-center text-muted">-</td>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="mt-3">
                        <small class="text-info">
                            <i class="fas fa-info-circle"></i> 
                            Data menampilkan harga negosiasi terakhir per tahun berdasarkan kategori pengadaan.
                            Harga dalam Rupiah. Fluktuasi dihitung berdasarkan harga negosiasi tahun berjalan vs tahun sebelumnya.
                        </small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$baseUrl = \yii\helpers\Url::to(['/paketpengadaan/view']);
$this->registerJs(<<<JS
$(document).ready(function() {
    // Filter produk
    $('#filter-produk').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        
        $('.table-produk tbody tr').filter(function() {
            $(this).toggle($(this).data('nama-produk').indexOf(value) > -1);
        });
        
        // Hide empty categories
        $('.produk-category').each(function() {
            var visibleRows = $(this).find('.table-produk tbody tr:visible').length;
            if (visibleRows === 0) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    });
});
JS
);
