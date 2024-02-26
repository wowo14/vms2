<?php
use yii\widgets\DetailView;
?>
<div class="draft-rab-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'tahun_anggaran',
            'kode_program:ntext',
            'nama_program:ntext',
            'kode_kegiatan:ntext',
            'nama_kegiatan:ntext',
            'kode_rekening:ntext',
            'uraian_anggaran:ntext',
            'jumlah_anggaran',
            'sisa_anggaran',
            'sumber_dana:ntext',
            'created_at:ntext',
            'updated_at:ntext',
            ['attribute' => 'created_by', 'value' => $model->usercreated->username ?? ''],
            ['attribute' => 'updated_by', 'value' => $model->userupdated->username ?? ''],
            'is_completed',
        ],
    ]) ?>
</div>