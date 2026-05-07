<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\bootstrap4\ActiveForm;

$this->title = $model->judul;
$this->params['breadcrumbs'][] = ['label' => 'Minikompetisi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$konsolidasiUrl = Url::to(['konsolidasi', 'id' => $model->id]);
?>

<style>
    /* ===== Panel simulasi ===== */
    .sim-card {
        border-left: 4px solid #6c5ffc;
    }

    .sim-slider {
        -webkit-appearance: none;
        appearance: none;
        height: 6px;
        border-radius: 3px;
        background: #dee2e6;
        outline: none;
    }

    .sim-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #6c5ffc;
        cursor: pointer;
    }

    .badge-rank {
        display: inline-block;
        min-width: 22px;
        padding: 2px 5px;
        border-radius: 99px;
        font-size: 10px;
        font-weight: 700;
        text-align: center;
    }

    .rank-1 {
        background: #ffc107;
        color: #212529;
    }

    .rank-2 {
        background: #adb5bd;
        color: #212529;
    }

    .rank-3 {
        background: #cd7f32;
        color: #fff;
    }

    .rank-n {
        background: #e9ecef;
        color: #495057;
    }

    /* ===== Item ranking panel ===== */
    #item-rank-panel .irp-card {
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #dee2e6;
    }

    #item-rank-panel .irp-head {
        background: #343a40;
        color: #fff;
        padding: 10px 14px;
        display: flex;
        gap: 10px;
        font-size: 12px;
        font-weight: 700;
    }

    #item-rank-panel .irp-head .col-v {
        flex: 1;
    }

    #item-rank-panel .irp-head .col-p {
        min-width: 120px;
        text-align: right;
    }

    #item-rank-panel .irp-head .col-t {
        min-width: 120px;
        text-align: right;
    }

    #item-rank-panel .irp-head .col-pct {
        min-width: 75px;
        text-align: right;
    }

    #item-rank-panel .irp-head .col-r {
        min-width: 50px;
        text-align: center;
    }

    #item-rank-panel .irp-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 14px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 13px;
    }

    #item-rank-panel .irp-row:last-child {
        border-bottom: none;
    }

    #item-rank-panel .irp-row .col-v {
        flex: 1;
        font-weight: 600;
    }

    #item-rank-panel .irp-row .col-p {
        min-width: 120px;
        text-align: right;
    }

    #item-rank-panel .irp-row .col-t {
        min-width: 120px;
        text-align: right;
        color: #6c757d;
        font-size: 12px;
    }

    #item-rank-panel .irp-row .col-pct {
        min-width: 75px;
        text-align: right;
        font-size: 12px;
    }

    #item-rank-panel .irp-row .col-r {
        min-width: 50px;
        text-align: center;
    }

    #item-rank-panel .irp-row.best-item {
        background: #d4edda;
    }

    #item-rank-panel .irp-row.worst-item {
        background: #f8d7da;
    }

    #item-rank-panel .irp-row.winner-vendor {
        background: #fff9db;
    }

    #item-rank-panel .irp-title {
        font-size: 14px;
        font-weight: 700;
        padding: 8px 14px;
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* ===== Matrix table ===== */
    #matrix-wrap {
        overflow-x: auto;
    }

    #matrix-table {
        min-width: 700px;
        border-collapse: collapse;
        font-size: 13px;
    }

    #matrix-table thead tr.vendor-header th {
        vertical-align: top;
        text-align: center;
        padding: 8px 6px;
    }

    #matrix-table thead tr.vendor-header th.winner-col {
        background: #fff3cd;
        border-top: 3px solid #ffc107;
    }

    #matrix-table tbody tr td {
        padding: 6px 8px;
        vertical-align: middle;
    }

    #matrix-table tbody tr:hover td {
        background: rgba(0, 0, 0, .03);
    }

    .cell-best {
        background: #d4edda !important;
    }

    .cell-worst {
        background: #f8d7da !important;
    }

    .cell-winner-vendor {
        background: rgba(255, 243, 205, .5) !important;
    }

    .item-name-col {
        min-width: 160px;
        font-weight: 600;
    }

    .harga-cell {
        text-align: right;
        white-space: nowrap;
    }

    .footer-row td {
        font-weight: 700;
        background: #f8f9fa;
    }

    .skor-row td {
        font-size: 12px;
        background: #f1f3f5;
    }

    .winner-badge {
        font-size: 11px;
    }
</style>

