<?php
use yii2ajaxcrud\ajaxcrud\CrudAsset;
use yii\bootstrap4\Modal;
use yii\grid\GridView;
use yii\helpers\{url,Html};
$this->title = 'Penawaran Pengadaan';
$this->params['breadcrumbs'][] = $this->title;
$mdl=$model[0];
CrudAsset::register($this);
?>
<div class="penawaran-pengadaan-index">
    <div id="ajaxCrudDatatable">
        <?= GridView::widget([
            'id' => 'crud-datatable',
            'dataProvider' =>new yii\data\ArrayDataProvider(['allModels' => $model->toArray(), 'pagination' => false]),
            'summary' => false,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                ],
                [
                    'attribute' => 'penyedia_id','format'=>'raw','header'=>'Penyedia/Negosiasi',
                    'value' => fn ($d) => Html::a($d->vendor->nama_perusahaan,Url::to('/penawaranpenyedia/nego?id='.$d->id),[
                        'data-pjax' => 1,'role' => 'modal-remote','data-target'=>'#'.$d->hash
                    ])??''
                    // 'value' => fn ($d) => Html::a($d->vendor->nama_perusahaan,Url::to('/penyedia/view?id='.$d->penyedia_id))??''
                ],
                'tanggal_mendaftar',
                'ip_client',
                [
                    'header'=>'Dokumen kualifikasi','format'=>'raw',
                ],
                [
                    'attribute' => 'lampiran_penawaran',
                    'header'=>'Penawaran','format'=>'raw',
                    'value' => fn ($d) => Html::a('Detail',Url::to('/uploads/'.$d->lampiran_penawaran)) ?? ''
                ],
                [
                    'attribute' => 'lampiran_penawaran',
                    'header'=>'Administrasi Teknis','format'=>'raw',
                    'value' => fn ($d) => Html::a('Detail',Url::to('/uploads/'.$d->lampiran_penawaran)) ?? ''
                ],
                [
                    'attribute' => 'lampiran_penawaran_harga',
                    'header'=>'Harga', 'format' => 'raw',
                    'value' => fn ($d) => Html::a('Detail', Url::to('/uploads/' . $d->lampiran_penawaran_harga)) ?? ''
                ],
                'masa_berlaku',
            ]
        ]) ?>
    </div>
</div>
<?php Modal::begin([
    "id" => $mdl->hash,'size' => 'modal-xl',
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