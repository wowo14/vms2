<?php
use yii\widgets\DetailView;
?>
<div class="penawaran-pengadaan-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'paket_id',
            'penyedia_id',
            'nomor',
            'kode',
            'tanggal_mendaftar',
            'ip_client',
            'masa_berlaku',
            'lampiran_penawaran:ntext',
            'lampiran_penawaran_harga:ntext',
            'penilaian',
            'created_by',
            'created_at',
            'updated_by',
            'updated_at',
        ],
    ]) ?>
</div>