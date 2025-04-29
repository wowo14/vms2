<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var $reports array */
/** @var $months array */
/** @var $year string */
/** @var $monthStart int */
/** @var $monthEnd int */

$this->title = 'Dashboard Laporan Pengadaan';
?>

<h2><?= Html::encode($this->title) ?></h2>
<p>Tahun: <?= $year ?>, Bulan: <?= $monthStart ?> - <?= $monthEnd ?></p>

<?php foreach ($reports as $key => $report): ?>
    <div class="section-header"><?= Html::encode($key) ?></div>
    <?= GridView::widget([
        'dataProvider' => $report['dataProvider'],
        'showFooter' => true,
        'columns' => $report['columnDefinitions'],
    ]); ?>
    <hr>
<?php endforeach; ?>