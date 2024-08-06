<?php
use yii\helpers\Html;
?>
<div class="negosiasi-update">
    <?= $this->render('_form', [
        'model' => $model,'penawaran'=>$penawaran
    ]) ?>
</div>