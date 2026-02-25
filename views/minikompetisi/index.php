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

                ['class' => 'yii\grid\ActionColumn'],
            ],
            'responsive' => true,
            'hover' => true,
        ]); ?>
    </div>
</div>