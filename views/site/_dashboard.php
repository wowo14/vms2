<?php
$this->title='Dashboard';
$this->registerJsFile('https://code.highcharts.com/highcharts.js',['depends'=>['app\assets\AppAsset']]);
$yearData=json_encode($params['yearData'],JSON_NUMERIC_CHECK);
$years=json_encode($params['years'],JSON_NUMERIC_CHECK);
$texttitle=json_encode("DATA PENGADAAN BARANG/JASA RSUD IBNU SINA KABUPATEN GRESIK PERIODE ".reset($params['years'])." s/d ".end($params['years']),JSON_NUMERIC_CHECK);
$JS=<<<JS
Highcharts.chart('dahsboardpertahun', {
    chart: {
        type: 'column'
    },
    title: {
        text: $texttitle,
        align: 'center'
    },
    xAxis: {
        categories: $years,
        crosshair: true,
        accessibility: {
            description: 'Year'
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'JUMLAH PAKET'
        }
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [
        {
            name: 'PAKET',
            data: $yearData
        },
    ]
});
JS;
$this->registerJs($JS);
?>
<div class="row">
    <div class="col-md-3">
        <form method="get" action="<?= \yii\helpers\Url::to(['site/dashboard']) ?>">
            <div class="form-group">
                <label>Filter Tahun:</label>
                <select name="year" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Tahun</option>
                    <?php if(isset($params['allYears'])): ?>
                        <?php foreach($params['allYears'] as $y): ?>
                            <option value="<?= $y ?>" <?= (isset($params['selectedYear']) && $y == $params['selectedYear']) ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </form>
    </div>
</div>
<?php /* Separator */ ?>

<div class="row">
    <div class="col-md-6">
        <!-- <div id="bymetode"></div> -->
    </div>
    <div class="col-md-6">
    </div>
</div>
<link href="https://cdn.webdatarocks.com/latest/webdatarocks.min.css" rel="stylesheet"/>
<script src="https://cdn.webdatarocks.com/latest/webdatarocks.toolbar.min.js"></script>
<script src="https://cdn.webdatarocks.com/latest/webdatarocks.js"></script>
<script src="https://cdn.webdatarocks.com/latest/webdatarocks.highcharts.js"></script>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-sm-6 col-12">
            <?= \hail812\adminlte\widgets\InfoBox::widget([
                'text' => 'Paket Pengadaan Selesai',
                'number' =>$params['paketselesai'],
                'icon' => 'far fa-envelope',
            ]) ?>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
            <?= \hail812\adminlte\widgets\InfoBox::widget([
                'text' => 'Paket Belum Selesai',
                'number' => $params['paketbelom'],
                'theme' => 'success',
                'icon' => 'far fa-flag',
            ]) ?>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
            <?= \hail812\adminlte\widgets\InfoBox::widget([
                'text' => 'Total Pagu Pengadaan',
                'number' => \Yii::$app->formatter->asCurrency($params['totalpagu']),
                'theme' => 'gradient-warning',
                'icon' => 'far fa-copy',
            ]) ?>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
            <?= \hail812\adminlte\widgets\InfoBox::widget([
                'text' => 'Total Penyerapan',
                'number' => \Yii::$app->formatter->asCurrency($params['totalpenyerapan']),
                'theme' => 'gradient-secondary',
                'icon' => 'far fa-copy',
            ]) ?>
        </div>
    </div>
</div>
<figure class="highcharts-figure">
    <div id="dahsboardpertahun"></div>
</figure>
<br>
<div class="clear-fix"><br></div>
<?php
include('_dash_by_metode.php');
include('_dash_by_kategori.php');
include('_dash_by_pp.php');
include('_dash_by_admin.php');
include('_dash_by_bidang.php');