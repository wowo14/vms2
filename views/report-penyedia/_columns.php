<?php
use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'nama_penyedia',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'kota',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'telepon',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'jenis_pekerjaan',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'nama_paket',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'bidang',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'nilai_evaluasi',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'source',
        'value' => function ($model) {
            return ucfirst($model->source);
        },
        'filter' => ['system' => 'System', 'excel' => 'Excel'],
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign' => 'middle',
        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to([$action, 'id' => $key]);
        },
        'viewOptions' => ['role' => 'modal-remote', 'title' => 'View', 'data-toggle' => 'tooltip'],
        'template' => '{view}',
    ],
];
