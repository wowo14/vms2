<?php
use app\models\Negosiasi;
use app\models\PaketPengadaanDetails;
use kartik\grid\GridView;
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Modal;
use yii\data\ActiveDataProvider;
use yii\helpers\{Html,Url};
$idmodal="frmpenawaranpenyedia";
// $negodet=Negosiasi::findOne(['penawaran_id' => $penawaran->id]);
// if($negodet){
//     $negodetails=json_decode($negodet->details,true);
// }else{
//     $negodetails=PaketPengadaanDetails::where(['id' => $model->id])->all();
// }
$negodetails=PaketPengadaanDetails::where(['id' => $model->id])->all();
?>
<table class="table table-bordered table-striped table-hover">
        <tr>
            <td>Penawaran Awal <?=$model->nama_produk?>:</td>
            <td><?= \Yii::$app->formatter->asCurrency($model->penawaran) ?></td>
        </tr>
</table>
<div class="riwayatnego">
    <?=GridView::widget([
    'id' => 'histori-crud-datatable',
    'dataProvider' => new yii\data\ArrayDataProvider([
        'allModels' => $negodetails, 'pagination' => false
    ]),
    'summary' => false,
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'header'=>'Nego Ke'
        ],
        [
            'attribute'=>'nama_produk',
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'qty',
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'volume',
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'satuan',
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'hps_satuan',
            'format'=>'currency',
            'contentOptions'=>['class'=>'text-right']
        ],
        [
            'attribute'=>'penawaran',
            'format'=>'raw',
            'contentOptions'=>['class'=>'text-right'],
            'value'=>function($d)use($idmodal){
                return Html::a(Yii::$app->formatter->asCurrency($d->penawaran)??'#',['/paketpengadaan/postpenawaran','id'=>$d->id],['role' => 'modal-remote','data-target' => '#' . $idmodal,'data-pjax' => '0','data-target'=>'#nego','title' => Yii::t('yii2-ajaxcrud', 'Penawaran')]);
            },
        ],
        [
            'attribute'=>'negosiasi',
            'format'=>'raw',
            'contentOptions'=>['class'=>'text-right'],
            'value'=>fn($d)=>Yii::$app->formatter->asCurrency($d->negosiasi),
            // 'value'=>function($d)use($idmodal){
            //     return Html::a($d->negosiasi??'#',['/paketpengadaan/negoproduk','id'=>$d->id],['role' => 'modal-remote','data-target' => '#' . $idmodal,'data-pjax' => '0','data-target'=>'#nego','title' => Yii::t('yii2-ajaxcrud', 'Nego')]);
            // },
        ],
        [
            'attribute'=>'totalhps',
            'format'=>'raw',
            'value'=>fn($d)=>Yii::$app->formatter->asCurrency(($d->qty??1)*($d->volume??1)*$d->hps_satuan),
            'contentOptions'=>['class'=>'text-right'],
            'pageSummary' => true,
            'pageSummaryOptions' => ['class' => 'auto unitsum', 'style' => 'text-align:right;'],
            'pageSummaryFunc' => function ($data) {
                return Yii::$app->tools->sumCurrency($data);
            },
        ],
        [
            'attribute'=>'totalpenawaran',
            'format'=>'raw',
            'value'=>fn($d)=>Yii::$app->formatter->asCurrency(($d->qty??1)*($d->volume??1)*$d->penawaran),
            'contentOptions'=>['class'=>'text-right'],
            'pageSummary' => true,
            'pageSummaryOptions' => ['class' => 'auto unitsum', 'style' => 'text-align:right;'],
            'pageSummaryFunc' => function ($data) {
                return Yii::$app->tools->sumCurrency($data);
            },
        ],
        [
            'attribute'=>'totalnegosiasi',
            'format'=>'raw',
            'value'=>fn($d)=>Yii::$app->formatter->asCurrency(($d->qty??1)*($d->volume??1)*$d->negosiasi),
            'contentOptions'=>['class'=>'text-right'],
            'pageSummary' => true,
            'pageSummaryOptions' => ['class' => 'auto unitsum', 'style' => 'text-align:right;'],
            'pageSummaryFunc' => function ($data) {
                return Yii::$app->tools->sumCurrency($data);
            },
        ],
    ]
]);?>
</div>
<div class="penawaran-form">
    <?php $form = ActiveForm::begin([
        'id'=>'penawaran-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
    <?= $form->field($model, 'penawaran')->textInput(['placeholder'=>'Masukkan Jumlah Penawaran','type'=>'number']) ?>
    <?php if (!Yii::$app->request->isAjax){ ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>
