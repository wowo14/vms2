<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap4\Tabs;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ArrayDataProvider */
/* @var $filters array */
/* @var $filterLabels array */
/* @var $year string */
/* @var $reports array */
/* @var $configs array */
/* @var $months array */
/* @var $model app\models\ReportModel */

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

<div class="report-reject">
    <div class="callout callout-info">
        <h5><?= Html::encode($this->title) ?></h5>
        <p><?= Html::encode($subtitle) ?></p>
    </div>

    <?php
    // Tab 1: Grid Detail
    $tabGrid = GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => null, // ArrayDataProvider with raw array doesn't support filterModel easily without a search model
        'pjax' => true,
        'responsive' => true,
        'hover' => true,
        'panel' => [
            'type' => GridView::TYPE_DANGER,
            'heading' => '<i class="fas fa-exclamation-circle"></i> Daftar Paket Status Reject / Perlu Perbaikan',
        ],
        'export' => [
            'fontAwesome' => true
        ],
        'columns' => [
            [
                'class' => '\kartik\grid\ExpandRowColumn',
                'width' => '50px',
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detail' => function ($model, $key, $index, $column) {
                    return $this->render('_reject_detail', ['model' => $model]);
                },
                'headerOptions' => ['class' => 'kartik-sheet-style'],
                'expandOneOnly' => true
            ],
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'nama_paket',
                'label' => 'Nama Paket',
                'contentOptions' => ['style' => 'white-space: pre-wrap; max-width: 300px;'],
            ],
            [
                'attribute' => 'pagu',
                'label' => 'Pagu',
                'format' => 'currency',
                'hAlign' => 'right'
            ],
            [
                'attribute' => 'pejabat_pengadaan',
                'label' => 'Pejabat Pengadaan',
            ],
            [
                'attribute' => 'tanggal_reject',
                'label' => 'Tgl Reject Terakhir',
                'format' => ['date', 'php:d/m/Y H:i'],
            ],
            [
                'attribute' => 'alasan_reject',
                'label' => 'Alasan Reject Terakhir',
                'format' => 'raw',
                'value' => function($model){
                     return nl2br(Html::encode($model['alasan_reject']));
                },
                'contentOptions' => ['style' => 'max-width: 300px; overflow-wrap: break-word;'],
            ],
        ],
    ]);

    // Tab 2: Statistics (Pivot)
    ob_start();
    ?>
    <div class="mt-3">
        <?php
        $keys = array_keys($reports);
        $lastKey = end($keys);
        foreach ($reports as $key => $report):
        ?>
            <div class="card mb-3 border-secondary">
                <div class="card-header bg-secondary text-white">
                    <strong><?= Html::encode($configs[$key]['title']) ?></strong>
                </div>
                <div class="card-body p-0">
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
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    $tabStats = ob_get_clean();

    echo Tabs::widget([
        'items' => [
            [
                'label' => 'Daftar Paket & Riwayat Detail',
                'content' => $tabGrid,
                'active' => true,
                'options' => ['class' => 'p-3'], // Padding for content
            ],
            [
                'label' => 'Statistik / Rekapitulasi',
                'content' => $tabStats,
                'options' => ['class' => 'p-3'],
            ],
        ],
    ]);
    ?>
</div>
