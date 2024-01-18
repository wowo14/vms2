<?php
use yii\widgets\DetailView;
?>
<div class="draft-usulan-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'tahun_anggaran',
            'unit_id',
            'created_by',
            'updated_by',
            'created_at:ntext',
            'updated_at:ntext',
        ],
    ]) ?>
</div>