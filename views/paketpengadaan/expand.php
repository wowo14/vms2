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
                'attribute' => 'volume',
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'satuan',
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'durasi',
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'harga',
                'format'=>'currency',
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'informasi_harga',
                'format'=>'currency',
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'hps',
                'format'=>'currency',
            ],
            [
                'attribute' => 'jumlah',
                'format' => 'html',
                'contentOptions' => ['class' => 'auto unitsum', 'style' => 'text-align:right;'],
                'value' => function ($d) {
                    return $d->volume*$d->hps;
                },
                'pageSummary' => true,
                'pageSummaryOptions' => ['class' => 'auto unitsum', 'style' => 'text-align:right;'],
                'pageSummaryFunc' => function ($data) {
                    return Yii::$app->formatter->asCurrency(array_sum(($data)));
                },
            ],
            'sumber_informasi'
        ],
    ]);
    ?>
    <div style="font-size: 16px; text-align: right; font-weight: bold;">
    </div>
</div>