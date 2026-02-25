<?php
use yii\helpers\Html;

$this->title = 'Update Minikompetisi: ' . $model->judul;
$this->params['breadcrumbs'][] = ['label' => 'Minikompetisi', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->judul, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="minikompetisi-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>