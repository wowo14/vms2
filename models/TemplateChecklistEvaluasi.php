<?php
namespace app\models;
use Yii;
class TemplateChecklistEvaluasi extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'template_checklist_evaluasi';
    }
    public function rules()
    {
        return [
            [['template', 'jenis_evaluasi'], 'required'],
            [['created_at', 'updated_at'], 'string'],
            [['created_by', 'updated_by'], 'integer'],
            [['template', 'element', 'jenis_evaluasi'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'template' => 'Template',
            'jenis_evaluasi' => 'Jenis Evaluasi',
            'element'=>'Element',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
    public function beforeSave($insert) {
       
        return parent::beforeSave($insert);
    }
    public function beforeDelete() {
        if (!parent::beforeDelete()) {
            return false;
        }
        TemplateChecklistEvaluasiDetail::deleteAll(['header_id' => $this->id]);
        return true;
    }
    public function getJenisevaluasi(){
        return $this->template.' - '.$this->jenis_evaluasi;
    }
    public function getDetail(){
        return $this->hasOne(TemplateChecklistEvaluasiDetail::class, ['header_id' => 'id']);
    }
}