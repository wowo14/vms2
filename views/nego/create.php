<?php
use yii\helpers\Html;
?>
<div class="negosiasi-create">
    <?= $this->render('_form', [
        'model' => $model,'penawaran'=>$penawaran
    ]) ?>
</div>