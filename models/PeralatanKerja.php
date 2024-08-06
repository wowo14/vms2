<?php
namespace app\models;
use Yii;
class PeralatanKerja extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'peralatan_kerja';
    }
    public function rules()
    {
        return [
            [['penyedia_id', 'nama_alat', 'jumlah', 'merk_tipe', 'tahun_pembuatan', 'kondisi', 'lokasi_sekarang', 'status_kepemilikan', 'bukti_kepemilikan'], 'required'],
            [['penyedia_id', 'jumlah', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at', 'file'], 'string'],
            [['nama_alat', 'kapasitas', 'merk_tipe', 'kondisi', 'lokasi_sekarang', 'status_kepemilikan', 'bukti_kepemilikan'], 'string', 'max' => 255],
            [['tahun_pembuatan'], 'string', 'max' => 4],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'penyedia_id' => 'Penyedia ID',
            'nama_alat' => 'Nama Alat',
            'jumlah' => 'Jumlah',
            'kapasitas' => 'Kapasitas',
            'merk_tipe' => 'Merk Tipe',
            'tahun_pembuatan' => 'Tahun Pembuatan',
            'kondisi' => 'Kondisi',
            'lokasi_sekarang' => 'Lokasi Sekarang',
            'status_kepemilikan' => 'Status Kepemilikan',
            'bukti_kepemilikan' => 'Bukti Kepemilikan',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'file' => 'File',
        ];
    }
    public function beforeSave($insert) {
        if ($insert) {
            $this->created_by = Yii::$app->user->identity->id;
            $this->created_at = date('Y-m-d H:i:s', time());
            $this->file=!empty($this->file) ? $this->upload($this->file, 'file_alat_' . $this->penyedia_id . '_' . time()) : '';
        } else {
            $this->updated_by = Yii::$app->user->identity->id;
            $this->updated_at = date('Y-m-d H:i:s', time());
            $this->file=!empty($this->file) ? $this->upload($this->file, 'file_alat_' . $this->penyedia_id . '_' . time()) : $this->file;
        }
        return parent::beforeSave($insert);
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
}