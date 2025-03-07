<?php

foreach ($params['byadmin'] as $row) {
    $admin = $row['admin_pengadaan'];
    if (!isset($dashbyadmin[$admin])) {
        $dashbyadmin[$admin] = [
            "name" => $admin,
            "y" => 0
        ];
    }
    $dashbyadmin[$admin]["y"] += $row['jml'];
}
$dashbyadmin = array_values($dashbyadmin);
$dashbyadmin=json_encode($dashbyadmin??[],JSON_NUMERIC_CHECK);
$js=<<<JS
Highcharts.chart('dash-byadmin', {
    chart: {
        type: 'pie'
    },
    title: {
        text: 'Admin Pengadaan'
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
            data: $dashbyadmin,
        }
    ]
});
JS;
$this->registerJs($js);
?>
<div class="row">
<div class="dash-byadmin col-md-6">
    Dashboard Admin Pengadaan
<?php
use app\widgets\PivotReport;
$titles='Admin Pengadaan';
$xColName = 'year'; // X-axis will be 'year'
$yColName = 'admin_pengadaan'; // Y-axis will be 'category'
$totalColName ='jml'; // The value to aggregate will be 'sales'
$pivotReport = new PivotReport($titles,$xColName, $yColName, $totalColName, $params['byadmin']);
$options = ['class' => 'table table-responsive'];
$pivotReport->generateHtml($options);
// echo '<table class="table table-responsive">';
// echo '<tr><th>No</th><th>Admin Pengadaan</th><th>Tahun</th><th>Jumlah</th></tr>';
// $totalbyadmin=array_sum(array_column($params['byadmin'],'jml'))??0;
// if($totalbyadmin>0){
// foreach($params['byadmin'] as $key => $row){
//     echo '<tr><td>'.($key+1).'</td>
//     <td>'.$row['admin_pengadaan'].'</td>
//     <td>'.$row['year'].'</td>
//     <td>'.$row['jml'].'</td>
//     </tr>';
// }
// echo '<tfoot><tr>
//     <td colspan="3">Total</td>
//     <td>'.($totalbyadmin).'</td>
//     </tr>
//     </tfoot>';
// }
// echo '</table>';
?>
</div>
<div class="col-md-6">
    <div id="dash-byadmin"></div>
</div>
</div>
<div class="clear-fix"><br></div>