<?php
use yii\widgets\DetailView;
?>
<div class="validasi-kualifikasi-penyedia-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute'=>'penyedia_id',
                'value'=>fn($d)=>$d->penyedia->nama_perusahaan,
            ],
            [
                'attribute'=> 'paket_pengadaan_id',
                'value'=>fn($d)=>$d->paketpengadaan->nomornamapaket,
            ],
            'keperluan:ntext',
            'is_active',
        ],
    ]) ?>
</div>