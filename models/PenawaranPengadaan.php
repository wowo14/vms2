<?php
namespace app\models;
use Yii;
class PenawaranPengadaan extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'penawaran_pengadaan';
    }
    public function rules()
    {
        return [
            [['paket_id', 'penyedia_id', 'penilaian', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['tanggal_mendaftar'], 'safe'],
            [['lampiran_penawaran', 'lampiran_penawaran_harga'], 'string'],
            [['nomor', 'kode'], 'string', 'max' => 50],
            [['ip_client', 'masa_berlaku'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'paket_id' => 'Paket ID',
            'penyedia_id' => 'Penyedia ID',
            'nomor' => 'Nomor',
            'kode' => 'Kode',
            'tanggal_mendaftar' => 'Tanggal Mendaftar',
            'ip_client' => 'Ip Client',
            'masa_berlaku' => 'Masa Berlaku',
            'lampiran_penawaran' => 'Lampiran Penawaran',
            'lampiran_penawaran_harga' => 'Lampiran Penawaran Harga',
            'penilaian' => 'Penilaian',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }
    public function getPaketPengadaan(){
        return $this->hasOne(PaketPengadaan::class, ['id' => 'paket_id'])->cache(self::cachetime(), self::settagdep('tag_paketpengadaan'));
    }
    public function getAllpaketpengadaan(){
        return PaketPengadaan::where(['not',['nomor'=>null] ])->asArray()->all();
    }
}