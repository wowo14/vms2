<?php
namespace app\models;
use Yii;
class ReviewDpp extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'review_dpp';
    }
    public function rules()
    {
        return [
            [['dpp_id', 'pejabat', 'kesesuaian', 'created_by', 'updated_by'], 'integer'],
            [['tanggal_review', 'created_at', 'updated_at'], 'safe'],
            [['uraian'], 'string'],
            [['keterangan'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dpp_id' => 'Dpp ID',
            'tanggal_review' => 'Tanggal Review',
            'pejabat' => 'Pejabat',
            'uraian' => 'Uraian',
            'kesesuaian' => 'Kesesuaian',
            'keterangan' => 'Keterangan',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
}