<?php
use yii\widgets\DetailView;
?>
<div class="template-checklist-evaluasi-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'template:ntext',
            'jenis_evaluasi:ntext',
            'element',
            'created_at:ntext',
            'updated_at:ntext',
            ['attribute' => 'created_by', 'value' => $model->usercreated->username ?? ''],
            ['attribute' => 'updated_by', 'value' => $model->userupdated->username ?? ''],
        ],
    ]) ?>
</div>