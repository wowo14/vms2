<?php
use kartik\grid\GridView;
use yii\helpers\Html;
?>

<div class="report-penilaian-penyedia-ppk">
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
            ['attribute' => 'ppk_nama', 'label' => 'Pejabat Pembuat Komitmen'],
            
            // Skor Penilaian Columns (1-4)
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

            // Bobot Penilaian Columns (20%, 20%, 30%, 30%)
            [
                'label' => '20%',
                'value' => function ($model) { return $model['weighted_scores'][0] ?? '-'; },
                'hAlign' => 'center',
            ],
            [
                'label' => '20%',
                'value' => function ($model) { return $model['weighted_scores'][1] ?? '-'; },
                'hAlign' => 'center',
            ],
            [
                'label' => '30%',
                'value' => function ($model) { return $model['weighted_scores'][2] ?? '-'; },
                'hAlign' => 'center',
            ],
            [
                'label' => '30%',
                'value' => function ($model) { return $model['weighted_scores'][3] ?? '-'; },
                'hAlign' => 'center',
            ],

            ['attribute' => 'nilai_kinerja', 'label' => 'Nilai Kinerja', 'hAlign' => 'center'],
            ['attribute' => 'hasil_evaluasi', 'label' => 'Hasil Evaluasi', 'hAlign' => 'center'],
            ['attribute' => 'keterangan', 'label' => 'Keterangan'],
        ],
        'beforeHeader' => [
            [
                'columns' => [
                    ['content' => '', 'options' => ['colspan' => 10]], // No to PPK
                    ['content' => 'Skor Penilaian', 'options' => ['colspan' => 4, 'class' => 'text-center']],
                    ['content' => 'Bobot Penilaian', 'options' => ['colspan' => 4, 'class' => 'text-center']],
                    ['content' => '', 'options' => ['colspan' => 3]], // Nilai Kinerja to Keterangan
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
