<?php
namespace app\models;
use Yii;
class Produk extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'produk';
    }
    public function rules()
    {
        return [
            [['kode_kbki', 'nama_produk', 'active'], 'required'],
            [['active', 'created_by', 'updated_by'], 'integer'],
            [['hargapasar', 'hargabeli', 'hargahps', 'hargalainya'], 'number'],
            [['created_at', 'updated_at'], 'string'],
            [['kode_kbki', 'nama_produk', 'merk', 'status_merk', 'nama_pemilik_merk', 'nomor_produk_penyedia', 'unit_pengukuran', 'jenis_produk', 'nilai_tkdn', 'nomor_sni', 'garansi_produk', 'spesifikasi_produk', 'layanan_lain', 'komponen_biaya', 'lokasi_tempat_usaha', 'keterangan_lainya', 'barcode'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kode_kbki' => 'Kode Kbki',
            'nama_produk' => 'Nama Produk',
            'merk' => 'Merk',
            'status_merk' => 'Status Merk',
            'nama_pemilik_merk' => 'Nama Pemilik Merk',
            'nomor_produk_penyedia' => 'Nomor Produk Penyedia',
            'unit_pengukuran' => 'Unit Pengukuran',
            'jenis_produk' => 'Jenis Produk',
            'nilai_tkdn' => 'Nilai Tkdn',
            'nomor_sni' => 'Nomor Sni',
            'garansi_produk' => 'Garansi Produk',
            'spesifikasi_produk' => 'Spesifikasi Produk',
            'layanan_lain' => 'Layanan Lain',
            'komponen_biaya' => 'Komponen Biaya',
            'lokasi_tempat_usaha' => 'Lokasi Tempat Usaha',
            'keterangan_lainya' => 'Keterangan Lainya',
            'active' => 'Active',
            'hargapasar' => 'Hargapasar',
            'hargabeli' => 'Hargabeli',
            'hargahps' => 'Hargahps',
            'hargalainya' => 'Hargalainya',
            'barcode' => 'Barcode',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}