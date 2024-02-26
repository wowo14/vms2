<?php
use yii\widgets\DetailView;
?>
<div class="pengalaman-penyedia-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'penyedia_id',
            'paket_pengadaan_id:ntext',
            'link:ntext',
            'pekerjaan:ntext',
            'lokasi:ntext',
            'instansi_pemberi_tugas:ntext',
            'alamat_instansi:ntext',
            'tanggal_kontrak:ntext',
            'tanggal_selesai_kontrak:ntext',
            'created_at:ntext',
            'updated_at:ntext',
            ['attribute' => 'created_by', 'value' => $model->usercreated->username ?? ''],
            ['attribute' => 'updated_by', 'value' => $model->userupdated->username ?? ''],
            'nilai_kontrak',
            'file:ntext',
        ],
    ]) ?>
</div>