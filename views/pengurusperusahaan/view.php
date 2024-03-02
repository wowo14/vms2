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
            ['attribute' => 'created_by', 'value' => $model->usercreated->username ?? ''],
            ['attribute' => 'updated_by', 'value' => $model->userupdated->username ?? ''],
            'user_id',
            ['attribute'=>'penyedia_id','value' => $model->vendor->nama_perusahaan??''],
            'password:ntext',
        ],
    ]) ?>
</div>