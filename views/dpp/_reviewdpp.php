<?php
use yii\helpers\Html;
?>
<table style="width: 100%; font-size: 14px; text-align: center; font-weight: bold;">
    <tr>
        <td style="width: 15%;">
            <?= Html::img(Yii::getAlias('@web/images/logogresik.png'), ['width' => '77px']) ?>
        </td>
        <td><?=$model::profile('dinas')?> KABUPATEN GRESIK <br>
            <p><?=$model::profile('address')?></p>
        </td>
        <td style="width: 15%;">
            <?= Html::img(Yii::getAlias('@web/images/logors.png'), ['width' => '77px']) ?>
        </td>
    </tr>
</table>
<hr>
<h5>REVIEW DOKUMEN PERSIAPAN PENGADAAN OLEH PEJABAT PENGADAAN</h5>
<table width="100%">
    <tr>
        <td width="20%">Bidang/Bagian</td>
        <td width="1%;">:</td>
        <td width="79%"><?= '' ?></td>
    </tr>
    <tr>
        <td>Nama Paket / Jenis Kegiatan</td>
        <td>:</td>
        <td><?= '' ?></td>
    </tr>
</table>
<table width="100%">
    <tr>
        <td>No</td>
        <td>URAIAN REVIE</td>
        <td>YA</td>
        <td>Tidak</td>
        <td>Keterangan</td>
    </tr>
    <?php
    foreach(json_decode(Yii::getAlias('@app/uraianreviewdpp.json')) as $v){
        print_r($v);
    }
    ?>
</table>
Review Oleh Pejabat Pengadaan:<br>
<ol>
    <li>...</li>
    <li>...</li>
</ol>
Kesimpulan:<br>
<ol>
    <li>...</li>
    <li>...</li>
</ol>
<table width="100%">
    <tr>
        <td width="33%"></td>
        <td width="33%"></td>
        <td width="33%">Gresik, <?= date('d F Y') ?><br>
            (Pejabat Pengadaan Barang/Jasa)
            <br>
            <br>
            <br>
            (........................................)<br>
            NIP. <?= '' ?>
        </td>
    </tr>
</table>
Tanggapan PPK atas dikembalikan DPP :<br>
<ol>
    <li>...</li>
</ol>
<table width="100%">
    <tr>
        <td width="50%" style="text-align:center">Yang Menerima,<br>
            (Pejabat Pengadaan Barang/Jasa)<br>
            <br>
            <br>
            <br>
            (........................................)
        </td>
        <td width="50%" style="text-align:center">Gresik, .....<br>
            (Pejabat Pembuat Komitmen)<br>
            <br>
            <br>
            <br>
            (........................................)
        </td>
    </tr>
</table>