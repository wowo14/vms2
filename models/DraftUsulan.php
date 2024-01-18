<?php
namespace app\models;
use Yii;
class DraftUsulan extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'draft_usulan';
    }
    public function rules()
    {
        return [
            [['tahun_anggaran', 'unit_id'], 'required'],
            [['tahun_anggaran', 'unit_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'string'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tahun_anggaran' => 'Tahun Anggaran',
            'unit_id' => 'Unit ID',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function getDraftusulandetails()
    {
        return $this->hasMany(DraftUsulanDetails::class, ['header_id' => 'id'])->cache(self::cachetime(), self::settagdep('tag_draftusulandetails'));
    }
    public function getChild()
    {
        return $this->hasOne(DraftUsulanDetails::class, ['header_id' => 'id'])->cache(self::cachetime(), self::settagdep('tag_draftusulandetails'));
    }
    public function getUnit()
    {
        return $this->hasOne(Unit::class, ['id' => 'unit_id'])->cache(self::cachetime(), self::settagdep('tag_unit'));
    }
}