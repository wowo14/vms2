<?php
namespace app\models;
use Yii;
class PersetujuanPengadaan extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'persetujuan_pengadaan';
    }
    public function rules()
    {
        return [
            [['dpp_id', 'paket_id', 'ppkom', 'kpa', 'created_by', 'updated_by'], 'integer'],
            [['tanggal_persetujuan', 'created_at', 'updated_at'], 'safe'],
            [['perihal'], 'string'],
            [['nomor_persetujuan'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dpp_id' => 'Dpp ID',
            'paket_id' => 'Paket ID',
            'nomor_persetujuan' => 'Nomor Persetujuan',
            'tanggal_persetujuan' => 'Tanggal Persetujuan',
            'perihal' => 'Perihal',
            'ppkom' => 'Ppkom',
            'kpa' => 'Kpa',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
}