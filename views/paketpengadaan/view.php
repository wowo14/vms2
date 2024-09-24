<?php
use app\models\PaketPengadaanDetails;
use app\widgets\FilePreview;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\widgets\DetailView;
?>
<div class="row clear-fix">
    <div class="col-md-6">
        <div class="paket-pengadaan-view">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'nomor:ntext',
                    'tanggal_dpp:ntext',
                    'nomor_persetujuan:ntext',
                    'tanggal_persetujuan',
                    'nama_paket:ntext',
                    'tanggal_paket:ntext',
                    ['attribute' => 'kode_program', 'value' => $model->kode_program . ' || ' . $model->programnya->desc ?? ''],
                    ['attribute' => 'kode_kegiatan', 'value' => $model->kode_kegiatan . ' || ' . $model->kegiatannya->desc ?? ''],
                    ['attribute' => 'kode_rekening', 'value' => $model->kode_rekening . ' || ' . $model->rekeningnya->rekening ?? ''],
                    ['attribute' => 'ppkom', 'value' => $model->pejabatppkom->nama ?? ''],
                    'pagu:currency',
                    'metode_pengadaan:ntext',
                    'kategori_pengadaan:ntext',
                    ['attribute' => 'created_by', 'value' => $model->usercreated->username ?? ''],
                    'tahun_anggaran',
                    'approval_by',
                    'unitnya.unit',
                    'alasan_reject:ntext',
                    'tanggal_reject:ntext',
                ],
            ]) ?>
            <?php
            if(!empty($model->details)){
                $dataProviderdetails = new ActiveDataProvider([
                    'query' => PaketPengadaanDetails::find()->where(['paket_id' => $model->id]),
                ]);
                echo GridView::widget([
                'dataProvider' => $dataProviderdetails,
                'responsiveWrap' => false,
                'pjax' => true,
                'showPageSummary' => true,
                'tableOptions' => ['class' => 'new_expand'],
                'id' => 'details1',
                'columns' => require('_column_details.php'),
            ]);
            }
            ?>
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
                            'attribute' => 'name', 'format' => 'raw',
                            'value' => fn ($d) => Html::a($d->name, Yii::getAlias('@web/uploads/') . $d->uri, ['target' => '_blank'])
                        ],
                        [
                            'attribute' => 'jenis_dokumen',
                            'value' => function ($d) {
                                return $d->jenisdokumen->value;
                            }
                        ],
                        [
                            'attribute' => 'uri',
                            'format' => 'raw',
                            'value' => function ($d) {
                                return FilePreview::widget([
                                    'model' => $d,
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
    <div class="clear-fix"></div>
</div>