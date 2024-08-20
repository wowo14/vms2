<?php
$this->registerJsFile('https://code.highcharts.com/highcharts.js',['depends'=>['app\assets\AppAsset']]);
$yearData=json_encode($params['yearData'],JSON_NUMERIC_CHECK);
$years=json_encode($params['years'],JSON_NUMERIC_CHECK);
$texttitle=json_encode("DATA PENGADAAN BARANG/JASA RSUD IBNU SINA KABUPATEN GRESIK PERIODE ".reset($params['years'])." s/d ".end($params['years']),JSON_NUMERIC_CHECK);
foreach($params['metode'] as $key => $value){
    $dashmetode[]=['name'=>$value['metode_pengadaan'],'y'=>$value['jml']];
}
$dashmetode=json_encode($dashmetode??[],JSON_NUMERIC_CHECK);
foreach($params['kategori'] as $key => $value){
    $dashkategori[]=['name'=>$value['kategori_pengadaan'],'y'=>$value['jml']];
}
$dashkategori=json_encode($dashkategori??[],JSON_NUMERIC_CHECK);
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
Highcharts.chart('dash-metode', {
    chart: {
        type: 'pie'
    },
    title: {
        text: 'Metode Pengadaan'
    },
    tooltip: {
        // valueSuffix: '%'
    },
    plotOptions: {
        series: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: [{
                enabled: true,
                distance: 20
            }, {
                enabled: true,
                distance: -40,
                format: '{point.percentage:.1f}%',
                style: {
                    fontSize: '1.2em',
                    textOutline: 'none',
                    opacity: 0.7
                },
                filter: {
                    operator: '>',
                    property: 'percentage',
                    value: 10
                }
            }]
        }
    },
    series: [
        {
            name: 'Jml',
            colorByPoint: true,
            data: $dashmetode,
        }
    ]
});
Highcharts.chart('dash-kategori', {
    chart: {
        type: 'pie'
    },
    title: {
        text: 'Kategori Pengadaan'
    },
    tooltip: {
        // valueSuffix: '%'
    },
    plotOptions: {
        series: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: [{
                enabled: true,
                distance: 20
            }, {
                enabled: true,
                distance: -40,
                format: '{point.percentage:.1f}%',
                style: {
                    fontSize: '1.2em',
                    textOutline: 'none',
                    opacity: 0.7
                },
                filter: {
                    operator: '>',
                    property: 'percentage',
                    value: 10
                }
            }]
        }
    },
    series: [
        {
            name: 'Jml',
            colorByPoint: true,
            data: $dashkategori,
        }
    ]
});
JS;
$this->registerJs($JS);
?>
<figure class="highcharts-figure">
    <div id="dahsboardpertahun"></div>
</figure>
<br>
<div id="dashmetode" class='row'>
<div class="col-md-6">
    Dashboard Metode Pengadaan
<?php
echo '<table class="table table-responsive">';
echo '<tr><th>No</th><th>Metode</th><th>Tahun</th><th>Jumlah</th><th>Pagu</th><th>%</th></tr>';
$totalmetode=array_sum(array_column($params['metode'],'ammount'))??0;
if($totalmetode>0){
foreach($params['metode'] as $key => &$row){
    echo '<tr><td>'.($key+1).'</td>
    <td>'.$row['metode_pengadaan'].'</td>
    <td>'.$row['year'].'</td>
    <td>'.$row['jml'].'</td>
    <td>'.\Yii::$app->formatter->asCurrency($row['ammount']).'</td>
    <td>'.\Yii::$app->formatter->asPercent($row['ammount']/$totalmetode).'</td>
    </tr>';
}
echo '<tfoot><tr>
    <td colspan="4">Total</td>
    <td>'.\Yii::$app->formatter->asCurrency($totalmetode).'</td>
    <td>'.\Yii::$app->formatter->asPercent($totalmetode/$totalmetode).'</td>
    </tr>
    </tfoot>';
}
echo '</table>';
?></div>

<div class="col-md-6">
    <div id="dash-metode"></div>
</div>
</div>
<div class="clear-fix"><br></div>
<div class="row">
<div class="dash-kategori col-md-6">
    Dashboard Kategori Pengadaan
<?php
echo '<table class="table table-responsive">';
echo '<tr><th>No</th><th>Kategori</th><th>Tahun</th><th>Jumlah</th><th>Pagu</th><th>%</th></tr>';
$totalkategori=array_sum(array_column($params['kategori'],'ammount'))??0;
if($totalkategori>0){
foreach($params['kategori'] as $key => $row){
    echo '<tr><td>'.($key+1).'</td>
    <td>'.$row['kategori_pengadaan'].'</td>
    <td>'.$row['year'].'</td>
    <td>'.$row['jml'].'</td>
    <td>'.\Yii::$app->formatter->asCurrency($row['ammount']).'</td>
    <td>'.\Yii::$app->formatter->asPercent($row['ammount']/$totalkategori).'</td>
    </tr>';
}
echo '<tfoot><tr>
    <td colspan="4">Total</td>
    <td>'.\Yii::$app->formatter->asCurrency($totalkategori).'</td>
    <td>'.\Yii::$app->formatter->asPercent($totalkategori/$totalkategori).'</td>
    </tr>
    </tfoot>';
}
echo '</table>';
?>
</div>
<div class="col-md-6">
    <div id="dash-kategori"></div>
</div>
</div>