<?php
use app\models\Unit;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title = 'Report Filter';
$this->params['breadcrumbs'][] = $this->title;
$tahun = $raw->unique('year')->pluck('year', 'year')->toArray();
$month = $model->months;
$month = array_merge([0 => 'all'], $month);
$kategori = array_merge(['' => '', 'all' => 'all'], $model::optionkategoripengadaan());
$metode = array_merge(['' => '', 'all' => 'all'], $model::optionmetodepengadaan());
$pejabat = array_merge(['' => '', 'all' => 'all'], $model::getAllpetugas());
$admin = array_merge(['' => '', 'all' => 'all'], $model::getAlladmin());
$bidang = array_merge(['' => '', 'all' => 'all'], Unit::collectAll()->pluck('unit', 'id')->toArray());
$form = ActiveForm::begin([
    'id' => 'rpt-form',
    'action' => \yii\helpers\Url::to(['report/metode']),
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
echo $form->field($model, 'metode')->widget(Select2::class, [
    'data' => $metode,
    'pluginOptions' => [
        'placeholder' => 'Pilih Metode',
        'allowClear' => true
    ]
]);
echo $form->field($model, 'pejabat')->widget(Select2::class, [
    'data' => $pejabat,
    'pluginOptions' => [
        'placeholder' => 'Pilih Pejabat',
        'allowClear' => true
    ]
]);
echo $form->field($model, 'admin')->widget(Select2::class, [
    'data' => $admin,
    'pluginOptions' => [
        'placeholder' => 'Pilih Admin',
        'allowClear' => true
    ]
]);
echo $form->field($model, 'bidang')->widget(Select2::class, [
    'data' => $bidang,
    'pluginOptions' => [
        'placeholder' => 'Pilih Bidang',
        'allowClear' => true
    ]
]);
//button
echo Html::submitButton('Submit', ['class' => 'btn btn-primary']);
ActiveForm::end();
