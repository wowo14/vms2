<?php

use yii\grid\GridView;

/** @var $reports array */
/** @var $pivotConfigs array */
/** @var $year string */
/** @var $months array */

foreach ($reports as $key => $report):
    $title = $pivotConfigs[$key]['title'] ?? $key;
?>
    <div class="section-header"><?= $title ?></div>
    <?= GridView::widget([
        'dataProvider' => $report['dataProvider'],
        'showFooter' => true,
        'columns' => $report['columnDefinitions'],
    ]); ?>
    <div class="page-break"></div>
<?php endforeach; ?>