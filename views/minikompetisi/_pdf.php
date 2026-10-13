<?php

use yii\helpers\Html;

?>
<div style="margin: 20px;">
    <h2 style="text-align: center; margin-bottom: 30px;">
        Ranking Minikompetisi - <?= Html::encode($model->judul) ?>
    </h2>

    <table style="width: 100%; margin-bottom: 20px; border: none;">
        <tr>
            <td style="width: 20%; font-weight: bold;">Judul:</td>
            <td><?= Html::encode($model->judul) ?></td>
        </tr>
        <tr>
            <td style="width: 20%; font-weight: bold;">Tanggal:</td>
            <td><?= Yii::$app->formatter->asDate($model->tanggal) ?></td>
        </tr>
        <tr>
            <td style="width: 20%; font-weight: bold;">Metode:</td>
            <td><?= Html::encode($model->getMetodeText()) ?></td>
        </tr>
        <tr>
            <td style="width: 20%; font-weight: bold;">Status:</td>
            <td><?= Html::encode($model->getStatusText()) ?></td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr>
                <th style="border: 1px solid #000; padding: 8px; background-color: #f2f2f2; font-weight: bold; text-align: center;">No</th>
                <th style="border: 1px solid #000; padding: 8px; background-color: #f2f2f2; font-weight: bold;">Nama Produk</th>
                <?php for ($i = 1; $i <= $maxRank; $i++): ?>
                    <?php $colorClass = "rank-" . ($i > 5 ? 5 : $i); ?>
                    <th colspan="2" style="border: 1px solid #000; padding: 8px; background-color: #f2f2f2; font-weight: bold; text-align: center;">Terpilih <?= $i ?></th>
                <?php endfor; ?>
            </tr>
            <tr>
                <th style="border: 1px solid #000; padding: 8px; background-color: #f2f2f2; font-weight: bold; text-align: center;"></th>
                <th style="border: 1px solid #000; padding: 8px; background-color: #f2f2f2; font-weight: bold;"></th>
                <?php for ($i = 1; $i <= $maxRank; $i++): ?>
                    <?php $colorClass = "rank-" . ($i > 5 ? 5 : $i); ?>
                    <th style="border: 1px solid #000; padding: 8px; background-color: #f2f2f2; font-weight: bold;">Perusahaan</th>
                    <th style="border: 1px solid #000; padding: 8px; background-color: #f2f2f2; font-weight: bold;">Harga</th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($itemRankings as $itemRanking): ?>
                <?php $item = $itemRanking['item']; ?>
                <?php $rankings = $itemRanking['rankings']; ?>
                <tr>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;"><?= $no++ ?></td>
                    <td style="border: 1px solid #000; padding: 8px;"><?= Html::encode($item->nama_produk) ?></td>
                    
                    <?php for ($i = 1; $i <= $maxRank; $i++): ?>
                        <?php if (isset($rankings[$i])): ?>
                            <?php $rank = $rankings[$i]; ?>
                            <td style="border: 1px solid #000; padding: 8px;"><?= Html::encode($rank['vendor']->nama_vendor ?? '-') ?></td>
                            <td style="border: 1px solid #000; padding: 8px; text-align: right;"><?= number_format($rank['harga'], 0, ',', '.') ?></td>
                        <?php else: ?>
                            <td style="border: 1px solid #000; padding: 8px;">-</td>
                            <td style="border: 1px solid #000; padding: 8px; text-align: right;">-</td>
                        <?php endif; ?>
                    <?php endfor; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
