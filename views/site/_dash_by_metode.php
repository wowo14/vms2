<?php
foreach($params['metode'] as $key => $value){
    $dashmetode[]=['name'=>$value['metode_pengadaan'],'y'=>$value['jml']];
}
$dashmetode=json_encode($dashmetode??[],JSON_NUMERIC_CHECK);
$js=<<<JS
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
JS;
$this->registerJs($js);
?>
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