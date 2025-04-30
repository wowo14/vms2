<?php
use yii\helpers\Html;
$this->title = 'Laporan Gabungan Tahun ' . Html::encode($year);
// $subtitle='';
if (!empty($filters)) {
    $subtitle = 'Filter berdasarkan ' ;//. implode(', ', array_map('ucwords', $filters));
    if (!empty($filterLabels)) {
        $subtitle .= implode(', ', $filterLabels);
    }
} else {
    $subtitle = 'Tanpa filter, menampilkan semua data';
}
// $this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-all">
    <!-- <h3><?= Html::encode($this->title) ?></h3> -->
    <h5><?= Html::encode($subtitle) ?></h5>
    <?php
    foreach ($reports as $key => $report):
        // $report = is_object($report) ? $report = $report->allModels : $report;
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
        <div style="page-break-before: always;"></div>
    <?php endforeach; ?>
</div>