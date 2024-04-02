<?php

use app\assets\AppAsset;
use kartik\grid\GridView;
use mdm\admin\components\Helper;
use yii2ajaxcrud\ajaxcrud\BulkButtonWidget;
use yii2ajaxcrud\ajaxcrud\CrudAsset;
use yii\bootstrap4\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
$idmodal = $searchModel->hash;

$this->title = 'Paket Pengadaan';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);
AppAsset::register($this);
$this->registerJsFile('js/popper.min.js', ['depends' => '\yii\bootstrap4\BootstrapPluginAsset']);
?>
<div class="paket-pengadaan-index">
    <div id="ajaxCrudDatatable">
        <?= GridView::widget([
            'id' => 'crud-datatable',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax' => true,
            'columns' => require(__DIR__ . '/_columns.php'),
            'toolbar' => [
                [
                    'content' =>
                    Html::a(
                        '<i class="fa fa-plus"></i>',
                        ['paketpengadaan/create'],
                        ['role' => 'modal-remote', 'data-target' => '#' . $idmodal, 'title' => Yii::t('yii2-ajaxcrud', 'Create New') . ' Paket Pengadaans', 'class' => 'btn btn-outline-primary']
                    ) .
                        Html::a(
                            '<i class="fa fa-redo"></i>',
                            [''],
                            ['data-pjax' => 1, 'data-target' => '#' . $idmodal, 'class' => 'btn btn-outline-success', 'title' => Yii::t('yii2-ajaxcrud', 'Reset Grid')]
                        ) .
                        '{toggleData}' .
                        '{export}'
                ],
            ],
            'exportConfig' => [
                'html' => ['filename' => str_replace(' ', '', $this->title)],
                'csv' => ['filename' => str_replace(' ', '', $this->title)],
                'txt' => ['filename' => str_replace(' ', '', $this->title)],
                'xls' => ['filename' => str_replace(' ', '', $this->title)],
                'pdf' => [
                    'filename' => str_replace(' ', '', $this->title),
                    'config' => [
                        'methods' => [
                            'SetTitle' => 'Your Custom PDF Title',
                            'SetSubject' => 'Your PDF Subject',
                            'SetHeader' => ['Your Custom Header Text||Generated On: ' . date("r")],
                            'SetFooter' => ['|Page {PAGENO}|'],
                            'SetAuthor' => 'Your Author Name',
                            'SetCreator' => 'Your Creator Name',
                            'SetKeywords' => 'Your, Custom, Keywords',
                        ],
                        'options' => [
                            'title' => str_replace(' ', '', $this->title),
                            'subject' => '',
                            'keywords' => ''
                        ]
                    ],
                ],
                'json' => ['filename' => str_replace(' ', '', $this->title)],
            ],
            'striped' => true,
            'condensed' => true,
            'responsive' => true,
            'panel' => [
                'type' => 'default',
                'heading' => '<i class="fa fa-list"></i> <b>' . $this->title . '</b>',
                'before' => (Helper::checkRoute($this->context->uniqueId . '/dpp') ? BulkButtonWidget::widget([
                    'buttons' => Html::a(
                        '<i class="fa fa-flag"></i>&nbsp; ' . Yii::t('yii2-ajaxcrud', 'Buat DPP'),
                        ["/paketpengadaan/dpp"],
                        [
                            'class' => 'btn btn-danger btn-xs',
                            'role' => 'modal-remote-bulk',
                            'data-target' => '#' . $idmodal,
                            'data-confirm' => false,
                            'data-method' => false,
                            'data-request-method' => 'post',
                            'data-confirm-title' => Yii::t('yii2-ajaxcrud', 'Buat DPP'),
                            'data-confirm-message' => 'Ajukan Paket Pengadaan Ke DPP',
                            'data-toggle' => 'tooltip',
                            'data-original-title' => Yii::t('yii2-ajaxcrud', 'Buat DPP'),
                        ]
                    ),
                ]) : ''),
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
                    '<div class="clearfix"></div>',
            ]
        ]) ?>
    </div>
</div>
<?php Modal::begin([
    "id" => $idmodal,
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
]) ?>
<?php Modal::end(); ?>
<?= app\widgets\Importer::widget(['searchModel' => $searchModel, 'action' => '/paketpengadaan/import']); ?>