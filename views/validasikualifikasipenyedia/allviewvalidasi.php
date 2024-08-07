<?php
use yii2ajaxcrud\ajaxcrud\CrudAsset;
use yii\bootstrap4\{Modal,Tabs};
use yii\grid\GridView;
use yii\helpers\{Html, Url};
CrudAsset::register($this);
$this->title = 'Validasi kualifikasi Penyedia';
$this->params['breadcrumbs'][] = ['label' => 'Proses Dpp', 'url' => ['/dpp/tab?id='.$kualifikasi[0]->paketpengadaan->dpp->id]];
$this->params['breadcrumbs'][] = $this->title;
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
            'value' => fn ($d) => Html::a($d->vendor->nama_perusahaan, Url::to('/penyedia/profile?id=' .$d->hashid($d->penyedia_id))) ?? ''
        ],
        'tanggal_mendaftar',
        'ip_client',
        [
            'attribute' => 'nilai_penawaran',
            'format'=>'raw',
            'value'=>fn ($e) => Html::a(Yii::$app->formatter->asCurrency($e->nilai_penawaran ?? 0),Url::to('/penawaranpenyedia/nego?id='.$e->id),['role'=>'modal-remote','data-target'=>'#'.$e->hash,'data-pjax'=>1]),
            // 'value' => fn ($e) => Yii::$app->formatter->asCurrency($e->nilai_penawaran ?? 0),
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
]);?>
<?php Modal::begin([
    "id" => $penawaran[0]->hash,'size' => 'modal-xl',
    "footer" => "",
    "clientOptions" => [
        "tabindex" => false,
        "backdrop" => "static",
        "keyboard" => true,
    ],
    "options" => [
        "tabindex" => false
    ]
])?>
<?php Modal::end(); ?>