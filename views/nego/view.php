<?php
use yii\widgets\DetailView;
?>
<div class="negosiasi-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
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
                // 'value'=>fn($d)=>\Yii::$app->formatter->asCurrency($d->ammount)
            ],
            'accept:boolean',
            [
                'attribute'=>'created_by',
                'value'=>fn($d)=>$d->usercreated->username??''
            ],
            'created_at',
        ],
    ]) ?>
</div>