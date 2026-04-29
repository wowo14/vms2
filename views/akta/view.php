<?php
use yii\helpers\{Html,Url};
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
            ['attribute'=>'file_akta','format'=>'raw',
             'value' => fn($model) => $model->file_akta
                ? Html::a(
                    $model->file_akta,
                    Url::to('@web/uploads/' . $model->file_akta),
                    ['target' => '_blank']
                )
                : '-',
            ],
            'created_at:ntext',
            'updated_at:ntext',
            ['attribute' => 'created_by', 'value' => $model->usercreated?->userpegawai?->nama ?? '-',],
            ['attribute' => 'updated_by', 'value' => $model->userupdated?->userpegawai?->nama ?? '-',],
        ],
    ]) ?>
</div>