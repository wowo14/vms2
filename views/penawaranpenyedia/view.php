<?php
use app\assets\AppAsset;
use app\widgets\FilePreview;
use yii\bootstrap4\Tabs;
use yii\helpers\Html;
use yii\widgets\DetailView;
AppAsset::register($this);
?>
<div class="row">
    <div class="col-md-6 penawaran-pengadaan-view">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                ['attribute' => 'paket_id', 'value' => $model->paketPengadaan->nomornamapaket ?? ''],
                ['attribute' => 'penyedia_id', 'value' => $model->vendor->nama_perusahaan ?? ''],
                'nomor',
                'kode',
                'tanggal_mendaftar',
                'ip_client',
                'masa_berlaku',
                'lampiran_penawaran:ntext',
                'lampiran_penawaran_harga:ntext',
                'penilaian',
                ['attribute' => 'created_by', 'value' => $model->usercreated->username ?? ''],
                'created_at',
                ['attribute' => 'updated_by', 'value' => $model->usercreated->username ?? ''],
                'updated_at',
            ],
        ]) ?>
    </div>
    <div class="clear-fix"></div>
    <div class="col-md-6">
        <?php
        echo Tabs::widget([
            'items' => [
                [
                    'label' => 'Lampiran Penawaran',
                    'content' => $model->lampiran_penawaran ? Html::a(
                        FilePreview::widget([
                            'model' => $model,
                            'attribute' => 'lampiran_penawaran',
                        ]),
                        Yii::getAlias('@web/uploads/') . $model->lampiran_penawaran,
                        ['target' => '_blank']
                    ) : '',
                    'options' => ['id' => 'filelampiranpenawaran' . $model->hash],
                ],
                [
                    'label' => 'Lampiran Penawaran Harga',
                    'content' => $model->lampiran_penawaran_harga ? Html::a(
                        FilePreview::widget([
                            'model' => $model,
                            'attribute' => 'lampiran_penawaran_harga',
                        ]),
                        Yii::getAlias('@web/uploads/') . $model->lampiran_penawaran_harga,
                        ['target' => '_blank']
                    ) : '',
                    'options' => ['id' => 'filelampiranpenawaranharga' . $model->hash],
                ],
            ]
        ]);
        ?>
    </div>
</div>