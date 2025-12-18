<?php
use kartik\grid\GridView;
use yii\helpers\Html;

// Register Highcharts JS like dashboard
$this->registerJsFile('https://code.highcharts.com/highcharts.js', ['depends' => ['app\assets\AppAsset']]);

// Prepare data for charts
$topByCount = collect($rows)
    ->sortByDesc('jumlah_kontrak')
    ->take(10)
    ->values()
    ->all();

$topByValue = collect($rows)
    ->sortByDesc('total_nilai_kontrak')
    ->take(10)
    ->values()
    ->all();

// Prepare categories and data for chart 1
$categoriesCount = array_map(function($item) {
    return substr($item['nama_penyedia'], 0, 30) . (strlen($item['nama_penyedia']) > 30 ? '...' : '');
}, $topByCount);

$dataCount = array_map(function($item) {
    return (int)$item['jumlah_kontrak'];
}, $topByCount);

// Prepare categories and data for chart 2
$categoriesValue = array_map(function($item) {
    return substr($item['nama_penyedia'], 0, 30) . (strlen($item['nama_penyedia']) > 30 ? '...' : '');
}, $topByValue);

$dataValue = array_map(function($item) {
    return (float)$item['total_nilai_kontrak'];
}, $topByValue);

// Prepare data for pie chart
$scoreRanges = [
    'Sangat Baik (85-100)' => 0,
    'Baik (70-84)' => 0,
    'Cukup (55-69)' => 0,
    'Kurang (<55)' => 0,
];

foreach ($rows as $row) {
    $score = $row['rata_nilai_evaluasi'];
    if ($score >= 85) {
        $scoreRanges['Sangat Baik (85-100)']++;
    } elseif ($score >= 70) {
        $scoreRanges['Baik (70-84)']++;
    } elseif ($score >= 55) {
        $scoreRanges['Cukup (55-69)']++;
    } else {
        $scoreRanges['Kurang (<55)']++;
    }
}

$pieData = [];
foreach ($scoreRanges as $label => $count) {
    if ($count > 0) {
        $pieData[] = ['name' => $label, 'y' => $count];
    }
}

// Encode data for JS
$categoriesCountJson = json_encode($categoriesCount, JSON_NUMERIC_CHECK);
$dataCountJson = json_encode($dataCount, JSON_NUMERIC_CHECK);
$categoriesValueJson = json_encode($categoriesValue, JSON_NUMERIC_CHECK);
$dataValueJson = json_encode($dataValue, JSON_NUMERIC_CHECK);
$pieDataJson = json_encode($pieData, JSON_NUMERIC_CHECK);

// Register JS for charts
$js = <<<JS
// Chart 1: Top 10 by Contract Count
Highcharts.chart('chartContractCount', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Top 10 Penyedia Berdasarkan Jumlah Kontrak',
        align: 'center'
    },
    xAxis: {
        categories: $categoriesCountJson,
        labels: {
            rotation: -45,
            style: {
                fontSize: '11px'
            }
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Jumlah Kontrak'
        }
    },
    legend: {
        enabled: false
    },
    tooltip: {
        pointFormat: 'Jumlah Kontrak: <b>{point.y}</b>'
    },
    series: [{
        name: 'Jumlah Kontrak',
        data: $dataCountJson,
        color: '#3498db'
    }],
    credits: {
        enabled: false
    }
});

// Chart 2: Top 10 by Total Value
Highcharts.chart('chartContractValue', {
    chart: {
        type: 'bar'
    },
    title: {
        text: 'Top 10 Penyedia Berdasarkan Total Nilai Kontrak',
        align: 'center'
    },
    xAxis: {
        categories: $categoriesValueJson,
        labels: {
            style: {
                fontSize: '11px'
            }
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Total Nilai Kontrak (Rp)'
        }
    },
    legend: {
        enabled: false
    },
    tooltip: {
        pointFormat: 'Total Nilai: <b>Rp {point.y:,.0f}</b>'
    },
    series: [{
        name: 'Total Nilai Kontrak',
        data: $dataValueJson,
        color: '#2ecc71'
    }],
    credits: {
        enabled: false
    }
});

// Chart 3: Evaluation Score Distribution
Highcharts.chart('chartEvaluationDistribution', {
    chart: {
        type: 'pie'
    },
    title: {
        text: 'Distribusi Nilai Evaluasi Penyedia',
        align: 'center'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b> ({point.y} penyedia)'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f}%'
            }
        }
    },
    series: [{
        name: 'Jumlah Penyedia',
        colorByPoint: true,
        data: $pieDataJson
    }],
    credits: {
        enabled: false
    }
});
JS;

$this->registerJs($js);
?>

<div class="statistik-penyedia">
    <div class="text-center mb-4">
        <h3><?= Html::encode($title) ?></h3>
    </div>

    <!-- GridView -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn', 'header' => 'No'],
            [
                'attribute' => 'nama_penyedia', 
                'label' => 'Nama Penyedia',
                'width' => '200px'
            ],
            [
                'attribute' => 'alamat', 
                'label' => 'Alamat',
                'width' => '180px'
            ],
            [
                'attribute' => 'unit_bidang', 
                'label' => 'Unit/Bidang Pemesan',
                'width' => '150px'
            ],
            [
                'attribute' => 'metode', 
                'label' => 'Metode Pengadaan',
                'width' => '120px'
            ],
            [
                'attribute' => 'jumlah_kontrak', 
                'label' => 'Jumlah Kontrak',
                'hAlign' => 'center',
                'width' => '100px'
            ],
            [
                'attribute' => 'total_nilai_kontrak', 
                'label' => 'Total Nilai Kontrak',
                'format' => ['decimal', 0],
                'hAlign' => 'right',
                'width' => '150px'
            ],
            [
                'attribute' => 'rata_nilai_evaluasi', 
                'label' => 'Nilai Evaluasi',
                'hAlign' => 'center',
                'width' => '100px',
                'value' => function($model) {
                    return $model['rata_nilai_evaluasi'] . '/100';
                }
            ],
            [
                'attribute' => 'ppk', 
                'label' => 'PPK',
                'width' => '180px'
            ],
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => false,
        ],
        'toolbar' =>  [
            '{export}',
            '{toggleData}'
        ],
        'export' => [
            'fontAwesome' => true
        ],
    ]); ?>

    <!-- Charts Section -->
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div id="chartContractCount" style="height: 400px;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div id="chartContractValue" style="height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div id="chartEvaluationDistribution" style="height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
