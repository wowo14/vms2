<?php
use app\assets\AppAsset;
use app\models\PaketPengadaan;
use app\widgets\FilePreview;
use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
AppAsset::register($this);
$referrer = Yii::$app->request->referrer;
if (strpos($referrer, 'paketpengadaan') !== false) { //disable edit from paketpengadaan
    $isactive=true;
} else {
    $isactive=false;
}
$js=<<<JS
jQuery(function ($) {
    setupImagePreview($("#imageInput"), $("#imagePreview"), $("#file_tanggapan"));
});
function autogenerates(el){
    console.log(el);
    $('#historireject-nomor').val();
}
JS;
$this->registerJs($js, \yii\web\View::POS_END);
?>
<div class="histori-reject-form">
    <?php $form = ActiveForm::begin([
        'id'=>'histori-reject-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
     <?= $form->field($model, 'paket_id')->widget(Select2::class, [
        'data' => PaketPengadaan::collectAll(['approval_by' => null,'pemenang'=>null])->pluck('nomornamapaket', 'id')->toArray(),
        'disabled'=>$isactive,
        'options' => [
            'placeholder' => 'Pilih Paket...',
        ],
        'pluginEvents' => [
            "change" => "function(){
                        var el=$(this);
                        autogenerates(el);
                    }",
        ],
    ]) ?>
      <?= $form->field($model, 'nomor')->textInput(['maxlength' => true,'disabled'=>$isactive]) ?>
      <?= $form->field($model, 'nama_paket')->textInput(['maxlength' => true,'disabled'=>$isactive]) ?>
      <?= $form->field($model, 'alasan_reject')->textarea(['rows' => 2,'disabled'=>$isactive]) ?>
      <?= $form->field($model, 'tanggal_reject')->widget(DateTimePicker::class, [
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd hh:ii:ss',
            'todayHighlight' => true,
            'autoclose' => true
        ],
        'disabled'=>$isactive
    ]) ?>
      <?= $form->field($model, 'kesimpulan')->textarea(['rows' => 2]) ?>
      <?= $form->field($model, 'tanggal_dikembalikan')->widget(DateTimePicker::class, [
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd hh:ii:ss',
            'todayHighlight' => true,
            'autoclose' => true
        ],
        // 'disabled'=>$isactive
    ]) ?>
      <?= $form->field($model, 'tanggapan_ppk')->textarea(['rows' => 2]) ?>
      <?= $form->field($model, 'file_tanggapan')->hiddenInput(['id' => 'file_tanggapan'])->label(false) ?>
        <div class="form-group ">
            <div class="row">
                <label class="control-label right col-sm-3" for="reviewdpp-file_tanggapan">File Tanggapan PPK (images/pdf)</label>
                <div class="col-sm-9">
                    <input type="file" accept=".pdf, .png, .jpg, .jpeg, .gif" id="imageInput">
                    <div id="imagePreview"></div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
        <?php echo $model->file_tanggapan ? Html::a(
            FilePreview::widget([
                'model' => $model,
                'attribute' => 'file_tanggapan',
            ]),
            Yii::getAlias('@web/uploads/') . $model->file_tanggapan,
            ['target' => '_blank']
        ) : '';
        ?>
        </div>
    <?php if (!Yii::$app->request->isAjax){ ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>
