<?php
use yii\widgets\DetailView;
?>
<div class="ijinusaha-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'penyedia_id',
            'instansi_pemberi:ntext',
            'nomor_ijinusaha:ntext',
            'tanggal_ijinusaha:ntext',
            'file_ijinusaha:ntext',
            'tanggal_berlaku_sampai:ntext',
            'kualifikasi:ntext',
            'klasifikasi:ntext',
            'created_at:ntext',
            'updated_at:ntext',
            'created_by',
            'updated_by',
            'tags:ntext',
            'is_active',
            'jenis_ijin:ntext',
        ],
    ]) ?>
</div>