<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
echo '<h4 style="text-align:center">'.$title.'</h4><br>';
if (!empty($paketpengadaan->attachments)) {
            $data=collect($paketpengadaan->attachments)->map(function ($el) {
                $el->uri = Url::home(true).''.
                str_replace('/uploads/', 'uploads/', $el->uri);
                return $el;
            });
            echo GridView::widget([
                'dataProvider' => new yii\data\ArrayDataProvider([
                    'allModels' =>$data->toArray(),
                    'pagination' => false
                ]),
                'summary' => false,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                    ],
                    [
                        'attribute' => 'name',
                        'label'=>'Nama File',
                    ],
                    [
                        'attribute' => 'jenis_dokumen',
                        'value'=>fn($r)=>$r->jenisdokumen->value??''
                    ],
                    'uri',
                ],
            ]);
        }