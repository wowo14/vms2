<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Price Intelligence — Analitik Harga Pengadaan';
$this->params['breadcrumbs'][] = ['label' => 'Minikompetisi', 'url' => ['/minikompetisi/index']];
$this->params['breadcrumbs'][] = ['label' => 'Price Intelligence', 'url' => ['price-intelligence']];

$fmt = fn($n) => Yii::$app->formatter->asCurrency($n);
$trendJson = json_encode($trend);
?>

<style>
    .pi-card {
        border-radius: 10px;
        border: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
    }

    .pi-stat {
        font-size: 1.6rem;
        font-weight: 700;
    }

    .pi-label {
        font-size: 0.78rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .vendor-rank-row {
        transition: background .15s;
    }

    .vendor-rank-row:hover {
        background: #f8f9fa;
    }

    .rank-badge {
        display: inline-block;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        font-size: 11px;
        font-weight: 700;
        text-align: center;
        line-height: 24px;
    }

    .rb-1 {
        background: #ffc107;
        color: #212529;
    }

    .rb-2 {
        background: #adb5bd;
        color: #212529;
    }

    .rb-3 {
        background: #cd7f32;
        color: #fff;
    }

    .rb-n {
        background: #e9ecef;
        color: #495057;
    }
</style>

<div class="container-fluid px-0">

    <!-- Header -->
    <div class="d-flex align-items-center mb-3" style="gap:12px;">
        <div>
            <h3 class="mb-0"><i class="fas fa-chart-line text-primary mr-2"></i>Price Intelligence</h3>
            <small class="text-muted">Analitik historis harga pengadaan & kompetitivitas vendor</small>
        </div>
        <div class="ml-auto d-flex" style="gap:8px;">
            <?= Html::a('<i class="fas fa-building mr-1"></i>Katalog Produk', ['product-catalog'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
            <?= Html::a('<i class="fas fa-trophy mr-1"></i>Vendor Ranking', ['vendor-ranking'], ['class' => 'btn btn-outline-warning btn-sm']) ?>
        </div>
    </div>

    <!-- Search -->
    <div class="card pi-card mb-3">
        <div class="card-body py-3">
            <form method="GET" action="<?= Url::to(['price-intelligence']) ?>">
                <div class="input-group input-group-lg">
                    <input type="text" name="q" class="form-control"
                        placeholder="Cari produk... (contoh: susu bubuk, pipa pvc, semen)"
                        value="<?= Html::encode($q) ?>" autofocus>
                    <div class="input-group-append">
                        <button class="btn btn-primary px-4" type="submit">
                            <i class="fas fa-search mr-1"></i> Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($q)): ?>
        <!-- Results Section -->
        <div class="row">

            <!-- Price Range Stats -->
            <div class="col-12 mb-3">
                <?php if ($priceRange && $priceRange['total_samples'] > 0): ?>
                    <div class="card pi-card">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">
                                <i class="fas fa-tag text-info mr-2"></i>
                                Rentang Harga: <strong>
                                    <?= Html::encode($q) ?>
                                </strong>
                                <span class="badge badge-info ml-2">
                                    <?= $priceRange['total_samples'] ?> data
                                </span>
                                <span class="badge badge-secondary ml-1">
                                    <?= $priceRange['vendor_count'] ?> vendor
                                </span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="pi-label">Harga Minimum</div>
                                    <div class="pi-stat text-success">
                                        <?= $fmt($priceRange['min_price']) ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="pi-label">Rata-rata</div>
                                    <div class="pi-stat text-primary">
                                        <?= $fmt($priceRange['avg_price']) ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="pi-label">Harga Maximum</div>
                                    <div class="pi-stat text-danger">
                                        <?= $fmt($priceRange['max_price']) ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="pi-label">Rentang</div>
                                    <div class="pi-stat text-warning">
                                        <?= $fmt($priceRange['max_price'] - $priceRange['min_price']) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col text-center text-muted" style="font-size:12px;">
                                    Data dari
                                    <?= $priceRange['first_seen'] ?> s/d
                                    <?= $priceRange['last_seen'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Tidak ada data harga untuk "<strong>
                            <?= Html::encode($q) ?>
                        </strong>".
                        Coba kata kunci lain atau periksa spelling.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Cheapest Vendors & Trend Chart -->
            <div class="col-md-5">
                <div class="card pi-card h-100">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0"><i class="fas fa-trophy text-warning mr-2"></i>Vendor Termurah</h6>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($cheapest)): ?>
                            <p class="text-muted text-center py-4">Belum ada data.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="36">#</th>
                                            <th>Vendor</th>
                                            <th class="text-right">Harga Min</th>
                                            <th class="text-right">Rata-rata</th>
                                            <th class="text-right">Bids</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cheapest as $i => $row): ?>
                                            <tr class="vendor-rank-row">
                                                <td>
                                                    <?php $r = $i + 1; ?>
                                                    <span
                                                        class="rank-badge <?= $r === 1 ? 'rb-1' : ($r === 2 ? 'rb-2' : ($r === 3 ? 'rb-3' : 'rb-n')) ?>">
                                                        <?= $r ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?= Html::encode($row['nama_vendor'] ?? 'Vendor #' . ($row['vendor_id'])) ?>
                                                </td>
                                                <td class="text-right text-success font-weight-bold">
                                                    <?= $fmt($row['min_price']) ?>
                                                </td>
                                                <td class="text-right text-muted">
                                                    <?= $fmt($row['avg_price']) ?>
                                                </td>
                                                <td class="text-right"><span class="badge badge-light">
                                                        <?= $row['bid_count'] ?>
                                                    </span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Trend Chart -->
            <div class="col-md-7">
                <div class="card pi-card h-100">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0"><i class="fas fa-chart-area text-primary mr-2"></i>Tren Harga Historis</h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($trend)): ?>
                            <p class="text-muted text-center py-4">Belum ada data tren.</p>
                        <?php else: ?>
                            <canvas id="trendChart" style="height:220px;"></canvas>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div><!-- /results row -->
    <?php endif; ?>

    <!-- Recent / Featured Products -->
    <?php if (!empty($recentProducts) && empty($q)): ?>
        <div class="card pi-card mt-3">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-history text-secondary mr-2"></i>Produk Terbaru di Dataset</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th class="text-right">Min</th>
                                <th class="text-right">Rata-rata</th>
                                <th class="text-right">Max</th>
                                <th class="text-right">Data</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentProducts as $rp): ?>
                                <tr>
                                    <td>
                                        <?= Html::encode($rp->product_norm_name) ?>
                                    </td>
                                    <td><span class="badge badge-secondary">
                                            <?= Html::encode($rp->product_category ?: '-') ?>
                                        </span></td>
                                    <td class="text-right text-success">
                                        <?= $fmt($rp->min_price) ?>
                                    </td>
                                    <td class="text-right">
                                        <?= $fmt($rp->avg_price) ?>
                                    </td>
                                    <td class="text-right text-danger">
                                        <?= $fmt($rp->max_price) ?>
                                    </td>
                                    <td class="text-right">
                                        <?= $rp->sample_count ?>
                                    </td>
                                    <td>
                                        <a href="?q=<?= urlencode($rp->product_norm_name) ?>"
                                            class="btn btn-xs btn-outline-primary" style="font-size:11px;padding:2px 6px;">
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php if (!empty($trend)): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function () {
            var trend = <?= $trendJson ?>;
            var labels = trend.map(function (r) { return r.year_month; });
            var avgPrices = trend.map(function (r) { return parseFloat(r.avg_price); });
            var minPrices = trend.map(function (r) { return parseFloat(r.min_price); });

            var ctx = document.getElementById('trendChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Harga Rata-rata',
                            data: avgPrices,
                            borderColor: 'rgb(54,162,235)',
                            backgroundColor: 'rgba(54,162,235,0.08)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 4,
                        },
                        {
                            label: 'Harga Minimum',
                            data: minPrices,
                            borderColor: 'rgb(75,192,100)',
                            backgroundColor: 'transparent',
                            borderDash: [5, 4],
                            tension: 0.3,
                            pointRadius: 3,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' } },
                    scales: {
                        y: {
                            beginAtZero: false,
                            ticks: {
                                callback: function (v) { return 'Rp ' + v.toLocaleString('id-ID'); }
                            }
                        }
                    }
                }
            });
        })();
    </script>
<?php endif; ?>