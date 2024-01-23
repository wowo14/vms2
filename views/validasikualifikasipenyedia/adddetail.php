<?php
use app\models\TemplateChecklistEvaluasi;
use kartik\select2\Select2;
use unclead\multipleinput\{MultipleInput, MultipleInputColumn};
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
?>
<div class="validasi-kualifikasi-penyedia-form">
    <?php $form = ActiveForm::begin([
        'id' => '1validasi-kualifikasi-penyedia-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
    <?= $form->field($model, 'template')->widget(Select2::class, [
        'data' => TemplateChecklistEvaluasi::collectAll()->pluck('nama_perusahaan', 'id')->toArray(),
        'options' => ['placeholder' => 'Pilih Penyedia...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]) ?>
    <?= $form->field($model, 'detail')->widget(MultipleInput::class, [
        'id' => 'detailuraian',
        'enableGuessTitle'  => true,
        'cloneButton' => false,
        'addButtonOptions' => [
            'class' => 'btn btn-success',
            'label' => '+'
        ],
        'removeButtonOptions' => [
            'label' => 'x'
        ],
        'data' => [],
        'columns' => [
            ['name' => 'id', 'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT,],
            [
                'name' => 'uraian',
                'title' => 'Uraian',
            ],
        ],
    ])->label(false); ?>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>