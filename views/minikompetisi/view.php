<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Modal;

$this->title = $model->judul;
$this->params['breadcrumbs'][] = ['label' => 'Minikompetisi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="minikompetisi-view">

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
                            'data' => [
                                'confirm' => 'Hapus paket ini beserta semua penawarannya?',
                                'method' => 'post',
                            ],
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
                            [
                                'attribute' => 'metode',
                                'value' => $model->getMetodeText(),
                            ],
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
                            <?php foreach($vendors as $v): ?>
                                <option value="<?= $v->id ?>"><?= Html::encode($v->nama_vendor) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>File Excel Penawaran</label>
                        <input type="file" name="file_excel" class="form-control" accept=".xlsx, .xls" required>
                    </div>

                    <button class="btn btn-success btn-block" type="submit"><i class="fas fa-upload"></i> Proses & Hitung Skor</button>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <!-- ITEMS DAN HASIL -->
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
                            <?php $no=1; foreach($items as $item): ?>
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

            <!-- HASIL RANKING -->
            <div class="card card-outline card-danger mt-3">
                <div class="card-header">
                    <h3 class="card-title">Hasil Konsolidasi & Ranking</h3>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-bordered table-sm table-striped">
                        <thead class="bg-secondary">
                            <tr>
                                <th>Rank</th>
                                <th>Vendor</th>
                                <th>Total Harga</th>
                                <th>Total Skor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($penawarans)): ?>
                                <tr><td colspan="5" class="text-center">Belum ada penawaran diproses</td></tr>
                            <?php else: ?>
                                <?php foreach($penawarans as $p): ?>
                                <tr class="<?= $p->is_winner ? 'table-warning' : '' ?>">
                                    <td><h3><?= $p->ranking ?></h3></td>
                                    <td><?= Html::encode($p->vendor->nama_vendor) ?></td>
                                    <td><?= Yii::$app->formatter->asCurrency($p->total_harga) ?></td>
                                    <td><?= round($p->total_skor_akhir, 2) ?></td>
                                    <td>
                                        <?php if($p->is_winner): ?>
                                            <span class="badge badge-success"><i class="fas fa-trophy"></i> Kandidat Terpilih</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CHART COMPARISON -->
            <div class="card card-outline card-warning mt-3">
                <div class="card-header">
                    <h3 class="card-title">Grafik Perbandingan Harga vs HPS</h3>
                </div>
                <div class="card-body">
                    <canvas id="komparasiChart" style="height: 250px;"></canvas>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php
$hps_total = 0;
$existing_total = 0;
foreach($items as $it) {
    if($it->harga_hps) $hps_total += ($it->harga_hps * $it->qty);
    if($it->harga_existing) $existing_total += ($it->harga_existing * $it->qty);
}

$labels = ['HPS', 'Harga Beli Existing'];
$data_values = [$hps_total, $existing_total];
$colors = ['rgba(54, 162, 235, 0.5)', 'rgba(255, 99, 132, 0.5)'];
$borders = ['rgb(54, 162, 235)', 'rgb(255, 99, 132)'];

foreach($penawarans as $p) {
    $labels[] = $p->vendor->nama_vendor;
    $data_values[] = $p->total_harga;
    $colors[] = $p->is_winner ? 'rgba(255, 193, 7, 0.6)' : 'rgba(75, 192, 192, 0.5)';
    $borders[] = $p->is_winner ? 'rgb(255, 193, 7)' : 'rgb(75, 192, 192)';
}

$labels_json = json_encode($labels);
$data_json = json_encode($data_values);
$colors_json = json_encode($colors);
$borders_json = json_encode($borders);

$script = <<<JS
$(function() {
    var ctx = document.getElementById('komparasiChart').getContext('2d');
    var komparasiChart = new Chart(ctx, {
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
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
JS;
$this->registerJs($script);
?>
