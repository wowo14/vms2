<?php
use kartik\grid\GridView;
use yii\helpers\Url;
return [
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'tahun_anggaran',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'unit_id',
        'value' => 'unit.unit',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => \app\models\Unit::collectAll(['aktif' => 1])->pluck('unit', 'id')->toArray(),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Pilih Unit'],
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'created_by',
        'value' => 'usercreated.username',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'label' => 'Status',
        'value' => function ($data) {
            return $data->child->status;
        },
        // 'filterType' => GridView::FILTER_SELECT2,
        // 'filter' => \app\models\Unit::collectAll(['aktif' => 1])->pluck('unit', 'id')->toArray(),
        // 'filterWidgetOptions' => [
        //     'pluginOptions' => ['allowClear' => true],
        // ],
        // 'filterInputOptions' => ['placeholder' => 'Pilih Unit'],
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'noWrap' => 'true',
        'template' => '{view} {update} {delete}',
        'vAlign' => 'middle',
        'urlCreator' => function($action, $model, $key, $index) {
                return Url::to([$action,'id'=>$key]);
        },
        'viewOptions' => ['role' => 'modal-remote', 'title' => Yii::t('yii2-ajaxcrud', 'View'), 'data-toggle' => 'tooltip', 'class' => 'btn btn-sm btn-outline-success'],
        'updateOptions' => ['role' => 'modal-remote', 'title' => Yii::t('yii2-ajaxcrud', 'Update'), 'data-toggle' => 'tooltip', 'class' => 'btn btn-sm btn-outline-primary'],
        'deleteOptions' => ['role' => 'modal-remote', 'title' => Yii::t('yii2-ajaxcrud', 'Delete'), 'class' => 'btn btn-sm btn-outline-danger',
            'data-confirm' => false,
            'data-method' => false,// for overide yii data api
            'data-request-method' => 'post',
            'data-toggle' => 'tooltip',
            'data-confirm-title' => Yii::t('yii2-ajaxcrud', 'Delete'),
            'data-confirm-message' => Yii::t('yii2-ajaxcrud', 'Delete Confirm') ],
    ],
];