<?php
namespace app\commands;
use app\models\Dpp;
use app\models\HistoriReject;
use app\models\PaketPengadaan;
use app\models\PaketPengadaanDetails;
use Yii;
use yii\console\Controller;
use yii\db\Expression;
use yii\console\ExitCode;
use yii\helpers\Json;
class HelloController extends Controller {
    /**
     * Menampilkan paket_pengadaan_details berdasarkan nomor DPP.
     *
     * Contoh:
     * php yii hello/list-paket-pengadaan-details "000.3.3/5700/437.52.35.13/2026"
     */
    public function actionListPaketPengadaanDetails(string $nomorDpp): int
    {
        $details = (new \yii\db\Query())
            ->select([
                'dpp_id'    => 'd.id',
                'nomor_dpp' => 'd.nomor_dpp',
                'paket_id'  => 'pp.id',
                'detail_id' => 'pd.id',
                'nama_produk' => 'pd.nama_produk',
                'qty'       => 'pd.qty',

                // Tambahkan kolom identitas barang sesuai struktur tabel Anda.
                // 'barang_id'   => 'pd.barang_id',
                // 'nama_barang' => 'pd.nama_barang',
                // 'satuan'      => 'pd.satuan',
            ])
            ->from(['d' => 'dpp'])
            ->innerJoin(
                ['pp' => 'paket_pengadaan'],
                'pp.id = d.paket_id'
            )
            ->innerJoin(
                ['pd' => 'paket_pengadaan_details'],
                'pd.paket_id = pp.id'
            )
            ->where([
                'd.nomor_dpp' => $nomorDpp,
            ])
            ->orderBy([
                'pd.id' => SORT_ASC,
            ])
            ->all();

        if (empty($details)) {
            $this->stderr(
                "Detail paket tidak ditemukan untuk DPP: {$nomorDpp}\n"
            );

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("Nomor DPP : {$nomorDpp}\n");
        $this->stdout("Jumlah     : " . count($details) . " detail\n\n");

        $this->stdout(
            str_pad('DETAIL ID', 15) .
            str_pad('PAKET ID', 15) .
            str_pad('NAMA PRODUK', 20) .
            str_pad('QTY', 10) .
            "\n"
        );

        $this->stdout(str_repeat('-', 55) . "\n");

        foreach ($details as $detail) {
            $this->stdout(
                str_pad((string) $detail['detail_id'], 15) .
                str_pad((string) $detail['paket_id'], 15) .
                str_pad('(' . (string) $detail['nama_produk'] . ')', 20) .
                str_pad('#'.(string) $detail['qty'], 10) .
                "\n"
            );
        }

        $this->stdout("\nFormat update:\n");
        $this->stdout(
            'php yii hello/update-paket-pengadaan-details ' .
            '"' . $nomorDpp . '" ' .
            '"DETAIL_ID=QTY,DETAIL_ID=QTY"' .
            "\n"
        );

        return ExitCode::OK;
    }
    /**
     * Update qty paket_pengadaan_details berdasarkan nomor DPP.
     *
     * Contoh:
     * php yii hello/update-paket-pengadaan-details \
     * "DPP/2439/2026" \
     * '12042:6,12046=2'
     *
     * Tanpa konfirmasi:
     * php yii hello/update-paket-pengadaan-details \
     * "DPP/2439/2026" \
     * '{"12042":6,"12046":2}' \
     * 1
     */
    public function actionUpdatePaketPengadaanDetails(
        string $nomorDpp,
        string $updatesInput,
        int $yes = 0
    ): int {
        $updates = [];

        foreach (explode(',', $updatesInput) as $item) {
            $item = trim($item);

            if (!preg_match('/^(\d+)=(\d+)$/', $item, $matches)) {
                $this->stderr(
                    "Format update tidak valid: {$item}\n" .
                    "Gunakan format: detail_id=qty,detail_id=qty\n"
                );

                return ExitCode::DATAERR;
            }

            $detailId = (int) $matches[1];
            $qty      = (int) $matches[2];

            if ($detailId <= 0) {
                $this->stderr("Detail ID harus lebih besar dari 0.\n");

                return ExitCode::DATAERR;
            }

            $updates[$detailId] = $qty;
        }

        if (empty($updates)) {
            $this->stderr("Data update tidak boleh kosong.\n");

            return ExitCode::DATAERR;
        }

        /*
         * Cari DPP menggunakan nomor lengkap agar tidak terjadi salah paket.
         */
        $details = (new \yii\db\Query())
            ->select([
                'dpp_id'          => 'd.id',
                'nomor_dpp'       => 'd.nomor_dpp',
                'paket_id'        => 'pp.id',
                'detail_id'       => 'pd.id',
                'qty_lama'        => 'pd.qty',
            ])
            ->from(['d' => 'dpp'])
            ->innerJoin(
                ['pp' => 'paket_pengadaan'],
                'pp.id = d.paket_id'
            )
            ->innerJoin(
                ['pd' => 'paket_pengadaan_details'],
                'pd.paket_id = pp.id'
            )
            ->where(['d.nomor_dpp' => $nomorDpp])
            ->andWhere(['pd.id' => array_keys($updates)])
            ->orderBy(['pd.id' => SORT_ASC])
            ->all();

        if (empty($details)) {
            $this->stderr(
                "Detail paket tidak ditemukan untuk nomor DPP: {$nomorDpp}\n"
            );

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $foundIds     = array_map('strval', array_column($details, 'detail_id'));
        $requestedIds = array_map('strval', array_keys($updates));
        $invalidIds   = array_diff($requestedIds, $foundIds);

        /*
         * Jangan lanjut jika ada detail ID yang bukan milik paket tersebut.
         */
        if (!empty($invalidIds)) {
            $this->stderr(
                "Update dibatalkan. Detail ID berikut bukan milik DPP " .
                "{$nomorDpp}: " . implode(', ', $invalidIds) . "\n"
            );

            return ExitCode::DATAERR;
        }

        $this->stdout("Rencana perubahan:\n");

        foreach ($details as $detail) {
            $detailId = (string) $detail['detail_id'];
            $qtyBaru  = $updates[$detailId] ?? $updates[(int) $detailId];

            $this->stdout(
                "Detail ID {$detailId}: " .
                "{$detail['qty_lama']} -> {$qtyBaru}\n"
            );
        }

        if (!$yes && !$this->confirm('Lanjutkan proses update?')) {
            $this->stdout("Update dibatalkan.\n");

            return ExitCode::OK;
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $totalUpdated = 0;

            foreach ($details as $detail) {
                $detailId = (string) $detail['detail_id'];
                $qtyBaru  = $updates[$detailId] ?? $updates[(int) $detailId];

                if (
                    filter_var($qtyBaru, FILTER_VALIDATE_INT) === false ||
                    (int) $qtyBaru < 0
                ) {
                    throw new \InvalidArgumentException(
                        "Qty detail ID {$detailId} harus berupa angka bulat " .
                        "dan tidak boleh negatif."
                    );
                }

                $totalUpdated += Yii::$app->db
                    ->createCommand()
                    ->update(
                        'paket_pengadaan_details',
                        [
                            'qty' => (int) $qtyBaru,
                        ],
                        [
                            'id'       => (int) $detailId,
                            'paket_id' => (int) $detail['paket_id'],
                        ]
                    )
                    ->execute();
            }

            $transaction->commit();
            Yii::$app->cache->flush();
            Yii::$app->db->schema->refresh();

            $this->stdout(
                "Sukses. {$totalUpdated} detail paket berhasil diperbarui.\n"
            );

            return ExitCode::OK;
        } catch (\Throwable $e) {
            $transaction->rollBack();

            $this->stderr("Update gagal: {$e->getMessage()}\n");

            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
    // public function actionUpdateadmindpp(){
    //     $sql="UPDATE dpp SET admin_pengadaan =20 WHERE jenis_dpp=304";
    //     Yii::$app->db->createCommand($sql)->execute();
    //     $sql="UPDATE dpp SET jenis_dpp=NULL WHERE jenis_dpp=303";
    //     Yii::$app->db->createCommand($sql)->execute();
    //     print_r('sukses update admin pengadaan');
    // }
    public function actionIndex() {
       print_r('hello world');
       $query=PaketPengadaan::find()->cache(10)
            ->select([
                new Expression("strftime('%Y', paket_pengadaan.tanggal_paket) as year"),
            ])
            ->distinct()
            ->asArray()
            ->all();
        print_r(collect($query)->pluck('year','year')->toArray());
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
    /*
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
    */
    /*
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
    */
    public function actionUpdateppbmhp(){
        $q="UPDATE dpp d
        JOIN paket_pengadaan p ON d.paket_id = p.id
        JOIN unit u ON p.unit = u.id
        SET d.jenis_dpp = 306
        WHERE u.unit LIKE '%KEPERAWATAN%'
        AND d.jenis_dpp = 304";
        Yii::$app->db->createCommand($q)->execute();
        echo "Update successfully.\n";
        Yii::$app->cache->flush();
        Yii::$app->db->schema->refresh();
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
    public function actionUpdatepaketppn($paket_id){
        foreach(PaketPengadaanDetails::find()->where(['paket_id' => $paket_id])->all() as $detail){
            $detail->hps_satuan += $detail->hps_satuan * 0.11;
            $detail->save();
        }
        Yii::$app->cache->flush();
        Yii::$app->db->schema->refresh();
    }
    public function actionPindahkategori($paket_id,$kategori){
        $paket = PaketPengadaan::findOne($paket_id);
        $paket->kategori_pengadaan = $kategori;
        $paket->save();
        Yii::$app->cache->flush();
        Yii::$app->db->schema->refresh();
    }
}
