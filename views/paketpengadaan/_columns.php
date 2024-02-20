<?php
use kartik\grid\GridView;
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
    'ExpandRowColumn' =>
    [
        'class' => '\kartik\grid\ExpandRowColumn',
        'value' => function ($model, $key, $index, $column) {
            return GridView::ROW_COLLAPSED;
        },
        'detailUrl' => Url::to(['/paketpengadaan/details']),
        'hiddenFromExport' => true
    ],
        [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'nomor',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'tanggal_paket',
        'filterType' => \kartik\grid\GridView::FILTER_DATE_RANGE,
        'filterWidgetOptions' => ([
            'attribute' => 'only_date',
            'presetDropdown' => true,
            'convertFormat' => false,
            'pluginOptions' => [
                'separator' => ' - ',
                'format' => 'YYYY-MM-DD',
                'locale' => [
                    'format' => 'YYYY-MM-DD'
                ],
            ],
        ]),
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'nama_paket',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'kode_program',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'kode_kegiatan',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'noWrap' => 'true',
        'template' => '{lampiran} {view} {update} {delete}',
        'vAlign' => 'middle',
        'urlCreator' => function($action, $model, $key, $index) {
                return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'lampiran' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-paperclip"></span>', $url, [
                    'title' => Yii::t('yii2-ajaxcrud', 'Lampiran'),
                    'data-pjax' => '0','class'=> 'btn btn-sm btn-outline-warning'
                ]);
            }
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