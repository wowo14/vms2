<?php
use kartik\grid\GridView;
use yii\helpers\Html;
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
        [
            'attribute' => 'nilai_penawaran', 'format' => 'html',
            'value' => function ($model, $key) {
                return ($key == 0) ? \Yii::$app->formatter->asCurrency($model->nilai_penawaran) .
                    ' ' . Html::tag('i', ' ', ['class' => 'fa fa-star', 'style' => 'color:gold']).
                    ' '.(!$model->paketPengadaan->pemenang?Html::a('Tetapkan', ['/dpp/pemenang?idvendor=' . $model->vendor->id. '&idpaket=' . $model->paket_id], ['class' => 'btn btn-primary']):'')
                    : \Yii::$app->formatter->asCurrency($model->nilai_penawaran);
            }
        ],
    ],
]);
?>
<legend>
    <span><i class="fa fa-star" style="color:gold"></i> Pemenang</span>
</legend>