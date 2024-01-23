<?php
use yii\widgets\DetailView;
?>
<div class="template-checklist-evaluasi-detail-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'header_id',
            'uraian:ntext',
            'created_at:ntext',
            'updated_at:ntext',
            'created_by',
            'updated_by',
        ],
    ]) ?>
</div>