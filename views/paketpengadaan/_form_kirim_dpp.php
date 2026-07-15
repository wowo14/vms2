<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
?>
<div class="form-kirim-dpp">
    <form id="form-kirim-dpp" action="<?= Url::to(['/paketpengadaan/dpp']) ?>" method="post">
        <?= Html::hiddenInput('pks', $pks) ?>
        <?= Html::hiddenInput('confirm_kirim', '1') ?>
        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
        <p class="badge badge-warning">
            <b>Untuk Dpp Reguler abaikan form isian dibawah ini</b>
        </p>
        <div class="form-group">
            <label class="control-label">Jenis DPP</label>
            <?= Html::dropDownList('jenis_dpp', null, $jenis_dpp_list, [
                'class' => 'form-control',
                'prompt' => 'Pilih Jenis DPP (Opsional)',
                'id' => 'jenis_dpp_dropdown'
            ]) ?>
        </div>
        <div class="form-group" id="pejabat_pengadaan_group" style="display:none;">
            <label class="control-label">Pejabat Pengadaan</label>
            <?= Html::dropDownList('pejabat_pengadaan', null, [], [
                'class' => 'form-control',
                'prompt' => 'Pilih Pejabat Pengadaan',
                'id' => 'pejabat_pengadaan_dropdown'
            ]) ?>
        </div>

        <div class="form-group" id="admin_pengadaan_group" style="display:none;">
            <label class="control-label">Admin Pengadaan</label>
            <?= Html::dropDownList('admin_pengadaan', null, [], [
                'class' => 'form-control',
                'prompt' => 'Pilih Admin Pengadaan',
                'id' => 'admin_pengadaan_dropdown'
            ]) ?>
        </div>

    </form>
</div>
<?php
$listsJson = Json::encode($lists);
$jenisMappingJson = Json::encode($jenis_mapping);
$adminListJson = Json::encode($admin_pengadaan_list);
$this->registerJs(<<<JS
(function() {
    var lists = {$listsJson};
    var jenis_mapping = {$jenisMappingJson};
    var admin_list = {$adminListJson};

    var admin_dropdown = $('#admin_pengadaan_dropdown');
    $.each(admin_list, function(id, nama) {
        admin_dropdown.append($('<option></option>').attr('value', id).text(nama));
    });

    $(document).off('change', '#jenis_dpp_dropdown').on('change', '#jenis_dpp_dropdown', function() {
        var selected_id = $(this).val();
        var pejabat_dropdown = $('#pejabat_pengadaan_dropdown');
        pejabat_dropdown.empty();

        if (selected_id && jenis_mapping[selected_id]) {
            var param = jenis_mapping[selected_id];
            var user_list = lists[param] || {};

            if (Object.keys(user_list).length > 0) {
                $('#pejabat_pengadaan_group').show();
                pejabat_dropdown.append($('<option></option>').attr('value', '').text('Pilih Pejabat Pengadaan'));
                $.each(user_list, function(id, nama) {
                    pejabat_dropdown.append($('<option></option>').attr('value', id).text(nama));
                });
                $('#admin_pengadaan_group').show();

            } else {
                $('#pejabat_pengadaan_group').hide();
                $('#admin_pengadaan_group').hide();
            }
        } else {
            $('#pejabat_pengadaan_group').hide();
            $('#admin_pengadaan_group').hide();
        }
    });

    // Wire submit button dari footer modal ke form ini
    $(document).off('click', '#modal-form-kirim-dpp-submit').on('click', '#modal-form-kirim-dpp-submit', function(e) {
        e.preventDefault();
        $('#form-kirim-dpp').submit();
    });
})();
JS
); ?>