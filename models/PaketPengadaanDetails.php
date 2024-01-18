<?php
namespace app\models;
use Yii;
class PaketPengadaanDetails extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'paket_pengadaan_details';
    }
    public function rules()
    {
        return [
            [['paket_id', 'volume'], 'integer'],
            [['nama_produk', 'volume', 'satuan', 'durasi', 'harga', 'informasi_harga', 'hps', 'jumlah', 'sumber_informasi'], 'required'],
            [['harga', 'informasi_harga', 'hps', 'jumlah'], 'number'],
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
            'satuan' => 'Satuan',
            'durasi' => 'Durasi',
            'harga' => 'Harga',
            'informasi_harga' => 'Informasi Harga',
            'hps' => 'Hps',
            'jumlah' => 'Jumlah',
            'sumber_informasi' => 'Sumber Informasi',
        ];
    }
}