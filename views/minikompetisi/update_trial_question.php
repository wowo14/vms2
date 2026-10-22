<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $question app\models\MinikompetisiTrialQuestion */

$this->title = 'Edit Pertanyaan Trial';
$this->params['breadcrumbs'][] = ['label' => 'Minikompetisi', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $question->minikompetisi->judul, 'url' => ['view', 'id' => $question->minikompetisi_id]];
$this->params['breadcrumbs'][] = ['label' => 'Form Trial', 'url' => ['trial-questions', 'id' => $question->minikompetisi_id]];
$this->params['breadcrumbs'][] = 'Edit Pertanyaan';
?>

<div class="minikompetisi-update-trial-question">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-edit text-primary"></i>
                <?= Html::encode($this->title) ?>
            </h3>
        </div>

        <div class="card-body">
            <?php $form = ActiveForm::begin(); ?>
            
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($question, 'item_id')->dropDownList(
                        \yii\helpers\ArrayHelper::map($question->minikompetisi->minikompetisiItems, 'id', 'nama_produk'),
                        ['prompt' => '-- Pilih Produk --', 'class' => 'form-control select2']
                    )->label('Produk yang Ditrial') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($question, 'tipe_jawaban')->dropDownList(
                        \app\models\MinikompetisiTrialQuestion::getTipeJawabanLabels(),
                        ['class' => 'form-control']
                    )->label('Tipe Jawaban') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($question, 'urutan')->textInput(['type' => 'number'])->label('Urutan') ?>
                </div>
            </div>

            <?= $form->field($question, 'pertanyaan')->textarea(['rows' => 4])->label('Pertanyaan') ?>

            <div class="form-group">
                <?= Html::submitButton('<i class="fas fa-save"></i> Simpan Perubahan', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('<i class="fas fa-times"></i> Batal', ['trial-questions', 'id' => $question->minikompetisi_id], ['class' => 'btn btn-default']) ?>
            </div>

            <?php ActiveForm::end(); ?>
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
