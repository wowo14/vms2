<?php
namespace app\models;
class HistoriReject extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'histori_reject';
    }
    public function rules()
    {
        return [
            [['paket_id'], 'required'],
            [['paket_id', 'user_id', 'created_at'], 'integer'],
            [['alasan_reject', 'kesimpulan', 'tanggapan_ppk','file_tanggapan'], 'string'],
            [['tanggal_reject', 'tanggal_dikembalikan'], 'safe'],
            [['nomor', 'nama_paket'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'paket_id' => 'Paket ID',
            'nomor' => 'Nomor',
            'nama_paket' => 'Nama Paket',
            'user_id' => 'User ID',
            'alasan_reject' => 'Alasan Reject',
            'tanggal_reject' => 'Tanggal Reject',
            'kesimpulan' => 'Kesimpulan',
            'tanggal_dikembalikan' => 'Tanggal Dikembalikan',
            'tanggapan_ppk' => 'Tanggapan Ppk',
            'file_tanggapan' => 'File Tanggapan',
            'created_at' => 'Created At',
        ];
    }
    public function getPaketpengadaan(){
        return $this->hasOne(PaketPengadaan::class, ['id' => 'paket_id'])->cache(self::cachetime(), self::settagdep('tag_paketpengadaan'));
    }
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->created_at = date('Y-m-d H:i:s');
                $this->user_id= \Yii::$app->user->identity->id;
            }
            return true;
        }
        return false;
    }
}