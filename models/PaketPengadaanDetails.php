<?php
namespace app\models;
use Yii;
class PaketPengadaanDetails extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public $totalhps;
    public $totalpenawaran;
    public $totalnegosiasi;
    public static function tableName()
    {
        return 'paket_pengadaan_details';
    }
    public function rules()
    {
        return [
            [['paket_id'], 'integer'],
            [['qty','volume'], 'number'],
            [['nama_produk', 'volume', 'satuan'], 'required'],
            [['hps_satuan', 'informasi_harga'], 'number'],
            [['nama_produk', 'satuan', 'durasi', 'sumber_informasi'], 'string', 'max' => 255],
            [['penawaran','negosiasi'],'safe'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'paket_id' => 'Paket ID',
            'nama_produk' => 'Nama Produk',
            'volume' => 'Volume',
            'qty'=>'Qty',
            'satuan' => 'Satuan',
            'hps_satuan'=> 'HPS Satuan',
            'penawaran'=>'Penawaran',
            'negosiasi'=>'Negosiasi',
            'durasi' => 'Durasi',
            // 'harga' => 'Harga',
            'informasi_harga' => 'Informasi Harga',
            'sumber_informasi' => 'Sumber Informasi',
        ];
    }
    public function beforeSave($insert)
    {
        
        $this->nama_produk=preg_replace('/\s+/', ' ', trim($this->nama_produk));
        return parent::beforeSave($insert);
    }
    public function getPaketpengadaan(){
        return $this->hasOne(PaketPengadaan::className(), ['id' => 'paket_id']);
    }
    public static function sumNegosiasi($paket_id) {
        $model = PaketPengadaanDetails::collectAll(['paket_id' => $paket_id]);
        $model = $model->map(function ($e) {
            $d['totalnegosiasi'] = (float)($e->qty ?? 1)
                * (float)($e->volume ?? 1)
                * (float)(Yii::$app->tools->reverseCurrency($e->negosiasi));
            return $d;
        });
        return $model->sum('totalnegosiasi');
    }
    public function getTotalnego() {
        $model = PaketPengadaanDetails::collectAll(['paket_id' => $this->paket_id]);
        $model = $model->map(function ($e) {
            $d['totalnegosiasi'] = (float)($e->qty ?? 1)
                * (float)($e->volume ?? 1)
                * (float)(Yii::$app->tools->reverseCurrency($e->negosiasi));
            return $d;
        });
        return $model->sum('totalnegosiasi');
    }
    public function getTotalpnwrn() {
        $model = PaketPengadaanDetails::collectAll(['paket_id' => $this->paket_id]);
        $model = $model->map(function ($e) {
            $d['totalpenawaran'] = (float)($e->qty ?? 1)
                * (float)($e->volume ?? 1)
                * (float)(Yii::$app->tools->reverseCurrency($e->penawaran));
            return $d;
        });
        return $model->sum('totalpenawaran');
    }
    public function getTotalhpssatuan() {
        $model = PaketPengadaanDetails::collectAll(['paket_id' => $this->paket_id]);
        $model = $model->map(function ($e) {
            $d['totalhps'] = (float)($e->qty ?? 1)
                * (float)($e->volume ?? 1)
                * (float)(Yii::$app->tools->reverseCurrency($e->hps_satuan));
            return $d;
        });
        return $model->sum('totalhps');
    }

    public static function getPriceComparisonByProduct($namaProduk)
    {
        $cleanName = preg_replace('/\s+/', ' ', trim($namaProduk));

        return (new \yii\db\Query())
            ->select([
                'pd.nama_produk',
                'pd.satuan',
                new \yii\db\Expression("strftime('%Y', pp.tanggal_paket) AS tahun"),
                new \yii\db\Expression("COUNT(pd.id) AS total_transaksi"),
                new \yii\db\Expression("AVG(pd.hps_satuan) AS avg_hps_satuan"),
                new \yii\db\Expression("MIN(pd.hps_satuan) AS min_hps_satuan"),
                new \yii\db\Expression("MAX(pd.hps_satuan) AS max_hps_satuan"),
            ])
            ->from(['pd' => self::tableName()])
            ->innerJoin(['pp' => PaketPengadaan::tableName()], 'pp.id = pd.paket_id')
            ->where(['like', 'pd.nama_produk', $cleanName])
            ->groupBy([
                new \yii\db\Expression("strftime('%Y', pp.tanggal_paket)"),
                'pd.nama_produk',
                'pd.satuan'
            ])
            ->orderBy(['tahun' => SORT_ASC])
            ->all();
    }

    public static function getYoYPriceTrend($namaProduk)
    {
        $cleanName = preg_replace('/\s+/', ' ', trim($namaProduk));

        $sql = "
            SELECT
                tahun,
                nama_produk,
                satuan,
                avg_hps,
                LAG(avg_hps) OVER (PARTITION BY nama_produk ORDER BY tahun) AS harga_tahun_lalu,
                ROUND(((avg_hps - LAG(avg_hps) OVER (PARTITION BY nama_produk ORDER BY tahun)) / NULLIF(LAG(avg_hps) OVER (PARTITION BY nama_produk ORDER BY tahun), 0)) * 100, 2) AS persentase_kenaikan
            FROM (
                SELECT
                    strftime('%Y', pp.tanggal_paket) AS tahun,
                    pd.nama_produk,
                    pd.satuan,
                    AVG(pd.hps_satuan) AS avg_hps
                FROM paket_pengadaan_details pd
                JOIN paket_pengadaan pp ON pp.id = pd.paket_id
                WHERE pd.nama_produk LIKE :nama
                GROUP BY tahun, pd.nama_produk, pd.satuan
            ) sub
        ";

        return Yii::$app->db->createCommand($sql, [':nama' => '%' . $cleanName . '%'])->queryAll();
    }

    public static function getReportPerbandinganHarga($params = [])
    {
        $tahunDipilih = $params['tahun'] ?? null;
        $kategori     = $params['kategori_pengadaan'] ?? null;
        $namaProduk   = $params['nama_produk'] ?? null;

        // Subquery untuk mendapatkan agregasi data
        $sql = "
            SELECT 
                kategori_pengadaan,
                nama_produk,
                satuan,
                tahun,
                jumlah_transaksi,
                avg_hps_satuan,
                avg_penawaran,
                avg_negosiasi,
                LAG(avg_negosiasi) OVER (PARTITION BY nama_produk, satuan ORDER BY tahun) AS harga_tahun_lalu,
                CASE 
                    WHEN LAG(avg_negosiasi) OVER (PARTITION BY nama_produk, satuan ORDER BY tahun) IS NULL OR LAG(avg_negosiasi) OVER (PARTITION BY nama_produk, satuan ORDER BY tahun) = 0 THEN NULL
                    ELSE ROUND(((avg_negosiasi - LAG(avg_negosiasi) OVER (PARTITION BY nama_produk, satuan ORDER BY tahun)) / LAG(avg_negosiasi) OVER (PARTITION BY nama_produk, satuan ORDER BY tahun)) * 100, 2)
                END AS persentase_fluktuasi
            FROM (
                SELECT 
                    pp.kategori_pengadaan,
                    pd.nama_produk,
                    pd.satuan,
                    strftime('%Y', pp.tanggal_paket) AS tahun,
                    COUNT(pd.id) AS jumlah_transaksi,
                    AVG(pd.hps_satuan) AS avg_hps_satuan,
                    AVG(pd.penawaran) AS avg_penawaran,
                    AVG(pd.negosiasi) AS avg_negosiasi
                FROM paket_pengadaan_details pd
                INNER JOIN paket_pengadaan pp ON pp.id = pd.paket_id
                WHERE pp.id IS NOT NULL
        ";

        $paramsSql = [];

        if (!empty($kategori)) {
            $sql .= " AND pp.kategori_pengadaan = :kategori";
            $paramsSql[':kategori'] = $kategori;
        }

        if (!empty($namaProduk)) {
            $cleanName = preg_replace('/\s+/', ' ', trim($namaProduk));
            $sql .= " AND pd.nama_produk LIKE :nama";
            $paramsSql[':nama'] = '%' . $cleanName . '%';
        }

        if (!empty($tahunDipilih)) {
            $sql .= " AND strftime('%Y', pp.tanggal_paket) = :tahun";
            $paramsSql[':tahun'] = $tahunDipilih;
        }

        $sql .= "
                GROUP BY pp.kategori_pengadaan, pd.nama_produk, pd.satuan, strftime('%Y', pp.tanggal_paket)
                ORDER BY pp.kategori_pengadaan ASC, pd.nama_produk ASC, tahun ASC
            ) sub
        ";

        $results = Yii::$app->db->createCommand($sql, $paramsSql)->queryAll();

        // Ambil paket data untuk setiap kombinasi produk-tahun
        foreach ($results as &$row) {
            $paketSql = "
                SELECT 
                    pp.id AS paket_id,
                    pp.nama_paket AS paket_name,
                    pd.nama_produk AS nama_produk,
                    pd.negosiasi AS harga_negosiasi
                FROM paket_pengadaan_details pd
                INNER JOIN paket_pengadaan pp ON pp.id = pd.paket_id
                WHERE pp.kategori_pengadaan = :kategori
                    AND pd.nama_produk = :nama_produk
                    AND pd.satuan = :satuan
                    AND strftime('%Y', pp.tanggal_paket) = :tahun
            ";

            $paketParams = [
                ':kategori' => $row['kategori_pengadaan'],
                ':nama_produk' => $row['nama_produk'],
                ':satuan' => $row['satuan'],
                ':tahun' => $row['tahun'],
            ];

            // Apply same filters for product name if exists
            if (!empty($namaProduk)) {
                $cleanName = preg_replace('/\s+/', ' ', trim($namaProduk));
                $paketSql .= " AND pd.nama_produk LIKE :nama_filter";
                $paketParams[':nama_filter'] = '%' . $cleanName . '%';
            }

            $paketResults = Yii::$app->db->createCommand($paketSql, $paketParams)->queryAll();
            
            // Encode paket_ids and structure the data
            $paketData = [];
            foreach ($paketResults as $paket) {
                $paketData[] = [
                    'id' => Yii::$app->hashids->encode($paket['paket_id']),
                    'nama_paket' => $paket['paket_name'],
                    'nama_produk' => $paket['nama_produk'],
                    'harga_negosiasi' => $paket['harga_negosiasi'],
                ];
            }
            
            $row['paket_data'] = $paketData;
            $row['paket_ids'] = implode(',', array_column($paketData, 'id'));
            $row['paket_names'] = implode(',', array_column($paketData, 'nama_paket'));
        }

        return $results;
    }

    public static function getKategoriList()
    {
        return (new \yii\db\Query())
            ->select(['kategori_pengadaan'])
            ->from(PaketPengadaan::tableName())
            ->where(['not', ['kategori_pengadaan' => null]])
            ->andWhere(['!=', 'kategori_pengadaan', ''])
            ->distinct()
            ->column();
    }

    public static function getTahunList()
    {
        return (new \yii\db\Query())
            ->select([
                new \yii\db\Expression("strftime('%Y', tanggal_paket) AS tahun")
            ])
            ->from(PaketPengadaan::tableName())
            ->where(['not', ['tanggal_paket' => null]])
            ->distinct()
            ->orderBy(['tahun' => SORT_DESC])
            ->column();
    }
}