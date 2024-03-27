<?php
use yii\bootstrap4\Tabs;
use yii\grid\GridView;
use yii\helpers\{Html, Url};
$t = collect($templates)->map(function ($e) use ($kualifikasi) {
    foreach ($kualifikasi as $k) {
        if ($k->template == $e->id) {
            $filteredModels[] = $k;
        }
    }
    $content = '';
    if (!empty($filteredModels)) {
        $rr = json_decode($filteredModels[0]->details[0]->hasil, true);
        $col = array_keys($rr[0]);
        $content.=GridView::widget([
            'dataProvider' => new yii\data\ArrayDataProvider([
                'allModels' => $rr
            ]),
            'columns' =>$col
        ]);
    }
    return [
        'label' => $e->jenis_evaluasi,
        'content' => $content,
        'options' => ['id' => 'val' . $e->id . $e->template],
    ];
})->toArray();
echo GridView::widget([
    'id' => 'crud-datatable',
    'dataProvider' => new yii\data\ArrayDataProvider([
        'allModels' => $penawaran->unique('penyedia_id')->toArray(), 'pagination' => false
    ]),
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
    'items' => $t
]);