<div class="minikompetisi-view">

    <!-- ROW 1: Detail + Upload -->
    <div class="row">
        <!-- DETAIL -->
        <div class="col-md-5">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Detail Paket</h3>
                    <div class="card-tools">
                        <?= Html::a('<i class="fas fa-edit"></i> Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
                        <?= Html::a('<i class="fas fa-trash"></i> Hapus', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger btn-sm',
                            'data' => ['confirm' => 'Hapus paket ini beserta semua penawarannya?', 'method' => 'post'],
                        ]) ?>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'judul',
                            'tanggal:date',
                            ['attribute' => 'metode', 'value' => $model->getMetodeText()],
                            'bobot_kualitas',
                            'bobot_harga',
                        ],
                    ]) ?>
                </div>
            </div>

            <!-- Upload Penawaran -->
            <div class="card card-outline card-success mt-3">
                <div class="card-header">
                    <h3 class="card-title">Upload Penawaran Vendor</h3>
                    <div class="card-tools">
                        <?= Html::a('<i class="fas fa-download"></i> Download Template Excel', ['template', 'id' => $model->id], ['class' => 'btn btn-warning btn-sm', 'target' => '_blank']) ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin([
                        'action' => ['import', 'id' => $model->id],
                        'options' => ['enctype' => 'multipart/form-data']
                    ]); ?>

                    <div class="form-group">
                        <label>Pilih Vendor</label>
                        <?php if (empty($vendors)): ?>
                            <div class="alert alert-warning p-2" style="font-size: 13px;">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Belum ada vendor diundang.
                                <?= Html::a('<strong>Kelola Vendor &raquo;</strong>', ['update', 'id' => $model->id, '#' => 'daftar-vendor'], ['class' => 'alert-link']) ?>
                            </div>
                        <?php endif; ?>
                        <select name="vendor_id" id="upload-vendor-select" class="form-control" required <?= empty($vendors) ? 'disabled' : '' ?>>
                            <option value="">- Pilih Vendor -</option>
                            <?php foreach ($vendors as $v): ?>
                                <option value="<?= $v->id ?>"><?= Html::encode($v->nama_vendor) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>File Excel Penawaran</label>
                        <input type="file" name="file_excel" class="form-control" accept=".xlsx, .xls" required>
                    </div>

                    <div class="form-group">
                        <label>Catatan Revisi <small class="text-muted">(opsional)</small></label>
                        <input type="text" name="revision_note" class="form-control"
                               placeholder="Contoh: Update harga Q2 2025, koreksi item no.3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Setiap upload akan membuat <strong>versi baru</strong>. Riwayat penawaran tetap tersimpan.
                        </small>
                    </div>

                    <button class="btn btn-success btn-block" type="submit"><i class="fas fa-upload"></i> Proses &amp;
                        Hitung Skor</button>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <!-- ITEMS & QUICK RANKING -->
        <div class="col-md-7">
            <!-- ITEMS -->
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Daftar Kebutuhan (Item)</h3>
                    <div class="card-tools">
                        <?= Html::a('<i class="fas fa-file-excel"></i> Template Excel', ['download-template-item'], [
                            'class' => 'btn btn-outline-success btn-sm mr-1',
                            'target' => '_blank',
                            'title' => 'Download template Excel untuk import item produk',
                        ]) ?>
                        <?= Html::a('<i class="fas fa-file-upload"></i> Import Item', ['import-item-form', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm']) ?>
                    </div>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Produk</th>
                                <th>Qty</th>
                                <th>Satuan</th>
                                <th>HPS (Satuan)</th>
                                <th>Existing</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($items as $item): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= Html::encode($item->nama_produk) ?></td>
                                    <td><?= $item->qty ?></td>
                                    <td><?= Html::encode($item->satuan) ?></td>
                                    <td><?= Yii::$app->formatter->asCurrency($item->harga_hps) ?></td>
                                    <td><?= Yii::$app->formatter->asCurrency($item->harga_existing) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- QUICK RANKING (from DB) -->
            <div class="card card-outline card-danger mt-3">
                <div class="card-header">
                    <h3 class="card-title">Rangking Tersimpan</h3>
                    <small class="text-muted ml-2">(berdasarkan data terakhir diimport)</small>
                    <div class="card-tools">
                        <?= Html::a('<i class="fas fa-chart-line mr-1"></i>Price Intelligence', ['price-intelligence'], ['class' => 'btn btn-outline-info btn-sm', 'target' => '_blank']) ?>
                    </div>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-bordered table-sm table-striped">
                        <thead class="bg-secondary text-white">
                            <tr>
                                <th>Rank</th>
                                <th>Vendor</th>
                                <th>Total Harga</th>
                                <th>Skor Harga</th>
                                <th>Skor Kualitas</th>
                                <th>Skor Akhir</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($penawarans)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">Belum ada penawaran diproses</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($penawarans as $p): ?>
                                    <tr class="<?= $p->is_winner ? 'table-warning' : '' ?>">
                                        <td class="text-center">
                                            <?php if ($p->ranking == 1): ?><span class="badge-rank rank-1">🥇 1</span>
                                            <?php elseif ($p->ranking == 2): ?><span class="badge-rank rank-2">🥈 2</span>
                                            <?php elseif ($p->ranking == 3): ?><span class="badge-rank rank-3">🥉 3</span>
                                            <?php else: ?><span class="badge-rank rank-n"><?= $p->ranking ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= Html::encode($p->vendor->nama_vendor) ?></td>
                                        <td class="text-right"><?= Yii::$app->formatter->asCurrency($p->total_harga) ?></td>
                                        <td class="text-right"><?= round($p->total_skor_harga, 2) ?></td>
                                        <td class="text-right"><?= round($p->total_skor_kualitas, 2) ?></td>
                                        <td class="text-right font-weight-bold"><?= round($p->total_skor_akhir, 2) ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-xs btn-outline-secondary btn-history"
                                                    style="font-size:11px;padding:2px 6px;"
                                                    data-mk="<?= $model->id ?>" data-vendor="<?= $p->vendor_id ?>">
                                                <i class="fas fa-history"></i> Riwayat
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- VERSION HISTORY PANEL (shown via AJAX) -->
            <div id="version-history-panel" class="d-none mt-3">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-history mr-1"></i>Riwayat Versi: <span id="vh-vendor-name"></span></h3>
                        <div class="card-tools"><button class="btn btn-xs btn-secondary" onclick="$('#version-history-panel').addClass('d-none')">✕</button></div>
                    </div>
                    <div class="card-body p-0">
                        <div id="vh-content"><div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i></div></div>
                    </div>
                </div>
            </div>

            <!-- CHART -->
            <div class="card card-outline card-warning mt-3">
                <div class="card-header">
                    <h3 class="card-title">Grafik Perbandingan Harga vs HPS</h3>
                </div>
                <div class="card-body">
                    <canvas id="komparasiChart" style="height:250px;"></canvas>
                </div>
            </div>
        </div>
    </div><!-- /row 1 -->

    <!-- ROW 2: SIMULASI + MATRIX (full-width) -->
    <div class="row mt-3">
        <div class="col-12">

            <!-- PANEL SIMULASI -->
            <div class="card sim-card shadow-sm">
                <div class="card-header bg-white d-flex align-items-center flex-wrap" style="gap:12px;">
                    <h3 class="card-title mb-0 mr-3"><i class="fas fa-sliders-h text-primary mr-1"></i> Simulasi
                        Evaluasi (Hot-Reload)</h3>

                    <div class="d-flex align-items-center" style="gap:8px;">
                        <label class="mb-0 font-weight-bold text-muted" style="font-size:13px;">Metode:</label>
                        <select id="sim-metode" class="form-control form-control-sm" style="min-width:160px;">
                            <option value="1">Harga Terendah</option>
                            <option value="2">Kualitas &amp; Harga</option>
                            <option value="3">Lumpsum</option>
                        </select>
                    </div>

                    <div id="sim-bobot-section" class="d-flex align-items-center flex-wrap" style="gap:12px;">
                        <div class="d-flex align-items-center" style="gap:8px;">
                            <label class="mb-0 text-muted" style="font-size:13px;">Bobot Kualitas:</label>
                            <input type="range" id="sim-kualitas" class="sim-slider" min="0" max="100" step="5"
                                value="50" style="width:110px;">
                            <span id="sim-kualitas-val" class="badge badge-secondary"
                                style="min-width:40px;font-size:13px;">50%</span>
                        </div>
                        <div class="d-flex align-items-center" style="gap:8px;">
                            <label class="mb-0 text-muted" style="font-size:13px;">Bobot Harga:</label>
                            <input type="range" id="sim-harga" class="sim-slider" min="0" max="100" step="5" value="50"
                                style="width:110px;" disabled>
                            <span id="sim-harga-val" class="badge badge-secondary"
                                style="min-width:40px;font-size:13px;">50%</span>
                        </div>
                    </div>

                    <button id="sim-reset" class="btn btn-outline-secondary btn-sm ml-auto"><i class="fas fa-undo"></i>
                        Reset ke Asal</button>
                </div>
            </div>

            <!-- MATRIX KONSOLIDASI -->
            <div class="card card-outline card-dark mt-2">
                <div class="card-header d-flex align-items-center flex-wrap" style="gap:8px;">
                    <h3 class="card-title mb-0"><i class="fas fa-table mr-1"></i> Matriks Konsolidasi Penawaran</h3>
                    <span class="badge badge-light ml-2" style="font-size:11px;">Kolom diurutkan dari terbaik (kiri) ke
                        terburuk (kanan) secara live</span>
                    <button id="btn-export-excel" class="btn btn-success btn-sm ml-auto" disabled>
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </button>
                </div>
                <div class="card-body p-2">
                    <!-- FILTER PER ITEM -->
                    <div id="item-filter-bar"
                        class="d-none mb-2 p-2 bg-light rounded border d-flex align-items-center flex-wrap"
                        style="gap:10px;">
                        <i class="fas fa-filter text-primary"></i>
                        <strong style="font-size:13px;">Filter Item:</strong>
                        <select id="item-filter-select" class="form-control form-control-sm" style="max-width:280px;">
                            <option value="">-- Tampilkan Semua Item --</option>
                        </select>
                        <small class="text-muted">Pilih item untuk melihat ranking harga antar penyedia</small>
                    </div>

                    <!-- ITEM RANKING DETAIL (muncul saat item dipilih) -->
                    <div id="item-rank-panel" class="d-none mb-2"></div>

                    <div id="matrix-wrap">
                        <div id="matrix-loading" class="text-center py-4 text-muted"><i
                                class="fas fa-spinner fa-spin mr-1"></i> Memuat data...</div>
                        <table id="matrix-table" class="table table-bordered" style="display:none;"></table>
                    </div>
                </div>
            </div>

        </div>
    </div><!-- /row 2 -->

</div><!-- /minikompetisi-view -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<?php
// Static chart (PHP-rendered, no hot-reload needed here)
$hps_total = 0;
$existing_total = 0;
foreach ($items as $it) {
    if ($it->harga_hps)
        $hps_total += ($it->harga_hps * $it->qty);
    if ($it->harga_existing)
        $existing_total += ($it->harga_existing * $it->qty);
}

$labels = ['HPS', 'Harga Beli Existing'];
$data_values = [$hps_total, $existing_total];
$colors = ['rgba(54,162,235,0.5)', 'rgba(255,99,132,0.5)'];
$borders = ['rgb(54,162,235)', 'rgb(255,99,132)'];

foreach ($penawarans as $p) {
    $labels[] = $p->vendor->nama_vendor;
    $data_values[] = $p->total_harga;
    $colors[] = $p->is_winner ? 'rgba(255,193,7,0.6)' : 'rgba(75,192,192,0.5)';
    $borders[] = $p->is_winner ? 'rgb(255,193,7)' : 'rgb(75,192,192)';
}

$labels_json = json_encode($labels);
$data_json = json_encode($data_values);
$colors_json = json_encode($colors);
$borders_json = json_encode($borders);
$konsolidasiUrl = json_encode(Url::to(['konsolidasi', 'id' => $model->id]));
$defaultMetode = (int) $model->metode;
$defaultKualitas = (float) $model->bobot_kualitas;
$defaultHarga = (float) $model->bobot_harga;

$script = <<<JS
$(function () {

    /* ─────────────── STATIC CHART ─────────────── */
    var ctx = document.getElementById('komparasiChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: $labels_json,
            datasets: [{
                label: 'Total Harga (Rp)',
                data: $data_json,
                backgroundColor: $colors_json,
                borderColor: $borders_json,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });

    /* ─────────────── FETCH KONSOLIDASI DATA ─────────────── */
    var _raw = null; // raw JSON from server
    var API_URL = $konsolidasiUrl;

    $.getJSON(API_URL, function (data) {
        _raw = data;
        $('#matrix-loading').hide();
        $('#matrix-table').show();

        // init sim panel defaults
        $('#sim-metode').val(data.metode);
        $('#sim-kualitas').val(data.bobot_kualitas || 50);
        $('#sim-harga').val(data.bobot_harga || 50);
        syncSliderLabels();
        toggleBobotSection();
        renderMatrix(calcRanking(data.metode, parseFloat(data.bobot_kualitas), parseFloat(data.bobot_harga)));

        // Populate item filter dropdown
        var sel = $('#item-filter-select');
        data.items.forEach(function(item) {
            sel.append('<option value="' + item.id + '">' + $('<div>').text(item.nama_produk).html() + ' (' + item.qty + ' ' + (item.satuan || '') + ')</option>');
        });
        $('#item-filter-bar').removeClass('d-none');
    });

    // Filter change handler
    $('#item-filter-select').on('change', function() {
        var itemId = parseInt($(this).val());
        if (!itemId || !_lastVendors.length) {
            $('#item-rank-panel').addClass('d-none').empty();
            $('#matrix-wrap').show();
            return;
        }
        renderItemRanking(itemId, _lastVendors);
        // Highlight row in matrix
        $('#matrix-table tbody tr').css('opacity', '0.4');
        $('#matrix-table tbody tr').filter(function() {
            return $(this).find('td.item-name-col').length > 0;
        }).each(function() {
            // we can't easily match by item name reliably; just show the panel
        });
        $('#matrix-table tbody tr').css('opacity', '');
    });

    /* ─────────────── SIMULASI CONTROLS ─────────────── */
    $('#sim-metode').on('change', function () {
        toggleBobotSection();
        triggerRecalc();
    });

    $('#sim-kualitas').on('input', function () {
        var kVal = parseInt($(this).val());
        var hVal = 100 - kVal;
        $('#sim-harga').val(hVal);
        syncSliderLabels();
        triggerRecalc();
    });

    $('#sim-reset').on('click', function () {
        if (!_raw) return;
        $('#sim-metode').val(_raw.metode);
        $('#sim-kualitas').val(_raw.bobot_kualitas || 50);
        $('#sim-harga').val(_raw.bobot_harga || 50);
        syncSliderLabels();
        toggleBobotSection();
        triggerRecalc();
    });

    function syncSliderLabels() {
        $('#sim-kualitas-val').text($('#sim-kualitas').val() + '%');
        $('#sim-harga-val').text($('#sim-harga').val() + '%');
    }

    function toggleBobotSection() {
        if ($('#sim-metode').val() == 2) {
            $('#sim-bobot-section').show();
        } else {
            $('#sim-bobot-section').hide();
        }
    }

    function triggerRecalc() {
        if (!_raw) return;
        var metode = parseInt($('#sim-metode').val());
        var bk = parseFloat($('#sim-kualitas').val());
        var bh = parseFloat($('#sim-harga').val());
        var vendors = calcRanking(metode, bk, bh);
        renderMatrix(vendors);
        // Refresh item panel if item is selected
        var selectedItem = parseInt($('#item-filter-select').val());
        if (selectedItem) renderItemRanking(selectedItem, vendors);
    }

    /* ─────────────── CALCULATION ENGINE ─────────────── */
    // Mirror of PHP calculateRanking()
    function calcRanking(metode, bobotKualitas, bobotHarga) {
        if (!_raw || !_raw.penawarans.length) return [];

        var vendors = JSON.parse(JSON.stringify(_raw.penawarans)); // deep clone

        // Find lowest total harga
        var lowestPrice = null;
        vendors.forEach(function (v) {
            if (v.total_harga > 0 && (lowestPrice === null || v.total_harga < lowestPrice)) {
                lowestPrice = v.total_harga;
            }
        });

        // Compute skor_harga and skor_akhir
        vendors.forEach(function (v) {
            var skorHarga = 0;
            if (v.total_harga > 0 && lowestPrice > 0) {
                skorHarga = (lowestPrice / v.total_harga) * 100;
            }
            v._skor_harga = skorHarga;

            if (metode === 1 || metode === 3) {
                v._skor_akhir = skorHarga;
            } else { // metode 2
                v._skor_akhir = (skorHarga * (bobotHarga / 100)) + (v.total_skor_kualitas * (bobotKualitas / 100));
            }
        });

        // Sort vendors by skor_akhir DESC (best first = leftmost column)
        vendors.sort(function (a, b) { return b._skor_akhir - a._skor_akhir; });

        // Assign ranking
        vendors.forEach(function (v, i) { v._ranking = i + 1; v._is_winner = (i === 0); });

        return vendors;
    }

    /* ─────────────── RENDER MATRIX TABLE ─────────────── */
    function fmt(num) {
        if (!num && num !== 0) return '-';
        return 'Rp ' + parseFloat(num).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }
    function fmtNum(n) { return parseFloat(n).toFixed(2); }

    function rankBadge(rank) {
        if (rank === 1) return '<span class="badge-rank rank-1">🥇&nbsp;1</span>';
        if (rank === 2) return '<span class="badge-rank rank-2">🥈&nbsp;2</span>';
        if (rank === 3) return '<span class="badge-rank rank-3">🥉&nbsp;3</span>';
        return '<span class="badge-rank rank-n">#' + rank + '</span>';
    }

    function renderMatrix(vendors) {
        _lastVendors = vendors || [];
        $('#btn-export-excel').prop('disabled', !_lastVendors.length);

        var tbl = $('#matrix-table');
        tbl.empty();

        if (!vendors || !vendors.length) {
            tbl.append('<tr><td colspan="99" class="text-center text-muted py-4">Belum ada penawaran untuk ditampilkan.</td></tr>');
            return;
        }

        var items = _raw.items;

        /* ── THEAD: vendor header row ── */
        var headHtml = '<thead>';
        // Row 1: vendor names + rank badges
        headHtml += '<tr class="vendor-header"><th class="item-name-col bg-light">Item Produk</th>';
        vendors.forEach(function (v) {
            var winnerClass = v._ranking === 1 ? 'winner-col' : '';
            var rankBdg = rankBadge(v._ranking);
            headHtml += '<th class="' + winnerClass + '">';
            headHtml += rankBdg + '&nbsp;<strong>' + htmlEnc(v.nama_vendor) + '</strong>';
            if (v._ranking === 1) headHtml += '<br><span class="badge badge-warning winner-badge">🏆 Kandidat Terpilih</span>';
            headHtml += '</th>';
        });
        headHtml += '</tr>';
        headHtml += '</thead>';
        tbl.append(headHtml);

        /* ── TBODY: item rows ── */
        var tbodyHtml = '<tbody>';

        items.forEach(function (item) {
            // Collect prices from all vendors for this item, to determine best/worst
            var prices = vendors.map(function (v) {
                var pi = v.items.find(function (i) { return i.item_id === item.id; });
                return pi ? pi.harga_penawaran : null;
            });
            var validPrices = prices.filter(function (p) { return p !== null && p > 0; });
            var minPrice = validPrices.length ? Math.min.apply(null, validPrices) : null;
            var maxPrice = validPrices.length ? Math.max.apply(null, validPrices) : null;

            // Rank prices per item (ascending = better)
            var sortedPrices = validPrices.slice().sort(function (a, b) { return a - b; });

            tbodyHtml += '<tr>';
            tbodyHtml += '<td class="item-name-col">' + htmlEnc(item.nama_produk) +
                '<br><small class="text-muted">' + item.qty + ' ' + htmlEnc(item.satuan || '') + ' | HPS: ' + fmt(item.harga_hps) + '</small></td>';

            vendors.forEach(function (v, vi) {
                var pi = v.items.find(function (i) { return i.item_id === item.id; });
                var price = pi ? pi.harga_penawaran : null;
                var total = (price && item.qty) ? price * item.qty : null;

                var cellClass = v._ranking === 1 ? 'cell-winner-vendor' : '';
                if (price !== null && price > 0 && validPrices.length > 1) {
                    if (price === minPrice) cellClass += ' cell-best';
                    else if (price === maxPrice) cellClass += ' cell-worst';
                }

                var itemRank = price !== null && price > 0 ? (sortedPrices.indexOf(price) + 1) : null;
                var itemRankBadge = itemRank ? rankBadge(itemRank) : '';

                tbodyHtml += '<td class="harga-cell ' + cellClass + '">';
                if (price !== null) {
                    tbodyHtml += '<strong>' + fmt(price) + '</strong>/sat<br>';
                    tbodyHtml += '<small class="text-muted">' + fmt(total) + ' total</small><br>';
                    if (pi.link_katalog) {
                        tbodyHtml += '<div class="mt-1"><a href="' + pi.link_katalog + '" target="_blank" class="btn btn-xs btn-outline-primary" style="font-size:9px;padding:0 3px;"><i class="fas fa-external-link-alt"></i> Katalog</a></div>';
                    }
                    tbodyHtml += '<div class="mt-1">' + itemRankBadge + '</div>';
                } else {
                    tbodyHtml += '<span class="text-muted">–</span>';
                }
                tbodyHtml += '</td>';
            });

            tbodyHtml += '</tr>';
        });

        /* ── FOOTER: summary rows ── */
        // Total Harga
        tbodyHtml += '<tr class="footer-row"><td>TOTAL HARGA</td>';
        vendors.forEach(function (v) {
            tbodyHtml += '<td class="text-right ' + (v._ranking === 1 ? 'cell-winner-vendor' : '') + '">' + fmt(v.total_harga) + '</td>';
        });
        tbodyHtml += '</tr>';

        // Skor Harga
        tbodyHtml += '<tr class="skor-row"><td>Skor Harga</td>';
        vendors.forEach(function (v) {
            tbodyHtml += '<td class="text-right">' + fmtNum(v._skor_harga) + '</td>';
        });
        tbodyHtml += '</tr>';

        // Skor Kualitas (only show if metode 2)
        var metode = parseInt($('#sim-metode').val());
        if (metode === 2) {
            tbodyHtml += '<tr class="skor-row"><td>Skor Kualitas</td>';
            vendors.forEach(function (v) {
                tbodyHtml += '<td class="text-right">' + fmtNum(v.total_skor_kualitas) + '</td>';
            });
            tbodyHtml += '</tr>';
        }

        // Skor Akhir (bold)
        tbodyHtml += '<tr class="footer-row"><td>⭐ SKOR AKHIR</td>';
        vendors.forEach(function (v) {
            var style = v._ranking === 1 ? 'background:#fff3cd;color:#856404;' : '';
            tbodyHtml += '<td class="text-right" style="' + style + '">' + fmtNum(v._skor_akhir) + '</td>';
        });
        tbodyHtml += '</tr>';

        tbodyHtml += '</tbody>';
        tbl.append(tbodyHtml);
    }

    function htmlEnc(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    /* ─────────────── ITEM RANKING ─────────────── */
    function renderItemRanking(itemId, vendors) {
        var item = _raw.items.find(function(i) { return i.id === itemId; });
        if (!item) return;

        // Collect vendor prices for this item, then sort ASC (cheapest = rank 1)
        var rows = [];
        vendors.forEach(function(v) {
            var pi = v.items.find(function(i) { return i.item_id === itemId; });
            rows.push({
                nama_vendor : v.nama_vendor,
                harga       : pi ? pi.harga_penawaran : null,
                total       : (pi && pi.harga_penawaran) ? pi.harga_penawaran * item.qty : null,
                is_winner   : v._is_winner,
                vendor_rank : v._ranking,
            });
        });

        // Sort by harga ASC, nulls last
        rows.sort(function(a, b) {
            if (a.harga === null && b.harga === null) return 0;
            if (a.harga === null) return 1;
            if (b.harga === null) return -1;
            return a.harga - b.harga;
        });

        var minH = null, maxH = null;
        rows.forEach(function(r) {
            if (r.harga > 0) {
                if (minH === null || r.harga < minH) minH = r.harga;
                if (maxH === null || r.harga > maxH) maxH = r.harga;
            }
        });

        var hpsTotal = item.harga_hps ? item.harga_hps * item.qty : null;

        var html = '<div class="irp-card">';
        html += '<div class="irp-title"><i class="fas fa-box-open text-info mr-1"></i>' +
            htmlEnc(item.nama_produk) +
            ' &nbsp;<small class="text-muted font-weight-normal">' + item.qty + ' ' + htmlEnc(item.satuan || '') +
            (item.harga_hps ? ' | HPS Satuan: ' + fmt(item.harga_hps) + ' | HPS Total: ' + fmt(hpsTotal) : '') +
            '</small></div>';
        html += '<div class="irp-head">' +
            '<span class="col-r">Rank Item</span>' +
            '<span class="col-v">Penyedia</span>' +
            '<span class="col-p">Harga/Satuan</span>' +
            '<span class="col-t">Total (×' + item.qty + ')</span>' +
            '<span class="col-pct">% vs HPS</span>' +
            '<span class="col-r">Rank Total</span>' +
            '</div>';

        rows.forEach(function(r, idx) {
            if (r.harga === null) return; // skip vendors without offer for this item
            var itemRank = idx + 1;
            var rowClass = '';
            if (r.is_winner) rowClass = 'winner-vendor';
            if (r.harga === minH && rows.filter(function(x){ return x.harga > 0; }).length > 1) rowClass = 'best-item';
            if (r.harga === maxH && rows.filter(function(x){ return x.harga > 0; }).length > 1) rowClass = 'worst-item';

            var pctVsHps = (item.harga_hps && item.harga_hps > 0)
                ? ((r.harga / item.harga_hps) * 100).toFixed(1) + '%'
                : '-';
            var pctClass = (item.harga_hps && r.harga <= item.harga_hps) ? 'text-success font-weight-bold' : 'text-danger';

            html += '<div class="irp-row ' + rowClass + '">' +
                '<span class="col-r">' + rankBadge(itemRank) + '</span>' +
                '<span class="col-v">' + htmlEnc(r.nama_vendor) + (r.is_winner ? ' <span class="badge badge-warning" style="font-size:10px;">🏆</span>' : '') + '</span>' +
                '<span class="col-p">' + fmt(r.harga) + '</span>' +
                '<span class="col-t">' + fmt(r.total) + '</span>' +
                '<span class="col-pct ' + pctClass + '">' + pctVsHps + '</span>' +
                '<span class="col-r">' + rankBadge(r.vendor_rank) + '</span>' +
                '</div>';
        });

        html += '</div>';

        $('#item-rank-panel').html(html).removeClass('d-none');
    }

    /* ─────────────── EXPORT EXCEL ─────────────── */
    var _lastVendors = [];

    function exportToExcel(vendors) {
        if (!vendors || !vendors.length || !_raw) {
            alert('Belum ada data untuk diexport.');
            return;
        }

        var items   = _raw.items;
        var metode  = parseInt($('#sim-metode').val());
        var bk      = parseFloat($('#sim-kualitas').val());
        var bh      = parseFloat($('#sim-harga').val());

        // Map metode label
        var metodeLabel = metode === 1 ? 'Harga Terendah' : (metode === 2 ? 'Kualitas & Harga (BK:' + bk + '% BH:' + bh + '%)' : 'Lumpsum');

        var wb = XLSX.utils.book_new();
        var wsData = [];

        // Header info rows
        wsData.push(['Matriks Konsolidasi Penawaran']);
        wsData.push(['Metode Evaluasi:', metodeLabel]);
        wsData.push([]);

        // Column headers: Item, then vendor names + rank
        var headerRow = ['Item Produk', 'Qty', 'Satuan', 'HPS/Satuan', 'Existing/Satuan'];
        vendors.forEach(function(v) {
            headerRow.push('[Rank #' + v._ranking + '] ' + v.nama_vendor);
        });
        wsData.push(headerRow);

        // Item rows
        items.forEach(function(item) {
            // Price row
            var row = [item.nama_produk, item.qty, item.satuan || '', item.harga_hps, item.harga_existing];
            vendors.forEach(function(v) {
                var pi = v.items.find(function(i) { return i.item_id === item.id; });
                row.push(pi ? pi.harga_penawaran : '');
            });
            wsData.push(row);

            // Link row (optional, only if at least one vendor has a link for this item)
            var hasAnyLink = vendors.some(function(v) {
                var pi = v.items.find(function(i) { return i.item_id === item.id; });
                return pi && pi.link_katalog;
            });

            if (hasAnyLink) {
                var linkRow = ['   (Link Katalog)', '', '', '', ''];
                vendors.forEach(function(v) {
                    var pi = v.items.find(function(i) { return i.item_id === item.id; });
                    linkRow.push(pi ? (pi.link_katalog || '') : '');
                });
                wsData.push(linkRow);
            }
        });

        // Blank separator
        wsData.push([]);

        // Total harga row
        var totalRow = ['TOTAL HARGA', '', '', '', ''];
        vendors.forEach(function(v) { totalRow.push(v.total_harga); });
        wsData.push(totalRow);

        // Skor harga row
        var skorHargaRow = ['SKOR HARGA', '', '', '', ''];
        vendors.forEach(function(v) { skorHargaRow.push(parseFloat(v._skor_harga.toFixed(2))); });
        wsData.push(skorHargaRow);

        if (metode === 2) {
            var skorKualRow = ['SKOR KUALITAS', '', '', '', ''];
            vendors.forEach(function(v) { skorKualRow.push(parseFloat(v.total_skor_kualitas.toFixed(2))); });
            wsData.push(skorKualRow);
        }

        // Skor akhir row
        var skorAkhirRow = ['SKOR AKHIR', '', '', '', ''];
        vendors.forEach(function(v) { skorAkhirRow.push(parseFloat(v._skor_akhir.toFixed(2))); });
        wsData.push(skorAkhirRow);

        var ws = XLSX.utils.aoa_to_sheet(wsData);

        // Column widths
        ws['!cols'] = [{wch: 28}, {wch: 7}, {wch: 8}, {wch: 14}, {wch: 14}];
        vendors.forEach(function() { ws['!cols'].push({wch: 20}); });

        XLSX.utils.book_append_sheet(wb, ws, 'Konsolidasi');

        var fileName = 'Konsolidasi_Penawaran_' + (_raw.id) + '.xlsx';
        XLSX.writeFile(wb, fileName);
    }

    $('#btn-export-excel').on('click', function() {
        exportToExcel(_lastVendors);
    });

    // Trigger modal import item — kompatibel Bootstrap 4 & 5
    $('#btn-open-import-item-view').on('click', function() {
        var el = document.getElementById('modalImportItemView');
        if (!el) return;
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            (new bootstrap.Modal(el)).show();
        } else if (typeof $ !== 'undefined' && $.fn.modal) {
            $(el).modal('show');
        } else {
            el.style.display = 'block';
            el.classList.add('show');
            document.body.classList.add('modal-open');
        }
    });

    /* ─── VERSION HISTORY ─── */
    $(document).on('click', '.btn-history', function() {
        var mkId     = $(this).data('mk');
        var vendorId = $(this).data('vendor');
        var url      = '/minikompetisi/version-history?id=' + mkId + '&vendor_id=' + vendorId;

        $('#version-history-panel').removeClass('d-none');
        $('#vh-vendor-name').text('Loading...');
        $('#vh-content').html('<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i></div>');

        $.getJSON(url, function(data) {
            $('#vh-vendor-name').text(data.vendor || '');
            var fmt = function(n) {
                return 'Rp ' + parseFloat(n || 0).toLocaleString('id-ID', {minimumFractionDigits:0});
            };
            var html = '<div class="table-responsive"><table class="table table-sm table-bordered mb-0" style="font-size:13px;">';
            html += '<thead class="thead-light"><tr><th>Versi</th><th>Label</th><th>Catatan Revisi</th><th class="text-right">Total Harga</th><th class="text-right">Skor Akhir</th><th class="text-center">Rank</th><th class="text-center">Waktu Upload</th><th class="text-center">Status</th></tr></thead><tbody>';
            (data.versions || []).forEach(function(v) {
                var rowCls = v.is_latest ? 'table-success' : (v.is_winner ? 'table-warning' : '');
                html += '<tr class="' + rowCls + '">';
                html += '<td><span class="badge badge-' + (v.is_latest ? 'success' : 'secondary') + '">v' + v.version_number + (v.is_latest ? ' ✓ Latest' : '') + '</span></td>';
                html += '<td>' + (v.version_label || '') + '</td>';
                html += '<td>' + (v.revision_note ? '<em>' + $('<span>').text(v.revision_note).html() + '</em>' : '<span class="text-muted">-</span>') + '</td>';
                html += '<td class="text-right">' + fmt(v.total_harga) + '</td>';
                html += '<td class="text-right">' + (v.skor_akhir ? parseFloat(v.skor_akhir).toFixed(2) : '-') + '</td>';
                html += '<td class="text-center">' + (v.ranking || '-') + (v.is_winner ? ' 🏆' : '') + '</td>';
                html += '<td class="text-center" style="font-size:11px;">' + (v.uploaded_at || '') + '</td>';
                html += '<td class="text-center"><span class="badge badge-info">' + (v.status || '') + '</span></td>';
                html += '</tr>';
            });
            if (!data.versions || !data.versions.length) {
                html += '<tr><td colspan="8" class="text-center text-muted py-3">Belum ada riwayat versi.</td></tr>';
            }
            html += '</tbody></table></div>';
            $('#vh-content').html(html);
        }).fail(function() {
            $('#vh-content').html('<div class="alert alert-danger m-2">Gagal memuat riwayat versi.</div>');
        });
    });

});
JS;

$this->registerJs($script);
?>