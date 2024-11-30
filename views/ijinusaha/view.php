<?php
use yii\helpers\Url;
use yii\widgets\DetailView;
?>
<div class="ijinusaha-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute'=>'penyedia_id','value' => $model->vendor->nama_perusahaan],
            'instansi_pemberi:ntext',
            'nomor_ijinusaha:ntext',
            'tanggal_ijinusaha:ntext',
            ['attribute' => 'file_ijinusaha', 'format' => 'raw', 'value' => fn($model) => "<a href='" . Url::to('@web/uploads/') . $model->file_ijinusaha . "' target='_blank'>$model->file_ijinusaha</a>"],
            'tanggal_berlaku_sampai:ntext',
            'kualifikasi:ntext',
            'klasifikasi:ntext',
            'created_at:ntext',
            'updated_at:ntext',
            ['attribute' => 'created_by', 'value' => $model->usercreated->username??''],
            ['attribute' => 'updated_by', 'value' => $model->userupdated->username??''],
            'tags:ntext',
            'is_active',
            'jenis_ijin:ntext',
        ],
    ]) ?>
</div>