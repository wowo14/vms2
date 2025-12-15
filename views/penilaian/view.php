<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap4\Tabs;
use yii\widgets\DetailView;
$viewdetail=DetailView::widget([
    'model' => $model,
    'attributes' => [
        'unit_kerja',
        'nama_perusahaan',
        'alamat_perusahaan',
        ['attribute' => 'dpp_id', 'format' => 'raw', 'value' => fn($d) => Html::a($d->dpp->nomor_dpp, Url::to('/dpp/tab?id=' . $d->dpp_id), ['data-pjax' => 0, 'role' => 'modal-remote', 'data-target' => '#' . $d->dpp_id])],
        'paket_pekerjaan',
        'lokasi_pekerjaan',
        'nomor_kontrak',
        'jangka_waktu',
        'tanggal_kontrak',
        'metode_pemilihan',
        // 'details:ntext',
        'pengguna_anggaran',
        'pejabat_pembuat_komitmen',
        [
            'attribute' => 'nilai_kontrak',
            'value' => fn($d) => \Yii::$app->formatter->asCurrency($d->nilai_kontrak)
        ],
        ['attribute' => 'created_by', 'value' => $model->usercreated->username ?? ''],
        ['attribute' => 'updated_by', 'value' => $model->userupdated->username ?? ''],
        'created_at',
        'updated_at',
    ],
]);
$details= GridView::widget([
    'dataProvider' => new \yii\data\ArrayDataProvider([
        'allModels' => $model->griddetail,
        'pagination' => false,
    ]),
    'summary' => false,
    'columns' => [
        'uraian',
        [
            'attribute' => 'skor',
            'value' => function ($data) {
                return \app\models\PenilaianPenyedia::getScoreDescription($data['uraian'], $data['skor']);
            }
        ],
    ],
]);

    $tabsItems = [
        [
            'label' => 'Penilaian',
            'content' => $viewdetail,
            'options' => ['id' => 'views' . $model->hash],
        ],
        [
            'label' => 'Detail',
            'content' => $details,
            'options' => ['id' => 'details' . $model->hash],
        ],
    ];
    echo Tabs::widget([
        'items' => $tabsItems,
    ]);
?>
<div class="penilaian-penyedia-view">
</div>