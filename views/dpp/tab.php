<?php
use yii\bootstrap4\{Collapse, Tabs, Modal};
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
$this->title = 'Proses Dpp';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
?>
<div class="row">
    <div class="col-md-12">
        <?php
        echo Tabs::widget([
            'items' => [
                [
                    'label' => 'DPP',
                    'content' =>$this->render('/dpp/view', [
                            'model' => $model,
                        ]),
                    'options' => ['id' => 'dpp' . $model->hash],
                ],
                [
                    'label' => 'Peserta',
                    'content' =>'List Peserta',
                    // $this->render('/pengurusperusahaan/index', [
                    //     'searchModel' => $pengurus,
                    //     'dataProvider' => $pengurus->search(Yii::$app->request->queryParams, ['penyedia_id' => $model->id]),
                    //     'params' => $model->hashid($model->id), //penyedia_id
                    // ]),
                    'options' => ['id' => 'peserta' . $model->hash],
                ],
                [
                    'label' => 'Evaluasi',
                    'content' =>'List all evaluasi penyedia',
                    // $this->render('/ijinusaha/index', [
                    //     'searchModel' => $i,
                    //     'dataProvider' => $i->search(Yii::$app->request->queryParams, ['penyedia_id' => $model->id]),
                    //     'params' => $model->hashid($model->id), //penyedia_id
                    // ]),
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