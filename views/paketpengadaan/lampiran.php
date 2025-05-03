<?php
use kartik\file\FileInput;
use kartik\select2\Select2;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\JsExpression;
$this->title = 'Upload Lampiran Paket : ' . $model->nomor;
?>
<?php $form = ActiveForm::begin(
    [
        'id' => 'lampiran-form',
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
                <?= $form->field($model, 'lampiran')->widget(MultipleInput::class, [
                    'id' => 'doklampiran',
                    'enableGuessTitle'  => true,
                    'cloneButton' => false,
                    'max' => count(collect($model::settingType('jenis_dokumen'))->where('param', 'lampiran')),
                    'addButtonOptions' => [
                        'class' => 'btn btn-success',
                        'label' => '+'
                    ],
                    'removeButtonOptions' => [
                        'label' => 'x'
                    ],
                    'data' => $model->isNewRecord ? '' : $model->attachments,
                    'columns' => [
                        ['name' => 'id', 'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT,],
                        [
                            'name'  => 'jenis_dokumen',
                            'title' => 'Jenis Dokumen Lampiran',
                            'type'  => Select2::class,
                            'options' => [
                                'data' => collect($model::settingType('jenis_dokumen'))->where('param', 'lampiran')->pluck('value', 'id')->toArray(),
                                // 'options' => ['placeholder' => 'pilih jenis dokumen ...'],
                                'pluginOptions' => [
                                    'allowClear' => false,
                                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                    'templateResult' => new JsExpression('function(item) { return item.text; }'),
                                    'templateSelection' => new JsExpression('function (item) { return item.text; }'),
                                ],
                                'pluginEvents' => [
                                    "change" => "function(){
                                                var el=$(this);
                                            }",
                                ],
                            ],
                        ],
                        [
                            'name' => 'name',
                            'title' => 'File Upload',
                            'type' => FileInput::class,
                            'options' => [
                                'purifyHtml' => false,
                                'pluginOptions' => [
                                    'multiple' => true,
                                    // 'capture'=>'camera',
                                    'maxFileSize' => 4096,
                                    'showPreview' => false,
                                    'showCaption' => true,
                                    'showRemove' => true,
                                    'showUpload' => false,
                                    'browseIcon' => '<i class="fa fa-camera"></i> ',
                                    'browseLabel' =>  'Pilih Foto'
                                ]
                            ]
                        ],
                    ],
                ])->label(false); ?>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<?php if (!Yii::$app->request->isAjax) { ?>
    <div class="form-group">
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-info']) ?>
        <?= Html::submitButton('Submit', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
<?php } ?>
<?php ActiveForm::end(); ?>