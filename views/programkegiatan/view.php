<?php
use yii\widgets\DetailView;
?>
<div class="program-kegiatan-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'code:ntext',
            'desc:ntext',
            'parent:ntext',
            'type:ntext',
            'tahun_anggaran',
            'is_active',
            'created_at:ntext',
            'updated_at:ntext',
            'created_by',
            'updated_by',
        ],
    ]) ?>
</div>