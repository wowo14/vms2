<?php
use yii\widgets\DetailView;
?>
<div class="penyedia-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'npwp:ntext',
            'nama_perusahaan:ntext',
            'alamat_perusahaan:ntext',
            'nomor_telepon:ntext',
            'email_perusahaan:ntext',
            'tanggal_pendirian:ntext',
            'kategori_usaha:ntext',
            'akreditasi:ntext',
            'active',
            'propinsi:ntext',
            'kota:ntext',
            'kode_pos:ntext',
            'mobile_phone:ntext',
            'website:ntext',
            'is_cabang',
            'alamat_kantorpusat:ntext',
            'telepon_kantorpusat:ntext',
            'fax_kantorpusat:ntext',
            'email_kantorpusat:ntext',
            ['attribute' => 'created_by', 'value' => $model->usercreated->username],
            ['attribute'=>'updated_by', 'value' => $model->userupdated->username],
            'created_at:ntext',
            'updated_at:ntext',
        ],
    ]) ?>
</div>