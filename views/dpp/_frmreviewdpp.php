<?php

use kartik\switchinput\SwitchInput;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
?>
<div id="form-reviewdpp">
    <?php $form = ActiveForm::begin([
        'id' => 'review-form-dpp',
        // 'enableAjaxValidation' => true,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
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
    <h5>REVIEW DOKUMEN PERSIAPAN PENGADAAN OLEH PEJABAT PENGADAAN</h5>
    <table width="100%">
        <tr>
            <td width="20%">Bidang/Bagian</td>
            <td width="1%;">:</td>
            <td width="79%"><?= $model->unit->unit ?? '' ?></td>
        </tr>
        <tr>
            <td>Nama Paket / Jenis Kegiatan</td>
            <td>:</td>
            <td><?= $model->paketpengadaan->nama_paket ?? '' ?></td>
        </tr>
    </table>
    <?php
    if (($reviews->uraian)) {
        $rr = json_decode($reviews->uraian, true);
        foreach ($rr as $e) {
            $rr[] = [
                'uraian' => $e['uraian'],
                'sesuai' => (isset($e['status'])) ? (($e['status'] == 'on' || $e['status'] == '1') ? '1' : '0') : '0',
                'keterangan' => $e['keterangan']
            ];
        }
        // print_r($rr);
    } else {
        $rr = collect($template)->map(function ($e, $index) {
            $r = ['id' => $index, 'uraian' => $e, 'sesuai' => '1', 'keterangan' => ''];
            return $r;
        });
    }
    ?>
    <?= $form->field($reviews, 'uraian')->widget(MultipleInput::class, [
        'id' => 'doklampiran',
        'enableGuessTitle'  => true,
        'cloneButton' => false,
        'max' => count($template) - 1,
        'addButtonOptions' => [
            'class' => 'btn btn-success',
            'label' => '+'
        ],
        'removeButtonOptions' => [
            'label' => 'x'
        ],
        'data' => $rr ?? [],
        'columns' => [
            ['name' => 'id', 'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT,],
            [
                'name' => 'uraian',
                'title' => 'Uraian',
            ],
            [
                'name' => 'sesuai',
                'title' => 'Sesuai',
                'type' => SwitchInput::class,
                'options' => [
                    'pluginOptions' => [
                        'required' => true,
                        'size' => 'mini',
                        'onText' => 'Ya', 'offText' => 'Tidak'
                    ]
                ]
            ],
            [
                'name' => 'keterangan',
                'title' => 'Keterangan',
            ],
        ],
    ])->label(false); ?>
    Review Oleh Pejabat Pengadaan:<br>
    <?= $form->field($reviews, 'keterangan') ?>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>