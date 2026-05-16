<?php
use kartik\datetime\DateTimePicker;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
$this->title="Reject DPP -> Paket Pengadaan";
?>
<div class="dpp-form">
    <?php $form = ActiveForm::begin([
        'id' => 'dpp-formreject',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 col-md-3 control-label text-sm-left text-md-right'],
        ],
    ]); ?>
    <?= $form->field($model, 'id')->hiddenInput()->label(false)?>
    <?= $form->field($model, 'nomor')->textInput(['maxlength' => true, 'readonly' => true]) ?>
    <?= $form->field($model, 'nama_paket')->textInput(['maxlength' => true, 'readonly' => true]) ?>
    <?= $form->field($model, 'alasan_reject')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'tanggal_reject')->widget(DateTimePicker::class, [
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd hh:ii:ss',
            'todayHighlight' => true,
            'autoclose' => true
        ],
    ]) ?>
    <?= $form->field($model, 'file_reject')->hiddenInput(['id' => 'file_reject_pkt'])->label(false) ?>
    <div class="form-group row">
        <label class="control-label right col-sm-3" for="paketpengadaan-file_reject">File Reject (images/pdf)</label>
        <div class="col-sm-9">
            <input type="file" accept=".pdf, .png, .jpg, .jpeg, .gif" id="imageInputRejectPkt">
            <div id="imagePreviewRejectPkt"></div>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-3">&nbsp;</div>
        <div class="col-sm-9">
            <?php echo $model->file_reject ? Html::a(
                \app\widgets\FilePreview::widget([
                    'model' => $model,
                    'attribute' => 'file_reject',
                ]),
                Yii::getAlias('@web/uploads/') . $model->file_reject,
                ['target' => '_blank']
            ) : '';
            ?>
        </div>
    </div>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>
<?php
$js = <<<JS
    $("#imageInputRejectPkt").on("change", function(e) {
        const input = e.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = $("#imagePreviewRejectPkt");
                preview.empty();
                const file = input.files[0];
                const extension = file.name.split(".").pop().toLowerCase();
                if (extension === "pdf") {
                    const pdfEmbed = $("<embed style='width:60%;height:400px;' src='" + e.target.result + "' type='application/pdf'>");
                    $("#file_reject_pkt").val(e.target.result);
                    preview.append(pdfEmbed);
                } else if (extension.match(/(jpg|jpeg|png|gif)$/)) {
                    const image = $("<img style='width:60%;' src='" + e.target.result + "'>");
                    $("#file_reject_pkt").val(e.target.result);
                    preview.append(image);
                } else {
                    preview.text("Unsupported file type");
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    });
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>