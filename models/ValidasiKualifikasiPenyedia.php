<?php
namespace app\models;
use Yii;
class ValidasiKualifikasiPenyedia extends \yii\db\ActiveRecord {
    use GeneralModelsTrait;
    public $detail;
    public static function tableName() {
        return 'validasi_kualifikasi_penyedia';
    }
    public function rules() {
        return [
            [['penyedia_id', 'keperluan', 'is_active'], 'required'],
            [['penyedia_id', 'is_active', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'string'],
            [['paket_pengadaan_id', 'keperluan'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'penyedia_id' => 'Penyedia ID',
            'paket_pengadaan_id' => 'Paket Pengadaan ID',
            'keperluan' => 'Keperluan',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
    public function beforeSave($insert) {
        if ($insert) {
            $this->created_by = Yii::$app->user->identity->id;
            $this->created_at = date('Y-m-d H:i:s', time());
        } else {
            $this->updated_by = Yii::$app->user->identity->id;
            $this->updated_at = date('Y-m-d H:i:s', time());
        }
        return parent::beforeSave($insert);
    }
    public function getDetails(){
        return $this->hasMany(ValidasiKualifikasiPenyediaDetail::class, ['header_id' => 'id'])->cache(self::cachetime(), self::settagdep('tag_validasi_kualifikasi_penyedia_detail'));
    }
    public function getPenyedia() {
        return $this->hasOne(Penyedia::class, ['id' => 'penyedia_id'])->cache(self::cachetime(), self::settagdep('tag_penyedia'));
    }
    public function getPaketpengadaan() {
        return $this->hasOne(PaketPengadaan::class, ['id' => 'paket_pengadaan_id'])->cache(self::cachetime(), self::settagdep('tag_paketpengadaan'));
    }
}
