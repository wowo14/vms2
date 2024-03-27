<?php
use app\models\PenawaranPengadaanSearch;
use app\models\TemplateChecklistEvaluasi;
use app\models\TemplateChecklistEvaluasiSearch;
use app\models\ValidasiKualifikasiPenyedia;
use app\models\ValidasiKualifikasiPenyediaSearch;
use yii\bootstrap4\{Collapse, Tabs, Modal};
?>
<?php
?>
<div class="row">
    <div class="col-md-12">
        <?php
        $qparam= Yii::$app->request->queryParams;
        $penawaranparams= $qparam;
        $penawaranparams['PenawaranPengadaanSearch']['paket_id'] = $model->paketpengadaan->nomor;
        $evaluasipenyediaparams= $qparam;
        $evaluasipenyediaparams['ValidasiKualifikasiPenyediaSearch']['paket_pengadaan_id'] = $model->paketpengadaan->nomor;
        echo Tabs::widget([
            'items' => [
                [
                    'label' => 'DPP',
                    'content' =>$this->render('//dpp/view', [
                            'model' => $model,
                        ]),
                    'options' => ['id' => 'dpp' . $model->hash],
                ],
                [
                    'label' => 'Peserta',
                    'content' =>$this->render('//penawaranpenyedia/allpenawaran', [
                        'searchModel' => $penawaran=new PenawaranPengadaanSearch(),
                        'dataProvider' => $penawaran->search($penawaranparams),
                        'params' => $model->hashid($model->id), //penyedia_id
                    ]),
                    'options' => ['id' => 'peserta' . $model->hash],
                ],
                [
                    'label' => 'Evaluasi',
                    'content' =>$this->render('//validasikualifikasipenyedia/validasikualifikasipenyedia', [
                        'searchModel' => $evaluasipenyedia = new ValidasiKualifikasiPenyediaSearch(),
                        'dataProvider' => $evaluasipenyedia->search($evaluasipenyediaparams),
                        // 'templates'=> TemplateChecklistEvaluasi::where(['like', 'template', 'ceklist_evaluasi'])->all(),
                        // 'model' => ValidasiKualifikasiPenyedia::collectAll(),
                    ]),
                    'options' => ['id' => 'evaluasi' . $model->hash],
                ],
                [
                    'label' => 'Pemenang',
                    'content' =>'List pemenang',
                    // $this->render('//akta/index', [
                    //     'searchModel' => $a,
                    //     'dataProvider' => $a->search(Yii::$app->request->queryParams, ['penyedia_id' => $model->id]),
                    //     'params' => $model->hashid($model->id), //penyedia_id
                    // ]),
                    'options' => ['id' => 'pemenang' . $model->hash],
                ],
            ],
        ]);
        ?>
    </div>
</div>
<?php
$this->title = 'Proses Dpp';
$this->params['breadcrumbs'][] = $this->title;