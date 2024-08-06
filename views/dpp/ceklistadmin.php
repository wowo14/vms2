<?php
use app\models\{Unit,CeklistModel};
use kartik\date\DatePicker;
use kartik\select2\Select2;
// use kartik\switchinput\SwitchInput;
use unclead\multipleinput\MultipleInput;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
$this->registerJs("
jQuery(function ($) {
    $('.list-cell__button').hide();
});", View::POS_END);

$additions=json_decode($model->addition, true);
$col = [];
// $units='';
// if(key_exists('unit', $additions)){
//     $units=$additions['unit'];
// }
$dataUnit=Unit::collectAll()->pluck('unit', 'id')->toArray();
if(key_exists('template', $additions)){
    $rr = $additions['template'];// generated from template kelengkapan dpp
}else{
    die;
}
$mdl=new CeklistModel();
$mdl->nomor_dpp=$dpp->nomor_dpp??'';
$mdl->nomor_tugas=$modelpenugasan->nomor_tugas??'';
$mdl->tanggal_tugas=$modelpenugasan->tanggal_tugas??'';
$mdl->pejabat=$modelpenugasan->pejabat??'';
$mdl->admin=$modelpenugasan->admin??'';
$mdl->paket_id=$dpp->paket_id??'';
$mdl->unit=$dpp->bidang_bagian??'';
$mdl->template=$additions['template'];
$template = array('uraian', 'sesuai', 'keterangan');
foreach ($model->reorderArray(array_keys($temp[0]),$template) as $item) {
    $trimmedKey = ucfirst(trim($item));
    $title = ($trimmedKey === 'Sesuai') ? 'Sesuai(ya/tidak)' : (($trimmedKey === 'Skala') ? 'Skala(1-5)' : ucfirst($trimmedKey));
    $col[] = $trimmedKey === 'Sesuai' ? [
        'name' => trim($item),
        'title' => $title,
        'type' =>'checkbox',// SwitchInput::class,
        // 'options' => [
        //     'pluginOptions' => [
        //         'size' => 'mini',
        //         'onText' => 'Ya', 'offText' => 'Tidak'
        //     ]
        // ]
    ] : [
        'name' => trim($item),
        'title' => $title,
        'type' => 'textArea'
    ];
}
$form = ActiveForm::begin([
        'id'=>'y-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]);
echo $form->field($mdl,'nomor_dpp')->textInput(['maxlength' => true]);
echo $form->field($mdl,'nomor_tugas')->textInput(['maxlength' => true]);
echo $form->field($mdl,'tanggal_tugas')->widget(DatePicker::class,[
    'language' => 'id',
    'pluginOptions' => [
        'autoclose' => true,
        'format' => 'yyyy-mm-dd',
    ]
]);
echo $form->field($mdl,'pejabat')->widget(Select2::class,[
    'data' => $datapenugasan['pejabat'],
    'options' => ['placeholder' => 'Pilih Pejabat'],
    'pluginOptions' => ['allowClear' => true],
]);
echo $form->field($mdl,'admin')->widget(Select2::class,[
    'data' => $datapenugasan['admin'],
    'options' => ['placeholder' => 'Pilih admin'],
    'pluginOptions' => ['allowClear' => true],
]);
echo $form->field($mdl,'unit')->widget(Select2::class,[
    'data' => $dataUnit,
    'options' => ['placeholder' => 'Pilih unit/Bidang/Bagian'],
    'pluginOptions' => ['allowClear' => true],
]);
echo $form->field($mdl,'paket_id')->widget(Select2::class,[
    'data' => $dataPaket,
    // 'options' => ['placeholder' => 'Pilih Paket'],
    // 'pluginOptions' => ['allowClear' => true],
]);
echo $form->field($mdl,'template')->widget(MultipleInput::class,[
    'id' => 'dok-template',
    'name' => 'template[]',
    'enableGuessTitle' => true,
    'cloneButton' => false,
    'removeButtonOptions' => [
        'label' => 'x'
    ],
    'data' => $rr ?? [],
    'columns' => $col
])->label(false);
echo '<div class="form-group">';
echo Html::submitButton('Submit', ['class' => 'btn btn-primary']);
echo '</div>';
ActiveForm::end();
?>