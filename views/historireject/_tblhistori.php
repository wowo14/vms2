<?php
use yii\grid\GridView;
use yii\helpers\Html;
echo GridView::widget([
            'id' => 'tbl_histori-datatable',
            'dataProvider' => new yii\data\ArrayDataProvider([
                'allModels' => $model,
                'pagination' => false]),
            'layout' => '{items}',
            'tableOptions' => ['class' => 'table responsive'],
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                ],
                // 'nomor',
                // 'nama_paket',
                'alasan_reject:ntext',
                'tanggal_reject',
                'kesimpulan:ntext',
                'tanggal_dikembalikan',
                'tanggapan_ppk:ntext',
                ['attribute'=>'file_tanggapan','format'=>'html',
                'value'=>function($model){
                    if($model['file_tanggapan']){
                        return Html::a($model['file_tanggapan'], \Yii::getAlias('@web/uploads/') . $model['file_tanggapan'], ['target' => '_blank','data-pjax'=>0]);
                    }
                }
                ]
            ]
]);