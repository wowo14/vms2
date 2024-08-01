<?php
use yii\bootstrap4\Tabs;
use yii\grid\GridView;
use yii\helpers\{Html, Url};
echo GridView::widget([
    'id' => 'crud-datatable',
    'dataProvider' => new yii\data\ArrayDataProvider([
        'allModels' => $penawaran->unique('penyedia_id')->toArray(), 'pagination' => false
    ]),
    'summary' => false,
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
        ],
        [
            'attribute' => 'penyedia_id', 'format' => 'raw', 'header' => 'Penyedia',
            'value' => fn ($d) => Html::a($d->vendor->nama_perusahaan, Url::to('/penyedia/view?id=' . $d->penyedia_id)) ?? ''
        ],
        'tanggal_mendaftar',
        'ip_client',
        [
            'attribute' => 'nilai_penawaran',
            'value' => fn ($e) => Yii::$app->formatter->asCurrency($e->nilai_penawaran ?? 0),
        ],
        [
            'header' => 'Dokumen kualifikasi', 'format' => 'raw',
        ],
        [
            'attribute' => 'lampiran_penawaran',
            'header' => 'Penawaran', 'format' => 'raw',
            'value' => fn ($d) => Html::a('Detail', Url::to('/uploads/' . $d->lampiran_penawaran)) ?? ''
        ],
        [
            'attribute' => 'lampiran_penawaran',
            'header' => 'Administrasi Teknis', 'format' => 'raw',
            'value' => fn ($d) => Html::a('Detail', Url::to('/uploads/' . $d->lampiran_penawaran)) ?? ''
        ],
        [
            'attribute' => 'lampiran_penawaran_harga',
            'header' => 'Harga', 'format' => 'raw',
            'value' => fn ($d) => Html::a('Detail', Url::to('/uploads/' . $d->lampiran_penawaran_harga)) ?? ''
        ],
        'masa_berlaku',
    ]
]);
echo Tabs::widget([
    'items' => $tabs
]);
