<?php
use app\assets\AppAsset;
use app\widgets\FilePreview;
use kartik\date\DatePicker;
use kartik\switchinput\SwitchInput;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use yii2ajaxcrud\ajaxcrud\CrudAsset;
// use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Modal;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
$idmodal='modal-reviewdpp';
CrudAsset::register($this);
AppAsset::register($this);
$this->registerJs('
jQuery(function ($) {
    $(".list-cell__button").hide();
    setupImagePreview($("#imageInput"), $("#imagePreview"), $("#file_tanggapan"));
});', View::POS_END);
?>
<div id="form-reviewdpp">
    <?php $form = ActiveForm::begin([
        'id' => 'review-form-dpp',
        // 'enableAjaxValidation' => true,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
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
    <div class="form-group row">
    <div class="col-sm-3">&nbsp;</div>
    <div class="col-sm-9">
        <?php
        if($model->paketpengadaan->historireject){
            echo Html::a('History Reject', [
                '/historireject/showbypaket',
                'id' => $model->paketpengadaan->id], [
                    'role' => 'modal-remote',
                    'data-target' => '#' . $idmodal,
                    'data-pjax'=>1,'data-toggle' => 'tooltip',
                    'class' => 'btn btn-danger']);
        }
        ?>
    </div></div>
    Kesimpulan :<br>
    <?= $form->field($reviews, 'kesimpulan') ?>
    <?= $form->field($reviews, 'tgl_dikembalikan')->widget(DatePicker::class, [
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
            'autoclose' => true
        ],
    ]) ?>
    Tanggapan PPK:<br>
    <?= $form->field($reviews, 'tanggapan_ppk') ?>
    <?= $form->field($reviews, 'file_tanggapan')->hiddenInput(['id' => 'file_tanggapan'])->label(false) ?>
    <div class="form-group ">
        <div class="row">
            <label class="control-label right col-sm-3" for="reviewdpp-file_tanggapan">File Tanggapan PPK (images/pdf)</label>
            <div class="col-sm-9">
                <input type="file" accept=".pdf, .png, .jpg, .jpeg, .gif" id="imageInput">
                <div id="imagePreview"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
    <?php echo $reviews->file_tanggapan ? Html::a(
        FilePreview::widget([
            'model' => $reviews,
            'attribute' => 'file_tanggapan',
        ]),
        Yii::getAlias('@web/uploads/') . $reviews->file_tanggapan,
        ['target' => '_blank']
    ) : '';
    ?>
    </div>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>
<?php Modal::begin([
    "id" => $idmodal, "size" => "modal-xl",
    "footer" => "",
    "clientOptions" => [
        "tabindex" => false,
        "backdrop" => "static",
        "keyboard" => true,
    ],
    "options" => [
        "tabindex" => true
    ]
]) ?>
<?php Modal::end(); ?>