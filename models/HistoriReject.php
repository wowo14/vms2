<?php
namespace app\models;
use Yii;
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
            [['alasan_reject', 'kesimpulan', 'tanggapan_ppk','file_tanggapan', 'file_reject'], 'string'],
            [['tanggal_reject', 'tanggal_dikembalikan'], 'safe'],
            [['nomor', 'nama_paket'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'paket_id' => 'Paket Pengadaan',
            'nomor' => 'Nomor',
            'nama_paket' => 'Nama Paket',
            'user_id' => 'User ID',
            'alasan_reject' => 'Alasan Reject',
            'tanggal_reject' => 'Tanggal Reject',
            'kesimpulan' => 'Kesimpulan',
            'tanggal_dikembalikan' => 'Tanggal Dikembalikan',
            'tanggapan_ppk' => 'Tanggapan Ppk',
            'file_tanggapan' => 'File Tanggapan',
            'file_reject' => 'File Lampiran Reject',
            'created_at' => 'Created At',
        ];
    }
    public function autoDeleteFile() {
        $filePath = Yii::getAlias('@uploads') . $this->file_tanggapan;
        if (file_exists($filePath) && !empty($this->file_tanggapan)) {
            unlink($filePath);
        }
        $filePathReject = Yii::getAlias('@uploads') . $this->file_reject;
        if (file_exists($filePathReject) && !empty($this->file_reject)) {
            unlink($filePathReject);
        }
    }
    public function init() {
        parent::init();
        $this->on(self::EVENT_AFTER_DELETE, [$this, 'autoDeleteFile']);
    }
    public function getPaketpengadaan(){
        return $this->hasOne(PaketPengadaan::class, ['id' => 'paket_id'])->cache(self::cachetime(), self::settagdep('tag_paketpengadaan'));
    }
    public function beforeSave($insert) {
        if ($insert) {
            $this->created_at = date('Y-m-d H:i:s');
            $this->user_id= \Yii::$app->user->identity->id;
        }
        $this->file_tanggapan = !empty($this->file_tanggapan) && self::isBase64Encoded($this->file_tanggapan) ? $this->upload($this->file_tanggapan, 'file_tanggapan' . '_' . time()) : $this->file_tanggapan;
        $this->file_reject = !empty($this->file_reject) && self::isBase64Encoded($this->file_reject) ? $this->upload($this->file_reject, 'file_reject' . '_' . time()) : $this->file_reject;
        
        return parent::beforeSave($insert);
    }
}