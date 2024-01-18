<?php
use yii\helpers\Html;
?>
<table style="width: 100%; font-size: 14px; text-align: center; font-weight: bold;">
    <tr>
        <td style="width: 15%;">
            <?= Html::img('/images/logogresik.png', ['width' => '77px']) ?>
        </td>
        <td><?= $model::profile('dinas') ?> KABUPATEN GRESIK <br>
            <p><?= $model::profile('address') ?></p>
        </td>
        <td style="width: 15%;">
            <?= Html::img('/images/logors.png', ['width' => '77px']) ?>
        </td>
    </tr>
</table>
<hr>
<h5>DOKUMEN PERSIAPAN PENGADAAN (DPP)<br><?= strtoupper($model->paketpengadaan->nama_paket) ?><br>NOMOR : <?= $model->nomor ?></h5>
Yang bertanda tangan dibawah ini :
<table width="100%">
    <tr>
        <td width="20%">Nama</td>
        <td width="1%;">:</td>
        <td width="79%"><?= '' ?></td>
    </tr>
    <tr>
        <td>Jabatan</td>
        <td>:</td>
        <td><?= '' ?></td>
    </tr>
    <tr>
        <td>Selaku</td>
        <td>:</td>
        <td><?= '' ?></td>
    </tr>
    <tr>
        <td>e-mail</td>
        <td>:</td>
        <td><?= '' ?></td>
    </tr>
</table>
<p>
    Pada hari ini <?= date('d F Y') ?> menetapkan Dokumen Persiapan Pengadaan (DPP) untuk pengadaan Barang/Jasa sebagai berikut :
</p>
<table width="100%">
    <tr>
        <td width="1%">No.</td>
        <td width="50%">Nama Pekerjaan</td>
        <td width="25%">Jenis Pengadaan</td>
        <td width="24%">Nilai HPS</td>
    </tr>
    <tr>
        <td colspan="3">Total HPS</td>
        <td></td>
    </tr>
</table>
Adapun DPP terdiri dari :
<ol>
    <li>Harga Perkiraan Sendiri (HPS)</li>
    <li>Spesifikasi Barang</li>
    <li>Rancangan Kontrak</li>
</ol>
<table width="100%">
    <tr>
        <td width="33%"></td>
        <td width="33%"></td>
        <td width="33%">Ditetapkan di : <br>Gresik,<br>Tangggal : <?= date('d F Y') ?><br>
        Pejabat Pembuat Komitmen (PPKom) Pada Sub Sub Kegiatan <?=''?>
            <br>
            <br>
            <br>
            (........................................)<br>
            NIP. <?= '' ?>
        </td>
    </tr>
</table>