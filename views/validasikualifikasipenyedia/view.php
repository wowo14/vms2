<?php
use yii\grid\GridView;
use yii\widgets\DetailView;
?>
<div class="validasi-kualifikasi-penyedia-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'penyedia_id',
                'value' => fn ($d) => $d->vendor->nama_perusahaan,
            ],
            [
                'attribute' => 'paket_pengadaan_id',
                'value' => fn ($d) => $d->paketpengadaan->nomornamapaket,
            ],
            'keperluan:ntext',
            'is_active',
        ],
    ]) ?>
</div>
<div class="clear-fix"></div>
<?php
if ($model->details) :
    $ext=$this->context->actionExtractdetail($model);
    echo GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $ext['models'],
        ]),
        'columns' => $ext['columns'],
        'tableOptions' => ['class' => 'table responsive'],
        'summary' => false
    ]);
endif;
?>