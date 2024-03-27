<?php
use yii\grid\GridView;
use yii\helpers\{url,Html};
use yii2ajaxcrud\ajaxcrud\CrudAsset;
$this->title = 'Evaluasi';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);
$this->registerJsFile('js/popper.min.js', ['depends' => '\yii\bootstrap4\BootstrapPluginAsset']);
?>
<div class="evaluasi-pengadaan-index">
    <div id="ajaxCrudDatatable">
        <?= GridView::widget([
            'id' => 'crud-datatable',
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                ],
                [
                    'attribute' => 'penyedia_id','format'=>'raw','header'=>'Penyedia',
                    'value' => fn ($d) => Html::a($d->vendor->nama_perusahaan,Url::to('/penyedia/view?id='.$d->penyedia_id))??''
                ],
                
            ]
        ]) ?>
    </div>
</div>