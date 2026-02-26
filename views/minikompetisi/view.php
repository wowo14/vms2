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
.sim-card { border-left: 4px solid #6c5ffc; }
.sim-slider { -webkit-appearance: none; appearance: none; height: 6px; border-radius: 3px; background: #dee2e6; outline: none; }
.sim-slider::-webkit-slider-thumb { -webkit-appearance: none; width: 18px; height: 18px; border-radius: 50%; background: #6c5ffc; cursor: pointer; }
.badge-rank { display: inline-block; min-width: 22px; padding: 2px 5px; border-radius: 99px; font-size: 10px; font-weight: 700; text-align: center; }
.rank-1  { background:#ffc107; color:#212529; }
.rank-2  { background:#adb5bd; color:#212529; }
.rank-3  { background:#cd7f32; color:#fff; }
.rank-n  { background:#e9ecef; color:#495057; }

/* ===== Matrix table ===== */
#matrix-wrap { overflow-x: auto; }
#matrix-table { min-width: 700px; border-collapse: collapse; font-size: 13px; }
#matrix-table thead tr.vendor-header th { vertical-align: top; text-align: center; padding: 8px 6px; }
#matrix-table thead tr.vendor-header th.winner-col { background: #fff3cd; border-top: 3px solid #ffc107; }
#matrix-table tbody tr td { padding: 6px 8px; vertical-align: middle; }
#matrix-table tbody tr:hover td { background: rgba(0,0,0,.03); }
.cell-best  { background: #d4edda !important; }
.cell-worst { background: #f8d7da !important; }
.cell-winner-vendor { background: rgba(255,243,205,.5) !important; }
.item-name-col { min-width: 160px; font-weight: 600; }
.harga-cell { text-align: right; white-space: nowrap; }
.footer-row td { font-weight: 700; background: #f8f9fa; }
.skor-row td { font-size: 12px; background: #f1f3f5; }
.winner-badge { font-size: 11px; }
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
                        <select name="vendor_id" class="form-control" required>
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

                    <button class="btn btn-success btn-block" type="submit"><i class="fas fa-upload"></i> Proses &amp; Hitung Skor</button>
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($penawarans)): ?>
                                    <tr><td colspan="6" class="text-center text-muted py-3">Belum ada penawaran diproses</td></tr>
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
                                        </tr>
                                    <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
                    <h3 class="card-title mb-0 mr-3"><i class="fas fa-sliders-h text-primary mr-1"></i> Simulasi Evaluasi (Hot-Reload)</h3>

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
                            <input type="range" id="sim-kualitas" class="sim-slider" min="0" max="100" step="5" value="50" style="width:110px;">
                            <span id="sim-kualitas-val" class="badge badge-secondary" style="min-width:40px;font-size:13px;">50%</span>
                        </div>
                        <div class="d-flex align-items-center" style="gap:8px;">
                            <label class="mb-0 text-muted" style="font-size:13px;">Bobot Harga:</label>
                            <input type="range" id="sim-harga" class="sim-slider" min="0" max="100" step="5" value="50" style="width:110px;" disabled>
                            <span id="sim-harga-val" class="badge badge-secondary" style="min-width:40px;font-size:13px;">50%</span>
                        </div>
                    </div>

                    <button id="sim-reset" class="btn btn-outline-secondary btn-sm ml-auto"><i class="fas fa-undo"></i> Reset ke Asal</button>
                </div>
            </div>

            <!-- MATRIX KONSOLIDASI -->
            <div class="card card-outline card-dark mt-2">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-table mr-1"></i> Matriks Konsolidasi Penawaran</h3>
                    <span class="badge badge-light ml-2" style="font-size:11px;">Kolom diurutkan dari terbaik (kiri) ke terburuk (kanan) secara live</span>
                </div>
                <div class="card-body p-2">
                    <div id="matrix-wrap">
                        <div id="matrix-loading" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat data...</div>
                        <table id="matrix-table" class="table table-bordered" style="display:none;"></table>
                    </div>
                </div>
            </div>

        </div>
    </div><!-- /row 2 -->

</div><!-- /minikompetisi-view -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        renderMatrix(calcRanking(metode, bk, bh));
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
                    tbodyHtml += fmt(price) + '/sat<br>';
                    tbodyHtml += '<small>' + fmt(total) + ' total</small><br>';
                    tbodyHtml += itemRankBadge;
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

});
JS;

$this->registerJs($script);
?>
