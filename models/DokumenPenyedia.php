<?php
namespace app\models;
use Yii;
class DokumenPenyedia extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'dokumen_penyedia';
    }
    public function rules()
    {
        return [
            [['penyedia_id', 'dokumen', 'file', 'tanggal_berlaku', 'is_active'], 'required'],
            [['penyedia_id', 'is_active', 'created_by', 'updated_by'], 'integer'],
            [['tanggal_berlaku', 'created_at', 'updated_at'], 'string'],
            [['dokumen', 'file'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'penyedia_id' => 'Penyedia ID',
            'dokumen' => 'Dokumen',
            'file' => 'File',
            'tanggal_berlaku' => 'Tanggal Berlaku',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
}