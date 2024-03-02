<?php
namespace app\models;
use Yii;
class Ijinusaha extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'dok_ijinusaha';
    }
    public function rules()
    {
        return [
            [['penyedia_id', 'instansi_pemberi', 'nomor_ijinusaha', 'tanggal_ijinusaha', 'tanggal_berlaku_sampai', 'jenis_ijin'], 'required'],
            [['penyedia_id', 'created_by', 'updated_by', 'is_active'], 'integer'],
            [['tanggal_ijinusaha', 'file_ijinusaha', 'tanggal_berlaku_sampai', 'created_at', 'updated_at', 'tags'], 'string'],
            [['instansi_pemberi', 'nomor_ijinusaha', 'kualifikasi', 'klasifikasi', 'jenis_ijin'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'penyedia_id' => 'Penyedia',
            'instansi_pemberi' => 'Instansi Pemberi',
            'nomor_ijinusaha' => 'Nomor Ijinusaha',
            'tanggal_ijinusaha' => 'Tanggal Ijinusaha',
            'file_ijinusaha' => 'File Ijinusaha',
            'tanggal_berlaku_sampai' => 'Tanggal Berlaku Sampai',
            'kualifikasi' => 'Kualifikasi',
            'klasifikasi' => 'Klasifikasi',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'tags' => 'Tags',
            'is_active' => 'Is Active',
            'jenis_ijin' => 'Jenis Ijin',
        ];
    }
    public function autoDeleteFile() {
        $filePath = Yii::getAlias('@uploads') . $this->file_ijinusaha;
        if (file_exists($filePath) && !empty($this->file_ijinusaha)) {
            unlink($filePath);
        }
    }
    public function init() {
        parent::init();
        $this->on(self::EVENT_AFTER_DELETE, [$this, 'autoDeleteFile']);
    }
    public function beforeSave($insert) {
        if ($insert) {
            $this->file_ijinusaha = !empty($this->file_ijinusaha) ? $this->upload($this->file_ijinusaha, 'file_ijinusaha_' . $this->penyedia_id . '_' . $this->nomor_ijinusaha . '_' . time()) : '';
        } else {
            $this->file_ijinusaha = self::isBase64Encoded($this->file_ijinusaha) ? $this->upload($this->file_ijinusaha, 'file_ijinusaha_' . $this->penyedia_id . '_' . $this->nomor_ijinusaha . '_' . time()) : $this->file_ijinusaha;
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
    public function getJenis() {
        return [
            'NIB' => 'NIB (Nomor Induk Berusaha)',
            'SIUP' => 'SIUP (Surat Izin Usaha Perdagangan)',
            'TDP' => 'TDP (Tanda Daftar Perusahaan)',
            'SITU' => 'SITU (Surat Izin Tempat Usaha)',
            'SKBP' => 'SKBP (Surat Keterangan Badan Penanaman Modal dan Perizinan)',
            'SKD' => 'SKD (Surat Keterangan Domisili)',
            'SKDP' => 'SKDP (Surat Keterangan Domisili Perusahaan)',
            'SK' => 'SK (Surat Keterangan)',
            // 'IUJK' => 'IUJK (Izin Usaha Jasa Konstruksi)',
            // 'SPKK'=>'SPKK (Surat Pernyataan Kemampuan Keuangan)',
            // 'RefBank'=>'Referensi Bank',
            // 'SKuasa'=>'Surat Kuasa',
        ];
    }
}