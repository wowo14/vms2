<?php
use app\models\DraftRab;
use kartik\select2\Select2;
use yii\helpers\Html;
?>
<form action="/draftrab/rekap" method="post">
    <div class="col-md-9">
        <div class="form-group">
            <?php
            echo Select2::widget([
                'id' => 'tahun',
                'name' => 'tahun',
                'data' => DraftRab::optiontahunanggaran(),
                'options' => [
                    'placeholder' => 'Pilih Tahun',
                ]
            ]);
            ?>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <div class="clearfix"></div>
</form>