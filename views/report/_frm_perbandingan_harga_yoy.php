<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Report Perbandingan Harga YoY';
$this->params['breadcrumbs'][] = $this->title;

$all = ['all' => 'Semua'];
$kategoriOptions = $all + array_combine($kategoriList, $kategoriList);
$tahunOptions = array_combine($tahunList, $tahunList);

$form = ActiveForm::begin([
    'id' => 'rpt-form-yoy',
    'action' => \yii\helpers\Url::to(['report/perbandingan-harga-yoy']),
    'enableAjaxValidation' => false,
    'fieldConfig' => [
        'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
        'labelOptions' => ['class' => 'col-sm-3 col-md-3 control-label text-sm-left text-md-right'],
    ],
]);

echo $form->field($model, 'kategori_pengadaan')->widget(Select2::class, [
    'data' => $kategoriOptions,
    'pluginOptions' => [
        'placeholder' => 'Pilih Kategori Pengadaan',
        'allowClear' => true
    ]
]);

echo $form->field($model, 'nama_produk')->textInput([
    'placeholder' => 'Cari nama produk (opsional)',
]);

echo $form->field($model, 'tahun')->widget(Select2::class, [
    'data' => $tahunOptions,
    'pluginOptions' => [
        'placeholder' => 'Pilih Tahun (opsional)',
        'allowClear' => true
    ]
]);

echo Html::submitButton('Tampilkan Laporan', ['class' => 'btn btn-primary']);
echo ' ' . Html::submitButton('Export PDF', ['class' => 'btn btn-success', 'name' => 'type', 'value' => 'pdf']);

ActiveForm::end();
