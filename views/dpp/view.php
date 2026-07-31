<?php
use yii\bootstrap4\Tabs;
use yii\widgets\DetailView;
?>
<div class="row clear-fix"></div>
<?php
echo Tabs::widget([
    'items' => [
        [
            'label' => 'Detail DPP',
            'content' => DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'nomor_dpp',
                    'tanggal_dpp',
                    ['attribute' => 'bidang_bagian', 'value' => fn ($model) => $model->unit->unit ?? ''],
                    'status_review:boolean',
                    'is_approved:boolean',
                    'nomor_persetujuan',
                    'kode',
                    ['attribute' => 'jenis_dpp', 'value' => fn ($model) => \app\Models\Setting::findOne(['id'=>$model->jenis_dpp])->value ?? ''],
                    ['attribute' => 'pejabat_pengadaan', 'value' => fn ($model) => $model->pejabat?->nama ?? ''],
                    ['attribute' => 'admin_pengadaan', 'value' => fn ($model) => $model->staffadmin?->nama ?? ''],
                    'created_at',
                    'updated_at',
                    ['attribute' => 'created_by', 'value' => fn ($model) => $model->usercreated?->userpegawai?->nama ?? '-',],
                    ['attribute' => 'updated_by', 'value' => fn ($model) => $model->userupdated?->userpegawai?->nama ?? '-',],
                ],
            ]),
            'options' => ['id' => 'dppview' . $model->hash],
        ],
        [
            'label' => 'Paket Pengadaan',
            'content' => $this->render('/paketpengadaan/view', ['model' => $model->paketpengadaan]),
            'options' => ['id' => 'paketview' . $model->hash],
        ]
    ],
]);
?>