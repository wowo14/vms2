<?php
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
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'penyedia_id',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'nama_alat',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'jumlah',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'kapasitas',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'merk_tipe',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'noWrap' => 'true',
        'template' => '{view} {update} {delete}',
        'vAlign' => 'middle',
        'urlCreator' => function($action, $model, $key, $index) {
                return Url::to(['peralatankerja'.$action,'id'=>$key]);
        },
        'viewOptions' => ['role' => 'modal-remote', 'data-target' => '#' . $idmodal, 'title' => Yii::t('yii2-ajaxcrud', 'View'), 'data-toggle' => 'tooltip', 'class' => 'btn btn-sm btn-outline-success'],
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