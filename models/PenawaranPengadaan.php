<?php
namespace app\models;
use Yii;
class PenawaranPengadaan extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public $isPemenang;
    public static function tableName()
    {
        return 'penawaran_pengadaan';
    }
    public function rules()
    {
        return [
            [['paket_id', 'penyedia_id', 'tanggal_mendaftar', 'masa_berlaku'], 'required'],
            [['paket_id', 'penyedia_id', 'penilaian', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['nilai_penawaran','tanggal_mendaftar'], 'safe'],
            [['lampiran_penawaran', 'lampiran_penawaran_harga'], 'string'],
            [['nomor', 'kode'], 'string', 'max' => 50],
            [['ip_client', 'masa_berlaku'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'paket_id' => 'Paket Pengadaan',
            'penyedia_id' => 'Penyedia',
            'nomor' => 'Nomor',
            'kode' => 'Kode',
            'tanggal_mendaftar' => 'Tanggal Mendaftar',
            'ip_client' => 'Ip Client',
            'masa_berlaku' => 'Masa Berlaku',
            'lampiran_penawaran' => 'Lampiran Penawaran',
            'lampiran_penawaran_harga' => 'Lampiran Penawaran Harga',
            'penilaian' => 'Penilaian',
            'nilai_penawaran' => 'Nilai Penawaran',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }
    public function getPenawaranpenyedia(){
        if(!$this->pemenang){
            return $this->kode.'||'.$this->paketpengadaan->nomornamapaket.'||'.$this->vendor->nama_perusahaan.'||'.\Yii::$app->formatter->asCurrency($this->nilai_penawaran);
        }
    }
    public function getPaketpengadaan(){
        return $this->hasOne(PaketPengadaan::class, ['id' => 'paket_id'])->cache(self::cachetime(), self::settagdep('tag_paketpengadaan'));
    }
    public function getPemenang(){
        return $isPemenang=$this->paketpengadaan->pemenang?true:false;
    }
    public function getAllpaketpengadaan(){
        return PaketPengadaan::where(['not',['nomor'=>null] ])->all();
    }
    public function getNegosiasi(){
        return $this->hasOne(Negosiasi::class, ['penawaran_id' => 'id'])->orderBy(['ammount' => SORT_ASC]);
    }
    public function autoDeleteFile() {
        $filePath = Yii::getAlias('@uploads') . $this->lampiran_penawaran;
        if (file_exists($filePath) && !empty($this->lampiran_penawaran)) {
            unlink($filePath);
        }
        $filePath = Yii::getAlias('@uploads') . $this->lampiran_penawaran_harga;
        if (file_exists($filePath) && !empty($this->lampiran_penawaran_harga)) {
            unlink($filePath);
        }
    }
    public function init() {
        parent::init();
        $this->on(self::EVENT_AFTER_DELETE, [$this, 'autoDeleteFile']);
    }
    public function beforeSave($insert) {
        if ($insert) {
            $this->ip_client= Yii::$app->request->userIp;
            $this->lampiran_penawaran = !empty($this->lampiran_penawaran) ? $this->upload($this->lampiran_penawaran, 'lampiran_penawaran_' . $this->penyedia_id . '_' . time()) : '';
            $this->lampiran_penawaran_harga = !empty($this->lampiran_penawaran_harga) ? $this->upload($this->lampiran_penawaran_harga, 'lampiran_penawaran_harga_' . $this->penyedia_id . '_' . time()) : '';
        } else {
            $this->lampiran_penawaran = !empty($this->lampiran_penawaran) && self::isBase64Encoded($this->lampiran_penawaran) ? $this->upload($this->lampiran_penawaran, 'lampiran_penawaran_' . $this->penyedia_id . '_' . time()) : $this->lampiran_penawaran;
            $this->lampiran_penawaran_harga = !empty($this->lampiran_penawaran_harga) && self::isBase64Encoded($this->lampiran_penawaran_harga) ? $this->upload($this->lampiran_penawaran_harga, 'lampiran_penawaran_harga_' . $this->penyedia_id . '_' . time()) : $this->lampiran_penawaran_harga;
        }
        return parent::beforeSave($insert);
    }
}