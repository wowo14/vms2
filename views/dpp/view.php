<?php
use app\models\PaketPengadaan;
use yii\widgets\DetailView;
?>
<div class="row clear-fix">
    <div class="col-md-4">
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
                    'kode',
                    'created_at',
                    'updated_at',
                    ['attribute' => 'created_by', 'value' => fn ($model) => $model->usercreated->username ?? ''],
                    ['attribute' => 'updated_by', 'value' => fn ($model) => $model->userupdated->username ?? ''],
                ],
            ]) ?>
        </div>
    </div>
    <div class="col-md-8">Paket Pengadaan
        <?php echo $this->render('/paketpengadaan/view', ['model' => PaketPengadaan::findOne($model->paket_id)]);
        ?>
    </div>
</div>