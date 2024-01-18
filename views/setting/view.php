<?php
use yii\widgets\DetailView;
?>
<div class="setting-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'type',
            'param',
            'value',
            'active',
        ],
    ]) ?>
</div>
