<?php
use yii\helpers\Html;

$this->title = 'Buat Minikompetisi';
$this->params['breadcrumbs'][] = ['label' => 'Minikompetisi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="minikompetisi-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>