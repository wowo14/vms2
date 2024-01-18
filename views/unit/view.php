<?php
use yii\widgets\DetailView;
?>
<div class="unit-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'kode:ntext',
            'unit:ntext',
            'fk_instalasi:ntext',
            'is_vip',
            'aktif',
            'logo:ntext',
        ],
    ]) ?>
</div>