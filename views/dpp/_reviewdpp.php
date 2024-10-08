<?php
use yii\helpers\Html;
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
<h5 style="text-align: center"><b>REVIEW DOKUMEN PERSIAPAN PENGADAAN OLEH PEJABAT PENGADAAN</b></h5>
<table width="100%">
    <tr>
        <td width="30%">Bidang/Bagian</td>
        <td width="1%;">:</td>
        <td width="69%"><?= $model->unit->unit ?? '' ?></td>
    </tr>
    <tr>
        <td>Nama Paket / Jenis Kegiatan</td>
        <td>:</td>
        <td><?= $model->paketpengadaan->nama_paket ?></td>
    </tr>
</table>
<table width="100%" class="border1solid table">
    <tr>
        <td rowspan="2" class="center border1solid">No</td>
        <td rowspan="2" class="center border1solid">URAIAN REVIEW</td>
        <td colspan="2" class="center border1solid">Sesuai?</td>
        <td rowspan="2" class="center border1solid">Keterangan</td>
    </tr>
    <tr>
        <td class="center border1solid">Ya</td>
        <td class="center border1solid">Tidak</td>
    </tr>
    <?php $i = 1;
    $uraian = json_decode($template->uraian, true);
    foreach ($uraian as $v) {
        echo "<tr>
        <td width=\"1%\" class=\"center border1solid\">$i</td>
        <td width=\"50%\" class=\"border1solid\">" . $v['uraian'] . "</td>
        <td width=\"12%\" class=\"center border1solid\">" . (isset($v['sesuai']) ? (($v['sesuai'] == 1 || $v['sesuai'] == 'on') ? 'v' : '') : '') . "</td>
        <td width=\"12%\" class=\"center border1solid\">" . (!isset($v['sesuai']) ? 'v' : '') . "</td>
        <td width=\"24%\" class=\"center border1solid\">" . $v['keterangan'] . "</td>
        </tr>";
        $i++;
    }
    ?>
</table>
Review Oleh Pejabat Pengadaan:<br>
<ol>
    <li><?= $template->keterangan ?? '' ?></li>
</ol>
Kesimpulan:<br>
<ol>
    <li><?= $template->kesimpulan ?? '' ?></li>
</ol>
<table width="100%">
    <tr>
        <td width="33%"></td>
        <td width="33%"></td>
        <td style="text-align: center;" width="33%">Gresik, <?= Yii::$app->formatter->asDate(($model->created_at??date('Y-m-d')),'php:d F Y') ?><br>
            (Pejabat Pengadaan Barang/Jasa)
            <br>
            <br>
            <br>
            <br>
            <span style="text-align:center"><?= $model->pejabat->nama??'' ?></span><br>
            NIP. <?= $model->pejabat->nip??'' ?>
        </td>
    </tr>
</table>
Tanggapan PPK atas dikembalikan DPP :<br>
<ol>
    <li><?= $template->tanggapan_ppk ?? '' ?></li>
</ol>
<table width="100%">
    <tr>
        <td width="50%" style="text-align:center">Yang Menerima,<br>
            (Pejabat Pengadaan Barang/Jasa)<br>
            <br>
            <br>
            <br>
            <br>
            <u>( <?= $model->pejabat->nama??'' ?> )</u><br>
            NIP. <?= $model->pejabat->nip??'' ?>
        </td>
        <td width="50%" style="text-align:center">Gresik, <?= ($template->tgl_dikembalikan!=="" || $template->tgl_dikembalikan!==null) ? Yii::$app->formatter->asDate(($template->tgl_dikembalikan??date('Y-m-d')),'php:d F Y') : ' ...... ' ?><br>
            (Pejabat Pembuat Komitmen)<br>
            <br>
            <br>
            <br>
            <br>
            <u>( <?= $model->paketpengadaan->pejabatppkom->nama??'' ?> )</u><br>
            NIP. <?= $model->paketpengadaan->pejabatppkom->nip??'' ?>
        </td>
    </tr>
</table>