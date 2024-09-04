<?php
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
                        'format'=>'currency',
                        'contentOptions'=>['class'=>'text-right']
                    ],
                    [
                        'attribute'=>'negosiasi',
                        'format'=>'currency',
                        'contentOptions'=>['class'=>'text-right']
                    ],
                    [
                        'attribute'=>'totalhps',
                        'format'=>'raw',
                        'value'=>function($d){
                            return $d->qty*$d->volume*$d->hps_satuan;
                        },
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
                        'value'=>function($d){
                            return $d->qty*$d->volume*$d->penawaran;
                        },
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
                        'value'=>function($d){
                            return $d->qty*$d->volume*$d->negosiasi;
                        },
                        'contentOptions'=>['class'=>'text-right'],
                        'pageSummary' => true,
                        'pageSummaryOptions' => ['class' => 'auto unitsum', 'style' => 'text-align:right;'],
                        'pageSummaryFunc' => function ($data) {
                            return Yii::$app->formatter->asCurrency(array_sum(($data)));
                        },
                    ],
                ];