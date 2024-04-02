<?php
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use yii\bootstrap4\ActiveForm;
use yii\helpers\{ArrayHelper,Html};
use yii\web\View;
$this->registerJs('
jQuery(function ($) {
    jQuery(".reveal").on("click",function() {
        var $pwd = $(".pwd");
        if ($pwd.attr("type") === "password") {
            $pwd.attr("type", "text");
        } else {
            $pwd.attr("type", "password");
        }
    });
});', View::POS_END);
?>
<div class="pengurusperusahaan-form">
    <?php $form = ActiveForm::begin([
        'id' => 'pengurusperusahaan-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
    <?php if (Yii::$app->tools->isAdmin()) : ?>
        <?php
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
    <?= $form->field($model, 'nama')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'nik')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'alamat')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'telepon')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'jabatan')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'is_active')->widget(SwitchInput::class, [
        'pluginOptions' => ['size' => 'mini'],
    ]); ?>
    <?= $form->field($model, 'password', ['template' => '
    <div class="row">
        {label}
        <div class="col-sm-9">
            <div class="input-group col-sm-12">
            {input}
                <span class="input-group-addon">
                    <i class="fa fa-eye reveal"></i></button>
                </span>
            </div>
            {error}{hint}
        </div>
   </div>'])->passwordInput(['class' => 'pwd form-control']) ?>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>