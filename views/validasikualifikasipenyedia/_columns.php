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
    [
        'class' => '\kartik\grid\ExpandRowColumn',
        'value' => function ($model, $key, $index, $column) {
            return GridView::ROW_COLLAPSED;
        },
        'detailUrl' => Url::to(['/validasikualifikasipenyedia/detail']),
        'hiddenFromExport' => true
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'penyedia_id',
        'value' => 'vendor.nama_perusahaan',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => \app\models\Penyedia::collectAll(['active' => 1])->pluck('nama_perusahaan', 'id')->toArray(),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Pilih penyedia'],
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'paket_pengadaan_id',
        'value' => 'paketpengadaan.nomornamapaket',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => \app\models\PaketPengadaan::collectAll()->pluck('nama_paket', 'id')->toArray(),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Pilih paket'],
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'keperluan',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => $searchModel::collectAll()->pluck('keperluan', 'keperluan')->toArray(),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Pilih keperluan'],
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'tgl_paket',
        'label' => 'Tanggal Pengadaan',
        'value' => 'paketpengadaan.tanggal_paket',
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
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'noWrap' => 'true',
        'template' => '{assestment} {view} {update} {delete}',
        'vAlign' => 'middle',
        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to(['validasikualifikasipenyedia/' . $action, 'id' => $key]);
        },
        'visibleButtons'=>[
            'assestment'=>function($d){
                return !$d->paketpengadaan->pemenang;
            },
            'delete'=>function($d){
                return !$d->paketpengadaan->pemenang;
            },
            'update'=>function($d){
                return !$d->paketpengadaan->pemenang;
            },
        ],
        'buttons' => [
            'assestment' => function ($url, $model) {
                return Html::a('<span class="fa fa-plus"></span>', $url, [
                    'class' => 'btn btn-sm btn-outline-primary',
                    'title' => Yii::t('yii2-ajaxcrud', 'Add Detail'),
                    'data-toggle' => 'tooltip',
                    'data-pjax' => '0',
                ]);
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
