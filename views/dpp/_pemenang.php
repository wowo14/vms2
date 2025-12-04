<?php
use kartik\grid\GridView;
use yii\helpers\Html;
echo GridView::widget([
    'id' => 'crud-datatable',
    'dataProvider' => new yii\data\ArrayDataProvider([
        'allModels' => $model, 'pagination' => false,
    ]),
    'summary' => false,
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
        ],
        ['attribute' => 'penyedia_id', 'header' => 'Penyedia','value' => fn ($model) => $model->vendor->nama_perusahaan ?? ''],
        [
            'attribute' => 'nilai_penawaran', 'format' => 'html',
            'value' => function ($model, $key) {
                $encoded=\Yii::$app->hashids->encode($model->paketpengadaan->id);
                return ($key == 0) ? \Yii::$app->formatter->asCurrency($model->nilai_penawaran) .
                    ' ' . Html::tag('i', ' ', ['class' => 'fa fa-star', 'style' => 'color:gold']).
                    ' '.(!$model->paketpengadaan->pemenang?Html::a('Tetapkan', ['/dpp/pemenang?idvendor=' . $model->vendor->id. '&idpaket=' . $model->paket_id], ['class' => 'btn btn-primary']):
                    ' '.Html::a('Batalkan', ['/paketpengadaan/batalkanpemenang?id=' . $encoded], ['class' => 'btn btn-primary']))
                    : \Yii::$app->formatter->asCurrency($model->nilai_penawaran)
                    ;
            }
        ],
    ],
]);
?>
<legend>
    <span><i class="fa fa-star" style="color:gold"></i> Pemenang</span>
</legend>