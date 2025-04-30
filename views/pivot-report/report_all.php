<?php
use yii\helpers\Html;
$this->title = 'Laporan Gabungan Tahun ' . Html::encode($year);
if (!empty($filters)) {
    $subtitle = 'Filter berdasarkan ';
    if (!empty($filterLabels)) {
        $subtitle .= implode(', ', $filterLabels);
    }
} else {
    $subtitle = 'Tanpa filter, menampilkan semua data';
}
?>
<div class="report-all">
    <!-- <h3><?= Html::encode($this->title) ?></h3> -->
    <h5><?= Html::encode($subtitle) ?></h5>
    <?php
    $keys = array_keys($reports);
    $lastKey = end($keys);
    foreach ($reports as $key => $report):
    ?>
        <div class="card mb-1 border-primary">
            <div class="card-header bg-primary text-white">
                <strong><?= Html::encode($configs[$key]['title']) ?></strong>
            </div>
            <div class="card-body p-0 ">
                <?php
                echo $this->render('_pivot_table', [
                    'report' => $report,
                    'months' => $months,
                    'rowLabel' => $configs[$key]['rowLabel'],
                    'keys' => $configs[$key],
                    'year' => $year,
                    'model' => $model,
                    'filters' => $filters
                ])
                ?>
            </div>
        </div>
    <?php
        if ($key !== $lastKey):
            echo '<div style="page-break-after: always;"></div>';
        endif;
    endforeach; ?>
</div>