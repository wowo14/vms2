<?php
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
?>
<div class="penugasan-pemilihanpenyedia-form">
    <?php $form = ActiveForm::begin([
        'id'=>'penugasan-pemilihanpenyedia-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
    <?php
    if(count($data['dpp'])>1):
    ?>
          <?= $form->field($model, 'dpp_id')->widget(Select2::class,[
              'data' => $data['dpp'],
            //'options' => ['placeholder' => 'Pilih DPP'],
              'pluginOptions' => ['allowClear' => true],
          ]) ?>
        <?php endif;?>
      <?= $form->field($model, 'nomor_tugas')->textInput(['maxlength' => true]) ?>
      <?= $form->field($model, 'tanggal_tugas')->widget(DatePicker::class, [
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                ]) ?>
      <?= $form->field($model, 'pejabat')->widget(Select2::class,[
          'data' => $data['pejabat'],
          'options' => ['placeholder' => 'Pilih Pejabat'],
          'pluginOptions' => ['allowClear' => true],
      ]) ?>
      <?= $form->field($model, 'admin')->widget(Select2::class,[
          'data' => $data['admin'],
          'options' => ['placeholder' => 'Pilih Admin'],
          'pluginOptions' => ['allowClear' => true],
      ]) ?>
    <?php if (!Yii::$app->request->isAjax){ ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>
