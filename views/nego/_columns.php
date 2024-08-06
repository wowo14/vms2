<?php
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
        [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'penawaran_id','format'=>'raw',
        'value'=>fn($d)=>Html::a($d->penawaran_id,['/penawaranpenyedia/view','id'=>$d->penawaran_id],['data-pjax'=>0,'target'=>'_blank']),
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'ammount',
        'value'=>fn($d)=>\Yii::$app->formatter->asCurrency($d->ammount),
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'accept',
        'value'=>fn($d)=>$d->accept?'Ya':'Tidak',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'created_by',
        'value'=>fn($d)=>$d->usercreated->username,
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'noWrap' => 'true',
        'template' => '{view} {update} {delete}',
        'vAlign' => 'middle',
        'urlCreator' => function($action, $model, $key, $index) {
                return Url::to([$action,'id'=>$key]);
        },
        'viewOptions' => ['role' => 'modal-remote', 'data-target' => '#' . $idmodal, 'title' => Yii::t('yii2-ajaxcrud', 'View'), 'data-toggle' => 'tooltip', 'class' => 'btn btn-sm btn-outline-success'],
        'updateOptions' => ['role' => 'modal-remote', 'data-target' => '#' . $idmodal, 'title' => Yii::t('yii2-ajaxcrud', 'Update'), 'data-toggle' => 'tooltip', 'class' => 'btn btn-sm btn-outline-primary'],
        'deleteOptions' => ['role' => 'modal-remote', 'title' => Yii::t('yii2-ajaxcrud', 'Delete'), 'class' => 'btn btn-sm btn-outline-danger',
            'data-confirm' => false,
            'data-method' => false, 'data-target' => '#' . $idmodal,
            'data-request-method' => 'post',
            'data-toggle' => 'tooltip',
            'data-confirm-title' => Yii::t('yii2-ajaxcrud', 'Delete'),
            'data-confirm-message' => Yii::t('yii2-ajaxcrud', 'Delete Confirm') ],
    ],
];