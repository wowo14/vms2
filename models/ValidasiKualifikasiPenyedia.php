<?php
namespace app\models;
use Yii;
class ValidasiKualifikasiPenyedia extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'validasi_kualifikasi_penyedia';
    }
    public function rules()
    {
        return [
            [['penyedia_id', 'keperluan', 'is_active'], 'required'],
            [['penyedia_id', 'is_active', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'string'],
            [['paket_pengadaan_id', 'keperluan'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'penyedia_id' => 'Penyedia ID',
            'paket_pengadaan_id' => 'Paket Pengadaan ID',
            'keperluan' => 'Keperluan',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
}