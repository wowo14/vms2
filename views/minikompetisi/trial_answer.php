<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $penawaran app\models\MinikompetisiPenawaran */
/* @var $minikompetisi app\models\Minikompetisi */
/* @var $questions app\models\MinikompetisiTrialQuestion[] */
/* @var $answers app\models\MinikompetisiTrialAnswer[] */

$this->title = 'Isi Form Trial - ' . $penawaran->vendor->nama_vendor;
$this->params['breadcrumbs'][] = ['label' => 'Minikompetisi', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $minikompetisi->judul, 'url' => ['view', 'id' => $minikompetisi->id]];
$this->params['breadcrumbs'][] = 'Isi Form Trial';
?>

<div class="minikompetisi-trial-answer">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-clipboard-check text-success"></i>
                <?= Html::encode($this->title) ?>
            </h3>
            <div class="card-tools">
                <?= Html::a('<i class="fas fa-arrow-left"></i> Kembali', ['view', 'id' => $minikompetisi->id], ['class' => 'btn btn-default btn-sm']) ?>
            </div>
        </div>

        <div class="card-body">
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle"></i> Informasi Trial</h5>
                <p><strong>Minikompetisi:</strong> <?= Html::encode($minikompetisi->judul) ?></p>
                <p><strong>Vendor:</strong> <?= Html::encode($penawaran->vendor->nama_vendor) ?></p>
                <p><strong>Metode:</strong> Kualitas & Harga</p>
            </div>

            <?php $form = ActiveForm::begin(['id' => 'trial-answer-form']); ?>
            
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Form Jawaban Trial</h3>
                </div>
                <div class="card-body">
                    
                    <!-- Operator Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <?= $form->field(reset($answers), 'pengisi')->textInput(['placeholder' => 'Nama operator lapangan'])->label('Nama Pengisi') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field(reset($answers), 'tanggal_isi')->textInput(['type' => 'date'])->label('Tanggal Mengisi') ?>
                        </div>
                    </div>

                    <hr>
                    <h4 class="mb-3">Pertanyaan Trial</h4>

                    <?php foreach ($questions as $index => $question): ?>
                        <?php $answer = $answers[$question->id]; ?>
                        
                        <div class="card mb-3">
                           <div class="card-header bg-light">
                                <div class="row">
                                    <div class="col-md-8">
                                        <strong><?= ($index + 1) . '. ' . Html::encode($question->pertanyaan) ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-box"></i> Produk: <?= Html::encode($question->item->nama_produk) ?>
                                            | 
                                            <i class="fas fa-list"></i> Tipe: <?= Html::encode($question->tipeJawabanLabel) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                                $fieldName = 'MinikompetisiTrialAnswer_' . $question->id;
                                
                                if ($question->tipe_jawaban == \app\models\MinikompetisiTrialQuestion::TIPE_SKALA) {
                                    // Skala 1-5
                                    echo $form->field($answer, 'jawaban', [
                                        'inputOptions' => ['name' => $fieldName . '[jawaban]']
                                    ])->radioList([
                                        '1' => '1 - Sangat Buruk',
                                        '2' => '2 - Buruk',
                                        '3' => '3 - Cukup',
                                        '4' => '4 - Baik',
                                        '5' => '5 - Sangat Baik',
                                    ], [
                                        'separator' => '<br>',
                                        'class' => 'radio-inline'
                                    ])->label(false);
                                } elseif ($question->tipe_jawaban == \app\models\MinikompetisiTrialQuestion::TIPE_YA_TIDAK) {
                                    // Ya/Tidak
                                    echo $form->field($answer, 'jawaban', [
                                        'inputOptions' => ['name' => $fieldName . '[jawaban]']
                                    ])->radioList([
                                        'Ya' => 'Ya',
                                        'Tidak' => 'Tidak',
                                    ], [
                                        'separator' => '<br>',
                                        'class' => 'radio-inline'
                                    ])->label(false);
                                } else {
                                    // Teks bebas
                                    echo $form->field($answer, 'jawaban', [
                                        'inputOptions' => ['name' => $fieldName . '[jawaban]']
                                    ])->textarea(['rows' => 3, 'placeholder' => 'Tulis jawaban Anda di sini...'])->label(false);
                                }
                                ?>

                                <?= $form->field($answer, 'catatan', [
                                    'inputOptions' => ['name' => $fieldName . '[catatan]']
                                ])->textarea(['rows' => 2, 'placeholder' => 'Catatan tambahan (opsional)'])->label('Catatan') ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="form-group mt-4">
                        <?= Html::submitButton('<i class="fas fa-save"></i> Simpan Jawaban', ['class' => 'btn btn-success btn-lg']) ?>
                        <?= Html::a('<i class="fas fa-times"></i> Batal', ['view', 'id' => $minikompetisi->id], ['class' => 'btn btn-default btn-lg']) ?>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
