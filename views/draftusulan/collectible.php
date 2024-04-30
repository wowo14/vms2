<?php
use kartik\select2\Select2;
use unclead\multipleinput\{MultipleInput, MultipleInputColumn};
use yii\helpers\{Html, ArrayHelper, Url};
use yii\widgets\ActiveForm;
?>
<div class="merger-usulan-form">
    <?php $form = ActiveForm::begin([
        'id' => 'frm-merge-usulan',
        'action' => ['draftusulan/saveusulan'],
        'method' => 'post',
    ]); ?>
    <div class="col-md-12 paneldetail">
        <div class="panel panel-default">
            <div class="panel-heading">Merger Usulan</div>
            <div class="panel-body">
                <?= $form->field($model, 'detailusulan')->widget(MultipleInput::class, [
                    'id' => 'detailusulan0',
                    'addButtonOptions' => ['class' => 'hide'],
                    'removeButtonOptions' => ['class' => 'hide'],
                    'data' => $data,
                    'columns' => [
                        ['name' => 'id', 'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT,],
                        ['name' => 'produk_id', 'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT,],
                        [
                            'name' => 'tahun_anggaran',
                            'title' => 'Tahun Anggaran',
                            'options' => ['readonly' => true],
                        ],
                        [
                            'name' => 'satuan',
                            'title' => 'Satuan', 'type'  => Select2::class,
                            'options' => [
                                'data' => $optionSatuan,
                                'options' => [
                                    'placeholder' => 'Select satuan ...',
                                    // 'disabled' => true,
                                ],
                                'pluginOptions' => [
                                    // 'disabled' => true,
                                ],
                            ],
                        ],
                        [
                            'name' => 'id_produk',
                            'title' => 'Produk',
                            'type'  => Select2::class,
                            'options' => [
                                'data' => $optionsProduk,
                                'options' => [
                                    'disabled' => true,
                                ],
                                'pluginOptions' => [
                                    'disabled' => true,
                                ],
                            ],
                        ],
                        [
                            'name' => 'qty_usulan', 'type' => MultipleInputColumn::TYPE_TEXT_INPUT,
                            'defaultValue' => 1,
                            'title' => 'Qty Usulan',
                            'options' => ['readonly' => true],
                        ],
                        [
                            'name' => 'rab_id',
                            'title' => 'Draft RAB',
                            'type'  => Select2::class,
                            'options' => [
                                'data' => $optionsRab,
                                'options' => [
                                    // 'disabled' => true,
                                    'placeholder' => 'Select RAB ...',
                                ],
                                'pluginOptions' => [
                                    // 'disabled' => true,
                                ],
                            ],
                        ],
                    ],
                ])->label(false); ?>
            </div>
        </div>
    </div>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>