<?php
use yii\widgets\DetailView;
?>
<div class="staff-ahli-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'penyedia_id',
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
            'created_by',
            'updated_by',
            'spesifikasi_pekerjaan:ntext',
        ],
    ]) ?>
</div>