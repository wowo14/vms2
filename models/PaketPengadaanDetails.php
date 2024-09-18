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
            [['paket_id', 'qty','volume'], 'integer'],
            [['nama_produk', 'qty','volume', 'satuan'], 'required'],
            [['hps_satuan','penawaran','negosiasi', 'informasi_harga'], 'number'],
            [['nama_produk', 'satuan', 'durasi', 'sumber_informasi'], 'string', 'max' => 255],
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
    public function getPaketpengadaan(){
        return $this->hasOne(PaketPengadaan::className(), ['id' => 'paket_id']);
    }
}