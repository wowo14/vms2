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
            [['unit_kerja','created_at', 'updated_at', 'nama_perusahaan', 'alamat_perusahaan', 'paket_pekerjaan', 'lokasi_pekerjaan', 'nomor_kontrak', 'jangka_waktu', 'tanggal_kontrak', 'metode_pemilihan', 'details', 'pengguna_anggaran', 'pejabat_pembuat_komitmen'], 'string'],
            [['alamat_perusahaan', 'paket_pekerjaan', 'nilai_kontrak', 'nomor_kontrak'], 'required'],
            [['nilai_kontrak'], 'number'],
            [['dpp_id', 'created_by', 'updated_by'], 'integer'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dpp_id'=>'Dpp',
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
        ];
    }
    public function getDpp(){
        return $this->hasOne(Dpp::class, ['id' => 'dpp_id'])->cache(self::cachetime(), self::settagdep('tag_dpp'));
    }
}
