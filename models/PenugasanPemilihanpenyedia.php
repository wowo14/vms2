<?php
namespace app\models;
use Yii;
class PenugasanPemilihanpenyedia extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'penugasan_pemilihanpenyedia';
    }
    public function rules()
    {
        return [
            [['dpp_id', 'pejabat', 'admin', 'created_by', 'updated_by'], 'integer'],
            [['tanggal_tugas', 'created_at', 'updated_at'], 'safe'],
            [['nomor_tugas'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dpp_id' => 'Dpp ID',
            'nomor_tugas' => 'Nomor Tugas',
            'tanggal_tugas' => 'Tanggal Tugas',
            'pejabat' => 'Pejabat',
            'admin' => 'Admin',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
    public function beforeSave($insert) {
        if ($insert) {
            if (empty($this->tanggal_tugas)) {
                $this->tanggal_tugas = date('Y-m-d H:i:s', time());
            }
        } else {
        }
        $dpp=$this->getDpp()->one();
        $dpp->pejabat_pengadaan=$this->pejabat;
        $dpp->admin_pengadaan=$this->admin;
        $dpp->save();
        return parent::beforeSave($insert);
    }
    public function beforeDelete() {
        if (!parent::beforeDelete()) {
            return false;
        }
        $dpp=$this->getDpp()->one();
        $dpp->pejabat_pengadaan=null;
        $dpp->admin_pengadaan=null;
        $dpp->save();
        return true;
    }
    public function getDpp(){
        return $this->hasOne(Dpp::class, ['id' => 'dpp_id'])->cache(self::cachetime(), self::settagdep('tag_dpp'));
    }
    public function getPejabatpengadaan(){
        return $this->hasOne(Pegawai::class, ['id' => 'pejabat'])->cache(self::cachetime(), self::settagdep('tag_pegawai'));
    }
    public function getStaffadmin(){
        return $this->hasOne(Pegawai::class, ['id' => 'admin'])->cache(self::cachetime(), self::settagdep('tag_pegawai'));
    }
}