<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\Modal;

$this->title = 'Laporan Perbandingan Harga Year-over-Year';
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

<div class="report-perbandingan-harga-yoy">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
            <div class="card-tools">
                <?= Html::a('<i class="fas fa-filter"></i> Filter', ['perbandingan-harga-yoy'], ['class' => 'btn btn-sm btn-default']) ?>
            </div>
        </div>
        <div class="card-body">
            <?php if ($reportData->isEmpty()): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Tidak ada data ditemukan berdasarkan filter yang dipilih.
                </div>
            <?php else: ?>
                
                <!-- Filter Produk di Hasil -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" id="filter-produk" class="form-control" placeholder="Cari nama produk...">
                        </div>
                    </div>
                </div>
                
                <?php foreach ($reportData as $kategori => $products): ?>
                    <div class="mb-4 produk-category" data-kategori="<?= Html::encode(strtolower($kategori)) ?>">
                        <h4 class="text-primary mb-3">
                            <i class="fas fa-folder"></i> <?= Html::encode($kategori) ?>
                        </h4>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm table-produk">
                                <thead class="thead-dark">
                                    <tr>
                                        <th rowspan="2" style="vertical-align: middle;">No</th>
                                        <th rowspan="2" style="vertical-align: middle;">Nama Produk</th>
                                        <th rowspan="2" style="vertical-align: middle;">Satuan</th>
                                        <?php foreach ($allYears as $tahun): ?>
                                            <th colspan="5" class="text-center"><?= $tahun ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <?php foreach ($allYears as $tahun): ?>
                                            <th class="text-center">HPS</th>
                                            <th class="text-center">Penawaran</th>
                                            <th class="text-center">Negosiasi</th>
                                            <th class="text-center">Fluktuasi</th>
                                            <th class="text-center">Jml Trx</th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr data-nama-produk="<?= Html::encode(strtolower($product['nama_produk'])) ?>">
                                            <td><?= $no++ ?></td>
                                            <td><?= Html::encode($product['nama_produk']) ?></td>
                                            <td><?= Html::encode($product['satuan']) ?></td>
                                            
                                            <?php foreach ($allYears as $tahun): ?>
                                                <?php 
                                                $data = $product['riwayat_tahun'][$tahun] ?? null;
                                                if ($data): 
                                                ?>
                                                    <td class="text-right">
                                                        <?= number_format($data['hps'] ?? 0, 0, ',', '.') ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?= number_format($data['penawaran'] ?? 0, 0, ',', '.') ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?= number_format($data['nego'] ?? 0, 0, ',', '.') ?>
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
                                                    <td class="text-center">
                                                        <?php 
                                                        $jumlahTrx = $data['jumlah_trx'];
                                                        $paketData = $data['paket_data'] ?? [];
                                                        $paketIds = $data['paket_ids'] ?? '';
                                                        $paketNames = $data['paket_names'] ?? '';
                                                        $paketIdArray = !empty($paketIds) ? explode(',', $paketIds) : [];
                                                        
                                                        if ($jumlahTrx == 1 && !empty($paketIdArray)): ?>
                                                            <a href="<?= \yii\helpers\Url::to(['paketpengadaan/view', 'id' => $paketIdArray[0]]) ?>" 
                                                               class="btn btn-sm btn-info" 
                                                               target="_blank"
                                                               title="Lihat detail paket">
                                                                <?= $jumlahTrx ?>
                                                            </a>
                                                        <?php elseif ($jumlahTrx > 1 && !empty($paketData)): ?>
                                                            <?= Html::button(
                                                                $jumlahTrx,
                                                                [
                                                                    'class' => 'btn btn-sm btn-primary btn-view-paket',
                                                                    'data-paket-data' => json_encode($paketData),
                                                                    'data-target' => '#' . $idmodal,
                                                                    'title' => 'Lihat daftar paket',
                                                                ]
                                                            ) ?>
                                                        <?php else: ?>
                                                            <?= $jumlahTrx ?>
                                                        <?php endif; ?>
                                                    </td>
                                                <?php else: ?>
                                                    <td class="text-center text-muted">-</td>
                                                    <td class="text-center text-muted">-</td>
                                                    <td class="text-center text-muted">-</td>
                                                    <td class="text-center text-muted">-</td>
                                                    <td class="text-center text-muted">-</td>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Data menampilkan rata-rata harga satuan per tahun berdasarkan kategori pengadaan.
                        Harga dalam Rupiah. Fluktuasi dihitung berdasarkan nilai negosiasi (hasil kontrak) tahun berjalan vs tahun sebelumnya.
                    </small>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
Modal::begin([
    'id' => $idmodal,
    'size' => Modal::SIZE_LARGE,
    'title' => '<h4><i class="fas fa-list"></i> Daftar Paket Pengadaan</h4>',
    'footer' => Html::button('Tutup', ['class' => 'btn btn-secondary', 'data-dismiss' => 'modal']),
]);
?>
<div id="paketListContent">
    <div class="text-center">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</div>
<?php Modal::end(); ?>

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
    
    // Handle click on Jml Trx button for multiple transactions
    $(document).on('click', '.btn-view-paket', function(e) {
        e.preventDefault();
        
        var btn = $(this);
        var idmodal = btn.data('target');
        var paketData = btn.data('paket-data');
        
        var paketListHtml = '<table class="table table-striped table-hover">';
        paketListHtml += '<thead><tr>';
        paketListHtml += '<th>Nama Produk</th>';
        paketListHtml += '<th>Harga Negosiasi</th>';
        paketListHtml += '<th>ID Paket</th>';
        paketListHtml += '</tr></thead><tbody>';
        
        var baseUrl = '$baseUrl';
        
        for (var i = 0; i < paketData.length; i++) {
            var url = baseUrl + '?id=' + paketData[i].id;
            var hargaNegosiasi = paketData[i].harga_negosiasi ? new Intl.NumberFormat('id-ID').format(paketData[i].harga_negosiasi) : '-';
            
            paketListHtml += '<tr>';
            paketListHtml += '<td>' + paketData[i].nama_produk + '</td>';
            paketListHtml += '<td>' + hargaNegosiasi + '</td>';
            paketListHtml += '<td><a href="' + url + '" target="_blank" class="btn btn-sm btn-info">' + paketData[i].id + '</a></td>';
            paketListHtml += '</tr>';
        }
        
        paketListHtml += '</tbody></table>';
        
        $(idmodal).find('#paketListContent').html(paketListHtml);
        $(idmodal).modal('show');
    });
});
JS
);
