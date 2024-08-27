<?php
foreach($params['kategori'] as $key => $value){
    $dashkategori[]=['name'=>$value['kategori_pengadaan'],'y'=>$value['jml']];
}
$dashkategori=json_encode($dashkategori??[],JSON_NUMERIC_CHECK);
$js=<<<JS
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
$this->registerJs($js);
?>
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
<div class="clear-fix"><br></div>