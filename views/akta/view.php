<?php
use yii\widgets\DetailView;
?>
<div class="akta-penyedia-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'penyedia_id',
            'jenis_akta:ntext',
            'nomor_akta:ntext',
            'tanggal_akta:ntext',
            'notaris:ntext',
            'file_akta:ntext',
            'created_at:ntext',
            'updated_at:ntext',
            'created_by',
            'updated_by',
        ],
    ]) ?>
</div>