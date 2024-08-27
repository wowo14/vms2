<?php
foreach($params['bypp'] as $key => $value){
    $dashbypp[]=['name'=>$value['pejabat_pengadaan'],'y'=>$value['jml']];
}
$dashbypp=json_encode($dashbypp??[],JSON_NUMERIC_CHECK);
$js=<<<JS
Highcharts.chart('dash-bypp', {
    chart: {
        type: 'pie'
    },
    title: {
        text: 'Pejabat Pengadaan'
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
            data: $dashbypp,
        }
    ]
});
JS;
$this->registerJs($js);
?>
<div class="row">
<div class="dash-bypp col-md-6">
    Dashboard Pejabat Pengadaan
<?php
echo '<table class="table table-responsive">';
echo '<tr><th>No</th><th>Pejabat Pengadaan</th><th>Tahun</th><th>Jumlah</th></tr>';
$totalbypp=array_sum(array_column($params['bypp'],'jml'))??0;
if($totalbypp>0){
foreach($params['bypp'] as $key => $row){
    echo '<tr><td>'.($key+1).'</td>
    <td>'.$row['pejabat_pengadaan'].'</td>
    <td>'.$row['year'].'</td>
    <td>'.$row['jml'].'</td>
    </tr>';
}
echo '<tfoot><tr>
    <td colspan="3">Total</td>
    <td>'.($totalbypp).'</td>
    </tr>
    </tfoot>';
}
echo '</table>';
?>
</div>
<div class="col-md-6">
    <div id="dash-bypp"></div>
</div>
</div>
<div class="clear-fix"><br></div>