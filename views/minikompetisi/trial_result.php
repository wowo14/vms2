<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $penawaran app\models\MinikompetisiPenawaran */
/* @var $minikompetisi app\models\Minikompetisi */
/* @var $questions app\models\MinikompetisiTrialQuestion[] */
/* @var $answers app\models\MinikompetisiTrialAnswer[] */

$this->title = 'Hasil Trial - ' . $penawaran->vendor->nama_vendor;
$this->params['breadcrumbs'][] = ['label' => 'Minikompetisi', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $minikompetisi->judul, 'url' => ['view', 'id' => $minikompetisi->id]];
$this->params['breadcrumbs'][] = 'Hasil Trial';
?>

<div class="minikompetisi-trial-result">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-clipboard-check text-success"></i>
                <?= Html::encode($this->title) ?>
            </h3>
            <div class="card-tools">
                <?= Html::a('<i class="fas fa-arrow-left"></i> Kembali', ['view', 'id' => $minikompetisi->id], ['class' => 'btn btn-default btn-sm']) ?>
                <?= Html::a('<i class="fas fa-print"></i> Cetak', 'javascript:void(0);', ['class' => 'btn btn-info btn-sm', 'onclick' => 'window.print();']) ?>
            </div>
        </div>

        <div class="card-body">
            <!-- Header Info -->
            <div class="alert alert-success">
                <h4 class="alert-heading">
                    <i class="fas fa-award"></i> Hasil Trial Produk
                </h4>
                <hr>
                <p><strong>Minikompetisi:</strong> <?= Html::encode($minikompetisi->judul) ?></p>
                <p><strong>Vendor:</strong> <?= Html::encode($penawaran->vendor->nama_vendor) ?></p>
                <p><strong>Metode:</strong> Kualitas & Harga</p>
                <p><strong>Tanggal Trial:</strong> <?= !empty($answers) ? Html::encode(reset($answers)->tanggal_isi) : '-' ?></p>
                <p><strong>Pengisi:</strong> <?= !empty($answers) ? Html::encode(reset($answers)->pengisi) : '-' ?></p>
            </div>

            <?php if (empty($questions)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Belum ada pertanyaan trial yang dibuat untuk minikompetisi ini.
                </div>
            <?php elseif (empty(array_filter($answers))): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Belum ada jawaban trial yang diisi untuk vendor ini.
                </div>
            <?php else: ?>
                <!-- Trial Results Table -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail Jawaban Trial</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="width: 150px;">Produk</th>
                                    <th>Pertanyaan</th>
                                    <th style="width: 150px;">Jawaban</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($questions as $index => $question): ?>
                                    <?php $answer = $answers[$question->id]; ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <span class="badge badge-info">
                                                <?= Html::encode($question->item->nama_produk) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?= Html::encode($question->pertanyaan) ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                Tipe: <?= Html::encode($question->tipeJawabanLabel) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php if ($answer): ?>
                                                <?php if ($question->tipe_jawaban == \app\models\MinikompetisiTrialQuestion::TIPE_SKALA): ?>
                                                    <?php
                                                    $badgeClass = 'badge-secondary';
                                                    if ($answer->jawaban == '5') $badgeClass = 'badge-success';
                                                    elseif ($answer->jawaban == '4') $badgeClass = 'badge-info';
                                                    elseif ($answer->jawaban == '3') $badgeClass = 'badge-warning';
                                                    elseif ($answer->jawaban == '2') $badgeClass = 'badge-danger';
                                                    elseif ($answer->jawaban == '1') $badgeClass = 'badge-dark';
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?> badge-lg">
                                                        <?= Html::encode($answer->jawaban) ?>/5
                                                    </span>
                                                <?php elseif ($question->tipe_jawaban == \app\models\MinikompetisiTrialQuestion::TIPE_YA_TIDAK): ?>
                                                    <?php
                                                    $badgeClass = $answer->jawaban == 'Ya' ? 'badge-success' : 'badge-danger';
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?> badge-lg">
                                                        <?= Html::encode($answer->jawaban) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">
                                                        <?= Html::encode($answer->jawaban) ?>
                                                    </span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Belum diisi</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($answer && $answer->catatan): ?>
                                                <small class="text-muted"><?= Html::encode($answer->catatan) ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Summary -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Ringkasan Skor</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        $totalQuestions = count($questions);
                        $answeredQuestions = 0;
                        $totalScore = 0;
                        $scoreCount = 0;

                        foreach ($questions as $question) {
                            $answer = $answers[$question->id];
                            if ($answer && !empty($answer->jawaban)) {
                                $answeredQuestions++;
                                if ($question->tipe_jawaban == \app\models\MinikompetisiTrialQuestion::TIPE_SKALA) {
                                    $totalScore += (int)$answer->jawaban;
                                    $scoreCount++;
                                }
                            }
                        }

                        $averageScore = $scoreCount > 0 ? round($totalScore / $scoreCount, 2) : 0;
                        $completionRate = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100, 1) : 0;
                        ?>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-question-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Pertanyaan</span>
                                        <span class="info-box-number"><?= $totalQuestions ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Terjawab</span>
                                        <span class="info-box-number"><?= $answeredQuestions ?> (<?= $completionRate ?>%)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-star"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Rata-rata Skor</span>
                                        <span class="info-box-number"><?= $averageScore ?>/5</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
@media print {
    .card-tools, .btn {
        display: none !important;
    }
    .card {
        border: 1px solid #ddd;
        box-shadow: none;
    }
}
</style>
