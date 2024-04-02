<?php
use yii\helpers\{Url,Html};
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
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'npwp',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'nama_perusahaan',
        'format' => 'raw',
        'value' => function ($model) {
            return Html::a($model->nama_perusahaan, ['penyedia/profile', 'id' => $model->hashid($model->id)], ['data-pjax' => 0]);
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'alamat_perusahaan',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'nomor_telepon',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'email_perusahaan',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'noWrap' => 'true',
        'template' => '{view} {update} {delete}',
        'vAlign' => 'middle',
        'urlCreator' => function($action, $model, $key, $index) {
                return Url::to(['penyedia/'.$action,'id'=>$key]);
        },
        'viewOptions' => ['role' => 'modal-remote','data-target' => '#' . $idmodal, 'title' => Yii::t('yii2-ajaxcrud', 'View'), 'data-toggle' => 'tooltip', 'class' => 'btn btn-sm btn-outline-success'],
        'updateOptions' => ['role' => 'modal-remote', 'data-target' => '#' . $idmodal, 'title' => Yii::t('yii2-ajaxcrud', 'Update'), 'data-toggle' => 'tooltip', 'class' => 'btn btn-sm btn-outline-primary'],
        'deleteOptions' => ['role' => 'modal-remote', 'title' => Yii::t('yii2-ajaxcrud', 'Delete'), 'class' => 'btn btn-sm btn-outline-danger',
            'data-confirm' => false, 'data-target' => '#' . $idmodal,
            'data-method' => false,// for overide yii data api
            'data-request-method' => 'post',
            'data-toggle' => 'tooltip',
            'data-confirm-title' => Yii::t('yii2-ajaxcrud', 'Delete'),
            'data-confirm-message' => Yii::t('yii2-ajaxcrud', 'Delete Confirm') ],
    ],
];