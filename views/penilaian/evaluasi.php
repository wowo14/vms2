<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Penyedia;
use yii2ajaxcrud\ajaxcrud\CrudAsset;
use app\assets\AppAsset;

$this->title = 'Evaluasi Kinerja Penyedia';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
AppAsset::register($this);

$months = [
    'all' => 'Semua Bulan',
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember'
];

$years = ['all' => 'Semua Tahun', '2024' => '2024', '2025' => '2025', '2026' => '2026'];
if (!$tahun)
    $tahun = 'all';
$vendors = Penyedia::find()->select(['nama_perusahaan', 'id'])->indexBy('id')->column();
$vendors = ['all' => 'Semua Penyedia'] + $vendors;

$sortOptions = [
    'rating_desc' => 'Rating Tertinggi',
    'rating_asc' => 'Rating Terendah',
    'count_desc' => 'Paket Terbanyak',
    'count_asc' => 'Paket Tersedikit',
    'nilai_desc' => 'Nilai Kontrak Terbesar',
    'nilai_asc' => 'Nilai Kontrak Terkecil',
];

?>

<div class="penilaian-evaluasi">
    <div class="card card-primary card-outline shadow-sm mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter mr-2"></i> Filter Evaluasi</h3>
        </div>
        <div class="card-body">
            <form method="get" action="<?= Url::to(['penilaian/evaluasi']) ?>">
                <div class="row align-items-end">

                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label>Tahun</label>
                            <?= Html::dropDownList('tahun', $tahun, $years, [
                                'class' => 'form-control select2 w-100'
                            ]) ?>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label>Bulan</label>
                            <?= Html::dropDownList('bulan', $bulan, $months, [
                                'class' => 'form-control select2 w-100'
                            ]) ?>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label>Penyedia</label>
                            <?= Html::dropDownList('vendor_id', $vendor_id, $vendors, [
                                'class' => 'form-control select2 w-100'
                            ]) ?>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label>Urutan</label>
                            <?= Html::dropDownList('sort', $sort, $sortOptions, [
                                'class' => 'form-control select2 w-100'
                            ]) ?>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label class="d-block invisible">Action</label>
                            <div class="d-flex h-100">
                                <button type="submit" class="btn btn-primary flex-fill mr-1" title="Cari">
                                    <i class="fas fa-search"></i>
                                </button>
                                <button type="submit" name="export" value="excel" class="btn btn-success flex-fill mr-1" title="Export Excel">
                                    <i class="fas fa-file-excel"></i>
                                </button>
                                <button type="submit" name="export" value="pdf" class="btn btn-danger flex-fill" title="Export PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($summary)): ?>
        <div class="row">
            <?php foreach ($summary as $vendor_nama => $s):
                $percentage = ($s['avg_score'] / 5) * 100;
                $color = $s['avg_score'] >= 4 ? 'success' : ($s['avg_score'] >= 3 ? 'warning' : 'danger');
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card card-outline card-<?= $color ?> h-100 shadow-sm provider-card" style="cursor:pointer;"
                        data-vendor="<?= Html::encode($vendor_nama) ?>" data-tahun="<?= $tahun ?>" data-bulan="<?= $bulan ?>">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-<?= $color ?> p-3 rounded mr-3 text-white">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                                <div style="flex-grow: 1; overflow: hidden;">
                                    <h5 class="card-title text-truncate mb-0" style="width: 100%;"
                                        title="<?= Html::encode($vendor_nama) ?>">
                                        <?= Html::encode($vendor_nama) ?>
                                    </h5>
                                    <small class="text-muted"><?= $s['count'] ?> Paket</small>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <strong><?= $s['avg_score'] ?> / 5.0</strong>
                                    <span class="badge badge-<?= $color ?>">
                                        <?= $s['avg_score'] >= 4 ? 'Sangat Baik' : ($s['avg_score'] >= 3 ? 'Baik' : 'Buruk') ?>
                                    </span>
                                </div>
                                <div class="progress progress-xxs">
                                    <div class="progress-bar bg-<?= $color ?>" style="width: <?= $percentage ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header bg-light">
                <h3 class="card-title font-weight-bold">Daftar Peringkat Kinerja</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Penyedia</th>
                            <th class="text-center">Jumlah Paket</th>
                            <th class="text-center">Total Nilai Kontrak</th>
                            <th class="text-right">Rata-rata Skor</th>
                            <th class="text-center">Predikat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($summary as $s):
                            ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td class="font-weight-bold text-primary"><?= Html::encode($s['nama']) ?></td>
                                <td class="text-center"><?= $s['count'] ?></td>
                                <td class="text-center">Rp. <?= number_format($s['total_nilai'], 0, ',', '.') ?></td>
                                <td class="text-right font-weight-bold"><?= $s['avg_score'] ?></td>
                                <td class="text-center">
                                    <?php
                                    $p = ($s['avg_score'] / 5) * 100;
                                    if ($p >= 90)
                                        echo '<span class="badge badge-success">SANGAT MEMUASKAN</span>';
                                    elseif ($p >= 80)
                                        echo '<span class="badge badge-primary">BAIK</span>';
                                    elseif ($p >= 60)
                                        echo '<span class="badge badge-warning">CUKUP</span>';
                                    else
                                        echo '<span class="badge badge-danger">BURUK</span>';
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
yii\bootstrap4\Modal::begin([
    "id" => "ajaxCrudModal",
    "size" => "modal-xl",
    "footer" => "",
]);
yii\bootstrap4\Modal::end();

yii\bootstrap4\Modal::begin([
    "id" => "ajaxCrudModal2",
    "size" => "modal-xl",
    "footer" => "",
]);
yii\bootstrap4\Modal::end();

$detailUrl = \yii\helpers\Url::to(['penilaian/drill-down']);
$js = <<<JS
$('.provider-card').on('click', function() {
    var vendor = $(this).data('vendor');
    var th = $(this).data('tahun');
    var bl = $(this).data('bulan');
    
    $('#ajaxCrudModal').modal('show');
    $('#ajaxCrudModal .modal-title').html('<i class="fas fa-list"></i> Detail Evaluasi Kinerja');
    $('#ajaxCrudModal .modal-body').html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-2">Memuat data...</p></div>');
    
    $.ajax({
        url: '{$detailUrl}',
        data: { vendor_nama: vendor, tahun: th, bulan: bl },
        success: function(data) {
            $('#ajaxCrudModal .modal-body').html(data);
        },
        error: function() {
            $('#ajaxCrudModal .modal-body').html('<div class="alert alert-danger">Gagal memuat data.</div>');
        }
    });
});
JS;
$this->registerJs($js);
?>

<style>
    .provider-card:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .progress {
        height: 4px;
        margin-top: 5px;
    }
</style>