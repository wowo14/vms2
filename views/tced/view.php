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
            ['attribute' => 'created_by', 'value' => $model->usercreated->username ?? ''],
            ['attribute' => 'updated_by', 'value' => $model->userupdated->username ?? ''],
        ],
    ]) ?>
</div>