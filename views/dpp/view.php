<?php
use app\models\PaketPengadaan;
use yii\widgets\DetailView;
?>
<div class="row">
    <div class="col-md-6">
        <div class="dpp-view">DPP
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'nomor_dpp',
                    'tanggal_dpp',
                    ['attribute' => 'bidang_bagian', 'value' => fn ($model) => $model->unit->unit ?? ''],
                    'status_review',
                    'is_approved',
                    'nomor_persetujuan',
                    'created_at',
                    'updated_at',
                    ['attribute' => 'created_by', 'value' => fn ($model) => $model->usercreated->username ?? ''],
                    ['attribute' => 'updated_by', 'value' => fn ($model) => $model->userupdated->username ?? ''],
                ],
            ]) ?>
        </div>
    </div>
    <div class="col-md-6">Paket Pengadaan
        <?php echo $this->render('/paketpengadaan/view', ['model' => PaketPengadaan::findOne($model->paket_id)]);
        ?>
    </div>
</div>