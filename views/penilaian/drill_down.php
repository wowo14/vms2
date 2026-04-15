<?php
use yii\helpers\Html;

/* @var $data app\models\PenilaianPenyedia[] */
/* @var $vendor_nama string */
?>

<div class="drill-down-content">
    <div class="alert alert-primary mb-3">
        <i class="fas fa-building mr-2"></i> <strong><?= Html::encode($vendor_nama) ?></strong>
        <span class="float-right badge badge-pill badge-light"><?= count($data) ?> Paket</span>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-bordered table-hover">
            <thead class="bg-light text-center">
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 25%">Paket Pekerjaan</th>
                    <th style="width: 15%">No. Kontrak</th>
                    <th style="width: 15%">Tgl Kontrak</th>
                    <th style="width: 15%">Nilai Kontrak</th>
                    <th style="width: 15%">Skor Akhir</th>
                    <th style="width: 10%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $i => $item): 
                    $details = json_decode($item->details, true);
                    $score_raw = 0;
                    if (is_string($details['nilaiakhir']) && strpos($details['nilaiakhir'], '=') !== false) {
                        $parts = explode('=', $details['nilaiakhir']);
                        $score_raw = (float) trim(end($parts));
                    } else {
                        $score_raw = (float) $details['nilaiakhir'];
                    }
                ?>
                <tr>
                    <td class="text-center"><?= $i + 1 ?></td>
                    <td><?= Html::encode($item->paket_pekerjaan) ?></td>
                    <td class="text-center text-muted small"><?= Html::encode($item->nomor_kontrak) ?></td>
                    <td class="text-center small"><?= date('d/m/Y', strtotime($item->tanggal_kontrak)) ?></td>
                    <td class="text-right">Rp. <?= number_format($item->nilai_kontrak, 0, ',', '.') ?></td>
                    <td class="text-center">
                        <span class="badge badge-<?= $score_raw >= 4 ? 'success' : ($score_raw >= 3 ? 'warning' : 'danger') ?>">
                            <?= Html::encode($details['nilaiakhir']) ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $item->id], [
                            'class' => 'btn btn-xs btn-info',
                            'role' => 'modal-remote',
                            'data-target' => '#ajaxCrudModal2',
                            'title' => 'Detail Penilaian'
                        ]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
