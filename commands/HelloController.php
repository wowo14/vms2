<?php

namespace app\commands;

use app\Controllers\DppController;
use app\models\Contacts;
use app\models\Dpp;
use app\models\PaketPengadaan;
use app\models\PaketPengadaanDetails;
use app\models\PenawaranPengadaan;
use app\models\Sertipikat;
use app\models\Setting;
use app\models\TemplateChecklistEvaluasi;
use app\models\TemplateChecklistEvaluasiDetail;
use app\models\User;
use app\models\ValidasiKualifikasiPenyedia;
use app\models\ValidasiKualifikasiPenyediaDetail;
use Yii;
use yii\console\Controller;
use yii\db\Expression;

class HelloController extends Controller {
    public function actionHitung() { // hitung pada paket pengadaan mana?
        $r = (new PaketPengadaan)->byMetode();
        print_r($r);
    }
    public function actionIndex() {
        echo "\n";
        Yii::error('hello world');

        $d = collect(Setting::type('metode_pengadaan'))->pluck('id', 'value');
        print_r($d);
        die;
    }
    public function actionSeed() {
    }
    public function actionDropAllTables() {
        $db = \Yii::$app->db;
        $schema = $db->schema;
        $tables = $schema->getTableNames();
        // $db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
        foreach ($tables as $table) {
            echo "Dropping table: $table\n";
            $db->createCommand()->dropTable($table)->execute();
        }
        // $db->createCommand('SET FOREIGN_KEY_CHECKS = 1;')->execute();
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
        $query = "SELECT name FROM sqlite_master WHERE type = 'table' AND name GLOB '_*'";
        $tables = $db->query($query);
        foreach ($tables as $table) {
            $tableName = $table['name'];
            $dropQuery = "DROP TABLE IF EXISTS " . $tableName;
            $db->exec($dropQuery);
            echo "Dropped table: " . $tableName . "\n";
        }
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
            'JUAL BELI',
            'HIBAH',
            'WARIS',
            'KONVERSI',
            'PECAH',
            'SERTIPIKAT HILANG'
        ];
        foreach ($peralihan as $key => $value) {
            Yii::$app->db->createCommand()->insert('setting', ['active' => 1, 'type' => 'jenis_peralihan', 'value' => $value])->execute();
        }
    }
    public function actionTes2() {
        $formattedAmount = 'Rp 2.440.000,00';
        $res = (new \app\widgets\Tools)->reverseCurrency($formattedAmount);
        print_r($res);
    }
}
