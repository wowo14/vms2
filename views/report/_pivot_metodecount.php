<?php
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\PaketPengadaan;
use yii\data\ArrayDataProvider;
$this->title='Dokumen Persiapan Pengadaan ';
$pivot = [];
$months = [];
$bulans=(new PaketPengadaan)->months;
foreach ($data as $row) {
    $method = $row['metode_pengadaan'];
    $month = $row['month'];
    $months[$month] = $month;
    if (!isset($pivot[$method][$month])) {
        $pivot[$method][$month] = 0;
    }
    $pivot[$method][$month]++;
}
ksort($months);
$pivotRows = [];
foreach ($pivot as $method => $row) {
    $entry = ['metode_pengadaan' => $method];
    foreach ($months as $month) {
        $entry[$month] = $row[$month] ?? 0;
    }
    $pivotRows[] = $entry;
}
$dataProviderpivot = new ArrayDataProvider([
    'allModels' => $pivotRows,
    'pagination' => false,
]);
$models = $dataProviderpivot->allModels;
foreach ($models as &$row) {
    $row['total_row'] = 0;
    foreach ($months as $month) {
        $row['total_row'] += $row[$month] ?? 0;
    }
}
unset($row);
$dataProvider = new \yii\data\ArrayDataProvider([
    'allModels' => $models,
    'pagination' => false,
]);
$totals = [];
foreach ($months as $month) {
    $totals[$month] = array_sum(array_column($dataProvider->allModels, $month));
}
$columnDefinitions = array_merge(
    [
        [
            'attribute' => 'metode_pengadaan',
            'label' => 'Metode Pengadaan',
            'footer' => 'Total',
        ]
    ],
    array_map(function ($month) use ($dataProvider,$bulans) {
        $total = array_sum(ArrayHelper::getColumn($dataProvider->allModels, $month));
        return [
            'attribute' => $month,
            'label' => $bulans[$month],
            'format' => 'raw',
            'value' => function ($model) use ($month) {
                return $model[$month] ?? 0;
            },
            'footer' => $total,
        ];
    }, array_keys($months)),
    [
        [
            'attribute' => 'total_row',
            'label' => 'Jumlah',
            'value' => function ($model) {
                return $model['total_row'];
            },
            'footer' => array_sum(ArrayHelper::getColumn($dataProvider->allModels, 'total_row')),
        ]
    ]
);?>
<h4 style="text-align: center;">Dokumen Persiapan Pengadaan </h4>
<h4 style="text-align: center;">Kegiatan Pengadaan Per Metode Pengadaan Tahun <?= $data->pluck('year')->first() ?></h4>
<?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => $columnDefinitions,
    'showFooter' => true,
    'summary' => false,
    'toolbar' => [
        [
            'content' =>
                '{export}'
        ],
    ],
    'exportConfig' => [
        'html' => ['filename' => str_replace(' ', '', $this->title)],
        'csv' => ['filename' => str_replace(' ', '', $this->title)],
        'txt' => ['filename' => str_replace(' ', '', $this->title)],
        'xls' => ['filename' => str_replace(' ', '', $this->title)],
        'pdf' => [
            'filename' => str_replace(' ', '', $this->title) . '_' . time(),
            'config' => [
                'methods' => [
                    'SetTitle' => $this->title,
                    'SetSubject' => 'Rekap Pengadaan',
                    'SetHeader' => [$this->title . '||Generated On: ' . date("r")],
                    'SetFooter' => ['|Page {PAGENO}|'],
                    'SetAuthor' => Yii::$app->user->identity->username,
                    'SetCreator' => Yii::$app->user->identity->username,
                    'SetKeywords' => 'Rekap DPP, Paket Pengadaan, Pengadaan',
                ],
                'options' => [
                    'title' => str_replace(' ', '', $this->title),
                    'subject' => '',
                    'keywords' => ''
                ]
            ],
        ],
        'json' => ['filename' => str_replace(' ', '', $this->title)],
    ],
    'striped' => true,
    'condensed' => true,
    'responsive' => true,
    'panel' => [
        'type' => 'success',
        'heading'=>false,
        'footer'=>false,
    ],
]);
