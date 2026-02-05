<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ReportPenyedia */
?>
<div class="report-penyedia-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'nama_penyedia',
            'alamat:ntext',
            'kota',
            'telepon',
            'produk_ditawarkan:ntext',
            'jenis_pekerjaan',
            'nama_paket',
            'bidang',
            'nilai_evaluasi',
            'source',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>