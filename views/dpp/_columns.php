<?php

use kartik\grid\GridView;
use yii\helpers\{Html, Url};

return [
    // [
    //     'class' => 'kartik\grid\CheckboxColumn',
    //     'width' => '20px',
    // ],
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
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'nomor_dpp',
        'format' => 'raw',
        'contentOptions' => ['class' => 'text-center'],
        'value' => fn($d) =>
        Html::a($d->nomor_dpp, ['/dpp/tab', 'id' => $d->id], ['class' => 'bg-white'])
            ?? ''
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'tanggal_dpp',
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
        'attribute' => 'bidang_bagian',
        'value' => fn($d) => $d->unit->unit ?? ''
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'paket_id',
        'format' => 'raw',
        'value' => fn($d) => $d->paketpengadaan->nomornamapaket ?? ''
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'status_review',
        'label'=>'Review'
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'pejabat_pengadaan',
        'value' => fn($d) => isset($d->pejabat) ? $d->pejabat->nama . $d->countPejabatWithNullPemenang($d->pejabat_pengadaan) : ''
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'admin_pengadaan',
        'value' => fn($d) => isset($d->staffadmin)?$d->staffadmin->nama .$d->countAdminWithNullPemenang($d->admin_pengadaan) :''
    ],
    // [
    //     'class' => '\kartik\grid\DataColumn',
    //     'attribute' => 'kode',
    //     'value' => fn ($d) => $d->kode ?? ''
    // ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => true,
        'noWrap' => 'false',
        'template' => '{reject} {approve} {ceklistadmin} {printceklistadmin} {formreview} {reviewdpp} {cetakpenugasan} {cetaklampiran} {view} {update} {delete}',
        'vAlign' => 'middle',
        'contentOptions' => function ($model, $key, $index, $column) {
            if (isset($model->paketpengadaan) && $model->paketpengadaan->pemenang) {
                return ['style' => 'background-color: white;'];
            }
            return []; // return an empty array if the condition is not met
        },
        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to([$action, 'id' => $key]);
        },
        'visibleButtons' => [
            // 'penugasan'=>function($d){
            //     return isset($d->paketpengadaan) && !$d->paketpengadaan->pemenang;
            // },
            'approve' => function ($d) {
                return isset($d->is_approved) && !$d->paketpengadaan->pemenang;
            },
            'delete' => function ($d) {
                return isset($d->paketpengadaan) && !$d->paketpengadaan->pemenang;
            },
            'update' => function ($d) {
                return isset($d->paketpengadaan) && !$d->paketpengadaan->pemenang;
            },
            'reject' => function ($d) {
                return isset($d->paketpengadaan) && !$d->paketpengadaan->pemenang;
            },
            // 'ceklistadmin' => function ($d) {
            //     return isset($d->paketpengadaan) && !$d->paketpengadaan->pemenang;
            // },
            // 'printceklistadmin'=>function($d){
            //     return isset($d->paketpengadaan) && !$d->paketpengadaan->pemenang;
            // },
            'formreview' => function ($d) {
                return isset($d->paketpengadaan) && !$d->paketpengadaan->pemenang;
            }
        ],
        'buttons' => [
            'cetakpenugasan' => function ($url, $model, $key) {
                return Html::a(
                    '<span class="fa fa-print"></span>',
                    $url,
                    ['class' => 'btn btn-sm btn-outline-info', 'data-pjax' => 0, 'title' => 'Penugasan', 'data-toggle' => 'tooltip']
                );
            },
            'cetaklampiran' => function ($url, $model, $key) {
                return Html::a(
                    '<span class="fa fa-print"></span>',
                    $url,
                    ['class' => 'btn btn-sm btn-outline-info', 'data-pjax' => 0, 'title' => 'Cetak Lampiran', 'data-toggle' => 'tooltip']
                );
            },
            'approve' => function ($url, $model, $key) {
                return Html::a(
                    '<span class="fa fa-check-double"></span>',
                    $url,
                    ['class' => 'btn btn-sm btn-outline-info', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote', 'title' => 'Approve', 'data-toggle' => 'tooltip']
                );
            },
            'ceklistadmin' => function ($url, $model, $key) {
                return Html::a(
                    '<span class="fa fa-archive"></span>',
                    $url,
                    ['class' => 'btn btn-sm btn-outline-warning', 'data-pjax' => 0, 'title' => 'Ceklist Admin', 'data-toggle' => 'tooltip']
                );
            },
            'printceklistadmin' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-print"></span>', $url, [
                    'title' => Yii::t('yii2-ajaxcrud', 'Print Ceklist Admin'),
                    'data-pjax' => '0',
                    'class' => 'btn btn-sm btn-outline-primary'
                ]);
            },
            'reject' => function ($url, $model, $key) {
                return Html::a(
                    '<span class="fa fa-ban"></span>',
                    $url,
                    ['class' => 'btn btn-sm btn-outline-danger', 'data-pjax' => 0, 'title' => 'Reject', 'data-toggle' => 'tooltip']
                );
            },
            'formreview' => function ($url, $model, $key) {
                return Html::a(
                    '<span class="fa fa-file"></span>',
                    $url,
                    ['class' => 'btn btn-sm btn-outline-primary', 'data-pjax' => 0, 'title' => 'Form reviewdpp', 'data-toggle' => 'tooltip']
                );
            },
            'reviewdpp' => function ($url, $model, $key) {
                return Html::a(
                    '<span class="fa fa-print"></span>',
                    $url,
                    ['class' => 'btn btn-sm btn-outline-info', 'data-pjax' => 0, 'title' => 'Cetak reviewdpp', 'data-toggle' => 'tooltip']
                );
            },
            'view' => function ($url, $model, $key) use ($idmodal) {
                return Html::a(
                    '<span class="fa fa-eye"></span>',
                    $url,
                    ['role' => 'modal-remote', 'data-target' => '#' . $idmodal, 'title' => Yii::t('yii2-ajaxcrud', 'View'), 'data-toggle' => 'tooltip', 'class' => 'btn btn-sm btn-outline-success']
                );
            },
            'update' => function ($url, $model, $key) use ($idmodal) {
                return Html::a(
                    '<span class="fa fa-pencil-alt"></span>',
                    $url,
                    ['role' => 'modal-remote', 'data-target' => '#' . $idmodal, 'title' => Yii::t('yii2-ajaxcrud', 'Update'), 'data-toggle' => 'tooltip', 'class' => 'btn btn-sm btn-outline-primary'],
                );
            },
            'delete' => function ($url, $model, $key) use ($idmodal) {
                return Html::a(
                    '<span class="fa fa-trash"></span>',
                    $url,
                    [
                        'role' => 'modal-remote',
                        'title' => Yii::t('yii2-ajaxcrud', 'Delete'),
                        'class' => 'btn btn-sm btn-outline-danger',
                        'data-confirm' => false,
                        'data-target' => '#' . $idmodal,
                        'data-method' => false, // for overide yii data api
                        'data-request-method' => 'post',
                        'data-toggle' => 'tooltip',
                        'data-confirm-title' => Yii::t('yii2-ajaxcrud', 'Delete'),
                        'data-confirm-message' => Yii::t('yii2-ajaxcrud', 'Delete Confirm')
                    ],
                );
            },
        ],
        // 'viewOptions' => ['role' => 'modal-remote', 'data-target' => '#' . $idmodal, 'title' => Yii::t('yii2-ajaxcrud', 'View'), 'data-toggle' => 'tooltip', 'class' => 'btn btn-sm btn-outline-success'],
        // 'updateOptions' => ['role' => 'modal-remote', 'data-target' => '#' . $idmodal, 'title' => Yii::t('yii2-ajaxcrud', 'Update'), 'data-toggle' => 'tooltip', 'class' => 'btn btn-sm btn-outline-primary'],
        // 'deleteOptions' => [
        //     'role' => 'modal-remote', 'title' => Yii::t('yii2-ajaxcrud', 'Delete'), 'class' => 'btn btn-sm btn-outline-danger',
        //     'data-confirm' => false, 'data-target' => '#' . $idmodal,
        //     'data-method' => false, // for overide yii data api
        //     'data-request-method' => 'post',
        //     'data-toggle' => 'tooltip',
        //     'data-confirm-title' => Yii::t('yii2-ajaxcrud', 'Delete'),
        //     'data-confirm-message' => Yii::t('yii2-ajaxcrud', 'Delete Confirm')
        // ],
    ],
];
