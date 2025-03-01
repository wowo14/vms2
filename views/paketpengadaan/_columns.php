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
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'nomor',
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
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'nama_paket',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'kode_program',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'kode_kegiatan',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'noWrap' => 'true',
        'template' => ' {lampiran} {kirimulang} {import-product} {view} {update} {delete}',
        'vAlign' => 'middle',
        'contentOptions'=>function ($model, $key, $index, $column) {
            if($model->pemenang || $model->alasan_reject || $model->dpp){
                return ['style' => 'background-color: white;'];
            }
        },
        'visibleButtons'=>[
            'delete'=>function($d){
                return !$d->pemenang;
            },
            'update'=>function($d){
                return !$d->pemenang;
            },
            'lampiran'=>function($d){
                return !$d->pemenang;
            },
            'import-product'=>function($d){
                return !$d->pemenang;
            },
            'ceklistadmin'=>function($d){
                return !$d->pemenang;
            },
            'printceklistadmin'=>function($d){
                return !$d->pemenang && $d->addition;
            },
        ],
        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to([$action, 'id' => $key]);
        },
        'buttons' => [
            'ceklistadmin' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-archive"></span>', $url, [
                    'title' => Yii::t('yii2-ajaxcrud', 'Ceklist Admin'),
                    'data-pjax' => '0', 'class' => 'btn btn-sm btn-outline-warning'
                ]);
            },
            'printceklistadmin' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-print"></span>', $url, [
                    'title' => Yii::t('yii2-ajaxcrud', 'Print Ceklist Admin'),
                    'data-pjax' => '0', 'class' => 'btn btn-sm btn-outline-primary'
                ]);
            },
            'lampiran' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-paperclip"></span>', $url, [
                    'title' => Yii::t('yii2-ajaxcrud', 'Lampiran'),
                    'data-pjax' => '0', 'class' => 'btn btn-sm btn-outline-warning'
                ]);
            },
            'import-product' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-paperclip"></span>', $url, [
                    'title' => Yii::t('yii2-ajaxcrud', 'Upload Produk'),
                    'data-pjax' => '0', 'class' => 'btn btn-sm btn-outline-primary'
                ]);
            },
            'kirimulang' => function ($url, $model, $key) {
                return ($model->alasan_reject && $model->tanggal_reject) ? Html::a('<span class="fa fa-plane"></span>', $url, [
                    'title' => Yii::t('yii2-ajaxcrud', 'Kirim Ulang DPP'),
                    'data-pjax' => '0', 'class' => 'btn btn-sm btn-outline-warning'
                ]) : '';
            },
        ],
        'viewOptions' => ['role' => 'modal-remote', 'data-target' => '#' . $idmodal, 'title' => Yii::t('yii2-ajaxcrud', 'View'), 'data-toggle' => 'tooltip', 'class' => 'btn btn-sm btn-outline-success'],
        'updateOptions' => ['role' => 'modal-remote', 'data-target' => '#' . $idmodal, 'title' => Yii::t('yii2-ajaxcrud', 'Update'), 'data-toggle' => 'tooltip', 'class' => 'btn btn-sm btn-outline-primary'],
        'deleteOptions' => [
            'role' => 'modal-remote', 'title' => Yii::t('yii2-ajaxcrud', 'Delete'), 'class' => 'btn btn-sm btn-outline-danger',
            'data-confirm' => false, 'data-target' => '#' . $idmodal,
            'data-method' => false, // for overide yii data api
            'data-request-method' => 'post',
            'data-toggle' => 'tooltip',
            'data-confirm-title' => Yii::t('yii2-ajaxcrud', 'Delete'),
            'data-confirm-message' => Yii::t('yii2-ajaxcrud', 'Delete Confirm')
        ],
    ],
];
