<?php
namespace app\models;
use Yii;
class Negosiasi extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'negosiasi';
    }
    public function rules()
    {
        return [
            [['penawaran_id', 'ammount'], 'required'],
            [['penawaran_id', 'accept', 'created_by'], 'integer'],
            [['ammount'], 'number'],
            [['created_at'], 'safe'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'penawaran_id' => 'Penawaran ID',
            'ammount' => 'Ammount',
            'accept' => 'Accept',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
        ];
    }
    public function getPenawaran(){
        return $this->hasOne(PenawaranPengadaan::class, ['id' => 'penawaran_id'])->cache(self::cachetime(), self::settagdep('tag_penawaranpengadaan'));
    }
    public function beforeSave($insert){
        if ($insert) {
            $this->created_at = date('Y-m-d H:i:s');
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }
}
