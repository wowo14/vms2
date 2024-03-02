<?php
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
<?php echo 'det'; //print_r($model->details[0]->hasil); die;
if ($model->details) :
    $aa= json_decode($model->details[0]->hasil,true);
    $col = [];
    foreach (array_keys($aa) as $item) {
        $trimmedKey = ucfirst(trim($item));
        $title = ($trimmedKey === 'Sesuai') ? 'Sesuai(ya/tidak)' : (($trimmedKey === 'Skala') ? 'Skala(1-5)' : ucfirst($trimmedKey));
        $col[] = [
            'attribute' => trim($item),
            'header' => $title,
        ];
    }
    echo $aa['uraian'];
?>

<?php
endif;
?>