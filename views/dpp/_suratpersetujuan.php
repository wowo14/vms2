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
<h5>SURAT PERSETUJUAN PENGADAAN BARANG DAN JASA<br>NOMOR : <?= $model->nomor_dpp ?></h5>
<table width="100%">
    <tr>
        <td width="20%">Dasar</td>
        <td width="1%;">:</td>
        <td width="79%"><?= '' ?></td>
    </tr>
    <tr>
        <td>Program</td>
        <td>:</td>
        <td><?= '' ?></td>
    </tr>
    <tr>
        <td>Kegiatan</td>
        <td>:</td>
        <td><?= '' ?></td>
    </tr>
    <tr>
        <td>Sub Kegiatan</td>
        <td>:</td>
        <td><?= '' ?></td>
    </tr>
    <tr>
        <td>Nomor</td>
        <td>:</td>
        <td><?= '' ?></td>
    </tr>
    <tr>
        <td>Tangal</td>
        <td>:</td>
        <td><?= '' ?></td>
    </tr>
</table>
Memerintahkan Kepada:<br>
Pejabat Pembuat Komitmen, untuk :<br>
<ol>
    <li>Melaksanakan proses pengadaan barang dan jasa, sesuai RAB tahun anggaran <?= '' ?>, berupa:<br>
        <table width="100%">
            <tr>
                <td width="1%">No.</td>
                <td width="60%">U R A I A N</td>
                <td width="19%">JUMLAH</td>
                <td width="20%">KETERANGAN</td>
            </tr>
            <?php ?>
        </table>
    </li>
    <li>Melaporkan hasil pelaksanaan pengadaan barang dan jasa kepada direktur selaku kuasa pengguna anggaran</li>
</ol>
<table width="100%">
    <tr>
        <td width="33%"></td>
        <td width="33%"></td>
        <td width="33%">Ditetapkan di Gresik, <?= date('d F Y') ?><br>
            Direktur RSUD Ibnu Sina Kab. Gresik<br>
            Selaku Kuasa Pengguna Anggaran
            <br>
            <br>
            <br>
            (........................................)<br>
            NIP. <?= '' ?>
        </td>
    </tr>
</table>