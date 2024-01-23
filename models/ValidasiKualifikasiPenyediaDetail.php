<?php
namespace app\models;
use Yii;
class ValidasiKualifikasiPenyediaDetail extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'validasi_kualifikasi_penyedia_detail';
    }
    public function rules()
    {
        return [
            [['header_id', 'uraian', 'hasil_evaluasi', 'hasil_pembuktian'], 'required'],
            [['header_id', 'created_by', 'updated_by'], 'integer'],
            [['hasil_pembuktian', 'created_at', 'updated_at'], 'string'],
            [['uraian', 'hasil_evaluasi'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'header_id' => 'Header ID',
            'uraian' => 'Uraian',
            'hasil_evaluasi' => 'Hasil Evaluasi',
            'hasil_pembuktian' => 'Hasil Pembuktian',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
}