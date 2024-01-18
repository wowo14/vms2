<?php
namespace app\models;
use Yii;
class KodeRekening extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'kode_rekening';
    }
    public function rules()
    {
        return [
            [['kode', 'rekening', 'tahun_anggaran'], 'required'],
            [['parent', 'is_active', 'tahun_anggaran', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'string'],
            [['kode', 'rekening'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kode' => 'Kode',
            'rekening' => 'Rekening',
            'parent' => 'Parent',
            'is_active' => 'Is Active',
            'tahun_anggaran' => 'Tahun Anggaran',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
}