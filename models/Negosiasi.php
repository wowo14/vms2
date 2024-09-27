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
            [['penawaran_id', 'accept','pp_accept','penyedia_accept', 'created_by'], 'integer'],
            [['ammount'], 'number'],
            [['created_at','detail'], 'safe'],
            [['ammount'], 'validateAmmount'],
        ];
    }
    public function validateAmmount($attribute, $params, $validator){
        $previousValue = self::last(['penawaran_id' => $this->penawaran_id])->ammount??PenawaranPengadaan::last(['id' => $this->penawaran_id])->nilai_penawaran;
        if ($previousValue !== null) {
            if ($this->$attribute >= $previousValue) {
                $this->addError($attribute, Yii::t('yii', "{$attribute} must be lower than previous value.".$previousValue));
                return false;
            }
        } else {
            Yii::error('No previous record found', __METHOD__);
        }
        return true;
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'penawaran_id' => 'Penawaran ID',
            'ammount' => 'Nilai Negosiasi',
            'accept' => 'Accept',
            'penyedia_accept'=>'Penyedia Accept',
            'pp_accept'=>'PP Accept',
            'detail'=>'Detail',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
        ];
    }
    public function getPenawaran(){
        return $this->hasOne(PenawaranPengadaan::class, ['id' => 'penawaran_id'])->cache(self::cachetime(), self::settagdep('tag_penawaranpengadaan'));
    }
    public function beforeSave($insert){

        return parent::beforeSave($insert);
    }
}
