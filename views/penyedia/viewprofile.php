<?php
use yii\bootstrap4\{Collapse, Tabs, Modal};
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
?>
<div class="row">
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-6">
                        <h3 class="panel-title"><?= $model->nama_perusahaan ?></h3>
                    </div>
                    <div class="col-xs-6 text-right">
                        <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary', 'role' => 'modal-remote', 'data-target' => '#' . $model->hash]) ?>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'npwp',
                        'nama_perusahaan',
                        'alamat_perusahaan',
                        'nomor_telepon',
                        'email_perusahaan:email',
                        'tanggal_pendirian',
                        'kategori_usaha',
                        'akreditasi',
                        'active',
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <?php
        $qparams = Yii::$app->request->queryParams;
        echo Tabs::widget([
            'items' => [
                [
                    'label' => 'Pengurus',
                    'content' => $this->render('//pengurusperusahaan/index', [
                        'searchModel' => $pengurus,
                        'dataProvider' => $pengurus->search($qparams,['penyedia_id' => $model->id]),
                        // new ArrayDataProvider($pengurus->where(['penyedia_id' => $model->id])),
                        // 'params' => '?id=' . $model->hashid($model->id), //penyedia_id
                    ]),
                    'options' => ['id' => 'pengurus' . $model->hash],
                ],
                [
                    'label' => 'Ijin Usaha',
                    'content' => $this->render('//ijinusaha/index', [
                        'searchModel' => $i,
                        'dataProvider' => $i->search($qparams, ['penyedia_id' => $model->id]),
                        // 'params' => '?id=' . $model->hashid($model->id), //penyedia_id
                    ]),
                    'options' => ['id' => 'iu' . $model->hash],
                ],
                [
                    'label' => 'Akta',
                    'content' => $this->render('//akta/index', [
                        'searchModel' => $a,
                        'dataProvider' => $a->search($qparams, ['penyedia_id' => $model->id]),
                        // 'params' => '?id=' . $model->hashid($model->id), //penyedia_id
                    ]),
                    'options' => ['id' => 'akta' . $model->hash],
                ],
                [
                    'label' => 'Staff Ahli',
                    'content' => $this->render('//staffahli/index', [
                        'searchModel' => $s,
                        'dataProvider' => $s->search($qparams, ['penyedia_id' => $model->id]),
                        // 'params' => '?id=' . $model->hashid($model->id), //penyedia_id
                    ]),
                    'options' => ['id' => 'stafahli' . $model->hash],
                ],
                [
                    'label' => 'Peralatan Kerja',
                    'content' => $this->render('//peralatankerja/index', [
                        'searchModel' => $peralatankerja,
                        'dataProvider' => $peralatankerja->search($qparams, ['penyedia_id' => $model->id]),
                        // 'params' => '?id=' . $model->hashid($model->id), //penyedia_id
                    ]),
                    'options' => ['id' => 'peralatan' . $model->hash],
                ],
                [
                    'label' => 'Pengalaman',
                    'content' => $this->render('//pengalaman/index', [
                        'searchModel' => $p,
                        'dataProvider' => $p->search($qparams, ['penyedia_id' => $model->id]),
                        // 'params' => '?id='.$model->hashid($model->id), //penyedia_id
                    ]),
                    'options' => ['id' => 'pengalaman' . $model->hash],
                ],
            ],
        ]);
        $this->title = "Profile Pelaku Usaha";
        $this->params['breadcrumbs'] = '';
        ?>
    </div>
</div>