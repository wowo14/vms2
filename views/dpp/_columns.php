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
        'detailUrl' => Url::to(['/dpp/detailpaket']),
        'hiddenFromExport' => true
    ],
        [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'nomor_dpp',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'tanggal_dpp',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'bidang_bagian',
        'value'=>fn($d)=>$d->unit->unit??''
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'paket_id','format'=>'raw',
        'value'=>fn($d)=>$d->paketpengadaan->nomornamapaket??''
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'status_review',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'noWrap' => 'true',
        'template' => '{approve} {cetak} {reviewdpp} {formreview} {view} {update} {delete}',
        'vAlign' => 'middle',
        'urlCreator' => function($action, $model, $key, $index) {
                return Url::to([$action,'id'=>$key]);
        },
        'buttons' => [
            'approve' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-check-double"></span>', $url,
                ['class' => 'btn btn-sm btn-outline-danger','role' => 'modal-remote', 'title' => 'Approve', 'data-toggle' => 'tooltip']);
            },
            'cetak' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-print"></span>', $url,
                ['class' => 'btn btn-sm btn-outline-success', 'data-pjax' => 0, 'title' => 'Cetak', 'data-toggle' => 'tooltip']);
            },
            'formreview' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-print"></span>', $url,
                ['class' => 'btn btn-sm btn-outline-primary', 'data-pjax' => 0, 'title' => 'reviewdpp', 'data-toggle' => 'tooltip']);
            },
            'reviewdpp' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-print"></span>', $url,
                ['class' => 'btn btn-sm btn-outline-primary', 'data-pjax' => 0, 'title' => 'reviewdpp', 'data-toggle' => 'tooltip']);
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