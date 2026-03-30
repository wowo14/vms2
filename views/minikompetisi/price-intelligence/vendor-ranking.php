<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Vendor Competitiveness Ranking';
$this->params['breadcrumbs'][] = ['label' => 'Minikompetisi', 'url' => ['/minikompetisi/index']];
$this->params['breadcrumbs'][] = ['label' => 'Price Intelligence', 'url' => ['price-intelligence']];
$this->params['breadcrumbs'][] = 'Vendor Ranking';

$fmt = fn($n) => Yii::$app->formatter->asCurrency($n);
?>

<div class="d-flex align-items-center mb-3" style="gap:12px;">
    <div>
        <h3 class="mb-0"><i class="fas fa-trophy text-warning mr-2"></i>Vendor Competitiveness Ranking</h3>
        <small class="text-muted">Peringkat vendor berdasarkan win rate, harga, dan rasio vs HPS</small>
    </div>
    <div class="ml-auto d-flex align-items-center" style="gap:8px;">
        <label class="mb-0 text-muted" style="font-size:13px;">Tahun:</label>
        <select onchange="window.location=this.value" class="form-control form-control-sm" style="width:auto;">
            <?php foreach ($years as $yr): ?>
                <option value="<?= Url::to(['vendor-ranking', 'year' => $yr]) ?>" <?= $yr == $fiscalYear ? 'selected' : '' ?>>
                    <?= $yr ?>
                </option>
            <?php endforeach; ?>
            <option value="<?= Url::to(['vendor-ranking']) ?>" <?= empty($years) ? 'selected' : '' ?>>Semua Tahun
            </option>
        </select>
        <?= Html::a('<i class="fas fa-chart-line mr-1"></i>Price Intelligence', ['price-intelligence'], ['class' => 'btn btn-outline-primary btn-sm']) ?>
    </div>
</div>

<div class="card" style="border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.08);border:none;">
    <div class="card-body p-0">
        <?php if (empty($leaderboard)): ?>
            <div class="py-5 text-center text-muted">
                <i class="fas fa-database fa-3x mb-3 d-block" style="opacity:.3;"></i>
                <p>Belum ada data vendor price index.<br>
                    Data akan terisi setelah vendor upload penawaran dan analitik diproses.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:13px;">
                    <thead class="thead-dark">
                        <tr>
                            <th width="50" class="text-center">Rank</th>
                            <th>Vendor</th>
                            <th class="text-right">Total Bids</th>
                            <th class="text-right">Wins</th>
                            <th class="text-right">Win Rate</th>
                            <th class="text-right">Rata-rata Harga</th>
                            <th class="text-right">Rasio vs HPS</th>
                            <th class="text-center">Skor Kompetitif</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leaderboard as $i => $vpi): ?>
                            <?php
                            $rank = $i + 1;
                            $score = (float) $vpi->competitiveness_score;
                            $scoreColor = $score >= 70 ? 'success' : ($score >= 40 ? 'warning' : 'danger');
                            $ratio = (float) $vpi->avg_price_vs_hps;
                            $ratioColor = $ratio > 0 && $ratio < 1 ? 'text-success' : ($ratio >= 1 ? 'text-danger' : 'text-muted');
                            ?>
                            <tr>
                                <td class="text-center">
                                    <?php if ($rank === 1): ?>
                                        <span style="font-size:18px;">🥇</span>
                                    <?php elseif ($rank === 2): ?>
                                        <span style="font-size:18px;">🥈</span>
                                    <?php elseif ($rank === 3): ?>
                                        <span style="font-size:18px;">🥉</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">#
                                            <?= $rank ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong>
                                        <?= Html::encode($vpi->vendor ? $vpi->vendor->nama_vendor : 'Vendor #' . $vpi->vendor_id) ?>
                                    </strong>
                                </td>
                                <td class="text-right">
                                    <?= number_format($vpi->total_bids) ?>
                                </td>
                                <td class="text-right text-success font-weight-bold">
                                    <?= number_format($vpi->total_wins) ?>
                                </td>
                                <td class="text-right">
                                    <span class="badge badge-<?= (float) $vpi->win_rate >= 50 ? 'success' : 'secondary' ?>">
                                        <?= number_format($vpi->win_rate, 1) ?>%
                                    </span>
                                </td>
                                <td class="text-right">
                                    <?= $fmt($vpi->avg_price) ?>
                                </td>
                                <td class="text-right <?= $ratioColor ?>">
                                    <?= $ratio > 0 ? number_format($ratio * 100, 1) . '%' : '-' ?>
                                    <?php if ($ratio > 0 && $ratio < 1): ?>
                                        <i class="fas fa-arrow-down" title="Di bawah HPS"></i>
                                    <?php elseif ($ratio >= 1): ?>
                                        <i class="fas fa-arrow-up" title="Di atas HPS"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="progress" style="height:18px;border-radius:9px;"
                                        title="<?= round($score, 1) ?>/100">
                                        <div class="progress-bar bg-<?= $scoreColor ?>"
                                            style="width:<?= min(100, $score) ?>%;font-size:11px;font-weight:700;line-height:18px;">
                                            <?= round($score, 1) ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="mt-3 alert alert-light border" style="font-size:12px;">
    <strong>Cara membaca skor:</strong>
    Skor Kompetitif (0–100) menggabungkan: <em>Win Rate (40%)</em> +
    <em>Harga di bawah HPS (40%)</em> + <em>basis (20%)</em>.
    Semakin tinggi = semakin kompetitif. Win Rate = persentase menang dari semua pengajuan penawaran.
    Rasio vs HPS &lt;100% = rata-rata harga di bawah estimasi pemilik pekerjaan.
</div>