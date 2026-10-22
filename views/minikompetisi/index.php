<?php

use yii\helpers\Html;
use kartik\grid\GridView;

$this->title = 'Minikompetisi';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="minikompetisi-index card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">
            <?= Html::encode($this->title) ?>
        </h3>
        <div class="card-tools">
            <?= Html::a('<i class="fas fa-plus"></i> Tambah Minikompetisi', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'judul',
                'tanggal:date',
                [
                    'attribute' => 'metode',
                    'value' => function ($model) {
                                return $model->getMetodeText();
                            },
                    'filter' => [1 => 'Harga Terendah', 2 => 'Kualitas & Harga', 3 => 'Lumpsum'],
                ],
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                                return $model->getStatusText();
                            },
                    'filter' => [0 => 'Draft', 1 => 'Dipublikasikan / Berjalan', 2 => 'Selesai'],
                ],
                [
                    'label' => 'Form Trial',
                    'format' => 'raw',
                    'value' => function ($model) {
                        if ($model->metode == 2) {
                            return Html::a('<i class="fas fa-clipboard-check"></i> Kelola Quiz', ['trial-questions', 'id' => $model->id], [
                                'class' => 'btn btn-info btn-sm',
                                'title' => 'Kelola Pertanyaan Trial'
                            ]);
                        }
                        return '<span class="text-muted text-small">-</span>';
                    },
                    'filter' => false,
                ],

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update} {delete}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $model->id], [
                                'class' => 'btn btn-primary btn-sm',
                                'title' => 'Lihat Detail',
                                'data-toggle' => 'tooltip'
                            ]);
                        },
                        'update' => function ($url, $model, $key) {
                            return Html::a('<i class="fas fa-edit"></i>', ['update', 'id' => $model->id], [
                                'class' => 'btn btn-warning btn-sm',
                                'title' => 'Edit',
                                'data-toggle' => 'tooltip'
                            ]);
                        },
                        'delete' => function ($url, $model, $key) {
                            return Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $model->id], [
                                'class' => 'btn btn-danger btn-sm',
                                'title' => 'Hapus',
                                'data-toggle' => 'tooltip',
                                'data' => [
                                    'confirm' => 'Apakah Anda yakin ingin menghapus minikompetisi ini?',
                                    'method' => 'post',
                                ]
                            ]);
                        },
                    ],
                    'visibleButtons' => [
                        'view' => true,
                        'update' => true,
                        'delete' => true,
                    ],
                ],
            ],
            'responsive' => true,
            'hover' => true,
        ]); ?>
    </div>
</div>