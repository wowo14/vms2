<?php

use yii\helpers\Html;

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
<h2 style="text-align: center;">Rekap Perbandingan Harga</h2>
<p style="text-align: center; font-size: 12px;">
    <?php if (!empty($params['kategori_pengadaan'])): ?>
        Kategori: <?= Html::encode($params['kategori_pengadaan']) ?><br>
    <?php endif; ?>
    <?php if (!empty($params['nama_produk'])): ?>
        Produk: <?= Html::encode($params['nama_produk']) ?><br>
    <?php endif; ?>
    <?php if (!empty($params['tahun'])): ?>
        Tahun: <?= Html::encode($params['tahun']) ?><br>
    <?php endif; ?>
    Tanggal Cetak: <?= date('d/m/Y H:i') ?>
</p>
<hr style="margin: 10px 0;">

<?php foreach ($reportData as $kategori => $products): ?>
    <div style="background-color: #e0e0e0; font-weight: bold; font-size: 12px; padding: 8px; margin-top: 15px;">
        <?= Html::encode($kategori) ?>
    </div>
    
    <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
        <thead>
            <tr>
                <th rowspan="2" style="border: 1px solid #000; padding: 4px; text-align: center; background-color: #f0f0f0; font-weight: bold;">No</th>
                <th rowspan="2" style="border: 1px solid #000; padding: 4px; text-align: center; background-color: #f0f0f0; font-weight: bold;">Nama Produk</th>
                <th rowspan="2" style="border: 1px solid #000; padding: 4px; text-align: center; background-color: #f0f0f0; font-weight: bold;">Satuan</th>
                <?php foreach ($allYears as $tahun): ?>
                    <th colspan="2" style="border: 1px solid #000; padding: 4px; text-align: center; background-color: #f0f0f0; font-weight: bold;"><?= $tahun ?></th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <?php foreach ($allYears as $tahun): ?>
                    <th style="border: 1px solid #000; padding: 4px; text-align: center; background-color: #f0f0f0; font-weight: bold;">Harga Negosiasi</th>
                    <th style="border: 1px solid #000; padding: 4px; text-align: center; background-color: #f0f0f0; font-weight: bold;">Fluktuasi</th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td style="border: 1px solid #000; padding: 4px; text-align: center;"><?= $no++ ?></td>
                    <td style="border: 1px solid #000; padding: 4px; text-align: left;"><?= Html::encode($product['nama_produk']) ?></td>
                    <td style="border: 1px solid #000; padding: 4px; text-align: center;"><?= Html::encode($product['satuan']) ?></td>
                    
                    <?php foreach ($allYears as $tahun): ?>
                        <?php 
                        $data = $product['riwayat_tahun'][$tahun] ?? null;
                        if ($data): 
                        ?>
                            <td style="border: 1px solid #000; padding: 4px; text-align: right;"><?= number_format($data['harga_negosiasi'] ?? 0, 0, ',', '.') ?></td>
                            <td style="border: 1px solid #000; padding: 4px; text-align: center;">
                                <?php 
                                $fluktuasi = $data['persentase_fluktuasi'];
                                if ($fluktuasi === null): ?>
                                    –
                                <?php elseif ($fluktuasi > 0): ?>
                                    +<?= number_format($fluktuasi, 2) ?>%
                                <?php elseif ($fluktuasi < 0): ?>
                                    <?= number_format($fluktuasi, 2) ?>%
                                <?php else: ?>
                                    0%
                                <?php endif; ?>
                            </td>
                        <?php else: ?>
                            <td style="border: 1px solid #000; padding: 4px; text-align: center;">-</td>
                            <td style="border: 1px solid #000; padding: 4px; text-align: center;">-</td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endforeach; ?>

<p style="font-size: 9px; margin-top: 20px;">
    <em>Catatan: Data menampilkan harga negosiasi terakhir per tahun berdasarkan kategori pengadaan. Harga dalam Rupiah. Fluktuasi dihitung berdasarkan harga negosiasi tahun berjalan vs tahun sebelumnya.</em>
</p>
