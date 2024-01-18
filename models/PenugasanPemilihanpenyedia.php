<?php
namespace app\models;
use Yii;
class PenugasanPemilihanpenyedia extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'penugasan_pemilihanpenyedia';
    }
    public function rules()
    {
        return [
            [['dpp_id', 'pejabat', 'admin', 'created_by', 'updated_by'], 'integer'],
            [['tanggal_tugas', 'created_at', 'updated_at'], 'safe'],
            [['nomor_tugas'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dpp_id' => 'Dpp ID',
            'nomor_tugas' => 'Nomor Tugas',
            'tanggal_tugas' => 'Tanggal Tugas',
            'pejabat' => 'Pejabat',
            'admin' => 'Admin',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
}