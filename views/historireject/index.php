<?php
use yii\helpers\{Url,Html};
use yii\bootstrap4\Modal;
use kartik\grid\GridView;
use yii2ajaxcrud\ajaxcrud\{CrudAsset,BulkButtonWidget};
$this->title = 'Histori Reject';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);
$idmodal = $searchModel->hash;
?>
<div class="histori-reject-index">
    <div id="ajaxCrudDatatable">
        <?=GridView::widget([
        'id' => 'crud-datatable',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax' => true,
        'columns' => require(__DIR__.'/_columns.php'),
        'toolbar' => [
        ['content'=>
        Html::a('<i class="fa fa-plus"></i>', ['create'],
        ['role' => 'modal-remote','data-target' => '#' . $idmodal, 'title' => Yii::t('yii2-ajaxcrud', 'Create New').' Histori Rejects', 'class' => 'btn btn-outline-primary']).
        Html::a('<i class="fa fa-redo"></i>', [''],
        ['data-pjax' => 1, 'data-target' => '#' . $idmodal,'class' => 'btn btn-outline-success', 'title' => Yii::t('yii2-ajaxcrud', 'Reset Grid')]).
        '{toggleData}'.
        '{export}'
        ],
        ],
        'striped' => true,
        'condensed' => true,
        'responsive' => true,
        'panel' => [
        'type' => 'default',
        'heading' => '<i class="fa fa-list"></i> <b>'.$this->title.'</b>',
        'before' =>'<em>* '.Yii::t('yii2-ajaxcrud', 'Resize Column').'</em>',
        'after' => BulkButtonWidget::widget([
        'buttons' => Html::a('<i class="fa fa-trash"></i>&nbsp; '.Yii::t('yii2-ajaxcrud', 'Delete All'),
        ["bulkdelete"] ,
        [
        'class' => 'btn btn-danger btn-xs',
        'role' => 'modal-remote-bulk',
        'data-confirm' => false,
        'data-target' => '#' . $idmodal,
        'data-method' => false,
        'data-request-method' => 'post',
        'data-confirm-title' => Yii::t('yii2-ajaxcrud', 'Delete'),
        'data-confirm-message' => Yii::t('yii2-ajaxcrud', 'Delete Confirm')
        ]),
        ]).
        '<div class="clearfix"></div>',
        ]
        ])?>
    </div>
</div>
<?php Modal::begin([
    "id" => $idmodal, "size" => "modal-xl",
    "footer" => "",
    "clientOptions" => [
        "tabindex" => false,
        "backdrop" => "static",
        "keyboard" => true,
    ],
    "options" => [
        "tabindex" => false
    ]
])?>
<?php Modal::end(); ?>