<?php
namespace app\models;
use Yii;
class AktaPenyedia extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'dok_akta_penyedia';
    }
    public function rules()
    {
        return [
            [['penyedia_id', 'jenis_akta', 'nomor_akta', 'tanggal_akta'], 'required'],
            [['penyedia_id', 'created_by', 'updated_by'], 'integer'],
            [['tanggal_akta', 'file_akta', 'created_at', 'updated_at'], 'string'],
            [['jenis_akta', 'nomor_akta', 'notaris'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'penyedia_id' => 'Penyedia ID',
            'jenis_akta' => 'Jenis Akta',
            'nomor_akta' => 'Nomor Akta',
            'tanggal_akta' => 'Tanggal Akta',
            'notaris' => 'Notaris',
            'file_akta' => 'File Akta',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
}