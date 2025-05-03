<?php
use yii\widgets\DetailView;
?>
<div class="attachment-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'name:ntext',
            'uri:ntext',
            'mime:ntext',
            'size',
            'type:ntext',
            'created_at:ntext',
            'updated_at:ntext',
            'jenis_dokumen',
            'updated_by',
            'created_by',
        ],
    ]) ?>
</div>