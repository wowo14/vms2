<?php
use yii\grid\GridView;
use yii\helpers\{Html,Url};
?>
<table class="">
    <tr>
        <td colspan="2">Lampiran Surat Penugasan Pemilihan Penyedia</td>
    </tr>
    <tr>
        <td>Nomor</td>
        <td>: <?= $paketpengadaan->nomor_persetujuan??'' ?></td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>: <?= $paketpengadaan->tanggal_persetujuan??'' ?></td>
    </tr>
    <tr>
        <td>Paket</td>
        <td>: <?= $paketpengadaan->nama_paket ?? '' ?></td>
    </tr>
</table>
<br>
<?php
if (!empty($paketpengadaan->attachments)) {
            $data=collect($paketpengadaan->attachments)->map(function ($el) {
                $el->uri = Url::home(true).''.
                str_replace('/uploads/', 'uploads/', $el->uri);
                return $el;
            });
            echo GridView::widget([
                'dataProvider' => new yii\data\ArrayDataProvider([
                    'allModels' =>$data->toArray(),
                    'pagination' => false
                ]),
                'summary' => false,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                    ],
                    [
                        'attribute' => 'name',
                        'label'=>'Nama File',
                    ],
                    [
                        'attribute' => 'jenis_dokumen',
                        'value'=>fn($r)=>$r->jenisdokumen->value??''
                    ],
                    'uri',
                ],
            ]);
        }