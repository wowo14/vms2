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
$month = $months;
$all = ['all' => 'all'];
$month = array_merge([0 => 'all'], $month);
// $kategori = array_merge(['' => '', 'all' => 'all'], $model::optionkategoripengadaan());
$kategori = $all + $model::optionsSettingtype('kategori_pengadaan', ['value', 'id']);
// $metode = array_merge(['' => '', 'all' => 'all'], $model::optionmetodepengadaan());
$metode = $all + $model::optionsSettingtype('metode_pengadaan', ['value', 'id']);
// $pejabat = array_merge(['' => '', 'all' => 'all'], $model::getAllpetugas());
$pejabat = $all + $model::getAllpetugas();
// $admin = array_merge(['' => '', 'all' => 'all'], $model::getAlladmin());
$admin = $all + $model::getAlladmin();
$ppkom = $all + $model::optionppkom();
$bidang = array_merge(['' => '', 'all' => 'all'], Unit::collectAll()->pluck('unit', 'id')->toArray());
$form = ActiveForm::begin([
    'id' => 'rpt-form',
    'action' =>$action??$link,
    'enableAjaxValidation' => false,
    'fieldConfig' => [
        'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
        'labelOptions' => ['class' => 'col-sm-3 col-md-3 control-label text-sm-left text-md-right'],
    ],
]);
echo $form->field($model, 'tahun')->widget(Select2::class, [
    'data' => $years,
]);
echo $form->field($model, 'bulan_awal')->widget(Select2::class, [
    'data' => $month,
    'pluginOptions' => [
        'placeholder' => 'Periode Awal',
        'allowClear' => true
    ]
]);
echo $form->field($model, 'bulan_akhir')->widget(Select2::class, [
    'data' => $month,
    'pluginOptions' => [
        'placeholder' => 'Periode Akhir',
        'allowClear' => true
    ]
]);
echo $form->field($model, 'kategori')->widget(Select2::class, [
    'data' => $kategori,
    'pluginOptions' => [
        'placeholder' => 'Pilih Kategori',
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
echo $form->field($model, 'ppkom')->widget(Select2::class, [
    'data' => $ppkom,
    'pluginOptions' => [
        'placeholder' => 'Pilih Bidang',
        'allowClear' => true
    ]
]);
//button
echo Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'type', 'value' => 'grid']);
echo Html::submitButton('Preview', ['class' => 'btn btn-warning', 'name' => 'type', 'value' => 'pdf']);
ActiveForm::end();
