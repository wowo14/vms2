<?php
use yii\helpers\Html;
?>

<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 10pt;
    }
    h3 {
        text-align: center;
        margin-bottom: 20px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    th {
        background-color: #f2f2f2;
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
        font-weight: bold;
    }
    td {
        border: 1px solid #ddd;
        padding: 6px;
    }
    .text-center {
        text-align: center;
    }
    .text-right {
        text-align: right;
    }
    .summary-section {
        margin-top: 20px;
        padding: 10px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
    }
    .summary-item {
        margin: 5px 0;
    }
</style>

<h3><?= Html::encode($title) ?></h3>

<table>
    <thead>
        <tr>
            <th class="text-center" style="width: 30px;">No</th>
            <th>Nama Penyedia</th>
            <th>Alamat</th>
            <th>Unit/Bidang Pemesan</th>
            <th>Metode Pengadaan</th>
            <th class="text-center">Jumlah Kontrak</th>
            <th class="text-right">Total Nilai Kontrak</th>
            <th class="text-center">Nilai Evaluasi</th>
            <th>PPK</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td class="text-center"><?= $row['no'] ?></td>
                    <td><?= Html::encode($row['nama_penyedia']) ?></td>
                    <td><?= Html::encode($row['alamat']) ?></td>
                    <td><?= Html::encode($row['unit_bidang']) ?></td>
                    <td><?= Html::encode($row['metode']) ?></td>
                    <td class="text-center"><?= $row['jumlah_kontrak'] ?></td>
                    <td class="text-right">Rp <?= number_format($row['total_nilai_kontrak'], 0, ',', '.') ?></td>
                    <td class="text-center"><?= $row['rata_nilai_evaluasi'] ?>/100</td>
                    <td><?= Html::encode($row['ppk']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" class="text-center">Tidak ada data</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!empty($rows)): ?>
    <div class="summary-section">
        <h4>Ringkasan Statistik</h4>
        <?php
        $totalPenyedia = count($rows);
        $totalKontrak = array_sum(array_column($rows, 'jumlah_kontrak'));
        $totalNilai = array_sum(array_column($rows, 'total_nilai_kontrak'));
        $avgEvaluasi = $totalPenyedia > 0 ? array_sum(array_column($rows, 'rata_nilai_evaluasi')) / $totalPenyedia : 0;
        
        // Distribution by evaluation score
        $scoreRanges = [
            'Sangat Baik (85-100)' => 0,
            'Baik (70-84)' => 0,
            'Cukup (55-69)' => 0,
            'Kurang (<55)' => 0,
        ];
        
        foreach ($rows as $row) {
            $score = $row['rata_nilai_evaluasi'];
            if ($score >= 85) {
                $scoreRanges['Sangat Baik (85-100)']++;
            } elseif ($score >= 70) {
                $scoreRanges['Baik (70-84)']++;
            } elseif ($score >= 55) {
                $scoreRanges['Cukup (55-69)']++;
            } else {
                $scoreRanges['Kurang (<55)']++;
            }
        }
        ?>
        
        <div class="summary-item">
            <strong>Total Penyedia:</strong> <?= $totalPenyedia ?> penyedia
        </div>
        <div class="summary-item">
            <strong>Total Kontrak:</strong> <?= $totalKontrak ?> kontrak
        </div>
        <div class="summary-item">
            <strong>Total Nilai Kontrak:</strong> Rp <?= number_format($totalNilai, 0, ',', '.') ?>
        </div>
        <div class="summary-item">
            <strong>Rata-rata Nilai Evaluasi:</strong> <?= number_format($avgEvaluasi, 2) ?>/100
        </div>
        
        <h5 style="margin-top: 15px;">Distribusi Nilai Evaluasi:</h5>
        <?php foreach ($scoreRanges as $label => $count): ?>
            <?php if ($count > 0): ?>
                <div class="summary-item">
                    <strong><?= $label ?>:</strong> <?= $count ?> penyedia (<?= number_format(($count / $totalPenyedia) * 100, 1) ?>%)
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div style="margin-top: 30px; font-size: 9pt; color: #666;">
    <p>Dicetak pada: <?= date('d-m-Y H:i:s') ?></p>
</div>
