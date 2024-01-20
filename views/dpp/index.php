<?php
use kartik\grid\GridView;
use sdelfi\datatables\DataTables;
use yii2ajaxcrud\ajaxcrud\BulkButtonWidget;
use yii2ajaxcrud\ajaxcrud\CrudAsset;
use yii\bootstrap4\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
$this->registerJsFile('js/popper.min.js', ['depends' => '\yii\bootstrap4\BootstrapPluginAsset']);
$this->title = 'Dpp';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);
?>
<?php
// DataTables::widget([
//     'dataProvider' => $dataProvider,
//     'filterModel' => $searchModel,
//     'columns' => [
//         'id',
//         'nomor_dpp',
//         'tanggal_dpp',
//         [
//             'attribute' => 'bidang_bagian',
//             'value' => fn ($d) => $d->unit->unit ?? ''
//         ],
//         ['attribute' => 'paket_id', 'value' => fn ($d) => $d->paketpengadaan->nomornamapaket ?? ''],
//         'status_review',
//     ],
//     'clientOptions' => [
//         "lengthMenu" => [[20, -1], [20, Yii::t('app', "All")]],
//         "info" => false,
//         "responsive" => true,
//         "dom" => 'lfTrtip',
//         "tableTools" => [
//             //empty for load button assets
//         ],
//         'buttons'   => ['copy', 'excel', 'pdf'],
//     ],
// ]);
?>
<div class="dpp-index">
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
                        ['create'],
                        ['role' => 'modal-remote', 'title' => Yii::t('yii2-ajaxcrud', 'Create New') . ' Dpps', 'class' => 'btn btn-outline-primary']
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
    "id" => "ajaxCrudModal","size"=>"modal-xl",
    "footer" => "",
    "clientOptions" => [
        "tabindex" => false,
        "backdrop" => "static",
        "keyboard" => false,
    ],
    "options" => [
        "tabindex" => false
    ]
]) ?>
<?php Modal::end(); ?>