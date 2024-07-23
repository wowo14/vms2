<?php
use app\widgets\FilePreview;
use yii\helpers\Html;
use yii\widgets\DetailView;
?>
<div class="histori-reject-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'id',
            'nomor',
            'nama_paket',
            'alasan_reject:ntext',
            'tanggal_reject',
            'kesimpulan:ntext',
            'tanggal_dikembalikan',
            'tanggapan_ppk:ntext',
            'created_at',
            'file_tanggapan',
        ],
    ]) ?>
</div>
<div class="filepreview col-md-6">
        File Preview:
    <?php echo $model->file_tanggapan ? Html::a(
        FilePreview::widget([
            'model' => $model,
            'attribute' => 'file_tanggapan',
        ]),
        \Yii::getAlias('@web/uploads/') . $model->file_tanggapan,
        ['target' => '_blank']
    ) : '';
    ?>
</div>