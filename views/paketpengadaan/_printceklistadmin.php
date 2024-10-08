<?php
use yii\grid\GridView;
use yii\helpers\{Html};
?>
<table style="width: 100%; font-size: 14px; text-align: center; font-weight: bold;">
    <tr>
        <td style="width: 15%;">
            <?= Html::img($data['logors'], ['width' => '77px']) ?>
        </td>
        <td><?= $model::profile('dinas') ?> KABUPATEN GRESIK <br>
            <p><?= $model::profile('address') ?></p>
        </td>
        <td style="width: 20%;text-align:right">
            <?php  echo 'FM-437.76.92.09<br>Revisi : 00';
            //Html::img($data['logors'], ['width' => '77px']) ?>
        </td>
    </tr>
</table>
<hr>
<table border='0' width="100%">
    <tr>
        <td style="text-align:center">
            <h4 style="text-align: center;">CHECK LIST DOKUMEN KELENGKAPAN DPP</h4>
    </td>
    </tr>
</table>
<br>
<table width="100%">
    <tr>
        <td width="35%">Bidang / Bagian</td>
        <td width="1%;">:</td>
        <td width="64%"><?=$data['unit']?></td>
    </tr>
    <tr>
        <td>Nama Paket / Jenis Kegiatan</td>
        <td>:</td>
        <td><?=$data['paket']?></td>
    </tr>
</table>
<?= GridView::widget([
            'id' => 'preview-details',
            'dataProvider' => new yii\data\ArrayDataProvider([
                'allModels' => $data['details'],
                'pagination' => false,
            ]),
            'summary' => false,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                ],
                ['attribute'=>'uraian','format'=>'raw',
                'value'=>function($model){
                    return nl2br($model['uraian']);
                },
                'headerOptions' => ['class'=>'text-center'],],
                ['attribute'=>'sesuai',
                'headerOptions' => ['class'=>'text-center'],
                'contentOptions' => ['class'=>'text-center'],
                'label'=>'Sesuai (Ya/Tidak)'],
                ['attribute'=>'keterangan','headerOptions' => ['class'=>'text-center'],],
            ]
    ]);
?>
CATATAN:<br>
*) Paket akan diproses setelah berkas lengkap
<br>
<table border='0' width="100%">
    <tr>
        <td width="33%" style="text-align:center">
            Mengetahui,<br>
            (Kepala Unit Pengadaan Barang/Jasa)<br>
            <br>
            <br>
            <br>
            <br>
            <u>( <?=$data['kepalapengadaan']??' . . . . . . . . . . . '?> )</u><br>
            NIP. <?=$data['nipkepalapengadaan']??''?>
        </td>
        <td width="34%" style="text-align:center">
            Yang Menerima,<br>
            (Admin Pengadaan Barang/Jasa)<br>
            <br>
            <br>
            <br>
            <br>
            <u>( <?=$data['admin']??' . . . . . . . . . . . '?> )</u><br>
            NIP. <?=$data['nipadmin']??''?>
        </td>
        <td width="33%" style="text-align:center">
            Gresik, <?=Yii::$app->formatter->asDate(($model->tanggal_paket??date('Y-m-d')),'php:l, d F Y')?>,<br>
            Yang Menyerahkan,<br>
            <br>
            <br>
            <br>
            <br>
            <u>( <?=$data['kurir']??'. . . . . . . . . . . '?> )</u><br>
            NIP. <?=$data['nipkurir']??''?>
        </td>
    </tr>
</table>