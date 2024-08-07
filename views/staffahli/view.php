<?php
use yii\widgets\DetailView;
?>
<div class="staff-ahli-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute'=>'penyedia_id', 'value' => $model->vendor->nama_perusahaan],
            'nama:ntext',
            'tanggal_lahir:ntext',
            'alamat:ntext',
            'email:ntext',
            'jenis_kelamin:ntext',
            'pendidikan:ntext',
            'warga_negara:ntext',
            'lama_pengalaman:ntext',
            'file:ntext',
            'keahlian:ntext',
            'created_at:ntext',
            'updated_at:ntext',
            ['attribute' => 'created_by', 'value' => $model->usercreated->username ?? ''],
            ['attribute' => 'updated_by', 'value' => $model->userupdated->username ?? ''],
            'spesifikasi_pekerjaan:ntext',
        ],
    ]) ?>
</div>