<?php
use app\assets\AppAsset;
use kartik\grid\GridView;
use mdm\admin\components\Helper;
use sdelfi\datatables\DataTables;
use yii2ajaxcrud\ajaxcrud\BulkButtonWidget;
use yii2ajaxcrud\ajaxcrud\CrudAsset;
use yii\bootstrap4\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
$idmodal = $searchModel->hash;
AppAsset::register($this);
CrudAsset::register($this);
$this->title = 'Dpp';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dpp-index">
    <div id="ajaxCrudDatatable">
        <?= GridView::widget([
            'id' => 'crud-datatable',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax' => true,
            'columns' => require(__DIR__ . '/_columns.php'),
            'rowOptions' => function ($model) {
                if (isset($model->paketpengadaan) && !$model->paketpengadaan->pemenang) {
                    return ['class' => 'bg-warning'];
                }
                return []; // return an empty array if the condition is not met
            },
            'toolbar' => [
                [
                    'content' =>
                    // Html::a(
                    //     '<i class="fa fa-plus"></i>',
                    //     ['dpp/create'],
                    //     ['role' => 'modal-remote', 'data-target' => '#' . $idmodal, 'title' => Yii::t('yii2-ajaxcrud', 'Create New') . ' Dpps', 'class' => 'btn btn-outline-primary']
                    // ) .
                        Html::a(
                            '<i class="fa fa-redo"></i>',
                            [''],
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
                // 'before' => (BulkButtonWidget::widget([
                //     'buttons' => (Helper::checkRoute($this->context->uniqueId . '/assign') ? Html::a(
                //         '<i class="fa fa-flag"></i>&nbsp; ' . Yii::t('yii2-ajaxcrud', ' Assign Petugas '),
                //         ["/dpp/assign"],
                //         [
                //             'class' => 'btn btn-primary btn-xs',
                //             'role' => 'modal-remote-bulk',
                //             'data-target' => '#' . $idmodal,
                //             'data-confirm' => false,
                //             'data-method' => false,
                //             'data-request-method' => 'post',
                //             'data-confirm-title' => Yii::t('yii2-ajaxcrud', 'Assign Pejabat Pengadaan'),
                //             'data-confirm-message' => $searchModel::formassignpetugas(),
                //             'data-toggle' => 'tooltip',
                //             'data-original-title' => Yii::t('yii2-ajaxcrud', 'Assign Petugas'),
                //         ]
                //     ) : '') . ' ' . (Helper::checkRoute($this->context->uniqueId . '/assignadmin') ? Html::a(
                //         '<i class="fa fa-flag"></i>&nbsp; ' . Yii::t('yii2-ajaxcrud', ' Assign Admin'),
                //         ["/dpp/assignadmin"],
                //         [
                //             'class' => 'btn btn-warning btn-xs',
                //             'role' => 'modal-remote-bulk',
                //             'data-target' => '#' . $idmodal,
                //             'data-confirm' => false,
                //             'data-method' => false,
                //             'data-request-method' => 'post',
                //             'data-confirm-title' => Yii::t('yii2-ajaxcrud', 'Assign Admin Pengadaan'),
                //             'data-confirm-message' => $searchModel::formassignadmin(),
                //             'data-toggle' => 'tooltip',
                //             'data-original-title' => Yii::t('yii2-ajaxcrud', 'Assign Admin Pengadaan'),
                //         ]
                //     ) : '')
                // ])),
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
                    <span class="bg-warning p-1 m-0 text-white">On Process</span>
                    <span class="bg-light p-1 m-0 text-white">Selesai</span>
                    <div class="clearfix"></div>',
            ]
        ]) ?>
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
        "tabindex" => true
    ]
]) ?>
<?php Modal::end(); ?>