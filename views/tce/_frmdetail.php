<?php
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
?>
<div class="template-checklist-evaluasi-detail-form">
    <?php $form = ActiveForm::begin([
        'id' => 'template-checklist-evaluasi-detail-form',
        'enableAjaxValidation' => false,
        // 'fieldConfig' => [
        //     'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
        //     'labelOptions' => ['class' => 'col-sm-3 col-md-3 control-label text-sm-left text-md-right'],
        // ],
    ]); ?>
    <?= $form->field($model, 'header_id')->widget(Select2::class, [
        'data' => ArrayHelper::map($model->headers, 'id', 'template'),
        'options' => [
            'placeholder' => 'Pilih Template...',
        ]
    ])->label('Template') ?>
    <?= $form->field($model, 'details')->widget(MultipleInput::class, [
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
        'data' => $model->uraian,
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
            <?= Html::submitButton('Submit', ['class' =>'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>