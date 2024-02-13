<?php
use kartik\select2\Select2;
use unclead\multipleinput\MultipleInput;
use yii\widgets\ActiveForm;
$form = ActiveForm::begin([
    'id' => 'copy-programkegiatan-form',
    'enableAjaxValidation' => false,
    'fieldConfig' => [
        'template' => "<div class='row'>{label}\n<div class='col-sm-10'>{input}\n{error}</div></div>",
        'labelOptions' => ['class' => 'col-sm-2 control-label right'],
    ],
    'action' => ['/programkegiatan/copyto'],
    'method' => 'post'
]);
echo $form->field($model, 'tahun_anggaran')->widget(MultipleInput::class, [
    'id' => 'copyprogramkegiatan',
    'addButtonOptions' => ['class' => 'hide'],
    'removeButtonOptions' => ['class' => 'hide'],
    'max' => 1,
    'columns' => [
        [
            'name' => 'from',
            'title' => 'Dari',
            'type'  => Select2::class,
            'options' => [
                'data' => $opttahun['from'],
                'options' => ['placeholder' => 'Select ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ],
        ],
        [
            'name' => 'to',
            'title' => 'Ke',
            'type'  => Select2::class,
            'options' => [
                'data' => $opttahun['to'],
                'options' => ['placeholder' => 'Select ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ],
        ],
    ],
])->label(false);
ActiveForm::end();
