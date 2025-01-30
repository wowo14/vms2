<?php
use yii\widgets\DetailView;
?>
<div class="penilaian-penyedia-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'unit_kerja',
            'nama_perusahaan',
            'alamat_perusahaan',
            'paket_pekerjaan',
            'lokasi_pekerjaan',
            'nomor_kontrak',
            'jangka_waktu',
            'tanggal_kontrak',
            'metode_pemilihan',
            'details:ntext',
            'pengguna_anggaran',
            'pejabat_pembuat_komitmen',
            'nilai_kontrak',
            'dpp_id',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>
</div>