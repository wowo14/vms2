<?php
namespace app\commands;
use app\models\PaketPengadaan;
use app\models\PaketPengadaanDetails;
use app\models\Sertipikat;
use app\models\TemplateChecklistEvaluasi;
use app\models\TemplateChecklistEvaluasiDetail;
use app\models\ValidasiKualifikasiPenyedia;
use app\models\ValidasiKualifikasiPenyediaDetail;
use Yii;
use yii\console\Controller;
class HelloController extends Controller
{
    public function actionIndex()
    {
        echo "\n";
        $hasil = [];
        $collect = TemplateChecklistEvaluasi::findOne(1);
        $ar_element = explode(',', $collect->element);
        foreach (json_decode($collect->detail->uraian, true) as $v) {
            $c = ['uraian' => $v['uraian']];
            foreach ($ar_element as $element) {
                if($element){
                    $c[$element] = '';
                }
            }
            $hasil[]=$c;
        }
        print_r($hasil);
    }
    public function actionSeed(){
    }
    public function actionDropAllTables()
    {
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
    public function actionTruncateTransaksi()
    {
        $db = \Yii::$app->db;
        $schema = $db->schema;
        // $tables = $schema->getTableNames();
        $tables=[
            // 'setting',
            'validasi_kualifikasi_penyedia',
            'validasi_kualifikasi_penyedia_detail',
            // 'attachment',
            'review_dpp',
            // 'dpp',
        ];
        // $db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
        foreach ($tables as $table) {
            echo "Dropping table: $table\n";
            $db->createCommand()->delete($table)->execute();
            $db->createCommand()->delete('sqlite_sequence',['name'=>$table])->execute();
            // DELETE FROM 'sqlite_sequence' WHERE name='your_table';
            // $db->createCommand()->truncateTable($table)->execute();
        }
        // $db->createCommand('SET FOREIGN_KEY_CHECKS = 1;')->execute();
        echo "Truncate successfully.\n";
        // cache flush
        Yii::$app->cache->flush();
    }
    public function actionJenisakta()
    {
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
    public function actionJenisperalihan(){
        $peralihan=[
            'JUAL BELI','HIBAH','WARIS','KONVERSI','PECAH','SERTIPIKAT HILANG'
        ];
        foreach ($peralihan as $key => $value) {
            Yii::$app->db->createCommand()->insert('setting', ['active' => 1, 'type' => 'jenis_peralihan', 'value' => $value])->execute();
        }
    }
}
