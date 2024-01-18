<?php
use app\widgets\FilePreview;
use yii\helpers\Html;
use yii\widgets\DetailView;
?>
<div class="row">
    <div class="col-md-6">
        <div class="paket-pengadaan-view">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'nomor:ntext',
                    'tanggal_paket:ntext',
                    'nama_paket:ntext',
                    'kode_program:ntext',
                    'kode_kegiatan:ntext',
                    'kode_rekening:ntext',
                    'ppkom:ntext',
                    'pagu',
                    'metode_pengadaan:ntext',
                    'created_by',
                    'tahun_anggaran',
                    'approval_by',
                ],
            ]) ?>
        </div>
    </div>
    <div class="col-md-6">
        File Preview:
        <?php
        if (!empty($model->attachments)) {
            collect($model->attachments)->map(function ($el) {
                $el->uri = str_replace('/uploads/', '', $el->uri);
                echo DetailView::widget([
                    'model' => $el,
                    'attributes' => [
                        [
                            'attribute' => 'name','format'=>'raw',
                            'value' => fn ($d) => Html::a($d->name, Yii::getAlias('@web/uploads/').$d->uri, ['target' => '_blank'])
                        ],
                        [
                            'attribute' => 'jenis_dokumen',
                            'value' => function ($model) {
                                return $model->jenisdokumen->value;
                            }
                        ],
                        [
                            'attribute' => 'uri',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return FilePreview::widget([
                                    'model' => $model,
                                    'attribute' => 'uri',
                                ]);
                            }
                        ]
                    ],
                ]);
                // return $el;
            });
        }
        ?>
    </div>
</div>