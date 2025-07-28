<?php
use yii\helpers\Html;
$bulan = '';
if (
    isset($model->bulan_awal) && isset($model->bulan_akhir)
    && $model->bulan_awal && $model->bulan_akhir
    && $model->bulan_awal != $model->bulan_akhir
) {
    $awal = (int)$model->bulan_awal;
    $akhir = (int)$model->bulan_akhir;
    if ($awal > $akhir) [$awal, $akhir] = [$akhir, $awal];
    $bulanRange = array_slice($months, $awal - 1, $akhir - $awal + 1, true);
    $bulan = $months[$model->bulan_awal] . ' s/d ' . $months[$model->bulan_akhir];
} elseif ($model->bulan_awal == $model->bulan_akhir) {
    $model->bulan = $model->bulan_awal;
    $bulanRange = [$model->bulan => $months[$model->bulan]];
    $bulan = $months[$model->bulan_awal];
}
$pivotData = $report['pivotData'];
$rowField = $keys['rowField'];
$rows = array_unique(array_column($pivotData, $rowField));
$isMultiSum = isset($keys['multi']);
$issumfiled = isset($keys['sumField']);
echo '<h3 style="text-align:center">' . $keys['subTitle'] . ' Periode ' . $bulan . ' Tahun ' . $year . '</h3>';
?>
<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr class="table-primary">
            <th rowspan="<?= $isMultiSum ? 2 : 1 ?>"><?= Html::encode($rowLabel) ?></th>
            <?php if ($isMultiSum): ?>
                <?php foreach ($bulanRange as $bulanLabel): ?>
                    <th colspan="3" class="text-center"><?= Html::encode($bulanLabel) ?></th>
                <?php endforeach; ?>
                <th colspan="3" class="text-center">Jumlah</th>
            <?php else: ?>
                <?php foreach ($bulanRange as $bulanLabel): ?>
                    <th class="text-center"><?= Html::encode($bulanLabel) ?></th>
                <?php endforeach; ?>
                <th class="text-center">Jumlah</th>
            <?php endif; ?>
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
                    $totalHps = $totalNego = $totalEfisien = 0;
                    foreach ($bulanRange as $bulanNum => $bulanLabel) {
                        $hps = $row['hps_' . $bulanNum] ?? 0;
                        $nego = $row['hasilnego_' . $bulanNum] ?? 0;
                        $efisien = $row['efisien_' . $bulanNum] ?? 0;
                        $totalHps += $hps;
                        $totalNego += $nego;
                        $totalEfisien += $efisien;
                        echo '<td class="text-right">' . Yii::$app->formatter->asCurrency($hps) . '</td>';
                        echo '<td class="text-right">' . Yii::$app->formatter->asCurrency($nego) . '</td>';
                        echo '<td class="text-right">' . Yii::$app->formatter->asCurrency($efisien) . '</td>';
                    }
                    echo '<td class="text-right"><strong>' . Yii::$app->formatter->asCurrency($totalHps) . '</strong></td>';
                    echo '<td class="text-right"><strong>' . Yii::$app->formatter->asCurrency($totalNego) . '</strong></td>';
                    echo '<td class="text-right"><strong>' . Yii::$app->formatter->asCurrency($totalEfisien) . '</strong></td>';
                } else {
                    $total = 0;
                    foreach ($bulanRange as $bulanNum => $bulanLabel) {
                        $val = $row[$bulanNum] ?? 0;
                        $formatedval = !$issumfiled ? $val : Yii::$app->formatter->asCurrency($val);
                        echo '<td class="text-right">' . $formatedval . '</td>';
                    }
                    $totaldata = !$issumfiled ? $row['total_data'] : Yii::$app->formatter->asCurrency($row['total_data']);
                    echo '<td class="text-right"><strong>' . $totaldata . '</strong></td>';
                }
                ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <?php if ($isMultiSum): ?>
            <tr class="table-secondary">
                <th>Total</th>
                <?php
                $totalHps = $totalNego = $totalEfisien = [];
                foreach ($bulanRange as $bulanNum => $bulanLabel) {
                    $totalHps[$bulanNum] = $totalNego[$bulanNum] = $totalEfisien[$bulanNum] = 0;
                    foreach ($pivotData as $row) {
                        $totalHps[$bulanNum] += $row['hps_' . $bulanNum] ?? 0;
                        $totalNego[$bulanNum] += $row['hasilnego_' . $bulanNum] ?? 0;
                        $totalEfisien[$bulanNum] += $row['efisien_' . $bulanNum] ?? 0;
                    }
                    echo '<th class="text-right">' . Yii::$app->formatter->asCurrency($totalHps[$bulanNum]) . '</th>';
                    echo '<th class="text-right">' . Yii::$app->formatter->asCurrency($totalNego[$bulanNum]) . '</th>';
                    echo '<th class="text-right">' . Yii::$app->formatter->asCurrency($totalEfisien[$bulanNum]) . '</th>';
                }
                $grandHps = array_sum($totalHps);
                $grandNego = array_sum($totalNego);
                $grandEfisien = array_sum($totalEfisien);
                echo '<th class="text-right"><strong>' . Yii::$app->formatter->asCurrency($grandHps) . '</strong></th>';
                echo '<th class="text-right"><strong>' . Yii::$app->formatter->asCurrency($grandNego) . '</strong></th>';
                echo '<th class="text-right"><strong>' . Yii::$app->formatter->asCurrency($grandEfisien) . '</strong></th>';
                ?>
            </tr>
        <?php else: ?>
            <tr class="table-secondary">
                <th>Total</th>
                <?php
                $totalPerBulan = [];
                foreach ($bulanRange as $bulanNum => $bulanLabel) {
                    $totalPerBulan[$bulanNum] = 0;
                    foreach ($pivotData as $row) {
                        $totalPerBulan[$bulanNum] += $row[$bulanNum] ?? 0;
                    }
                    $totalbulan = !$issumfiled ? $totalPerBulan[$bulanNum] : Yii::$app->formatter->asCurrency($totalPerBulan[$bulanNum]);
                    echo '<th class="text-right">' . $totalbulan . '</th>';
                }
                $grandTotal = array_sum($totalPerBulan);
                $gt = !$issumfiled ? $grandTotal : Yii::$app->formatter->asCurrency($grandTotal);
                echo '<th class="text-right"><strong>' . $gt . '</strong></th>';
                ?>
            </tr>
        <?php endif; ?>
    </tfoot>
</table>