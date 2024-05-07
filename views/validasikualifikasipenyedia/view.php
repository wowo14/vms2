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
    $aa = json_decode($model->details[0]->hasil, true);
    $count = $total = 0;
    foreach ($aa as $c) {
        if (key_exists('sesuai', $c)) {
            if ($c['sesuai'] == 'ya') {
                $count++;
            }
        }
        if ($c) {
            $total++;
        }
    }
    $col = [];
    foreach (array_keys($aa[0]) as $item) {
        $trimmedKey = ucfirst(trim($item));
        $title = ($trimmedKey === 'Sesuai') ? 'Sesuai(ya/tidak)' : (($trimmedKey === 'Skala') ? 'Skala(1-5)' : ucfirst($trimmedKey));
        $col[] = [
            'attribute' => trim($item),
            'header' => $title,
        ];
    }
    echo GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $aa,
        ]),
        'columns' => $col,
        'tableOptions' => ['class' => 'table responsive'],
    ]);
endif;
?>