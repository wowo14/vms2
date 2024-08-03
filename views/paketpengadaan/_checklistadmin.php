<?php
use app\models\{Unit,PaketPengadaan};
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use unclead\multipleinput\MultipleInput;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
$this->title=$title;
$additions=json_decode($model->addition, true);
$col = [];
$units='';
if(key_exists('unit', $additions)){
    $units=$additions['unit'];
}
$dataUnit=Unit::collectAll()->pluck('unit', 'id')->toArray();
if(key_exists('template', $additions)){
    $rr = $additions['template'];// generated from template kelengkapan dpp
}else{
    die;
}
$template = array('uraian', 'sesuai', 'keterangan');
foreach ($model->reorderArray(array_keys($temp[0]),$template) as $item) {
    $trimmedKey = ucfirst(trim($item));
    $title = ($trimmedKey === 'Sesuai') ? 'Sesuai(ya/tidak)' : (($trimmedKey === 'Skala') ? 'Skala(1-5)' : ucfirst($trimmedKey));
    $col[] = $trimmedKey === 'Sesuai' ? [
        'name' => trim($item),
        'title' => $title,
        'type' => SwitchInput::class,
        'options' => [
            'pluginOptions' => [
                // 'type' => 'checkbox',
                // 'required' => true,
                'size' => 'mini',
                'onText' => 'Ya', 'offText' => 'Tidak'
            ]
        ]
    ] : [
        'name' => trim($item),
        'title' => $title,
        'type' => 'textArea'
    ];
}
$this->registerJs("
jQuery(function ($) {
    $('.list-cell__button').hide();
});", View::POS_END);
?>
<div id="form-ceklistadmin">
    <?php $form = ActiveForm::begin([
        'id' => 'ceklistadmin-dpp',
        'enableAjaxValidation' => false,
        // 'action'=>'/paketpengadaan/ceklistadmin?id='.$model->id,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-12'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 col-md-3 control-label text-sm-left text-md-right'],
        ],
    ]); ?>
    <table style="width: 100%; font-size: 14px; text-align: center; font-weight: bold;">
        <tr>
            <td style="width: 15%;">
                <?= Html::img(Yii::getAlias('@web/images/logogresik.png'), ['width' => '77px']) ?>
            </td>
            <td><?= $model::profile('dinas') ?> KABUPATEN GRESIK <br>
                <p><?= $model::profile('address') ?></p>
            </td>
            <td style="width: 15%;">
                <?= Html::img(Yii::getAlias('@web/images/logors.png'), ['width' => '77px']) ?>
            </td>
        </tr>
    </table>
    <hr>
    <h5 style="text-align: center;">CHECK LIST DOKUMEN KELENGKAPAN DPP</h5>
    <table width="100%">
        <tr>
            <td width="20%">Bidang / Bagian</td>
            <td width="1%;">:</td>
            <td width="79%"><?=$form->field($model, 'unit')->widget(Select2::class, [
                'data' => $dataUnit,
                'options' => ['placeholder' => 'Select bidang/bagian','value'=>$units],
            ])->label(false) ?></td>
        </tr>
        <tr>
            <td>Nama Paket / Jenis Kegiatan</td>
            <td>:</td>
            <td><?=$form->field($model, 'id')->widget(Select2::class,[
                'data'=>$dataPaket,
            ])->label(false)?></td>
        </tr>
    </table>
    <?= $form->field($model, 'template')->widget(MultipleInput::class, [
        'id' => 'dok-template',
        'enableGuessTitle'  => true,
        'cloneButton' => false,
        'removeButtonOptions' => [
            'label' => 'x'
        ],
        'data' => $rr ?? [],
        'columns' => $col
    ])->label(false); ?>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>