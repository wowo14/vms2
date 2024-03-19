<?php
use app\models\PaketPengadaan;
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
                            'status_review',
                            'is_approved',
                            'nomor_persetujuan',
                            'kode',
                            'created_at',
                            'updated_at',
                            ['attribute' => 'created_by', 'value' => fn ($model) => $model->usercreated->username ?? ''],
                            ['attribute' => 'updated_by', 'value' => fn ($model) => $model->userupdated->username ?? ''],
                        ],
                    ]),
                    'options' => ['id' => 'dppview' . $model->hash],
                ],
                [
                    'label' => 'Paket Pengadaan',
                    'content' => $this->render('/paketpengadaan/view', ['model' => PaketPengadaan::findOne($model->paket_id)]),
                    'options' => ['id' => 'paketview' . $model->hash],
                ]
            ],
    ]);
    ?>
