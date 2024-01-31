<?php
use yii\widgets\DetailView;
?>
<div class="pegawai-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'nik',
            'nama',
            'alamat',
            'telp',
            [
                'attribute'=>'status',
                'value'=>fn($d)=>$d->status==1?'AKTIF':'TIDAK AKTIF',
            ],
            'id_user',
            'hak_akses',
            'username',
            'password',
        ],
    ]) ?>
</div>