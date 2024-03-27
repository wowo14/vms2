<?php
use yii\grid\GridView;
use yii\helpers\{url, Html};
use yii2ajaxcrud\ajaxcrud\CrudAsset;
$this->title = 'Penawaran Pengadaan';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);
$this->registerJsFile('js/popper.min.js', ['depends' => '\yii\bootstrap4\BootstrapPluginAsset']);
?>
<div class="penawaran-pengadaan-index">
    <div id="ajaxCrudDatatable">
        <?= GridView::widget([
            'id' => 'crud-datatable',
            'dataProvider' => new yii\data\ArrayDataProvider([
                'allModels' => $model->unique('penyedia_id')->toArray(), 'pagination' => false]),
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                ],
                [
                    'attribute' => 'penyedia_id', 'format' => 'raw', 'header' => 'Penyedia',
                    'value' => fn ($d) => Html::a($d->vendor->nama_perusahaan, Url::to('/validasikualifikasipenyedia/viewvalidasipenyedia?id=' . $d->penyedia_id)) ?? ''
                ],
            ]
        ]) ?>
    </div>
</div>