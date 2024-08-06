<?php
namespace app\models;
use Yii;
class StaffAhli extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'staff_ahli';
    }
    public function rules()
    {
        return [
            [['penyedia_id', 'nama', 'tanggal_lahir', 'alamat', 'pendidikan', 'lama_pengalaman', 'keahlian'], 'required'],
            [['penyedia_id', 'created_by', 'updated_by'], 'integer'],
            [['tanggal_lahir', 'file', 'created_at', 'updated_at'], 'string'],
            [['nama', 'alamat', 'email', 'jenis_kelamin', 'pendidikan', 'warga_negara', 'lama_pengalaman', 'keahlian', 'spesifikasi_pekerjaan'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'penyedia_id' => 'Penyedia ID',
            'nama' => 'Nama',
            'tanggal_lahir' => 'Tanggal Lahir',
            'alamat' => 'Alamat',
            'email' => 'Email',
            'jenis_kelamin' => 'Jenis Kelamin',
            'pendidikan' => 'Pendidikan',
            'warga_negara' => 'Warga Negara',
            'lama_pengalaman' => 'Lama Pengalaman',
            'file' => 'File',
            'keahlian' => 'Keahlian',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'spesifikasi_pekerjaan' => 'Spesifikasi Pekerjaan',
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
            $this->created_by = Yii::$app->user->identity->id;
            $this->created_at = date('Y-m-d H:i:s', time());
            $this->file=!empty($this->file) ? $this->upload($this->file, 'file_sertifikat_' . $this->penyedia_id . '_' . time()) : '';
        } else {
            $this->updated_by = Yii::$app->user->identity->id;
            $this->updated_at = date('Y-m-d H:i:s', time());
            $this->file=!empty($this->file) ? $this->upload($this->file, 'file_sertifikat_' . $this->penyedia_id . '_' . time()) : $this->file;
        }
        return parent::beforeSave($insert);
    }
}