<?php
namespace app\models;
use Yii;
class PaketPengadaan extends \yii\db\ActiveRecord {
    use GeneralModelsTrait;
    public $oldrecord;
    public static function tableName() {
        return 'paket_pengadaan';
    }
    public function rules() {
        return [
            [['nomor', 'tanggal_paket', 'nama_paket'], 'required'],
            [['tanggal_paket','tanggal_reject', 'alasan_reject'], 'string'],
            [['pagu'], 'number'],
            [['nama_paket'], 'unique'],
            [['created_by', 'tahun_anggaran', 'approval_by'], 'integer'],
            [['nomor', 'kategori_pengadaan','nama_paket', 'kode_program', 'kode_kegiatan', 'kode_rekening', 'ppkom', 'metode_pengadaan'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'nomor' => 'Nomor',
            'tanggal_paket' => 'Tanggal Paket',
            'nama_paket' => 'Nama Paket',
            'kode_program' => 'Kode Program',
            'kode_kegiatan' => 'Kode Kegiatan',
            'kode_rekening' => 'Kode Rekening',
            'ppkom' => 'Ppkom',
            'pagu' => 'Pagu',
            'metode_pengadaan' => 'Metode Pengadaan',//EPL,PL,E-Purchasing,
            'kategori_pengadaan' => 'Kategori Pengadaan', //barang/jasa, konstruksi, konsultansi
            'created_by' => 'Created By',
            'tahun_anggaran' => 'Tahun Anggaran',
            'approval_by' => 'Approval By',//null->belom,ditolak oleh ,<>0->diterima oleh
            'alasan_reject'=>'Alasan Reject',//not null ditolak
            'tanggal_reject'=>'Tanggal Reject',//not null ditolak
        ];
    }
    public function getListpaketoutstanding() {
        return PaketPengadaan::where(['approval_by' => null])->all();
    }
    public function getNomornamapaket() {
        return $this->nomor . '||' . $this->nama_paket;
    }
    public function getDetails() {
        return $this->hasMany(PaketPengadaanDetails::class, ['paket_id' => 'id'])->cache(self::cachetime(), self::settagdep('tag_paketpengadaandetails'));
    }
    public function afterFind() {
        $this->oldrecord = clone $this;
        parent::afterFind();
    }
    public function beforeSave($insert) {
        if ($insert) {
            $this->created_by = Yii::$app->user->identity->id;
            $this->tanggal_paket = date('Y-m-d H:i:s', time());
            $exist = self::where(['nama_paket' => $this->nama_paket])->exists();
            if ($exist) {
                Yii::$app->session->setFlash('error', 'Paket Pengadaan sudah ada');
                return false;
            }
        } else {
        }
        self::invalidatecache('tag_' . self::getModelname());
        Dpp::invalidatecache('tag_' . Dpp::getModelname());
        return parent::beforeSave($insert);
    }
    public function getAttachments() {
        return $this->hasMany(Attachment::class, ['user_id' => 'id'])->cache(self::cachetime(), self::settagdep('tag_attachment'));
    }
    public function getRequiredlampiran(){//array id
        return collect(self::settingType('jenis_dokumen'))->where('param', 'lampiran')->pluck('id')->toArray();
    }
    public function getDpp(){
        return $this->hasOne(Dpp::class, ['paket_id' => 'id'])->cache(self::cachetime(), self::settagdep('tag_dpp'));
    }
    public function getSubmitedpenawaran(){
        return $this->hasMany(PenawaranPengadaan::class, ['paket_id' => 'id'])->cache(self::cachetime(), self::settagdep('tag_penawaranpengadaan'));
    }
}
