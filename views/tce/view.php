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
            'created_at:ntext',
            'updated_at:ntext',
            'created_by',
            'updated_by',
        ],
    ]) ?>
</div>