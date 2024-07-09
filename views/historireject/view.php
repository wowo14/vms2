<?php
use yii\widgets\DetailView;
?>
<div class="histori-reject-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'paket_id',
            'nomor',
            'nama_paket',
            'user_id',
            'alasan_reject:ntext',
            'tanggal_reject',
            'kesimpulan:ntext',
            'tanggal_dikembalikan',
            'tanggapan_ppk:ntext',
            'file_tanggapan',
            'created_at',
        ],
    ]) ?>
</div>