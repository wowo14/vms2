<?php
use yii\helpers\Html;
$bulan = '';
$bulanRange = [];
// Menentukan bulanRange dan label bulan
if (isset($model->bulan_awal) && isset($model->bulan_akhir) && $model->bulan_awal && $model->bulan_akhir) {
    $awal = (int)$model->bulan_awal;
    $akhir = (int)$model->bulan_akhir;
    // Pastikan awal tidak lebih besar dari akhir, jika ya, tukar
    if ($awal > $akhir) {
        [$awal, $akhir] = [$akhir, $awal];
    }
    $bulanRange = array_slice($months, $awal - 1, $akhir - $awal + 1, true);
    if ($awal == $akhir) {
        $bulan = $months[$model->bulan_awal];
    } else {
        $bulan = $months[$awal] . ' s/d ' . $months[$akhir];
    }
}
// Persiapan data laporan
$pivotData = $report['pivotData'];
$rowField = $keys['rowField'];
$rowLabel = $keys['rowLabel']; // Ambil rowLabel dari $keys jika sudah ada
$isMultiSum = isset($keys['multi']); // Asumsi 'multi' mengindikasikan multiSumFields
$issumfiled = isset($keys['sumField']); // Asumsi 'sumField' mengindikasikan perlu format currency
// Fungsi pembantu untuk format currency atau tidak
$formatValue = function ($value) use ($issumfiled) {
    return $issumfiled ? Yii::$app->formatter->asCurrency($value) : $value;
};
// Hitung total kolom untuk footer di awal (lebih efisien)
$totalHpsBulan = [];
$totalNegoBulan = [];
$totalEfisienBulan = [];
$totalPerBulanSingle = [];
if ($isMultiSum) {
    foreach ($bulanRange as $bulanNum => $bulanLabel) {
        $totalHpsBulan[$bulanNum] = array_sum(array_column($pivotData, 'hps_' . $bulanNum));
        $totalNegoBulan[$bulanNum] = array_sum(array_column($pivotData, 'hasilnego_' . $bulanNum));
        $totalEfisienBulan[$bulanNum] = array_sum(array_column($pivotData, 'efisien_' . $bulanNum));
    }
} else {
    foreach ($bulanRange as $bulanNum => $bulanLabel) {
        $totalPerBulanSingle[$bulanNum] = array_sum(array_column($pivotData, $bulanNum));
    }
}
// Hitung grand totals
$grandHps = array_sum($totalHpsBulan);
$grandNego = array_sum($totalNegoBulan);
$grandEfisien = array_sum($totalEfisienBulan);
$grandTotalSingle = array_sum($totalPerBulanSingle);
// --- Bagian Tampilan (HTML & PHP Campuran) ---
?>
<h3 style="text-align:center"><?= Html::encode($keys['subTitle']) ?> Periode <?= Html::encode($bulan) ?> Tahun <?= Html::encode($year) ?></h3>
<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr class="table-primary">
            <th rowspan="<?= $isMultiSum ? 2 : 1 ?>"><?= Html::encode($rowLabel) ?></th>
            <?php foreach ($bulanRange as $bulanLabel): ?>
                <th colspan="<?= $isMultiSum ? 3 : 1 ?>" class="text-center"><?= Html::encode($bulanLabel) ?></th>
            <?php endforeach; ?>
            <th colspan="<?= $isMultiSum ? 3 : 1 ?>" class="text-center">Jumlah</th>
        </tr>
        <?php if ($isMultiSum): ?>
            <tr class="table-primary">
                <?php foreach ($bulanRange as $bulanLabel): ?>
                    <th class="text-center">HPS</th>
                    <th class="text-center">Hasil Nego</th>
                    <th class="text-center">Efisien</th>
                <?php endforeach; ?>
                <th class="text-center">HPS</th>
                <th class="text-center">Hasil Nego</th>
                <th class="text-center">Efisien</th>
            </tr>
        <?php endif; ?>
    </thead>
    <tbody>
        <?php foreach ($pivotData as $row): ?>
            <tr>
                <td><?= Html::encode($row[$rowField]) ?></td>
                <?php
                if ($isMultiSum) {
                    $rowTotalHps = $rowTotalNego = $rowTotalEfisien = 0;
                    foreach ($bulanRange as $bulanNum => $bulanLabel) {
                        $hps = $row['hps_' . $bulanNum] ?? 0;
                        $nego = $row['hasilnego_' . $bulanNum] ?? 0;
                        $efisien = $row['efisien_' . $bulanNum] ?? 0;
                        $rowTotalHps += $hps;
                        $rowTotalNego += $nego;
                        $rowTotalEfisien += $efisien;
                        echo '<td class="text-right">' . Yii::$app->formatter->asCurrency($hps) . '</td>';
                        echo '<td class="text-right">' . Yii::$app->formatter->asCurrency($nego) . '</td>';
                        echo '<td class="text-right">' . Yii::$app->formatter->asCurrency($efisien) . '</td>';
                    }
                    echo '<td class="text-right"><strong>' . Yii::$app->formatter->asCurrency($rowTotalHps) . '</strong></td>';
                    echo '<td class="text-right"><strong>' . Yii::$app->formatter->asCurrency($rowTotalNego) . '</strong></td>';
                    echo '<td class="text-right"><strong>' . Yii::$app->formatter->asCurrency($rowTotalEfisien) . '</strong></td>';
                } else {
                    foreach ($bulanRange as $bulanNum => $bulanLabel) {
                        $val = $row[$bulanNum] ?? 0;
                        echo '<td class="text-right">' . $formatValue($val) . '</td>';
                    }
                    echo '<td class="text-right"><strong>' . $formatValue($row['total_data'] ?? 0) . '</strong></td>';
                }
                ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr class="table-secondary">
            <th>Total</th>
            <?php if ($isMultiSum): ?>
                <?php foreach ($bulanRange as $bulanNum => $bulanLabel): ?>
                    <th class="text-right"><?= Yii::$app->formatter->asCurrency($totalHpsBulan[$bulanNum]) ?></th>
                    <th class="text-right"><?= Yii::$app->formatter->asCurrency($totalNegoBulan[$bulanNum]) ?></th>
                    <th class="text-right"><?= Yii::$app->formatter->asCurrency($totalEfisienBulan[$bulanNum]) ?></th>
                <?php endforeach; ?>
                <th class="text-right"><strong><?= Yii::$app->formatter->asCurrency($grandHps) ?></strong></th>
                <th class="text-right"><strong><?= Yii::$app->formatter->asCurrency($grandNego) ?></strong></th>
                <th class="text-right"><strong><?= Yii::$app->formatter->asCurrency($grandEfisien) ?></strong></th>
            <?php else: ?>
                <?php foreach ($bulanRange as $bulanNum => $bulanLabel): ?>
                    <th class="text-right"><?= $formatValue($totalPerBulanSingle[$bulanNum]) ?></th>
                <?php endforeach; ?>
                <th class="text-right"><strong><?= $formatValue($grandTotalSingle) ?></strong></th>
            <?php endif; ?>
        </tr>
    </tfoot>
</table>