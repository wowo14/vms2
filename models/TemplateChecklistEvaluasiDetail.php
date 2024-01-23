<?php
namespace app\models;
use Yii;
class TemplateChecklistEvaluasiDetail extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'template_checklist_evaluasi_detail';
    }
    public function rules()
    {
        return [
            [['header_id', 'uraian'], 'required'],
            [['header_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'string'],
            [['uraian'], 'string'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'header_id' => 'Header ID',
            'uraian' => 'Uraian',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
    public function getHeaders(){
        return $this->hasMany(TemplateChecklistEvaluasi::class, ['id' => 'header_id'])->cache(self::cachetime(),self::settagdep('tag_template_checklist_evaluasi'));
    }
}