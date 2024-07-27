<?php
use yii\grid\GridView;
use yii\helpers\{Html};
?>
<table style="width: 100%; font-size: 14px; text-align: center; font-weight: bold;">
    <tr>
        <td style="width: 15%;">
            <?= Html::img($logogresik, ['width' => '77px']) ?>
        </td>
        <td><?= $model::profile('dinas') ?> KABUPATEN GRESIK <br>
            <p><?= $model::profile('address') ?></p>
        </td>
        <td style="width: 15%;">
            <?= Html::img($logors, ['width' => '77px']) ?>
        </td>
    </tr>
</table>
<hr>
<h5 style="text-align: center;">CHECK LIST DOKUMEN KELENGKAPAN DPP</h5>
<table width="100%">
    <tr>
        <td width="30%">Bidang / Bagian</td>
        <td width="1%;">:</td>
        <td width="69%"><?=$data['unit']?></td>
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
                'uraian',
                ['attribute'=>'sesuai','label'=>'Sesuai (Ya/Tidak)'],
                'keterangan'
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
            <u>(NAMA)</u><br>
            NIP.
        </td>
        <td width="34%" style="text-align:center">
            Yang Menerima,<br>
            (Admin Pengadaan Barang/Jasa)<br>
            <br>
            <br>
            <br>
            <u>( . . . . . . . . . . . )</u><br>
            NIP.
        </td>
        <td width="33%" style="text-align:center">
            Gresik, 10,<br>
            Yang Menyerahkan,<br>
            <br>
            <br>
            <br>
            <br>
            <u>( . . . . . . . . . . . )</u><br>
            NIP.
        </td>
    </tr>
</table>
