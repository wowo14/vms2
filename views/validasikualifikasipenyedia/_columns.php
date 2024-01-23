<?php
use yii\helpers\Html;
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
        'value'=>'penyedia.nama_perusahaan',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'paket_pengadaan_id',
        'value'=>'paketpengadaan.nomornamapaket',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'keperluan',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'is_active',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'noWrap' => 'true',
        'template' => '{adddetail} {view} {update} {delete}',
        'vAlign' => 'middle',
        'urlCreator' => function($action, $model, $key, $index) {
                return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'adddetail' => function ($url, $model) {
                return Html::a('<span class="fa fa-plus"></span>', $url, [
                    'title' => Yii::t('yii2-ajaxcrud', 'Add Detail'),
                    'data-toggle' => 'tooltip',
                    'data-pjax' => '0',
                ]);
            },
        ],
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