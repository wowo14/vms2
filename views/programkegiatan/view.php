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
            ['attribute' => 'created_by', 'value' => $model->usercreated->username ?? ''],
            ['attribute' => 'updated_by', 'value' => $model->userupdated->username ?? ''],
        ],
    ]) ?>
</div>