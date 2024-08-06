<?php
use app\assets\AppAsset;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
$arpenyedia=[];
AppAsset::register($this);
$this->registerJs('
jQuery(function ($) {
    setupImagePreview($("#imageInput"), $("#imagePreview"), $("#file"));
});', View::POS_END);
?>
<div class="staff-ahli-form">
    <?php $form = ActiveForm::begin([
        'id'=>'staff-ahli-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
        <?php if (Yii::$app->tools->isAdmin()) :
        $arpenyedia = isset($_GET['uid'])
            ? collect($model->vendors)
            ->filter(fn ($vendor) => $vendor->id == $this->context->decodeurl($_GET['uid'])->id)
            ->pluck('nama_perusahaan', 'id')
            ->toArray()
            : ArrayHelper::map($model->vendors, 'id', 'nama_perusahaan');
    ?>
        <?= $form->field($model, 'penyedia_id')->widget(Select2::class, [
            'data' => $arpenyedia,
        ]); ?>
    <?php endif; ?>
    <?php if ($vendordisabled = $this->context->isVendor()) : ?>
        <?php
        $companyId = Yii::$app->session->get('companygroup');
        if ($vendordisabled) {
            echo $form->field($model, 'penyedia_id')->hiddenInput(['value' => $companyId])->label(false);
        }
        ?>
    <?php endif; ?>
      <?= $form->field($model, 'nama')->textInput() ?>
      <?= $form->field($model, 'tanggal_lahir')->widget(DatePicker::class, [
        'options' => ['placeholder' => 'Select date ...'],
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
            'autoclose' => true
        ]
    ]) ?>
      <?= $form->field($model, 'alamat')->textarea(['rows' => 2]) ?>
      <?= $form->field($model, 'email')->textInput() ?>
      <?= $form->field($model, 'jenis_kelamin')->textInput()?>
      <?= $form->field($model, 'pendidikan')->textInput() ?>
      <?= $form->field($model, 'warga_negara')->textInput() ?>
      <?= $form->field($model, 'lama_pengalaman')->textInput() ?>
      <?= $form->field($model, 'file')->hiddenInput(['id' => 'file'])->label(false) ?>
    <div class="form-group ">
        <div class="row">
            <label class="control-label right col-sm-3" for="Staffahli-file">File Sertifikat Keahlian (images/pdf)</label>
            <div class="col-sm-9">
                <input type="file" accept=".pdf, .png, .jpg, .jpeg, .gif" id="imageInput">
                <div id="imagePreview"></div>
            </div>
        </div>
    </div>
      <?= $form->field($model, 'keahlian')->textInput() ?>
      <?= $form->field($model, 'spesifikasi_pekerjaan')->textInput() ?>
    <?php if (!Yii::$app->request->isAjax){ ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>
