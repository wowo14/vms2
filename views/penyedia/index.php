<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap4\Modal;
use kartik\grid\GridView;
use yii2ajaxcrud\ajaxcrud\CrudAsset;
use yii2ajaxcrud\ajaxcrud\BulkButtonWidget;

$this->title = 'Penyedia';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);
$this->registerJsFile('js/popper.min.js', ['depends' => '\yii\bootstrap4\BootstrapPluginAsset']);
?>
<div class="penyedia-index">
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
                        ['penyedia/create'],
                        ['role' => 'modal-remote', 'title' => Yii::t('yii2-ajaxcrud', 'Create New') . ' Penyedias', 'class' => 'btn btn-outline-primary']
                    ) .
                        Html::a(
                            '<i class="fa fa-redo"></i>',
                            [''],
                            ['data-pjax' => 1, 'class' => 'btn btn-outline-success', 'title' => Yii::t('yii2-ajaxcrud', 'Reset Grid')]
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
                'before' => '<em>* ' . Yii::t('yii2-ajaxcrud', 'Resize Column') . '</em>',
                'after' => BulkButtonWidget::widget([
                    'buttons' => Html::a(
                        '<i class="fa fa-trash"></i>&nbsp; ' . Yii::t('yii2-ajaxcrud', 'Delete All'),
                        ["bulkdelete"],
                        [
                            'class' => 'btn btn-danger btn-xs',
                            'role' => 'modal-remote-bulk',
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
    "id" => "ajaxCrudModal",
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