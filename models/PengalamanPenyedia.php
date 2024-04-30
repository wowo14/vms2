<?php
namespace app\models;
use Yii;
class PengalamanPenyedia extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'pengalaman_penyedia';
    }
    public function rules()
    {
        return [
            [['penyedia_id', 'pekerjaan', 'lokasi', 'instansi_pemberi_tugas', 'alamat_instansi', 'tanggal_kontrak'], 'required'],
            [['penyedia_id', 'created_by', 'updated_by'], 'integer'],
            [['tanggal_kontrak', 'tanggal_selesai_kontrak', 'created_at', 'updated_at', 'file'], 'string'],
            [['nilai_kontrak'], 'number'],
            [['paket_pengadaan_id', 'link', 'pekerjaan', 'lokasi', 'instansi_pemberi_tugas', 'alamat_instansi'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'penyedia_id' => 'Penyedia ID',
            'paket_pengadaan_id' => 'Paket Pengadaan ID',
            'link' => 'Link',
            'pekerjaan' => 'Pekerjaan',
            'lokasi' => 'Lokasi',
            'instansi_pemberi_tugas' => 'Instansi Pemberi Tugas',
            'alamat_instansi' => 'Alamat Instansi',
            'tanggal_kontrak' => 'Tanggal Kontrak',
            'tanggal_selesai_kontrak' => 'Tanggal Selesai Kontrak',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'nilai_kontrak' => 'Nilai Kontrak',
            'file' => 'File',
        ];
    }
    public function autoDeleteFile() {
        $filePath = Yii::getAlias('@uploads') . $this->file;
        if (file_exists($filePath) && !empty($this->file)) {
            unlink($filePath);
        }
    }
    public function init() {
        parent::init();
        $this->on(self::EVENT_AFTER_DELETE, [$this, 'autoDeleteFile']);
    }
    public function beforeSave($insert) {
        if ($insert) {
            $this->file = !empty($this->file) ? $this->upload($this->file, 'file_' . $this->penyedia_id . '_' . time()) : '';
        } else {
            $this->file = self::isBase64Encoded($this->file) ? $this->upload($this->file, 'file_' . $this->penyedia_id . '_' . time()) : $this->file;
        }
        if ($insert) {
            $this->created_by = Yii::$app->user->identity->id;
            $this->created_at = date('Y-m-d H:i:s', time());
        } else {
            $this->updated_by = Yii::$app->user->identity->id;
            $this->updated_at = date('Y-m-d H:i:s', time());
        }
        return parent::beforeSave($insert);
    }
}