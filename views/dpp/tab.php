<?php
use app\models\PenawaranPengadaan;
use yii\bootstrap4\{Collapse, Tabs, Modal};
$this->params['breadcrumbs'][] = ['label' => 'Dpp', 'url' => ['/dpp/index']];
?>
<div class="row">
    <div class="col-md-12">
        <?php
        // $qparam= Yii::$app->request->queryParams;
        // $penawaranparams= $qparam;
        // $penawaranparams['PenawaranPengadaanSearch']['paket_id'] = $model->paketpengadaan->nomor;
        // $evaluasipenyediaparams= $qparam;
        // $evaluasipenyediaparams['ValidasiKualifikasiPenyediaSearch']['paket_pengadaan_id'] = $model->paketpengadaan->nomor;
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
                    'label' => 'Peserta/Penawaran/Negosiasi',
                    'content' =>$this->render('//penawaranpenyedia/allpenawaran', [
                        'model'=> PenawaranPengadaan::collectAll(['paket_id'=>$model->paket_id]),
                    ]),
                    'options' => ['id' => 'peserta' . $model->hash],
                ],
                [
                    'label' => 'Evaluasi',
                    'content' =>$this->render('//validasikualifikasipenyedia/validasikualifikasipenyedia', [
                        'model' => PenawaranPengadaan::collectAll(['paket_id' => $model->paket_id]),
                    ]),
                    'options' => ['id' => 'evaluasi' . $model->hash],
                ],
                [
                    'label' => 'Pemenang',
                    'content' =>$this->render('//dpp/_pemenang', [
                        'model'=>$this->context->actionListpemenang(['paket_pengadaan_id' => $model->paket_id]),
                    ]),
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