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
echo '<tr><th>No</th><th>Kategori</th>';
$years = array_unique(array_column($params['kategori'], 'year'));
sort($years);
foreach ($years as $year) {
    echo '<th>' . $year . '</th>';
}
echo '<th>Total Jumlah</th><th>Total Pagu</th><th>%</th></tr>';
$totalkategori = array_sum(array_column($params['kategori'], 'ammount')) ?? 0;
$categories = [];
foreach ($params['kategori'] as $row) {
    $kategori = $row['kategori_pengadaan'];
    $year = $row['year'];
    if (!isset($categories[$kategori])) {
        $categories[$kategori] = [
            'kategori_pengadaan' => $kategori,
            'total_jml' => 0,
            'total_pagu' => 0,
            'years' => array_fill_keys($years, ['jml' => 0, 'ammount' => 0]),
        ];
    }
    $categories[$kategori]['years'][$year] = [
        'jml' => $row['jml'],
        'ammount' => $row['ammount'],
    ];
    $categories[$kategori]['total_jml'] += $row['jml'];
    $categories[$kategori]['total_pagu'] += $row['ammount'];
}
if ($totalkategori > 0) {
    $no = 1;
    foreach ($categories as $kategori => $data) {
        echo '<tr><td>' . $no++ . '</td>';
        echo '<td>' . $kategori . '</td>';
        foreach ($years as $year) {
            echo '<td>' . $data['years'][$year]['jml'] . '</td>';
        }
        echo '<td>' . $data['total_jml'] . '</td>';
        echo '<td>' . \Yii::$app->formatter->asCurrency($data['total_pagu']) . '</td>';
        echo '<td>' . \Yii::$app->formatter->asPercent($data['total_pagu'] / $totalkategori) . '</td>';
        echo '</tr>';
    }
    echo '<tfoot><tr>';
    echo '<td colspan="' . (count($years) + 2) . '">Total</td>';
    echo '<td>' . array_sum(array_column($categories, 'total_jml')) . '</td>';
    echo '<td>' . \Yii::$app->formatter->asCurrency($totalkategori) . '</td>';
    echo '<td>' . \Yii::$app->formatter->asPercent(1) . '</td>';
    echo '</tr></tfoot>';
}
echo '</table>';
?>
</div>
<div class="col-md-6">
    <div id="dash-kategori"></div>
</div>
</div>
<div class="clear-fix"><br></div>