<?php
namespace app\models;
use Yii;
use app\models\GeneralModelsTrait;
class Setting extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName(){
        return 'setting';
    }
    public function rules(){
        return [
            [['type', 'active'], 'required'],
            [['active'], 'integer'],
            [['type', 'param', 'value'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels(){
        return [
            'id' => 'ID',
            'type' => 'Type',
            'param' => 'Param',
            'value' => 'Value',
            'active' => 'Active',
        ];
    }
    public static function type($type){
        return self::where(['type' => $type, 'active' => 1])->all();
    }
    public function getParamvalue(){
        return $this->param . ' | ' . $this->value;
    }
    public function getValueparam(){
        return $this->value . ' | ' . $this->param;
    }
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        self::invalidatecache('tag_' . self::getModelname());
    }
}
