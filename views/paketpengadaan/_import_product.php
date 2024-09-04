<?php
use kartik\file\FileInput;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
$this->title = 'Upload produk pada paket : ' . $model->nomor;
?>
<?php $form = ActiveForm::begin(
    [
        'id' => 'paket-produk-form',
        'enableAjaxValidation' => false,
        'options' => ['enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 col-md-3 control-label text-sm-left text-md-right'],
        ],
    ]
); ?>
<div class="col-md-9">
    <div class="col-md-12 panelpaket">
        <div class="panel panel-default">
            <div class="panel-heading"></div>
            <div class="panel-body">
                <?php
                echo '<label class="control-label">Upload Document</label>';
                echo FileInput::widget([
                    'name' => 'produk',
                    // accept excels
                    'options' => ['accept' => '.xls,.xlsx'],
                    'pluginOptions' => [
                        'allowedFileExtensions' => ['xls', 'xlsx'],
                        'showPreview' => false,
                        'showRemove' => false,
                        'showUpload' => false,
                        'overwriteInitial' => true],
                ]);
                ?>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"><br></div>
<?php if (!Yii::$app->request->isAjax) { ?>
    <div class="form-group">
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-info']) ?>
        <?= Html::submitButton('Submit', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
<?php } ?>
<?php ActiveForm::end(); ?>