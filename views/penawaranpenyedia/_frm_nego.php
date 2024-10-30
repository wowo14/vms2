<?php
use app\models\Negosiasi;
use app\models\PaketPengadaanDetails;
use kartik\grid\GridView as KartikGridView;
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\{Html,Url};
use yii\widgets\Pjax;
$paketpengadaan=$penawaran->paketpengadaan;
// $negosiasi=$paketpengadaan->penawaranpenyedia->negosiasi;
?>
<table class="table table-bordered table-striped table-hover">
        <tr>
            <td>Penawaran Awal:</td>
            <td><?= Yii::$app->formatter->asCurrency($penawaran->nilai_penawaran) ?></td>
        </tr>
</table>
<div class="riwayatnego">
    <?=GridView::widget([
    'id' => 'histori-crud-datatable',
    'dataProvider' => new yii\data\ArrayDataProvider([
        'allModels' => Negosiasi::where(['penawaran_id' => $penawaran->id])->all(), 'pagination' => false
    ]),
    'summary' => false,
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'header'=>'Nego Ke'
        ],
        [
            'attribute'=>'penawaran_id',
            'label'=>'Paket Pengadaan',
            'value'=>fn($d)=>$d->penawaran->paketpengadaan->nomor??''
        ],
        [
            'attribute'=>'penawaran_id',
            'label'=>'Penyedia',
            'value'=>fn($d)=>$d->penawaran->vendor->nama_perusahaan??''
        ],
        [
            'attribute'=>'ammount',
            //'value'=>fn($d)=>($d->ammount)
        ],
        'created_at',
        ['attribute'=>'accept','value'=>fn($d)=>$d->accept?'Ya':'Tidak'],
        [
            'attribute'=>'created_by',
            'value'=>fn($d)=>$d->usercreated->username
        ]
    ]
]);?>
<?php
if(count($paketpengadaan->details) > 0){
    $dataProviderdetails = new ActiveDataProvider([
            'query' => PaketPengadaanDetails::find()->where(['paket_id' => $paketpengadaan->id]),
            'pagination' => false
        ]);
        echo KartikGridView::widget([
        'dataProvider' => $dataProviderdetails,
        'pjax' => true,
        'showPageSummary' => true,
        'tableOptions' => ['class' => 'new_expand'],
        'id' => 'detailspaket',
        'responsiveWrap' => false,
        'columns' => require(Yii::getAlias('@app/views/paketpengadaan/_column_details.php')),
    ]);
}
?>
</div>
<?php
if(!$paketpengadaan->details):?>
<div class="negosiasi-form">
    <?php $form = ActiveForm::begin([
        'id'=>'negosiasi-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 control-label right'],
        ],
    ]); ?>
    <?= $form->field($model, 'penawaran_id')->hiddenInput(['value' => $penawaran->id, 'readonly' => 'readonly'])->label(false) ?>
      <?= $form->field($model, 'ammount')->widget(\yii\widgets\MaskedInput::class, [
        'clientOptions' => [
            'alias' => 'numeric',
            'groupSeparator' => '.',
            'radixPoint' => ',',
            'autoGroup' => true,
            'digits' => 2,
            'digitsOptional' => false,
            'prefix' => 'Rp ',
            'rightAlign' => false,
            'removeMaskOnSubmit' => true,
        ],
    ]) ?>
      <?php if($this->context->isVendor()):?>
      <?= $form->field($model, 'peneydia_accept')->widget(SwitchInput::class,[
          'pluginOptions' => [
              'onText' => 'Ya',
              'offText' => 'Tidak',
          ]
      ]) ?>
      <?php endif; ?>
    <?php if (!Yii::$app->request->isAjax){ ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>
<?php endif;?>