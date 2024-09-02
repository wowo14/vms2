<?php
$this->title='Dashboard';
$this->registerJsFile('https://code.highcharts.com/highcharts.js',['depends'=>['app\assets\AppAsset']]);
$yearData=json_encode($params['yearData'],JSON_NUMERIC_CHECK);
$years=json_encode($params['years'],JSON_NUMERIC_CHECK);
$bypp=json_encode($params['metode'],JSON_NUMERIC_CHECK);
$texttitle=json_encode("DATA PENGADAAN BARANG/JASA RSUD IBNU SINA KABUPATEN GRESIK PERIODE ".reset($params['years'])." s/d ".end($params['years']),JSON_NUMERIC_CHECK);
$JS=<<<JS

// const pivot = new WebDataRocks({
//   container: "#bymetode",
//   toolbar: false,
//   report: {
//     dataSource: {
//       data: $bypp
//     },
//     "slice": {
//         "rows": [
//             {
//                 "uniqueName": "metode_pengadaan"
//             }
//         ],
//         "columns": [
//             {
//                 "uniqueName": "year"
//             },
//             {
//                 "uniqueName": "Measures"
//             }
//         ],
//         "measures": [
//             {
//                 "uniqueName": "jml",
//                 "aggregation": "sum"
//             },
//             {
//                 "uniqueName": "ammount",
//                 "aggregation": "sum",
//                 "format": "64dhbcr6"
//             }
//         ]
//     },
//     "formats": [
//         {
//             "name": "64dhbcr6",
//             "thousandsSeparator": ".",
//             "decimalSeparator": ",",
//             "currencySymbol": "Rp. ",
//             "currencySymbolAlign": "left",
//             "nullValue": "",
//             "textAlign": "right",
//             "isPercent": false
//         }
//     ],
//   }
// });

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
        <div class="col-md-4 col-sm-6 col-12">
            <?= \hail812\adminlte\widgets\InfoBox::widget([
                'text' => 'Paket Pengadaan Selesai',
                'number' =>$params['paketselesai'],
                'icon' => 'far fa-envelope',
            ]) ?>
        </div>
        <div class="col-md-4 col-sm-6 col-12">
            <?= \hail812\adminlte\widgets\InfoBox::widget([
                'text' => 'Paket Pengadaan Belum Selesai',
                'number' => $params['paketbelom'],
                'theme' => 'success',
                'icon' => 'far fa-flag',
            ]) ?>
        </div>
        <div class="col-md-4 col-sm-6 col-12">
            <?= \hail812\adminlte\widgets\InfoBox::widget([
                'text' => 'Total Pagu Pengadaan',
                'number' => \Yii::$app->formatter->asCurrency($params['totalpagu']),
                'theme' => 'gradient-warning',
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