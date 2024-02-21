<?php
use yii\widgets\DetailView;
?>
<div class="peralatankerja-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'penyedia_id',
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
            'created_by',
            'updated_by',
            'file:ntext',
        ],
    ]) ?>
</div>