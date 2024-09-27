<?php
use yii\bootstrap4\Modal;
use yii\helpers\Html;
$idmodelnego="negodetails";
$idmodelpenawaran="frmpenawaranpenyedia";
return [
                    ['class' => 'kartik\grid\SerialColumn'],
                    [
                        'class' => '\kartik\grid\DataColumn',
                        'attribute' => 'nama_produk',
                    ],
                    [
                        'class' => '\kartik\grid\DataColumn',
                        'attribute' => 'qty',
                    ],
                    [
                        'class' => '\kartik\grid\DataColumn',
                        'attribute' => 'volume',
                    ],
                    [
                        'class' => '\kartik\grid\DataColumn',
                        'attribute' => 'satuan',
                    ],
                    [
                        'class' => '\kartik\grid\DataColumn',
                        'attribute' => 'hps_satuan',
                        'format'=>'currency',
                        'contentOptions'=>['class'=>'text-right']
                    ],
                    [
                        'attribute'=>'penawaran',
                        'format'=>'raw',
                        'contentOptions'=>['class'=>'text-right'],
                        'value'=>function($d)use($idmodelpenawaran){
                            return Html::a($d->penawaran??'#',['/paketpengadaan/postpenawaran','id'=>$d->id],['role' => 'modal-remote','data-pjax' => '0','data-target'=>'#'.$idmodelpenawaran,'title' => Yii::t('yii2-ajaxcrud', 'Penawaran')]);
                        },
                    ],
                    [
                        'attribute'=>'negosiasi',
                        'format'=>'raw',
                        'contentOptions'=>['class'=>'text-right'],
                        'value'=>function($d)use($idmodelnego){
                            return Html::a($d->negosiasi??'#',['/paketpengadaan/negoproduk','id'=>$d->id],['role' => 'modal-remote','data-pjax' => '0','data-target'=>'#'.$idmodelnego,'title' => Yii::t('yii2-ajaxcrud', 'Nego')]);
                        },
                    ],
                    [
                        'attribute'=>'totalhps',
                        'format'=>'raw',
                        'value'=>fn($d)=>($d->qty??1)*($d->volume??1)*$d->hps_satuan,
                        'contentOptions'=>['class'=>'text-right'],
                        'pageSummary' => true,
                        'pageSummaryOptions' => ['class' => 'auto unitsum', 'style' => 'text-align:right;'],
                        'pageSummaryFunc' => function ($data) {
                            return Yii::$app->formatter->asCurrency(array_sum(($data)));
                        },
                    ],
                    [
                        'attribute'=>'totalpenawaran',
                        'format'=>'raw',
                        'value'=>fn($d)=>($d->qty??1)*($d->volume??1)*$d->penawaran,
                        'contentOptions'=>['class'=>'text-right'],
                        'pageSummary' => true,
                        'pageSummaryOptions' => ['class' => 'auto unitsum', 'style' => 'text-align:right;'],
                        'pageSummaryFunc' => function ($data) {
                            return Yii::$app->formatter->asCurrency(array_sum(($data)));
                        },
                    ],
                    [
                        'attribute'=>'totalnegosiasi',
                        'format'=>'raw',
                        'value'=>fn($d)=>($d->qty??1)*($d->volume??1)*$d->negosiasi,
                        'contentOptions'=>['class'=>'text-right'],
                        'pageSummary' => true,
                        'pageSummaryOptions' => ['class' => 'auto unitsum', 'style' => 'text-align:right;'],
                        'pageSummaryFunc' => function ($data) {
                            return Yii::$app->formatter->asCurrency(array_sum(($data)));
                        },
                    ],
                ];
Modal::begin([
    "id" => $idmodelnego,
    "footer" => "",
    "size" => "modal-xl",
    "clientOptions" => [
        "tabindex" => false,
        "backdrop" => "static",
        "keyboard" => true,
        "focus" => true,
    ],
    "options" => [
        "tabindex" => true
    ]
]);