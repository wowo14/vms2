<?php
use kartik\grid\GridView;
?>
<div class="riwayatnego">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'responsiveWrap' => false,
        'pjax' => true,
        'showPageSummary' => true,
        'tableOptions' => ['class' => 'new_expand'],
        'id' => 'details1',
        'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'header'=>'Nego Ke'
        ],
        [
            'attribute'=>'penawaran_id',
            'label'=>'Paket Pengadaan',
            'value'=>fn($d)=>$d->penawaran->paketpengadaan->nomor??''
        ],
        [
            'attribute'=>'penawaran_id',
            'label'=>'Penyedia',
            'value'=>fn($d)=>$d->penawaran->vendor->nama_perusahaan??''
        ],
        [
            'attribute'=>'ammount',
            'value'=>fn($d)=>\Yii::$app->formatter->asCurrency($d->ammount)
        ],
        'created_at',
        ['attribute'=>'accept','value'=>fn($d)=>$d->accept?'Ya':'Tidak'],
        [
            'attribute'=>'created_by',
            'value'=>fn($d)=>$d->usercreated->username
        ]
    ]
]);?>