<?php
use yii\web\View;
use yii\helpers\Html;
use kartik\date\DatePicker;
use yii\helpers\Json;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use app\models\{Unit, CeklistModel};
use unclead\multipleinput\MultipleInput;
$this->title = 'Ceklist Admin DPP ';
$tglditerima= '<b>Tgl Diterima : '.$dpp->tanggal_terima.'</b>';
$this->registerJs("
jQuery(function ($) {
    $('.list-cell__button').hide();
    $('.field-ceklistmodel-template').find('.col-sm-9').removeClass('col-sm-9');
    $('.field-ceklistmodel-template').find('.row').removeClass('row');
    var tglditerima=$('.content-header .container-fluid .row .col-sm-6').last();
    tglditerima.addClass('right');
    tglditerima.html('$tglditerima');
});", View::POS_END);
$additions = json_decode($model->addition, true);
$col = [];
$dataUnit = Unit::collectAll()->pluck('unit', 'id')->toArray();
if (key_exists('template', $additions)) {
    $rr = $additions['template']; // generated from template kelengkapan dpp
} else {
    die;
}
$mdl = new CeklistModel();
$mdl->nomor_dpp = $dpp->nomor_dpp ?? '';
$mdl->nomor_tugas = $modelpenugasan->nomor_tugas ?? $nomortugas;
$mdl->tanggal_tugas = $modelpenugasan->tanggal_tugas ?? '';
$mdl->pejabat = $modelpenugasan->pejabat ?? '';
$mdl->admin = $modelpenugasan->admin ?? '';
$mdl->paket_id = $dpp->paket_id ?? '';
$mdl->unit = $dpp->bidang_bagian ?? '';
$mdl->nomor_persetujuan = $model->nomor_persetujuan ?? '';
$mdl->linksirup = $model->linksirup ?? '';
$mdl->tanggal_persetujuan = $model->tanggal_persetujuan ?? '';
$mdl->template = $additions['template'];
$template = array('uraian', 'sesuai', 'keterangan');
foreach ($model->reorderArray(array_keys($temp[0]), $template) as $item) {
    $trimmedKey = ucfirst(trim($item));
    $title = ($trimmedKey === 'Sesuai') ? 'Sesuai(ya/tidak)' : (($trimmedKey === 'Skala') ? 'Skala(1-5)' : ucfirst($trimmedKey));
    $col[] = $trimmedKey === 'Sesuai' ? [
        'name' => trim($item),
        'title' => $title,
        'type' => 'checkbox', // SwitchInput::class,
        // 'options' => [
        //     'pluginOptions' => [
        //         'size' => 'mini',
        //         'onText' => 'Ya', 'offText' => 'Tidak'
        //     ]
        // ]
    ] : [
        'name' => trim($item),
        'title' => $title,
        'type' => 'textArea',
    ];
}
$form = ActiveForm::begin([
    'id' => 'y-form',
    'enableAjaxValidation' => false,
    'fieldConfig' => [
        'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
        'labelOptions' => ['class' => 'col-sm-3 control-label right'],
    ],
]);
echo '<div class="row">
    <div class="col-md-6">' .
    $form->field($mdl, 'nomor_dpp')->textInput(['maxlength' => true]) .
    $form->field($mdl, 'nomor_persetujuan')->textInput(['maxlength' => true]) .
    $form->field($mdl, 'paket_id')->widget(Select2::class, ['data' => $dataPaket])->label('Nama Paket') .
    $form->field($mdl, 'nomor_tugas')->textInput(['maxlength' => true]) .
    $form->field($mdl, 'pejabat')->widget(Select2::class, [
        'data' => $datapenugasan['pejabat'],
        'options' => ['placeholder' => 'Pilih Pejabat'],
        'pluginOptions' => ['allowClear' => true],
    ]) .
    $form->field($mdl, 'unit')->widget(Select2::class, [
        'data' => $dataUnit,
        'options' => ['placeholder' => 'Pilih Unit/Bidang/Bagian'],
        'pluginOptions' => ['allowClear' => true],
    ])->label('Bidang/Bagian') .
    '</div>
    <div class="col-md-6">' .
    $form->field($dpp, 'tanggal_dpp')->textInput(['maxlength' => true]) .
    $form->field($mdl, 'tanggal_persetujuan')->textInput(['maxlength' => true]) .
    $form->field($mdl, 'linksirup')->textInput() .
    $form->field($mdl, 'tanggal_tugas')->widget(DatePicker::class, [
        'language' => 'id',
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
        ]
    ]) .
    $form->field($mdl, 'admin')->widget(Select2::class, [
        'data' => $datapenugasan['admin'],
        'options' => ['placeholder' => 'Pilih admin'],
        'pluginOptions' => ['allowClear' => true],
    ]) .
    '</div>
    </div>';
