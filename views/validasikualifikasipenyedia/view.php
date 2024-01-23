<?php
use yii\widgets\DetailView;
?>
<div class="validasi-kualifikasi-penyedia-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'penyedia_id',
            'paket_pengadaan_id:ntext',
            'keperluan:ntext',
            'is_active',
            'created_at:ntext',
            'updated_at:ntext',
            'created_by',
            'updated_by',
        ],
    ]) ?>
</div>