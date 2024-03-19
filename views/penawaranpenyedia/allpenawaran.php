<?php
use yii\grid\GridView;
use yii\helpers\{url,Html};
use yii2ajaxcrud\ajaxcrud\CrudAsset;
$this->title = 'Penawaran Pengadaan';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);
$this->registerJsFile('js/popper.min.js', ['depends' => '\yii\bootstrap4\BootstrapPluginAsset']);
//detail penawaran penyedia
?>
<div class="penawaran-pengadaan-index">
    <div id="ajaxCrudDatatable">
        <?= GridView::widget([
            'id' => 'crud-datatable',
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                ],
                [
                    'attribute' => 'penyedia_id','format'=>'raw','header'=>'Penyedia',
                    'value' => fn ($d) => Html::a($d->vendor->nama_perusahaan,Url::to('/penyedia/view?id='.$d->penyedia_id))??''
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