<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap4\Modal;
use kartik\grid\GridView;
use yii2ajaxcrud\ajaxcrud\CrudAsset;
use yii2ajaxcrud\ajaxcrud\BulkButtonWidget;
$idmodal = $searchModel->hash;$this->title = 'Penawaran Pengadaan';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);
?>
<div class="penawaran-pengadaan-index">
    <div id="ajaxCrudDatatable">
        <?= GridView::widget([
            'id' => 'crud-datatable',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax' => true,
            'columns' => require(__DIR__ . '/_columns.php'),
            'rowOptions' => function ($model) {
                if (isset($model->pemenang) && $model->pemenang) {
                    return ['class' => 'bg-primary'];
                }
                return []; // return an empty array if the condition is not met
            },
            'toolbar' => [
                [
                    'content' =>
                    Html::a(
                        '<i class="fa fa-plus"></i>',
                        ['/penawaranpenyedia/create'],
                        ['role' => 'modal-remote', 'data-target' => '#' . $idmodal, 'title' => Yii::t('yii2-ajaxcrud', 'Create New') . ' Penawaran Pengadaans', 'class' => 'btn btn-outline-primary']
                    ) .
                        Html::a(
                            '<i class="fa fa-redo"></i>',
                            ['/penawaranpenyedia'],
                            ['data-pjax' => 1, 'data-target' => '#' . $idmodal, 'class' => 'btn btn-outline-success', 'title' => Yii::t('yii2-ajaxcrud', 'Reset Grid')]
                        ) .
                        '{toggleData}' .
                        '{export}'
                ],
            ],
            'striped' => true,
            'condensed' => true,
            'responsive' => true,
            'panel' => [
                'type' => 'default',
                'heading' => '<i class="fa fa-list"></i> <b>' . $this->title . '</b>',
                'before' => '<em>* ' . Yii::t('yii2-ajaxcrud', 'Resize Column') . '</em>',
                'after' => BulkButtonWidget::widget([
                    'buttons' => Html::a(
                        '<i class="fa fa-trash"></i>&nbsp; ' . Yii::t('yii2-ajaxcrud', 'Delete All'),
                        ["bulkdelete"],
                        [
                            'class' => 'btn btn-danger btn-xs',
                            'role' => 'modal-remote-bulk',
                            'data-target' => '#' . $idmodal,
                            'data-confirm' => false,
                            'data-method' => false,
                            'data-request-method' => 'post',
                            'data-confirm-title' => Yii::t('yii2-ajaxcrud', 'Delete'),
                            'data-confirm-message' => Yii::t('yii2-ajaxcrud', 'Delete Confirm')
                        ]
                    ),
                ]) .
                    'Legend:
                    <span class="bg-primary p-1 m-0 text-white">Selesai</span>
                    <div class="clearfix"></div>',
            ]
        ]) ?>
    </div>
</div>
<?php Modal::begin([
    "id" => $idmodal,
    "footer" => "",
    'size' => 'modal-xl',
    "clientOptions" => [
        "tabindex" => false,
        "backdrop" => "static",
        "keyboard" => true,
    ],
    "options" => [
        "tabindex" => true
    ]
]) ?>
<?php Modal::end(); ?>