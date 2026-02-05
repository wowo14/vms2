<?php
use yii\helpers\Url;
use yii\helpers\Html;
use app\assets\AppAsset;
use kartik\grid\GridView;
use yii\bootstrap4\Modal;
use yii2ajaxcrud\ajaxcrud\CrudAsset;

$this->title = 'Report Penyedia Detail';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
AppAsset::register($this);
?>
<div class="report-penyedia-index">
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
                            '<i class="fa fa-sync"></i> Sync from System',
                            ['sync'],
                            ['class' => 'btn btn-outline-primary', 'title' => 'Sync data from existing evaluation']
                        ) .
                        Html::a(
                            '<i class="fa fa-file-excel"></i> Import Excel',
                            ['import'],
                            ['role' => 'modal-remote', 'class' => 'btn btn-outline-info', 'title' => 'Import data from Excel']
                        ) .
                        Html::a(
                            '<i class="fa fa-redo"></i>',
                            [''],
                            ['data-pjax' => 1, 'class' => 'btn btn-outline-success', 'title' => 'Reset Grid']
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
                'heading' => '<i class="fa fa-list"></i> <b>' . Html::encode($this->title) . '</b>',
                'before' => '<em>* Resize columns as needed.</em>',
                'after' => '<div class="clearfix"></div>',
            ]
        ]) ?>
    </div>
</div>
<?php Modal::begin([
    "id" => "ajaxCrudModal",
    "size" => "modal-xl",
    "footer" => "", // clear footer
]) ?>
<?php Modal::end(); ?>