echo $form->field($mdl, 'template')->widget(MultipleInput::class, [
    'id' => 'dok-template',
    'name' => 'template[]',
    'enableGuessTitle' => true,
    'cloneButton' => false,
    'removeButtonOptions' => [
        'label' => 'x'
    ],
    'data' => $rr ?? [],
    'columns' => $col
])->label(false);
echo '<div class="form-group">';
echo Html::button('<i class="fas fa-magic"></i> Magic Auto Fill', [
    'id' => 'btn-magic-autofill',
    'class' => 'btn btn-warning mr-2',
    'title' => 'Auto-fill tanggal, pejabat, admin, dan checklist'
]);
echo Html::submitButton('Submit', ['class' => 'btn btn-primary']);
echo '</div>';
ActiveForm::end();

$tanggalDpp = Json::encode($dpp->tanggal_dpp ?? '');
$this->registerJs("
jQuery(function ($) {
    $('#btn-magic-autofill').on('click', function () {
        const \$button = \$(this);

        if (!confirm(
            'Jalankan Magic Auto Fill?\\n\\n' +
            'Tanggal tugas akan diisi dengan tgl dpp. ' +
            'Checklist akan dicentang. ' +
            'Periksa seluruh hasil sebelum menyimpan.'
        )) {
            return;
        }

        // 1. Isi tanggal tugas menggunakan tanggal DPP
        const tanggalDpp = {$tanggalDpp};
        \$('#ceklistmodel-tanggal_tugas')
            .val(tanggalDpp)
            .trigger('change');

        // 2. Pilih pejabat/admin.
        // Isi ID berikut setelah menentukan aturan bisnis.
        const defaultPejabatId = '20';
        const defaultAdminId = '20';

        function selectIfUnambiguous(selector, preferredId) {
            const \$select = \$(selector);

            if (!\$select.length) {
                return false;
            }

            // Prioritaskan ID yang dikonfigurasi.
            if (
                preferredId !== '' &&
                \$select.find('option').filter(function () {
                    return this.value === String(preferredId);
                }).length
            ) {
                \$select.val(String(preferredId))
                    .trigger('change');

                return true;
            }

            // Jika hanya ada satu pilihan nyata, pilih otomatis.
            const \$options = \$select.find('option').filter(function () {
                return this.value !== '' && !this.disabled;
            });

            if (\$options.length === 1) {
                \$select.val(\$options.first().val())
                    .trigger('change');

                return true;
            }

            // Jangan menebak jika ada beberapa pilihan.
            return false;
        }

        const pejabatTerpilih = selectIfUnambiguous(
            '#ceklistmodel-pejabat',
            defaultPejabatId
        );

        const adminTerpilih = selectIfUnambiguous(
            '#ceklistmodel-admin',
            defaultAdminId
        );

        // 3. Centang kolom \"sesuai\" pada MultipleInput.
        // Gunakan hanya jika seluruh item memang sudah diverifikasi.
        const bolehCentangSemua = true;

        if (bolehCentangSemua) {
            const \$checkboxes = \$(
                '#dok-template input[type=\"checkbox\"]'
            ).filter(function () {
                const name = \$(this).attr('name') || '';

                return /\\[sesuai\\]\$/.test(name);
            });

            \$checkboxes
                .prop('checked', true)
                .trigger('change');
        }

        // 4. Beri informasi jika pemilihan belum dapat dilakukan.
        const pesan = [
            'Magic Auto Fill selesai.',
            'Tanggal tugas: ' + tanggalDpp,
            'Pejabat: ' +
                (pejabatTerpilih ? 'terisi' : 'perlu dipilih'),
            'Admin: ' +
                (adminTerpilih ? 'terisi' : 'perlu dipilih'),
            'Checklist: periksa kembali seluruh item.',
        ];

        alert(pesan.join('\\n'));
    });
});
", View::POS_END);
