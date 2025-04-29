<?php
use yii\helpers\Html;
?>
<style>
    h2 {
        margin-bottom: 5px;
        font-size: 16pt;
        border-bottom: 1px solid #ccc;
        padding-bottom: 5px;
    }
    table.report-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }
    table.report-table th,
    table.report-table td {
        border: 1px solid #999;
        padding: 6px;
        text-align: center;
        font-size: 10pt;
    }
    .report-section {
        page-break-after: always;
    }
    .report-section:last-child {
        page-break-after: avoid;
    }
</style>
<h1 style="text-align:center">Laporan Rekapitulasi Pengadaan Tahun <?= Html::encode($year) ?></h1>
<?php foreach ($reports as $key => $report): ?>
    <div class="report-section">
        <h2><?= Html::encode($configs[$key]['title']) ?></h2>
        <table class="report-table">
            <thead>
                <tr>
                    <th><?= Html::encode($configs[$key]['rowLabel']) ?></th>
                    <?php foreach ($months as $month): ?>
                        <th><?= Html::encode($month) ?></th>
                    <?php endforeach; ?>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($report as $rowName => $rowData): ?>
                    <tr>
                        <td><?= Html::encode($rowName) ?></td>
                        <?php
                        $total = 0;
                        foreach ($months as $month) {
                            $value = isset($rowData[$month]) ? $rowData[$month] : 0;
                            $total += $value;
                            echo '<td>' . number_format($value, 0, ',', '.') . '</td>';
                        }
                        ?>
                        <td><strong><?= number_format($total, 0, ',', '.') ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endforeach; ?>