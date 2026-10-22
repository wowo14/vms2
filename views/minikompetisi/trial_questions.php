<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use app\models\MinikompetisiTrialQuestion;

/* @var $this yii\web\View */
/* @var $model app\models\Minikompetisi */
/* @var $questions app\models\MinikompetisiTrialQuestion[] */
/* @var $newQuestion app\models\MinikompetisiTrialQuestion */

$this->title = 'Form Trial Produk - ' . $model->judul;
$this->params['breadcrumbs'][] = ['label' => 'Minikompetisi', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->judul, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Form Trial';
?>

<div class="minikompetisi-trial-questions">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-clipboard-check text-primary"></i>
                <?= Html::encode($this->title) ?>
            </h3>
            <div class="card-tools">
                <?= Html::a('<i class="fas fa-arrow-left"></i> Kembali', ['view', 'id' => $model->id], ['class' => 'btn btn-default btn-sm']) ?>
            </div>
        </div>

        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Form trial ini hanya tersedia untuk metode <strong>Kualitas & Harga</strong>. 
                Operator lapangan akan mengisi form trial ini untuk setiap vendor yang mengikuti minikompetisi.
            </div>

            <!-- Add New Question Form -->
            <div class="card card-outline card-primary mb-4">
                <div class="card-header">
                    <h3 class="card-title">Tambah Pertanyaan Baru</h3>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['id' => 'new-question-form']); ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($newQuestion, 'item_id')->dropDownList(
                                \yii\helpers\ArrayHelper::map($model->minikompetisiItems, 'id', 'nama_produk'),
                                ['prompt' => '-- Pilih Produk --', 'class' => 'form-control select2']
                            )->label('Produk yang Ditrial') ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($newQuestion, 'tipe_jawaban')->dropDownList(
                                MinikompetisiTrialQuestion::getTipeJawabanLabels(),
                                ['class' => 'form-control']
                            )->label('Tipe Jawaban') ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($newQuestion, 'urutan')->textInput(['type' => 'number', 'value' => count($questions) + 1])->label('Urutan') ?>
                        </div>
                    </div>

                    <?= $form->field($newQuestion, 'pertanyaan')->textarea(['rows' => 3, 'placeholder' => 'Contoh: Bagaimana kualitas rasa produk ini?'])->label('Pertanyaan') ?>

                    <div class="form-group">
                        <?= Html::submitButton('<i class="fas fa-plus"></i> Tambah Pertanyaan', ['class' => 'btn btn-primary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <!-- Questions List -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Pertanyaan Trial</h3>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($questions)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-question-circle fa-3x mb-3"></i>
                            <p>Belum ada pertanyaan trial yang dibuat.</p>
                        </div>
                    <?php else: ?>
                        <table class="table table-striped table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="width: 80px;">Urutan</th>
                                    <th>Produk</th>
                                    <th>Pertanyaan</th>
                                    <th style="width: 120px;">Tipe Jawaban</th>
                                    <th style="width: 150px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($questions as $index => $question): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= $question->urutan ?></td>
                                        <td>
                                            <span class="badge badge-info">
                                                <?= Html::encode($question->item->nama_produk) ?>
                                            </span>
                                        </td>
                                        <td><?= Html::encode($question->pertanyaan) ?></td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                <?= Html::encode($question->tipeJawabanLabel) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?= Html::a('<i class="fas fa-edit"></i>', ['update-trial-question', 'id' => $question->id], [
                                                'class' => 'btn btn-sm btn-info',
                                                'title' => 'Edit',
                                                'data-toggle' => 'tooltip'
                                            ]) ?>
                                            <?= Html::a('<i class="fas fa-trash"></i>', ['delete-trial-question', 'id' => $question->id], [
                                                'class' => 'btn btn-sm btn-danger',
                                                'title' => 'Hapus',
                                                'data-toggle' => 'tooltip',
                                                'data' => [
                                                    'confirm' => 'Apakah Anda yakin ingin menghapus pertanyaan ini?',
                                                    'method' => 'post',
                                                ]
                                            ]) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    $(document).ready(function() {
        if ($.fn.select2) {
            $(".select2").select2({
                width: "100%"
            });
        }
    });
');
