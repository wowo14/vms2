<?php
use yii\widgets\DetailView;
?>
<div class="kode-rekening-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'kode:ntext',
            'rekening:ntext',
            'parent',
            'is_active',
            'tahun_anggaran',
            'created_at:ntext',
            'updated_at:ntext',
            'created_by',
            'updated_by',
        ],
    ]) ?>
</div>