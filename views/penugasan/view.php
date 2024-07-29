<?php
use yii\widgets\DetailView;
?>
<div class="penugasan-pemilihanpenyedia-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'dpp.nomor_dpp',
            'nomor_tugas',
            'tanggal_tugas',
            ['attribute' => 'pejabat','value' => fn($d)=> $d->pejabatpengadaan->nama ?? ''],
            ['attribute' => 'admin','value' => fn($d)=> $d->staffadmin->nama ?? ''],
            'created_at',
            'updated_at',
            ['attribute' => 'created_by','value' => fn($d)=> $d->usercreated->username ?? ''],
            ['attribute' => 'updated_by','value' => fn($d)=> $d->userupdated->username ?? ''],
        ],
    ]) ?>
</div>