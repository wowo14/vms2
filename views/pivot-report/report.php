<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var $report array */
/** @var $reportConfig array */
/** @var $type string */
/** @var $year string */
/** @var $monthStart int */
/** @var $monthEnd int */

$this->title = 'Laporan: ' . $reportConfig['title'];
?>

<h3><?= Html::encode($this->title) ?></h3>
<p>Tahun: <?= $year ?></p>

<?= GridView::widget([
    'dataProvider' => $report['dataProvider'],
    'showFooter' => true,
    'columns' => $report['columnDefinitions'],
]); ?>