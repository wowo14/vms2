<?php
use yii\grid\GridView;
use yii\helpers\{url, Html};
$this->title = 'Penawaran Pengadaan';
$this->params['breadcrumbs'][] = $this->title;
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
                    'value' => fn ($d) => Html::a($d->vendor->nama_perusahaan, Url::to('/validasikualifikasipenyedia/viewvalidasipenyedia?paket_id='.$d->paket_id.'&id=' . $d->penyedia_id)) ?? ''
                ],
            ]
        ]) ?>
    </div>
</div>