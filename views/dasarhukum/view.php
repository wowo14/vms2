<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
?>
<div class="galery-dasarhukum-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'id',
            'judul:ntext',
            'summary:ntext',
            ['attribute'=>'foto','format'=>'raw','value'=>Html::a(Html::img($model->foto, ['width' => '100px']), $model->file_pdf, ['target' => '_blank','data-pjax' => 0])],
            ['attribute'=>'file_pdf','format'=>'raw','value'=>Html::a($model->file_pdf, $model->file_pdf, ['target' => '_blank','data-pjax' => 0])],
            'tags:ntext',
            'is_active',
            'created_at:ntext',
            'updated_at:ntext',
            'created_by',
            'updated_by',
            'kategori:ntext',
            'nomor:ntext',
            'tanggal_ditetapkan:ntext',
            'penerbit:ntext',
        ],
    ]) ?>
</div>