<?php

use app\models\Unit;
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

$this->title = 'Report Filter';
$this->params['breadcrumbs'][] = $this->title;
$currentController = Yii::$app->controller->id;
$currentAction = Yii::$app->controller->action->id;
$link = Url::to([$currentController . '/' . $currentAction]);
$tahun = $raw->unique('year')->pluck('year', 'year')->toArray();
$month = $model->months;
$all = ['all' => 'all'];
$month = array_merge([0 => 'all'], $month);

$form = ActiveForm::begin([
    'id' => 'rpt-form',
    'action' => $action ?? $link,
    'enableAjaxValidation' => false,
    'fieldConfig' => [
        'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
        'labelOptions' => ['class' => 'col-sm-3 col-md-3 control-label text-sm-left text-md-right'],
    ],
]);
echo $form->field($model, 'tahun')->widget(Select2::class, [
    'data' => $tahun,
]);
echo $form->field($model, 'bulan')->widget(Select2::class, [
    'data' => $month,
    'pluginOptions' => [
        'placeholder' => 'Pilih Bulan',
        'allowClear' => true
    ]
]);

echo Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'type', 'value' => 'grid']);
echo Html::submitButton('Preview', ['class' => 'btn btn-warning', 'name' => 'type', 'value' => 'pdf']);
ActiveForm::end();
