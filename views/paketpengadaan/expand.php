<?php
use kartik\grid\GridView;
use yii\helpers\{Html, Url};
$this->title = 'Details';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="details-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'responsiveWrap' => false,
        'pjax' => true,
        'showPageSummary' => true,
        'tableOptions' => ['class' => 'new_expand'],
        'id' => 'details1',
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'nama_produk',
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'qty',
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'volume',
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'satuan',
            ],
            // [
            //     'class' => '\kartik\grid\DataColumn',
            //     'attribute' => 'durasi',
            // ],
            // [
            //     'class' => '\kartik\grid\DataColumn',
            //     'attribute' => 'informasi_harga',
            //     'format'=>'currency',
            // ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'hps_satuan',
                'format'=>'currency',
            ],
            // 'sumber_informasi',
            [
                'attribute'=>'penawaran',
                'format'=>'currency'
            ],
            [
                'attribute'=>'negosiasi',
                'format'=>'currency'
            ],
            [
                'attribute'=>'totalhps',
                'format'=>'raw',
                'value'=>function($d){
                    return $d->qty*$d->volume*$d->hps_satuan;
                },
                'pageSummary' => true,
                'pageSummaryOptions' => ['class' => 'auto unitsum', 'style' => 'text-align:right;'],
                'pageSummaryFunc' => function ($data) {
                    return Yii::$app->formatter->asCurrency(array_sum(($data)));
                },
            ],
            [
                'attribute'=>'totalpenawaran',
                'format'=>'raw',
                'value'=>function($d){
                    return $d->qty*$d->volume*$d->penawaran;
                },
                'pageSummary' => true,
                'pageSummaryOptions' => ['class' => 'auto unitsum', 'style' => 'text-align:right;'],
                'pageSummaryFunc' => function ($data) {
                    return Yii::$app->formatter->asCurrency(array_sum(($data)));
                },
            ],
            [
                'attribute'=>'totalnegosiasi',
                'format'=>'raw',
                'value'=>function($d){
                    return $d->qty*$d->volume*$d->negosiasi;
                },
                'pageSummary' => true,
                'pageSummaryOptions' => ['class' => 'auto unitsum', 'style' => 'text-align:right;'],
                'pageSummaryFunc' => function ($data) {
                    return Yii::$app->formatter->asCurrency(array_sum(($data)));
                },
            ],
        ],
    ]);
    ?>
    <div style="font-size: 16px; text-align: right; font-weight: bold;">
    </div>
</div>