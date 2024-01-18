<?php
use yii\widgets\DetailView;
?>
<div class="produk-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'kode_kbki:ntext',
            'nama_produk:ntext',
            'merk:ntext',
            'status_merk:ntext',
            'nama_pemilik_merk:ntext',
            'nomor_produk_penyedia:ntext',
            'unit_pengukuran:ntext',
            'jenis_produk:ntext',
            'nilai_tkdn:ntext',
            'nomor_sni:ntext',
            'garansi_produk:ntext',
            'spesifikasi_produk:ntext',
            'layanan_lain:ntext',
            'komponen_biaya:ntext',
            'lokasi_tempat_usaha:ntext',
            'keterangan_lainya:ntext',
            'active',
            'hargapasar',
            'hargabeli',
            'hargahps',
            'hargalainya',
            'barcode:ntext',
            'created_by',
            'updated_by',
            'created_at:ntext',
            'updated_at:ntext',
        ],
    ]) ?>
</div>