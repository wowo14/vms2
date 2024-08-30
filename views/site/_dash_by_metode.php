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
echo '<tr><th>No</th><th>Metode</th>';
// Dynamically generate year columns
$years = array_unique(array_column($params['metode'], 'year'));
sort($years);
foreach ($years as $year) {
    echo '<th>' . $year . '</th>';
    // echo '<th>Pagu ' . $year . '</th>';
}
echo '<th>Total Jumlah</th><th>Total Pagu</th><th>%</th></tr>';
$totalmetode = array_sum(array_column($params['metode'], 'ammount')) ?? 0;
$methods = [];
foreach ($params['metode'] as $row) {
    $metode = $row['metode_pengadaan'];
    $year = $row['year'];
    if (!isset($methods[$metode])) {
        $methods[$metode] = [
            'metode_pengadaan' => $metode,
            'total_jml' => 0,
            'total_pagu' => 0,
            'years' => array_fill_keys($years, ['jml' => 0, 'ammount' => 0]),
        ];
    }
    $methods[$metode]['years'][$year] = [
        'jml' => $row['jml'],
        'ammount' => $row['ammount'],
    ];
    $methods[$metode]['total_jml'] += $row['jml'];
    $methods[$metode]['total_pagu'] += $row['ammount'];
}
if ($totalmetode > 0) {
    $no = 1;
    foreach ($methods as $metode => $data) {
        echo '<tr><td>' . $no++ . '</td>';
        echo '<td>' . $metode . '</td>';
        foreach ($years as $year) {
            echo '<td>' . $data['years'][$year]['jml'] . '</td>';
            // echo '<td>' . \Yii::$app->formatter->asCurrency($data['years'][$year]['ammount']) . '</td>';
        }
        echo '<td>' . $data['total_jml'] . '</td>';
        echo '<td>' . \Yii::$app->formatter->asCurrency($data['total_pagu']) . '</td>';
        echo '<td>' . \Yii::$app->formatter->asPercent($data['total_pagu'] / $totalmetode) . '</td>';
        echo '</tr>';
    }
    echo '<tfoot><tr>';
    echo '<td colspan="' . (count($years) + 2) . '">Total</td>';
    echo '<td>' . array_sum(array_column($methods, 'total_jml')) . '</td>';
    echo '<td>' . \Yii::$app->formatter->asCurrency($totalmetode) . '</td>';
    echo '<td>' . \Yii::$app->formatter->asPercent(1) . '</td>';
    echo '</tr></tfoot>';
}
echo '</table>';
?>
</div>
<div class="col-md-6">
    <div id="dash-metode"></div>
</div>
</div>
<div class="clear-fix"><br></div>