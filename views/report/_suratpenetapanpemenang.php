<?php

use yii\helpers\Html;
?>
<table style="width: 100%; font-size: 14px; text-align: center; font-weight: bold;">
    <tr>
        <td style="width: 15%;">
            <?= Html::img(Yii::getAlias('@web/images/logogresik.png'), ['width' => '77px']) ?>
        </td>
        <td><?= $model::profile('dinas') ?> KABUPATEN GRESIK <br>
            <p><?= $model::profile('address') ?></p>
        </td>
        <td style="width: 15%;">
            <?= Html::img(Yii::getAlias('@web/images/logors.png'), ['width' => '77px']) ?>
        </td>
    </tr>
</table>
<div style="font-family: Arial, sans-serif; font-size: 12pt;">
    <div style="text-align:center; font-weight:bold;">
        PEMERINTAH KABUPATEN GRESIK<br>
        PANITIA PENGADAAN BARANG/JASA<br>
        RUMAH SAKIT UMUM DAERAH IBNU SINA<br>
    </div>
    <div style="text-align:center;">
        Jl. Dr. Wahidin Sudirohusodo No. 243 B Telp.031-3951239 Fax (031) 3955217<br>
        GRESIK 61161
    </div>

    <p>
        Nomor: <?= Html::encode($model->nomor_surat) ?><br>
        Sifat: Segera<br>
        Lampiran: 1 (satu) berkas<br>
        Perihal: Penetapan Pemenang<br><br>
        Gresik, <?= Yii::$app->formatter->asDate($model->tanggal_surat, 'php:d F Y') ?><br><br>
        Kepada Yth:<br>
        Kepala Bagian Perencanaan dan Pendidikan<br>
        Selaku PPK<br>
        <?= Html::encode($model->nama_paket) ?><br>
        di<br>Tempat
    </p>

    <p>
        Bersama ini kami beritahukan bahwa dalam rangka kegiatan pengadaan barang/jasa untuk pekerjaan:<br><br>
        <strong><?= Html::encode($model->nama_paket) ?></strong><br>
        Dengan metode: <strong><?= Html::encode($model->metode_pengadaan) ?></strong><br><br>
        Maka dengan ini, penyedia barang/jasa berikut ditetapkan sebagai <strong>Pemenang</strong>:
    </p>

    <table>
        <tr>
            <td>Nama Perusahaan</td>
            <td>: <?= Html::encode($model->pemenang_nama) ?></td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: <?= Html::encode($model->pemenang_alamat) ?></td>
        </tr>
        <tr>
            <td>NPWP</td>
            <td>: <?= Html::encode($model->pemenang_npwp) ?></td>
        </tr>
        <tr>
            <td>Harga Penawaran</td>
            <td>: Rp <?= number_format($model->harga_penawaran, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td>Harga Negosiasi</td>
            <td>: Rp <?= number_format($model->harga_negosiasi, 0, ',', '.') ?></td>
        </tr>
    </table>

    <p>
        Demikian kami sampaikan untuk proses lebih lanjut sesuai tugas dan wewenang Pejabat Pembuat Komitmen.
        Atas perhatian dan kerja samanya kami ucapkan terima kasih.
    </p>

    <br><br>
    Hormat Kami,<br>
    Pejabat Pengadaan Barang/Jasa<br>
    RSUD Ibnu Sina Kabupaten Gresik<br><br><br><br>

    <strong><?= Html::encode($model->pejabat_nama) ?></strong><br>
    NIP. <?= Html::encode($model->pejabat_nip) ?>
</div>