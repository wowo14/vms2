<?php
use kartik\grid\GridView;
echo GridView::widget([
    'id' => 'crud-datatable',
    'dataProvider' => new yii\data\ArrayDataProvider([
        'allModels' => $model, 'pagination' => false,
        // 'showPageSummary' => false,
    ]),
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
        ],
        ['attribute' => 'penyedia_id', 'value' => fn ($model) => $model->vendor->nama_perusahaan ?? ''],
        'nilai_penawaran',
    ],
]);
