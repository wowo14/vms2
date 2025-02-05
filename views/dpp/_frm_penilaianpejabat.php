<?php
use kartik\date\DatePicker;
use unclead\multipleinput\{MultipleInput, MultipleInputColumn};
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
$this->title="Evaluasi Kinerja Penyedia oleh Pejabat Pengadaan";
$js = <<<JS
 $('select[name^="skor"]').on('change', function() {
     const index = $(this).attr('name').match(/\d+/)[0];
     const skor = parseFloat($(this).val());
    recalculate();
 });
 function recalculate(){
     let total = 0;
     $('select[name^="skor"]').each(function () {
         var val = $(this).val();
         if (!isNaN(val) && val !== '') {
             total += parseFloat(val);
         }
     });
     var count=$('select[name^="skor"]').length;
     var nilai=total.toFixed(2)/count;
     if (nilai > 3) {
        var point = "A";
    } else if (nilai >= 2 && nilai <= 3) {
        var point = "B";
    } else {
        var point = "C";
    }
    $('#total').val(total.toFixed(2));
    $('#nilaiakhir').val(point + " = " + total.toFixed(2)/count);
    if (point === "A") {
        $('#hasil_evaluasi').val("Direkomendasi untuk digunakan kembali");
        $('#hasil_evaluasi').prop('readonly', true);
    } else if (point === "B") {
        $('#hasil_evaluasi').prop('readonly', false);
        $('#hasil_evaluasi').val("Direkomendasi dengan catatan ( pemantauan lebih intensif ):");
    } else {
        $('#hasil_evaluasi').val("Tidak direkomendasi untuk digunakan kembali");
        $('#hasil_evaluasi').prop('readonly', true);
    }
 }
JS;
$this->registerJs($js);
?>
<div class="dpp-form">
    <?php $form = ActiveForm::begin([
        'id' => 'dpp-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "<div class='row'>{label}\n<div class='col-sm-9'>{input}\n{error}</div></div>",
            'labelOptions' => ['class' => 'col-sm-3 col-md-3 control-label text-sm-left text-md-right'],
        ],
    ]); ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($penilaian, 'unit_kerja')->textInput(['readonly' => true]) ?>
            <?= $form->field($penilaian, 'nama_perusahaan')->textInput(['readonly' => true]) ?>
            <?= $form->field($penilaian, 'alamat_perusahaan')->textInput(['readonly' => true]) ?>
            <?= $form->field($penilaian, 'paket_pekerjaan')->textInput(['readonly' => true]) ?>
            <?= $form->field($penilaian, 'lokasi_pekerjaan')->textInput(['readonly' => true]) ?>
            <?= $form->field($penilaian, 'nilai_kontrak')->textInput(['readonly' => true]) ?>
            <?= $form->field($penilaian, 'nomor_kontrak')->textInput(['readonly' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($penilaian, 'created_at')->widget(DatePicker::class, [
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'autoclose' => true
                ],
            ])->label('Tanggal Penilaian') ?>
            <?= $form->field($penilaian, 'tanggal_kontrak')->widget(DatePicker::class, [
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'autoclose' => true
                ],
                'readonly' => true
            ]) ?>
            <?= $form->field($penilaian, 'jangka_waktu')->textInput(['maxlength' => true]) ?>
            <?= $form->field($penilaian, 'metode_pemilihan')->textInput(['readonly' => true]) ?>
            <?= $form->field($penilaian, 'pengguna_anggaran')->textInput(['maxlength' => true]) ?>
            <?= $form->field($penilaian, 'pejabat_pembuat_komitmen')->textInput(['readonly' => true]) ?>
        </div>
    </div>
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th class="text-center col-md-0 align-middle m-0 p-0">No.</th>
                <th class="text-center col-md-5 align-middle m-0 p-0">Aspek Kinerja</th>
                <th class="text-center col-md-7 align-middle m-0 p-0">Skor <br>Tidak Baik (1) Baik (3) Sangat Baik (5)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $inIndex = 1;
            $total = $nilaiakhir = $hasil_evaluasi = $ulasan_pejabat_pengadaan = '';
            echo "<tr>
            <td class='text-center align-middle m-0 p-0'>1</td>
            <td class='text-center align-middle m-0 p-0'>2</td>
            <td class='text-center align-middle m-0 p-0'>3</td>
            </tr>";
            foreach ($template as $v => $item) {
                echo "<tr>";
                echo "<td>{$no}</td>";
                echo "<td class='text-center align-middle  p-1'>
                    <input name='uraian[$v]' class='form-control' readonly  value='" . (is_array($item['uraian']) ? $item['uraian'][$v] : $item['uraian']) . "'/></td>";
                echo "<td class='text-center align-middle  p-1'>";
                echo "<select name='skor[$v]' class='select2 form-control'>";
                echo "<option></option>";
                foreach ($item['desc'] as $i => $d) {
                    echo "<option value=$i" . (is_array($item['skor']) ? (($item['skor'][$v] == $i) ? ' selected' : '') : '') . ">" . $d . "</option>";
                }
                echo "</select>";
                echo "</td>";
                echo "</tr>";
                $no++;
                $total = $item['total'];
                $nilaiakhir = $item['nilaiakhir'];
                $hasil_evaluasi = $item['hasil_evaluasi'];
                $ulasan_pejabat_pengadaan = $item['ulasan_pejabat_pengadaan'];
            }
            echo '<tr>
                <td class="align-middle text-right" colspan="2">Total Nilai</td>
                <td class="align-middle"><input class="form-control" type="text" readonly name="total" id="total" value="' . ($total ?? 0) . '"></span></td>
                </tr>';
            echo '<tr>
                <td class="align-middle text-right" colspan="2">Nilai Akhir (Nilai rata-rata) </td>
                <td class="align-middle"><input class="form-control" type="text" readonly name="nilaiakhir" id="nilaiakhir" value="' . ($nilaiakhir ?? 0) . '"></span></td>
                </tr>';
            ?>
        </tbody>
    </table>
    <div class="form-group">
        <div class="row">
            <label class="control-label right col-sm-3" for="hasil_evaluasi">Hasil Evaluasi</label>
            <input class="form-control col-sm-9" type="text" id="hasil_evaluasi" name="hasil_evaluasi" value="<?= $hasil_evaluasi ?>">
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="control-label right col-sm-3" for="ulasan_pejabat_pengadaan">Ulasan Pejabat Pengadaan</label>
            <input class="form-control col-sm-9" type="text" id="ulasan_pejabat_pengadaan" name="ulasan_pejabat_pengadaan" value="<?= $ulasan_pejabat_pengadaan ?>">
        </div>
    </div>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($penilaian->isNewRecord ? 'Create' : 'Update', ['class' => $penilaian->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>