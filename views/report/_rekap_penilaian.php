<?php
use kartik\grid\GridView;
use yii\helpers\Html;
?>

<div class="report-penilaian-penyedia">
    <div class="text-center mb-4">
        <h3><?= Html::encode($title) ?></h3>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn', 'header' => 'No'],
            ['attribute' => 'nama_penyedia', 'label' => 'Nama Penyedia'],
            ['attribute' => 'alamat', 'label' => 'Alamat'],
            ['attribute' => 'kategori', 'label' => 'Kategori'],
            ['attribute' => 'nama_kegiatan', 'label' => 'Nama Kegiatan'],
            ['attribute' => 'bidang', 'label' => 'Bidang/Bagian'],
            ['attribute' => 'metode', 'label' => 'Metode Pemilihan'],
            ['attribute' => 'tanggal_kontrak', 'label' => 'Tanggal Kontrak', 'format' => ['date', 'php:d-m-Y']],
            ['attribute' => 'nilai_kontrak', 'label' => 'Nilai Kontrak', 'format' => ['decimal', 0], 'hAlign' => 'right'],
            ['attribute' => 'pejabat_pengadaan', 'label' => 'Pejabat Pengadaan'],
            
            // Score Columns (1-5)
            [
                'label' => '1',
                'value' => function ($model) { return $model['scores'][0] ?? '-'; },
                'hAlign' => 'center',
            ],
            [
                'label' => '2',
                'value' => function ($model) { return $model['scores'][1] ?? '-'; },
                'hAlign' => 'center',
            ],
            [
                'label' => '3',
                'value' => function ($model) { return $model['scores'][2] ?? '-'; },
                'hAlign' => 'center',
            ],
            [
                'label' => '4',
                'value' => function ($model) { return $model['scores'][3] ?? '-'; },
                'hAlign' => 'center',
            ],
            [
                'label' => '5',
                'value' => function ($model) { return $model['scores'][4] ?? '-'; },
                'hAlign' => 'center',
            ],

            ['attribute' => 'total', 'label' => 'Total Nilai', 'hAlign' => 'center'],
            ['attribute' => 'rata', 'label' => 'Rata-Rata', 'hAlign' => 'center'],
            ['attribute' => 'hasil_evaluasi', 'label' => 'Hasil Evaluasi', 'hAlign' => 'center'],
            ['attribute' => 'keterangan', 'label' => 'Keterangan'],
        ],
        'beforeHeader' => [
            [
                'columns' => [
                    ['content' => '', 'options' => ['colspan' => 10]], // No to Pejabat Pengadaan
                    ['content' => 'Kriteria Penilaian', 'options' => ['colspan' => 5, 'class' => 'text-center']],
                    ['content' => '', 'options' => ['colspan' => 4]], // Total to Keterangan
                ],
            ]
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => false,
        ],
        'toolbar' =>  [
            '{export}',
            '{toggleData}'
        ],
        'export' => [
            'fontAwesome' => true
        ],
    ]); ?>
</div>
