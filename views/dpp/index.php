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
<?= DataTables::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' =>[
        'id',
        'nomor_dpp',
        'tanggal_dpp',
        [
            'attribute' => 'bidang_bagian',
            'value' => fn ($d) => $d->unit->unit ?? ''
        ],
        ['attribute'=>'paket_id','value'=> fn ($d) => $d->paketpengadaan->nomornamapaket ?? ''],
        'status_review',

    ],
    'clientOptions' => [
        "lengthMenu" => [[20, -1], [20, Yii::t('app', "All")]],
        "info" => false,
        "responsive" => true,
        "dom" => 'lfTrtip',
        "tableTools" => [
            //empty for load button assets
        ],
        'buttons'   => ['copy', 'excel', 'pdf'],
    ],
]); ?>
