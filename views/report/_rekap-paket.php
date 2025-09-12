<?php

use kartik\grid\GridView;

$this->title = $title;
$columns = [
    ['class' => 'kartik\grid\SerialColumn', 'header' => 'No'],
    'nama_paket',
    'metode',
    'kategori',
    [
        'attribute' => 'pagu',
        'format' => ['decimal', 2],
        'hAlign' => 'right',
        'pageSummary' => true,
    ],
    [
        'attribute' => 'hps',
        'label' => 'HPS',
        'format' => ['decimal', 2],
        'hAlign' => 'right',
        'pageSummary' => true,
    ],
    [
        'attribute' => 'penawaran',
        'format' => ['decimal', 2],
        'hAlign' => 'right',
        'pageSummary' => true,
    ],
    [
        'attribute' => 'hasilnego',
        'label' => 'Hasil Nego',
        'format' => ['decimal', 2],
        'hAlign' => 'right',
        'pageSummary' => true,
    ],
    'pejabat',
    'admin',
    'ppkom',
    'bidang',
    'tahun',
    'bulan',
];

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => $columns,
    'responsive'   => true,
    'hover'        => true,
    'condensed'    => false,
    'bordered'     => true,
    'striped'      => false,
    'panel' => [
        'heading' => '<i class="fas fa-table"></i> Rekap Pengadaan',
        'type'    => GridView::TYPE_PRIMARY,
        // 'before'  => false,
        'after'   => false,
    ],
    'toolbar' => [
        '{export}',
        '{toggleData}',
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
    // 'export' => [
    //     'fontAwesome' => true,
    // ],
    'showPageSummary' => true,
]);
