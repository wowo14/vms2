<?php
namespace app\models;
use Yii;
class PenilaianPenyedia extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'penilaian_penyedia';
    }
    public function rules()
    {
        return [
            [['unit_kerja', 'bast', 'bast_diterimagudang', 'created_at', 'updated_at', 'nama_perusahaan', 'alamat_perusahaan', 'paket_pekerjaan', 'lokasi_pekerjaan', 'nomor_kontrak', 'jangka_waktu', 'tanggal_kontrak', 'metode_pemilihan', 'details', 'pengguna_anggaran', 'pejabat_pembuat_komitmen'], 'string'],
            [['alamat_perusahaan', 'paket_pekerjaan', 'nilai_kontrak', 'nomor_kontrak'], 'required'],
            [['nilai_kontrak'], 'number'],
            [['dpp_id', 'created_by', 'updated_by'], 'integer'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dpp_id' => 'Dpp',
            'unit_kerja' => 'Unit Kerja',
            'nama_perusahaan' => 'Nama Perusahaan',
            'alamat_perusahaan' => 'Alamat Perusahaan',
            'paket_pekerjaan' => 'Paket Pekerjaan',
            'lokasi_pekerjaan' => 'Lokasi Pekerjaan',
            'nilai_kontrak' => 'Nilai Kontrak',
            'nomor_kontrak' => 'Nomor Kontrak',
            'jangka_waktu' => 'Jangka Waktu',
            'tanggal_kontrak' => 'Tanggal Kontrak',
            'metode_pemilihan' => 'Metode Pemilihan',
            'details' => 'Details',
            'pengguna_anggaran' => 'Pengguna Anggaran',
            'pejabat_pembuat_komitmen' => 'Pejabat Pembuat Komitmen',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'bast' => 'BAST',
            'bast_diterimagudangan' => 'BAST Diterima Gudang',
        ];
    }
    public function getDpp()
    {
        return $this->hasOne(Dpp::class, ['id' => 'dpp_id'])->cache(self::cachetime(), self::settagdep('tag_dpp'));
    }
    public function store()
    {
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = date('Y-m-d H:i:s');
                if (isset(Yii::$app->user) && Yii::$app->user->identity) {
                    $this->created_by = Yii::$app->user->id;
                }
                if (isset($this->bast)) {
                    $this->bast = !empty($this->bast) ? $this->upload($this->bast, 'bast_' . $this->dpp_id . '_' . time()) : '';
                }
            } else {
                $this->updated_at = date('Y-m-d H:i:s');
                if (isset(Yii::$app->user) && Yii::$app->user->identity) {
                    $this->updated_by = Yii::$app->user->id;
                }
                $this->bast = !empty($this->bast) && self::isBase64Encoded($this->bast) ? $this->upload($this->bast, 'bast_' . $this->dpp_id . '_' . time()) : $this->bast;
            }
            return true;
        } else {
            return false;
        }
    }
    public function getGriddetail()
    {
        $data = json_decode($this->details, true);
        $rows = [];
        //add check isset key uraian
        if (isset($data['uraian'])) {
            foreach ($data['uraian'] as $index => $uraian) {
                $rows[] = [
                    'uraian' => $uraian,
                    'skor' => $data['skor'][$index],
                ];
            }
        }
        $summary = [
            ['uraian' => 'Total', 'skor' => $data['total']],
            ['uraian' => 'Nilai Akhir', 'skor' => $data['nilaiakhir']],
            ['uraian' => 'Hasil Evaluasi', 'skor' => $data['hasil_evaluasi']],
            ['uraian' => 'Ulasan Pejabat Pengadaan', 'skor' => $data['ulasan_pejabat_pengadaan']],
        ];
        // Merge the main rows and summary rows
        return array_merge($rows, $summary);
    }
    public static function getScoreDescription($uraian, $score)
    {
        $types = ['evaluasi_suplier_ppk', 'evaluasi_suplier_pejabat'];

        foreach ($types as $type) {
            $setting = \app\models\Setting::findOne(['type' => $type, 'active' => 1]);
            if ($setting) {
                $config = json_decode($setting->value, true);
                if (isset($config['kriteria'])) {
                    foreach ($config['kriteria'] as $criteria) {
                        if (strcasecmp(trim($criteria['name']), trim($uraian)) === 0) {
                            if (isset($criteria['description'][$score])) {
                                return $score . ' (' . $criteria['description'][$score] . ')';
                            }
                        }
                    }
                }
            }
        }

        return $score;
    }
}
