<?php
use yii\widgets\DetailView;
?>
<div class="akta-penyedia-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            ['attribute' => 'penyedia_id', 'value' => $model->vendor->nama_perusahaan??''],
            'jenis_akta:ntext',
            'nomor_akta:ntext',
            'tanggal_akta:ntext',
            'notaris:ntext',
            'file_akta:ntext',
            'created_at:ntext',
            'updated_at:ntext',
            ['attribute' => 'created_by', 'value' => $model->usercreated->username??''],
            ['attribute' => 'updated_by', 'value' => $model->userupdated->username??''],
        ],
    ]) ?>
</div>