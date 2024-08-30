<?php
foreach($params['bybidang'] as $key => $value){
    $dashbybidang[]=['name'=>$value['bidang_bagian'],'y'=>$value['jml']];
}
$dashbybidang=json_encode($dashbybidang??[],JSON_NUMERIC_CHECK);
$js=<<<JS
Highcharts.chart('dash-bybidang', {
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
            data: $dashbybidang,
        }
    ]
});
JS;
$this->registerJs($js);
?>
<div class="row">
<div class="dash-bybidang col-md-6">
    Dashboard Bidang / Bagian
<?php
use app\widgets\PivotReport;
$data = [
    ['year' => '2022', 'category' => 'Electronics','qty'=>20, 'sales' => 1000],
    ['year' => '2022', 'category' => 'Clothing', 'qty'=>22,'sales' => 1500],
    ['year' => '2023', 'category' => 'Electronics', 'qty'=>2,'sales' => 2000],
    ['year' => '2023', 'category' => 'Clothing', 'qty'=>4,'sales' => 2500],
    ['year' => '2024', 'category' => 'Electronics','qty'=>3, 'sales' => 3000],
    ['year' => '2024', 'category' => 'Clothing','qty'=>5, 'sales' => 3500],
];
$xColName = 'year'; // X-axis will be 'year'
$yColName = 'bidang_bagian'; // Y-axis will be 'category'
$totalColName = 'jml'; // The value to aggregate will be 'sales'
$pivotReport = new PivotReport($xColName, $yColName, $totalColName, $params['bybidang']);
// $pivotReport = new PivotReport($xColName, $yColName, $totalColName, $data);
$options = ['class' => 'pivot-table']; // You can add a class for styling
$pivotReport->generateHtml($options);
/*$years = array_unique(array_column($params['bybidang'], 'year'));
sort($years); // Sort years to display them in order
$pivotData = [];
foreach ($params['bybidang'] as $row) {
    $bidang = $row['bidang_bagian'];
    $year = $row['year'];
    $ammount=$row['ammount'];
    $jml = $row['jml'];
    if (!isset($pivotData[$bidang])) {
        $pivotData[$bidang] = ['bidang_bagian' => $bidang];
    }
    $pivotData[$bidang][$year] = $jml;
}
$totalByBidang = array_sum(array_column($params['bybidang'], 'jml')) ?? 0;
$totalAmmount = array_sum(array_column($params['bybidang'], 'ammount')) ?? 0;
echo '<table class="table table-responsive">';
echo '<tr><th>No</th><th>Bidang / Bagian</th>';
// Create table headers for each year
foreach ($years as $year) {
    echo '<th>'.$year.'</th>';
}
echo '<th>Total</th>';
echo '<th>Total Pagu</th></tr>';
if ($totalByBidang > 0) {
    $key = 0;
    foreach ($pivotData as $bidang => $data) {
        $key++;
        echo '<tr><td>'.$key.'</td>';
        echo '<td>'.$bidang.'</td>';
        $rowTotal = 0;
        foreach ($years as $year) {
            $jml = $data[$year] ?? 0;
            echo '<td>'.$jml.'</td>';
            $rowTotal += $jml;
        }
        echo '<td>'.$rowTotal.'</td>
        <td>'.$ammount.'</tr>';
    }
    // Footer with total sums
    echo '<tfoot><tr>
        <td colspan="2">Total</td>';
    foreach ($years as $year) {
        $yearTotal = array_sum(array_column($params['bybidang'], 'jml', $year));
        echo '<td>'.$yearTotal.'</td>';
    }
    echo '<td>'.($totalByBidang).'</td>';
    echo '<td>'.($totalAmmount).'</td>';
    echo '</tr></tfoot>';
}
echo '</table>';
*/
?>
</div>
<div class="col-md-6">
    <div id="dash-bybidang"></div>
</div>
</div>
<div class="clear-fix"><br></div>