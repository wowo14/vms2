<?php
// Surat Penugasan Pemilihan Penyedia
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
            <h4 style="text-align: center;">SURAT PENUGASAN PEMILIHAN PENYEDIA</h4>
            <h5 style="text-align: center;">NOMOR : <?= $data['nomor_tugas'] ?></h5>
    </td>
    </tr>
</table>
<br>
<table border='0' width="100%">
    <tr>
        <td width="33%" style="text-align:center">
        </td>
        <td width="34%" style="text-align:center">
        </td>
        <td width="33%">
            Kepada Yth, Sdr/i Pejabat Pengadaan:<br>
            <?=$data['pejabat']?>
            <br>
            Serta Admin Pengadaan<br>
            <?=$data['admin']?>
            <br>
            di
            <br>
            &nbsp;
            &nbsp;
            &nbsp;Tempat
            <br>
        </td>
    </tr>
</table>
<br>
Berdasarkan Surat Persetujuan Pengadaan Barang/Jasa :
<table width="100%">
    <tr>
        <td width="25%">Nomor </td>
        <td width="1%;">:</td>
        <td width="74%"><?=$data['nomorpersetujuan']?></td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>:</td>
        <td><?=Yii::$app->formatter->asDate($data['tanggalpersetujuan'], 'php:d F Y')?></td>
    </tr>
    <tr>
        <td>Perihal</td>
        <td>:</td>
        <td><?=$data['perihal']?></td>
    </tr>
</table>
<br>
Serta Dokumen Persiapan Pengadaan (DPP):
<table width="100%">
    <tr>
        <td width="25%">Nomor </td>
        <td width="1%;">:</td>
        <td width="74%"><?=$data['nomordpp']?></td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>:</td>
        <td><?=Yii::$app->formatter->asDate($data['tanggaldpp'], 'php:d F Y')?></td>
    </tr>
    <tr>
        <td>Dari Bidang/Bagian</td>
        <td>:</td>
        <td><?=$data['bidang']?></td>
    </tr>
    <tr>
        <td>Nama Paket/Jenis Kegiatan</td>
        <td>:</td>
        <td><?=$data['paketpengadaan']?></td>
    </tr>
</table>
<br>
<p>
    Untuk Melaksanakan Kegiatan Pemilihan penyedia barang/jasa sebagaimana pada isi pokok surat sesuai dengan prosedur dan ketentuan yang berlaku serta
    terlampir berkas dokumen persiapan pengadaan terkait pengadaan barang/jasa dimaksud.
</p>
<br>
<p>
    Demikian surat penugasan ini dikeluarakan untuk dapat dilaksanakan dengan sebaik-baiknya dan penuh rasa tanggung jawab.
</p>
<br>
<p>
    Atas perhatian dan kerjasamanya diucapkan terima kasih.
</p>
<table border='0' width="100%">
    <tr>
        <td width="33%" style="text-align:center">
        </td>
        <td width="34%" style="text-align:center">
        </td>
        <td width="33%" style="text-align:center">
            Gresik, <?=Yii::$app->formatter->asDate('now', 'php:l, d F Y');?><br>
            (Kepala Unit Pengadaan Barang/Jasa)<br>
            RSUD Ibnu Sina Kabupaten Gresik<br>
            <br>
            <br>
            <br>
            <br>
            <u>( <?=$data['kepalapengadaan']??' . . . . . . . . . . . '?> )</u><br>
            NIP. <?=$data['nipkepalapengadaan']??''?>
        </td>
    </tr>
</table>