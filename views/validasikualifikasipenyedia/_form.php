<?php
use app\models\{Dpp,Penyedia,TemplateChecklistEvaluasi};
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
$dpp=Dpp::find()->with('paketpengadaans')->where(['status_review' => 1])->all();
$dpp=collect($dpp)->pluck('paketpengadaans')->flatten()->pluck('nomornamapaket','id')->toArray();
?>
<div class="validasi-kualifikasi-penyedia-form">
    <?php $form = ActiveForm::begin([
        'id'=>'validasi-kualifikasi-penyedia-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
      <?= $form->field($model, 'penyedia_id')->widget(Select2::class,[
            'data'=>Penyedia::collectAll()->pluck('nama_perusahaan','id')->toArray(),
            'options' => ['placeholder' => 'Pilih Penyedia...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
          ]) ?>
      <?= $form->field($model, 'paket_pengadaan_id')->widget(Select2::class,[
        'data'=>$dpp,
        'options' => ['placeholder' => 'Pilih Paket Pengadaan...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
      ]) ?>
      <?= $form->field($model, 'keperluan')->textInput() ?>
      <?= $form->field($model, 'template')->widget(Select2::class,[
        'data'=>TemplateChecklistEvaluasi::collectAll()->pluck('template','id')->sortByDesc(fn($key)=>$key)->toArray(),
        'options' => ['placeholder' => 'Pilih Template...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
      ]) ?>
      <?= $form->field($model, 'is_active')->widget(SwitchInput::class,[
        'pluginOptions' => [
            'onText' => 'Aktif',
            'offText' => 'Tidak Aktif',
        ],
      ]) ?>
    <?php if (!Yii::$app->request->isAjax){ ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>
