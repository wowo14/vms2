<?php
use kartik\switchinput\SwitchInput;
use unclead\multipleinput\MultipleInput;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
$rr = json_decode($model->details[0]->hasil, true);
//generate columns
$aa = [...$rr]; //clone array
$aa = $aa[0];
$col = [];
foreach (array_keys($aa) as $item) {
    $trimmedKey = ucfirst(trim($item));
    $title = ($trimmedKey === 'Sesuai') ? 'Sesuai(ya/tidak)' : (($trimmedKey === 'Skala') ? 'Skala(1-5)' : ucfirst($trimmedKey));
    $col[] = $trimmedKey === 'Sesuai' ? [
        'name' => trim($item),
        'title' => $title,
        'type' => 'checkbox',//SwitchInput::class,
        // 'options' => [
        //     'pluginOptions' => [
        //         'required' => true,
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
// print_r($rr);
// $rr=collect($rr)->map(function($el){
//     $el['sesuai'] = $el['sesuai'] == '1' ? '1' : '0';
//     return $el;
// })->toArray();
if ($model->jenisevaluasi->jenis_evaluasi == 'Kesimpulan') {
    $key = array_search('sesuai', array_column($col, 'name'));
    if ($key !== false) {
        unset($col[$key]);
    }
}
// $js=<<<JS
// $('.list-cell__button').hide();
// JS;
// $this->registerJs($js, \yii\web\View::POS_END); //ValidasiKualifikasiPenyedia[assestment][1][uraian]
?>
<div id="form-reviewdpp">
    <?php $form = ActiveForm::begin([
        'id' => 'review-form-dpp',
        'enableAjaxValidation' => false,
        'action'=>'/validasikualifikasipenyedia/assestment?id='.$model->id,
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
    <h5 style="text-align: center;">CHECK LIST EVALUASI PENYEDIA BARANG/JASA</h5>
    <table width="100%">
        <tr>
            <td width="20%">Nama Perusahaan</td>
            <td width="1%;">:</td>
            <td width="79%"><?= $model->vendor->nama_perusahaan ?? '' ?></td>
        </tr>
        <tr>
            <td>Jenis Evaluasi</td>
            <td>:</td>
            <td><?= $model->jenisevaluasi->jenisevaluasi ?? '' ?></td>
        </tr>
        <tr>
            <td>Paket Pengadaan</td>
            <td>:</td>
            <td><?= $model->paketpengadaan->nama_paket ?? '' ?></td>
        </tr>
    </table>
    <?= $form->field($model, 'assestment')->widget(MultipleInput::class, [
        'id' => 'dokassestment',
        'enableGuessTitle'  => true,
        'cloneButton' => false,
        'addButtonPosition' => MultipleInput::POS_FOOTER,
        'addButtonOptions' => [
            'class' => 'btn btn-success',
            'label' => '+'
        ],
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