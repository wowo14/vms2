<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
$this->title = 'Persetujuan Pakta Integritas';
$this->registerJs("$('#paktaModal').modal({backdrop: 'static', keyboard: false});");
?>
<div class="site-pakta-integritas">
    <!-- Modal -->
    <div class="modal fade" id="paktaModal" tabindex="-1" role="dialog" aria-labelledby="paktaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h4 class="modal-title" id="paktaModalLabel">PAKTA INTEGRITAS</h4>
                </div>
                <div class="modal-body">
                    <div style="white-space: pre-line;">
                        <?= $paktaText ?>
                    </div>
                    <?php $form = ActiveForm::begin(); ?>

                        <?= $form->field($model, 'tahun')->hiddenInput(['value' => date('Y')])->label(false) ?>
                        <?= $form->field($model, 'user_id')->hiddenInput(['value' => Yii::$app->user->id])->label(false) ?>

                        <!-- Radio button untuk status -->
                        <?= $form->field($model, 'status')->radioList([
                            'accept' => 'Setuju',
                            'reject' => 'Tidak Setuju'
                        ])->label(false) ?>

                        <div class="form-group mt-3">
                            <?= Html::submitButton('Kirim', ['class' => 'btn btn-primary']) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>