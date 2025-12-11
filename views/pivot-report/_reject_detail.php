<?php
use yii\helpers\Html;
use app\models\HistoriReject;

/* @var $model array */
// $model is the row from ArrayDataProvider, so it's an array, not an object.
$paketId = $model['id'];

$history = HistoriReject::find()
    ->where(['paket_id' => $paketId])
    ->orderBy(['created_at' => SORT_DESC])
    ->all();
?>

<div class="reject-detail-view" style="padding: 10px; background-color: #f8f9fa;">
    <h5>Riwayat Penolakan / Koreksi</h5>
    <?php if (empty($history)): ?>
        <p class="text-muted">Tidak ada data riwayat detail (hanya status saat ini).</p>
    <?php else: ?>
        <table class="table table-bordered table-sm table-striped bg-white">
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 15%">Tanggal Reject</th>
                    <th style="width: 30%">Alasan / Detail Koreksi</th>
                    <th style="width: 30%">Tanggapan PPK</th>
                    <th style="width: 20%">User (Role)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $idx => $hist): ?>
                    <tr>
                        <td><?= $idx + 1 ?></td>
                        <td>
                            <?= $hist->tanggal_reject ? Yii::$app->formatter->asDatetime($hist->tanggal_reject) : '-' ?>
                            <br>
                            <small class="text-muted">Dikembalikan: <?= $hist->tanggal_dikembalikan ? Yii::$app->formatter->asDate($hist->tanggal_dikembalikan) : '-' ?></small>
                        </td>
                        <td>
                            <div style="max-height: 100px; overflow-y: auto;">
                                <?= nl2br(Html::encode($hist->alasan_reject)) ?>
                            </div>
                            <?php if ($hist->kesimpulan): ?>
                                <hr style="margin: 5px 0;">
                                <strong>Kesimpulan:</strong> <?= Html::encode($hist->kesimpulan) ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="max-height: 100px; overflow-y: auto;">
                                <?= nl2br(Html::encode($hist->tanggapan_ppk)) ?>
                            </div>
                            <?php if ($hist->file_tanggapan): ?>
                                <br>
                                <?= Html::a('<i class="fa fa-paperclip"></i> File Tanggapan', ['/uploads/' . $hist->file_tanggapan], ['class' => 'btn btn-xs btn-info', 'target' => '_blank', 'data-pjax' => 0]) ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            // Try to get user info if possible 
                            // Assuming HistoriReject has user_id, referencing User model
                            $username = 'Unknown';
                            if ($hist->user_id) {
                                $user = \app\models\User::find()->where(['id' => $hist->user_id])->one();
                                if ($user) {
                                    $username = $user->username; 
                                    // You might want to get the actual Role name if available
                                }
                            }
                            echo Html::encode($username);
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
