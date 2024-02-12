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
                'attribute' => 'produk_id',
                'label'=>'Produk',
                'value'=> function ($d) {
                    return $d->produk->nama_produk??'';
                }
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'qty_usulan',
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'keterangan',
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'satuan',
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'harga_pasar',
                'format' => 'currency',
            ],
            [
                'attribute' => 'harga_total',
                'format' => 'html',
                'contentOptions' => ['class' => 'auto unitsum', 'style' => 'text-align:right;'],
                'pageSummary' => true,
                'pageSummaryOptions' => ['class' => 'auto unitsum', 'style' => 'text-align:right;'],
                'pageSummaryFunc' => function ($data) {
                    return Yii::$app->formatter->asCurrency(array_sum(($data)));
                },
            ],
            // 'sumber_informasi'
        ],
    ]);
    ?>
    <div style="font-size: 16px; text-align: right; font-weight: bold;">
    </div>
</div>