<?php
use kartik\grid\GridView;
use yii\helpers\{Html,Url};
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
        'detailUrl' => Url::to(['/penawaranpenyedia/detailnego']),
        'hiddenFromExport' => true
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'paket_id',
        'value' => 'paketpengadaan.nomornamapaket',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => \app\models\PaketPengadaan::collectAll()->pluck('nama_paket', 'id')->toArray(),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Pilih Paket'],
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'penyedia_id',
        'value' => 'vendor.nama_perusahaan',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => $searchModel->vendors->pluck('nama_perusahaan', 'id')->toArray(),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Pilih penyedia'],
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'masa_berlaku',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'kode','format'=>'raw',
        'value' => fn ($d) =>Html::a($d->kode, ['/penawaranpenyedia/nego', 'id' => $d->id],
            ['class'=>'bg-white','role' => 'modal-remote','data-pjax' => '1','data-target'=>'#'.$idmodal,'title' => Yii::t('yii2-ajaxcrud', 'Nego'),
            'aria-label' => Yii::t('yii2-ajaxcrud', 'Nego'),]
        )?? ''
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'tanggal_mendaftar',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'noWrap' => 'true',
        'template' => '{view} {update} {delete}',
        'vAlign' => 'middle',
        'buttons' => [
            'view' => function ($url, $model, $key) {
                $url = '/penawaranpenyedia/view?id=' . $key;
                $options = [
                    'title' => Yii::t('yii2-ajaxcrud', 'View'),
                    'aria-label' => Yii::t('yii2-ajaxcrud', 'View'),
                    'data-pjax' => '0',
                ];
                return Html::a('<i class="fa fa-eye"></i>', $url, $options);
            },
            'update' => function ($url, $model, $key) {
                $url = '/penawaranpenyedia/update?id=' . $key;
                $options = [
                    'title' => Yii::t('yii2-ajaxcrud', 'Update'),
                    'aria-label' => Yii::t('yii2-ajaxcrud', 'Update'),
                    'data-pjax' => '0',
                ];
                return Html::a('<i class="fa fa-edit"></i>', $url, $options);
            },
            'delete' => function ($url, $model, $key) {
                $url = '/penawaranpenyedia/delete?id=' . $key;
                $options = [
                    'title' => Yii::t('yii2-ajaxcrud', 'Delete'),
                    'aria-label' => Yii::t('yii2-ajaxcrud', 'Delete'),
                    'data-pjax' => '0',
                    'data-confirm' => false,
                    'data-method' => false, // for override yii data api
                    'data-request-method' => 'post',
                ];
                return Html::a('<i class="fa fa-trash"></i>', $url, $options);
            }
        ],
        'visibleButtons'=>[
            'update'=>function($d){
                return isset($d->paketpengadaan) && !$d->paketpengadaan->pemenang;
            },
            'delete'=>function($d){
                return isset($d->paketpengadaan) && !$d->paketpengadaan->pemenang;
            },
        ],
        'viewOptions' => ['role' => 'modal-remote', 'data-target' => '#' . $idmodal, 'title' => Yii::t('yii2-ajaxcrud', 'View'), 'data-toggle' => 'tooltip', 'class' => 'btn btn-sm btn-outline-success'],
        'updateOptions' => ['role' => 'modal-remote', 'data-target' => '#' . $idmodal, 'title' => Yii::t('yii2-ajaxcrud', 'Update'), 'data-toggle' => 'tooltip', 'class' => 'btn btn-sm btn-outline-primary'],
        'deleteOptions' => [
            'role' => 'modal-remote', 'title' => Yii::t('yii2-ajaxcrud', 'Delete'), 'class' => 'btn btn-sm btn-outline-danger',
            'data-confirm' => false, 'data-target' => '#' . $idmodal,
            'data-method' => false, // for override yii data api
            'data-request-method' => 'post',
            'data-toggle' => 'tooltip',
            'data-confirm-title' => Yii::t('yii2-ajaxcrud', 'Delete'),
            'data-confirm-message' => Yii::t('yii2-ajaxcrud', 'Delete Confirm')
        ],
    ],
];
