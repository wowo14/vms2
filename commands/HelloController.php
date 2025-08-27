<?php
namespace app\commands;
use app\models\Dpp;
use app\models\HistoriReject;
use app\models\PaketPengadaan;
use Yii;
use yii\console\Controller;
use yii\db\Expression;
class HelloController extends Controller {
    public function actionIndex() {
        echo "\n";
        // Yii::error('hello world');
        $r= Dpp::find()
        ->joinWith(['paketpengadaan pp'])
        ->select(['tahun_anggaran','tanggal_paket','pejabat_pengadaan','admin_pengadaan','pp.pemenang'])
        ->where(['is', 'pp.pemenang', null])
            ->andWhere(['pp.tahun_anggaran' => date('Y')])
            // ->andWhere(['pejabat_pengadaan' => 40])
            ->asArray()->all();
            print_r($r);
        die;
    }
    public function actionRemoverejectpaket($id){
        $pp=PaketPengadaan::findOne($id);
        $pp->alasan_reject = null;
        $pp->tanggal_reject = null;
        $pp->save();
        Yii::$app->cache->flush();
        Yii::$app->db->schema->refresh();
        print_r('sukses delete reject');
    }
    public function actionDpptgl($nomordpp, $createddate, $updateddate) {
        Dpp::updateAll(['created_at' => $createddate, 'updated_at' => $updateddate], ['nomor_dpp' => $nomordpp]);
        Yii::$app->cache->flush();
        Yii::$app->db->schema->refresh();
    }
    public function actionDpptglterima($nomordpp, $tglterima) {
        Dpp::updateAll(['tanggal_terima' => $tglterima], ['nomor_dpp' => $nomordpp]);
        Yii::$app->cache->flush();
        Yii::$app->db->schema->refresh();
    }
    public function actionRemovehistory($nomordpp) {
        $paket=PaketPengadaan::find()
        ->where(['nomor' => $nomordpp])->one();
        $history=HistoriReject::last(['paket_id' => $paket->id])->delete();
        if($history)
            print_r('sukses delete history');
        Yii::$app->cache->flush();
        Yii::$app->db->schema->refresh();
    }
    public function actionHistoritgl($nomordpp, $tanggal_reject, $tanggal_dikembalikan) {
        $paket = PaketPengadaan::find()
            ->where(['nomor' => $nomordpp])->one();
        $his=HistoriReject::collectAll(['paket_id' => $paket->id]);
        // print_r($his->toArray());
        $his->values()->map(function ($h,$i) use ($tanggal_reject, $tanggal_dikembalikan) {
            $h->tanggal_reject = date('Y-m-d H:i:s', strtotime("-{$i} day", strtotime($tanggal_reject)));
            $h->tanggal_dikembalikan = date('Y-m-d H:i:s', strtotime("-{$i} day", strtotime($tanggal_dikembalikan)));
            $h->save();
        });
        print_r($his->pluck('tanggal_dikembalikan','tanggal_reject')->toArray());
        Yii::$app->cache->flush();
        Yii::$app->db->schema->refresh();
    }
    public function actionTglpaket($nomordpp,$tglpaket) {
        PaketPengadaan::updateAll(['tanggal_paket' => $tglpaket], ['nomor' => $nomordpp]);
        Yii::$app->cache->flush();
        Yii::$app->db->schema->refresh();
    }
    public function actionUnpemenang($id) {
        $dpp = PaketPengadaan::findOne($id);
        $dpp->pemenang = null;
        $dpp->save();
        Yii::$app->cache->flush();
        Yii::$app->db->schema->refresh();
    }
    // public function actionCount() {
    //     $dpp = Dpp::where(['is', 'pp.pemenang', null])
    //         ->joinWith(['paketpengadaan pp'])->asArray()->all();
    //     $countpp = collect($dpp)->pluck('pejabat_pengadaan')->countBy();
    //     $countadminpp = collect($dpp)->pluck('admin_pengadaan')->countBy();
    //     print_r($countpp);
    //     print_r($countadminpp);
    // }
    // public function actionDeletedpp() {
    //     //grab dpp without paket pengadaan
    //     $dpp = Dpp::collectAll();
    //     $dpp->map(function ($d) {
    //         if (!$d->paketpengadaan) {
    //             $d->unlinkAll('reviews', true);
    //             $d->unlinkAll('penugasan', true);
    //             $d->delete();
    //             Yii::error('deleted dpp ' . $d->id);
    //         }
    //     });
    // }
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
    // public function actionTes2() {
    //     $formattedAmount = 'Rp 2.440.000,00';
    //     $res = (new \app\widgets\Tools)->reverseCurrency($formattedAmount);
    //     // print_r($res);
    // }
}
