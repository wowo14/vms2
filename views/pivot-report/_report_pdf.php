<?php

use yii\grid\GridView;

/** @var $report array */
/** @var $reportConfig array */
/** @var $type string */
/** @var $year string */
/** @var $months array */

?>

<div class="report-title">Laporan: <?= $reportConfig['title'] ?></div>
<div class="report-date">Tanggal Cetak: <?= date('d-m-Y') ?></div>

<?= GridView::widget([
    'dataProvider' => $report['dataProvider'],
    'showFooter' => true,
    'columns' => $report['columnDefinitions'],
]); ?>