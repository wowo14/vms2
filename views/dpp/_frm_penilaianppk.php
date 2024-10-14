<?php
use kartik\date\DatePicker;
use unclead\multipleinput\{MultipleInput,MultipleInputColumn};
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
$js=<<<JS
 $('select[name^="skor"]').on('change', function() {
     const index = $(this).attr('name').match(/\d+/)[0];
     const bobot = $(this).parent().parent().find('input[name="bobot['+index+']"]').val();
     const skor = parseFloat($(this).val());
     const nilaiKinerja = (parseFloat(bobot) * skor) / 100;
     const hasil=$(this).parent().parent().find('input[name="nilaikinerja['+index+']"]');
    hasil.val(nilaiKinerja.toFixed(2));
    recalculate();
 });
 function recalculate(){
     let total = 0;
     $('input[name^="nilaikinerja"]').each(function () {
         total += parseFloat($(this).val());
     });
     $('#total').val(total.toFixed(2));
 }
    recalculate();
JS;
$this->registerJs($js);
$data=collect($template)->map(function ($e, $index) {
    $r = [
        'id' => $index,
        'aspek' => $e['kategori'],
        'indicators'=>$e['description'],
        'bobot'=>$e['bobot'],
        'skor'=>$e['skor'][$index],
        'nilaikinerja'=>$e['nilaikinerja'][$index],
    ];
    return $r;
})->toArray();
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
            <?= $form->field($penilaian, 'unit_kerja')->textInput(['maxlength' => true]) ?>
            <?= $form->field($penilaian, 'nama_perusahaan')->textInput(['maxlength' => true]) ?>
            <?= $form->field($penilaian, 'alamat_perusahaan')->textInput(['maxlength' => true]) ?>
            <?= $form->field($penilaian, 'paket_pekerjaan')->textInput(['maxlength' => true]) ?>
            <?= $form->field($penilaian, 'lokasi_pekerjaan')->textInput(['maxlength' => true]) ?>
            <?= $form->field($penilaian, 'nilai_kontrak')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($penilaian, 'nomor_kontrak')->textInput(['maxlength' => true]) ?>
            <?= $form->field($penilaian, 'tanggal_kontrak')->widget(DatePicker::class, [
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'autoclose' => true
                ],
            ]) ?>
            <?= $form->field($penilaian, 'jangka_waktu')->textInput(['maxlength' => true]) ?>
            <?= $form->field($penilaian, 'metode_pemilihan')->textInput(['maxlength' => true]) ?>
            <?= $form->field($penilaian, 'pengguna_anggaran')->textInput(['maxlength' => true]) ?>
            <?= $form->field($penilaian, 'pejabat_pembuat_komitmen')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th class="text-center align-middle m-0 p-0">No.</th>
            <th class="text-center align-middle m-0 p-0">Aspek Kinerja</th>
            <th class="text-center align-middle m-0 p-0" colspan="2">Indikator</th>
            <th class="text-center align-middle m-0 p-0">Bobot (%)</th>
            <th class="text-center align-middle m-0 p-0">Skor <br>Cukup (1) Baik (2) Sangat Baik (3)</th>
            <th class="text-center align-middle m-0 p-0">Nilai Kinerja (4 x 5)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $inIndex=1;
        echo "<tr>
        <td class='text-center align-middle m-0 p-0'>1</td>
        <td class='text-center align-middle m-0 p-0'>2</td>
        <td class='text-center align-middle m-0 p-0' colspan='2'>3</td>
        <td class='text-center align-middle m-0 p-0'>4</td>
        <td class='text-center align-middle m-0 p-0'>5</td>
        <td class='text-center align-middle m-0 p-0'>6</td>
        </tr>";
        foreach ($data as $v=>$item) {
            foreach ($item['indicators'] as $indicatorIndex => $indicator) {
                echo '<tr>';
                if ($indicatorIndex == 0) {
                    $inIndex=($indicatorIndex+1);
                    echo "<td class='text-center align-middle p-1' rowspan='3'>{$no}</td>";
                    echo "<td class='text-center align-middle  p-1' rowspan='3'>{$item['aspek']}</td>";
                    echo "<td>{$inIndex}</td>";
                    echo "<td class='align-middle  p-1'>{$indicator}</td>";
                    echo "<td rowspan='3' class='text-center align-middle  p-1'>
                    <input name='bobot[$v]' class='form-control' readonly  value='" . $item['bobot'] . "'/></td>";
                    echo "<td rowspan='3' class='text-center align-middle  p-1'>";
                    echo "<select name='skor[$v]' class='select2 form-control'>";
                    echo "<option></option>";
                    echo "<option value='1'" .(($item['skor']== '1') ? ' selected' : ''). ">1</option>";
                    echo "<option value='2'" .(($item['skor']== '2') ? ' selected' : ''). ">2</option>";
                    echo "<option value='3'" .(($item['skor']== '3') ? ' selected' : ''). ">3</option>";
                    echo "</select>";
                    echo "</td>";
                    echo "<td rowspan='3' class='text-center align-middle  p-1'>
                    <input name='nilaikinerja[$v]' class='form-control' readonly  value='" . ($item['nilaikinerja']) . "'/></td>";
                } else {
                    echo "<td class='align-middle p-1'>{$inIndex}</td>";
                    echo "<td class='align-middle  p-1'>{$indicator}</td>";
                }
                echo '</tr>';
                $inIndex++;
            }
            $no++;
        }
        echo '<tr>
        <td class="align-middle text-right" colspan="6">Total</td>
        <td class="align-middle"><input class="form-control" type="text" readonly id="total"></span></td>
        </tr>';
        ?>
    </tbody>
    </table>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($penilaian->isNewRecord ? 'Create' : 'Update', ['class' => $penilaian->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>