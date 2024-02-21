<?php
use yii\widgets\DetailView;
?>
<div class="pengurusperusahaan-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nama:ntext',
            'nik:ntext',
            'alamat:ntext',
            'email:ntext',
            'telepon:ntext',
            'nip:ntext',
            'jabatan:ntext',
            'instansi:ntext',
            'unit',
            'is_vendor',
            'is_internal',
            'is_active',
            'created_at:ntext',
            'updated_at:ntext',
            'created_by',
            'updated_by',
            'user_id',
            'penyedia_id',
            'password:ntext',
        ],
    ]) ?>
</div>