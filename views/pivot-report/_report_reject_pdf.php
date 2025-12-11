<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ArrayDataProvider */
/* @var $year string */
/* @var $reports array */
/* @var $configs array */
/* @var $months array */
/* @var $model app\models\ReportModel */
/* @var $filters array */
/* @var $filterLabels array */

$this->title = 'Laporan & Monitoring Paket Ditolak Tahun ' . Html::encode($year);

if (!empty($filters)) {
    $subtitle = 'Filter berdasarkan ';
    if (!empty($filterLabels)) {
        $subtitle .= implode(', ', $filterLabels);
    }
} else {
    $subtitle = 'Tanpa filter, menampilkan semua data';
}
?>

<h3><?= Html::encode($this->title) ?></h3>
<p><?= Html::encode($subtitle) ?></p>

<div class="section-list">
    <h4>Daftar Paket Status Reject / Perlu Perbaikan</h4>
    <table class="report-table" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;" border="1" cellpadding="5">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="width: 5%">No</th>
                <th style="width: 30%">Nama Paket</th>
                <th style="width: 15%">Pagu</th>
                <th style="width: 20%">Pejabat Pengadaan</th>
                <th style="width: 15%">Tgl Reject Terakhir</th>
                <th style="width: 15%">Alasan Terakhir</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $models = $dataProvider->getModels();
            if (empty($models)) {
                echo '<tr><td colspan="6" class="text-center">Tidak ada data.</td></tr>';
            } else {
                foreach ($models as $index => $row): 
            ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= Html::encode($row['nama_paket']) ?></td>
                    <td style="text-align: right;"><?= Yii::$app->formatter->asCurrency($row['pagu']) ?></td>
                    <td><?= Html::encode($row['pejabat_pengadaan']) ?></td>
                    <td><?= $row['tanggal_reject'] ? Yii::$app->formatter->asDatetime($row['tanggal_reject'], 'php:d/m/Y H:i') : '-' ?></td>
                    <td><?= nl2br(Html::encode($row['alasan_reject'])) ?></td>
                </tr>
                <tr>
                    <td colspan="6" style="background-color: #fcfcfc; padding: 10px;">
                        <strong>Riwayat Detail:</strong><br>
                        <?= $this->render('_reject_detail', ['model' => $row]) ?>
                    </td>
                </tr>
            <?php 
                endforeach; 
            }
            ?>
        </tbody>
    </table>
</div>

<div style="page-break-before: always;"></div>

<div class="section-stats">
    <h4>Statistik / Rekapitulasi</h4>
    <?php foreach ($reports as $key => $report): ?>
        <div style="margin-bottom: 30px;">
            <div style="background-color: #6c757d; color: white; padding: 5px 10px; font-weight: bold; margin-bottom: 10px;">
                <?= Html::encode($configs[$key]['title']) ?>
            </div>
            <?= $this->render('_pivot_table', [
                'report' => $report,
                'months' => $months,
                'rowLabel' => $configs[$key]['rowLabel'],
                'keys' => $configs[$key],
                'year' => $year,
                'model' => $model,
                'filters' => $filters
            ]) ?>
        </div>
    <?php endforeach; ?>
</div>
