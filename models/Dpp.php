<?php
namespace app\models;
use Yii;
class Dpp extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'dpp';
    }
    public function rules()
    {
        return [
            [['tanggal_dpp', 'created_at', 'updated_at'], 'safe'],
            ['paket_id','unique'],
            [['paket_id', 'created_by', 'updated_by'], 'integer'],
            [['status_review', 'is_approved'], 'integer','max'=>1],
            [['nomor_dpp', 'bidang_bagian', 'nomor_persetujuan'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nomor_dpp' => 'Nomor Dpp',
            'tanggal_dpp' => 'Tanggal Dpp',
            'bidang_bagian' => 'Bidang Bagian',
            'paket_id' => 'Paket Pengadaan',
            'status_review' => 'Status Review',
            'is_approved' => 'Is Approved',
            'nomor_persetujuan' => 'Nomor Persetujuan',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = Yii::$app->user->identity->id;
            $this->created_at = date('Y-m-d H:i:s', time());
            if(empty($this->tanggal_dpp)){
                $this->tanggal_dpp = date('Y-m-d H:i:s', time());
            }
        } else {
            $this->updated_by = Yii::$app->user->identity->id;
            $this->updated_at = date('Y-m-d H:i:s', time());
        }
        return parent::beforeSave($insert);
    }
    
    public function getPaketpengadaan(){
        return $this->hasOne(PaketPengadaan::class, ['id' => 'paket_id'])->cache(self::cachetime(), self::settagdep('tag_paketpengadaan'));
    }
    public function getUnit(){
        return $this->hasOne(Unit::class, ['id' => 'bidang_bagian'])->cache(self::cachetime(), self::settagdep('tag_unit'));
    }
    public function getReviews(){
        return $this->hasOne(ReviewDpp::class, ['dpp_id' => 'id'])->cache(self::cachetime(), self::settagdep('tag_reviewdpp'));
    }
}