<?php
use kartik\grid\GridView;
use yii\helpers\{Html, Url};
$this->title = 'Details';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="draftrabdetails-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'responsiveWrap' => false,
        'pjax' => true,
        'showPageSummary' => true,
        'tableOptions' => ['class' => 'new_expand'],
        'id' => $iddetail . md5($dataProvider->query->modelClass),
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'produk_id',
                'label' => 'Produk',
                'value' => 'produk.nama_produk'
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'volume',
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'satuan',
            ],
            [
                'attribute' => 'harga_satuan',
                'contentOptions' => ['class' => 'auto unitsum', 'style' => 'text-align:right;'],
                'value' => function ($data) {
                    return Yii::$app->formatter->asCurrency($data->harga_satuan);
                }
            ],
            [
                'attribute' => 'subtotal',
                'label'=>'Sub Total',
                'format' => 'html',
                'contentOptions' => ['class' => 'auto unitsum', 'style' => 'text-align:right;'],
                'value' => function ($data) {
                    return $data->subtotal;
                },
                'pageSummary' => true,
                'pageSummaryOptions' => ['class' => 'auto unitsum', 'style' => 'text-align:right;'],
                'pageSummaryFunc' => function ($data) {
                    return Yii::$app->formatter->asCurrency(array_sum(($data)));
                },
            ]
        ],
    ]);
    ?>
    <div style="font-size: 16px; text-align: right; font-weight: bold;">
    </div>
</div>