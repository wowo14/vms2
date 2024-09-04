<?php
use kartik\grid\GridView;
use yii\helpers\{Html, Url};
$this->title = 'Details';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="details-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'responsiveWrap' => false,
        'pjax' => true,
        'showPageSummary' => true,
        'tableOptions' => ['class' => 'new_expand'],
        'id' => 'details1',
        'columns' => require('_column_details.php'),
    ]);
    ?>
    <div style="font-size: 16px; text-align: right; font-weight: bold;">
    </div>
</div>