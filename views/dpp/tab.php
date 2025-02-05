<?php
use yii\bootstrap4\{Collapse, Tabs, Modal};
$this->params['breadcrumbs'][] = ['label' => 'Dpp', 'url' => ['/dpp/index']];
?>
<div class="row">
    <div class="col-md-12">
        <?php
        $tabsItems = [
            [
                'label' => 'DPP',
                'content' => $this->render('//dpp/view', ['model' => $model,]),
                'options' => ['id' => 'dpp' . $model->hash],
            ],
            [
                'label' => 'Evaluasi',
                'content' => $this->render('//validasikualifikasipenyedia/validasikualifikasipenyedia', ['model' => $penawaran,]),
                'options' => ['id' => 'evaluasi' . $model->hash],
            ],
            [
                'label' => 'Negosiasi',
                'content' => $this->render('//penawaranpenyedia/allpenawaran', ['model' => $penawaran,]),
                'options' => ['id' => 'peserta' . $model->hash],
            ],
            [
                'label' => 'Pemenang',
                'content' => $this->render('//dpp/_pemenang', ['model' => $this->context->actionListpemenang(['paket_pengadaan_id' => $model->paket_id]),]),
                'options' => ['id' => 'pemenang' . $model->hash],
            ],
        ];
        if ($model->paketpengadaan->pemenang) {
            $tabsItems[] = [
                'label' => 'Evaluasi Kinerja Penyedia',
                'content' => $this->render('//dpp/_evaluasi', ['model' => $model]),
                'options' => ['id' => 'eval' . $model->hash],
            ];
        }
        echo Tabs::widget([
            'items' => $tabsItems,
        ]);
        ?>
    </div>
</div>
<?php
$this->title = 'Proses Dpp';
$this->params['breadcrumbs'][] = $this->title;