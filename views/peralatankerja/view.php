<?php
use yii\widgets\DetailView;
?>
<div class="peralatankerja-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute'=>'penyedia_id', 'value' => $model->vendor->nama_perusahaan],
            'nama_alat:ntext',
            'jumlah',
            'kapasitas:ntext',
            'merk_tipe:ntext',
            'tahun_pembuatan:ntext',
            'kondisi:ntext',
            'lokasi_sekarang:ntext',
            'status_kepemilikan:ntext',
            'bukti_kepemilikan:ntext',
            'created_at:ntext',
            'updated_at:ntext',
            ['attribute' => 'created_by', 'value' => $model->usercreated->username ?? ''],
            ['attribute' => 'updated_by', 'value' => $model->userupdated->username ?? ''],
            'file:ntext',
        ],
    ]) ?>
</div>