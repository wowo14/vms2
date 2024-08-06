<?php
namespace app\commands;
use app\Controllers\DppController;
use app\models\Contacts;
use app\models\Dpp;
use app\models\PaketPengadaan;
use app\models\PaketPengadaanDetails;
use app\models\PenawaranPengadaan;
use app\models\Sertipikat;
use app\models\TemplateChecklistEvaluasi;
use app\models\TemplateChecklistEvaluasiDetail;
use app\models\User;
use app\models\ValidasiKualifikasiPenyedia;
use app\models\ValidasiKualifikasiPenyediaDetail;
use Yii;
use yii\console\Controller;
use yii\db\Expression;

class HelloController extends Controller {
    public function actionTest() {
        $model=collect(PenawaranPengadaan::where(['paket_id' => 2, 'penyedia_id' =>1])
        ->select(new Expression('paket_id,penyedia_id,coalesce(negosiasi.ammount, nilai_penawaran) as nilai_penawaran,nilai_penawaran as _nilai_penawaran'))
        ->joinWith('negosiasi')->asArray()
        ->one())->map(function($e){
            return $e;
        })->toArray();
        print_r($model);
    }
    public function actionHitung() { // hitung pada paket pengadaan mana?
        $r = ValidasiKualifikasiPenyedia::getCalculated(1);
        $r = collect($r)->where('penyedia_id', 1)->where('paket_pengadaan_id', 1)->first();
        $templates = TemplateChecklistEvaluasi::where(['like', 'template', 'ceklist_evaluasi'])->andWhere(['!=', 'template', 'Ceklist_Evaluasi_Kesimpulan'])->asArray()->all();
        $arTemplate = collect($templates)->pluck('id')->toArray();
        $ar1 = $r['templates'];
        $ar1 = (explode(',', $ar1));
        sort($ar1, SORT_NUMERIC);
        $ar_difference = array_diff($arTemplate, $ar1);
        print_r($ar1);
        print_r($ar_difference);
        print_r($arTemplate);
        // echo in_array($ar1[3], $arTemplate)?'ada '. $ar1[3]:'';
    }
    public function actionIndex() {
        echo "\n";
        Yii::error('hello world');
        die;
    }
    public function actionSeed() {
    }
    public function actionDropAllTables() {
        $db = \Yii::$app->db;
        $schema = $db->schema;
        $tables = $schema->getTableNames();
        $db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
        foreach ($tables as $table) {
            echo "Dropping table: $table\n";
            $db->createCommand()->dropTable($table)->execute();
        }
        $db->createCommand('SET FOREIGN_KEY_CHECKS = 1;')->execute();
        echo "All tables dropped successfully.\n";
    }
    public function actionTruncateTransaksi() {
        $db = \Yii::$app->db;
        $schema = $db->schema;
        // $tables = $schema->getTableNames();
        $tables = [
            // 'setting',
            'validasi_kualifikasi_penyedia',
            'validasi_kualifikasi_penyedia_detail',
            'attachment',
            'penawaran_pengadaan',
            'negosiasi',
            'rincian_penawaran',
            'histori_reject',
            'paket_pengadaan',
            'paket_pengadaan_details',
            'penugasan_pemilihanpenyedia',
            'persetujuan_pengadaan',
            'review_dpp',
            'dpp',
            'dok_akta_penyedia',
            'dok_ijinusaha',
            'pengalaman_penyedia',
            'peralatan_kerja',
            'staff_ahli'
        ];
        // unlink all files in folder web/uploads
        $dir = Yii::getAlias('@uploads');
        if (is_dir($dir)) {
            $files = glob($dir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
        // $db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
        foreach ($tables as $table) {
            echo "Dropping table: $table\n";
            $db->createCommand()->delete($table)->execute();
            $db->createCommand()->delete('sqlite_sequence', ['name' => $table])->execute();
            // DELETE FROM 'sqlite_sequence' WHERE name='your_table';
            // $db->createCommand()->truncateTable($table)->execute();
        }
        // $db->createCommand('SET FOREIGN_KEY_CHECKS = 1;')->execute();
        echo "Truncate successfully.\n";
        // cache flush
        Yii::$app->cache->flush();
    }
    public function actionJenisakta() {
        $jenisAkta = [
            'PENDIRIAN PT',
            'PENDIRIAN CV',
            'PEMBUATAN UD',
            'SKMHT',
            'FIDUSIA',
            'SURAT KUASA JUAL',
            'PENGIKATAN JUAL BELI',
            'SURAT KUASA',
            'WAARMERKING',
            'LEGALISASI',
            'LEGALISIR',
            'PERJANJIAN KAWIN',
            'PERJANJIAN SEWA',
            'PERKUMPULAN',
            'PEMBUATAN YAYASAN',
            'AKTA PERUBAHAN',
            'LAIN-LAIN',
        ];
        //insert batch to table setting , key is jenis_akta, value is array above
        foreach ($jenisAkta as $key => $value) {
            Yii::$app->db->createCommand()->insert('setting', ['active' => 1, 'type' => 'jenis_akta', 'value' => $value])->execute();
        }
    }
    public function actionJenisperalihan() {
        $peralihan = [
            'JUAL BELI', 'HIBAH', 'WARIS', 'KONVERSI', 'PECAH', 'SERTIPIKAT HILANG'
        ];
        foreach ($peralihan as $key => $value) {
            Yii::$app->db->createCommand()->insert('setting', ['active' => 1, 'type' => 'jenis_peralihan', 'value' => $value])->execute();
        }
    }
}
