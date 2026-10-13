<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Ranking - ' . $model->judul;
$this->params['breadcrumbs'][] = ['label' => 'Ranking Minikompetisi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="minikompetisi-ranking-view">
    <style>
        .rank-1 { background-color: #d4edda !important; } /* Hijau */
        .rank-2 { background-color: #fff3cd !important; } /* Kuning */
        .rank-3 { background-color: #f8d7da !important; } /* Merah */
        .rank-4 { background-color: #e2e3e5 !important; } /* Abu-abu */
        .rank-5 { background-color: #d1ecf1 !important; } /* Biru muda */
        .rank-6 { background-color: #fce4ec !important; } /* Pink */
        .rank-7 { background-color: #e0e0e0 !important; } /* Abu-abu gelap */
        .rank-8 { background-color: #d1c4e9 !important; } /* Ungu muda */
        .rank-9 { background-color: #c8e6c9 !important; } /* Hijau muda */
        .rank-10 { background-color: #ffccbc !important; } /* Oranye */
    </style>

    <div class="card card-outline card-success">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-trophy"></i> <?= Html::encode($this->title) ?>
            </h3>
            <div class="card-tools">
                <?= Html::a('<i class="fas fa-arrow-left"></i> Kembali', ['view','id'=>$model->id], ['class' => 'btn btn-secondary btn-sm']) ?>
                <?= Html::a('<i class="fas fa-file-excel"></i> Export Excel', ['export-excel', 'id' => $model->id], ['class' => 'btn btn-success btn-sm', 'target' => '_blank']) ?>
                <?= Html::a('<i class="fas fa-file-pdf"></i> Export PDF', ['export-pdf', 'id' => $model->id], ['class' => 'btn btn-danger btn-sm', 'target' => '_blank']) ?>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th width="30%">Judul</th>
                            <td><?= Html::encode($model->judul) ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td><?= Yii::$app->formatter->asDate($model->tanggal) ?></td>
                        </tr>
                        <tr>
                            <th>Metode</th>
                            <td><?= Html::encode($model->getMetodeText()) ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <?php if ($model->metode == 2): ?>
                        <table class="table table-sm table-bordered">
                            <tr>
                                <th width="30%">Bobot Harga</th>
                                <td><?= $model->bobot_harga ?>%</td>
                            </tr>
                            <tr>
                                <th>Bobot Kualitas</th>
                                <td><?= $model->bobot_kualitas ?>%</td>
                            </tr>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <hr>

            <h4 class="mb-3">
                <i class="fas fa-list-ol"></i> Ranking Per Item
            </h4>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm">
                    <thead class="thead-white">
                        <tr>
                            <th rowspan="2" width="5%">No</th>
                            <th rowspan="2" width="20%">Nama Produk</th>
                            <!-- <th rowspan="2" width="10%">Qty</th> -->
                            <!-- <th rowspan="2" width="10%">Satuan</th> -->
                            <?php for ($i = 1; $i <= $maxRank; $i++): ?>
                                <?php $colorClass = "rank-" . ($i > 10 ? 10 : $i); ?>
                                <th colspan="2" class="text-center <?= $colorClass ?>">Harga Terbaik <?= $i ?></th>
                            <?php endfor; ?>
                        </tr>
                        <tr>
                            <?php for ($i = 1; $i <= $maxRank; $i++): ?>
                                <?php $colorClass = "rank-" . ($i > 10 ? 10 : $i); ?>
                                <th width="10%" class="<?= $colorClass ?>">Perusahaan</th>
                                <!-- <th width="10%" class="<?= $colorClass ?>">Merek</th> -->
                                <th width="10%" class="<?= $colorClass ?>">Harga</th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($itemRankings as $itemRanking): ?>
                            <?php $item = $itemRanking['item']; ?>
                            <?php $rankings = $itemRanking['rankings']; ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= Html::encode($item->nama_produk) ?></td>
                                <!-- <td class="text-center"><?= $item->qty ?></td> -->
                                <!-- <td><?= Html::encode($item->satuan) ?></td> -->
                                
                                <?php for ($i = 1; $i <= $maxRank; $i++): ?>
                                    <?php $colorClass = "rank-" . ($i > 10 ? 10 : $i); ?>
                                    <?php if (isset($rankings[$i])): ?>
                                        <?php $rank = $rankings[$i]; ?>
                                        <td class="<?= $colorClass ?>"><?= Html::encode($rank['vendor']->nama_vendor ?? '-') ?></td>
                                        <!-- <td class="<?= $colorClass ?>"><?= Html::encode($rank['vendor']->nama_vendor ?? '-') ?></td> -->
                                        <td class="<?= $colorClass ?> text-right">
                                            <?= number_format($rank['harga'], 0, ',', '.') ?>
                                        </td>
                                    <?php else: ?>
                                        <td class="text-muted">-</td>
                                        <!-- <td class="text-muted">-</td> -->
                                        <td class="text-muted text-right">-</td>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
