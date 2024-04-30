<?php
namespace app\models;
use Yii;
class AktaPenyedia extends \yii\db\ActiveRecord {
    use GeneralModelsTrait;
    public static function tableName() {
        return 'dok_akta_penyedia';
    }
    public function rules() {
        return [
            [['penyedia_id', 'jenis_akta', 'nomor_akta', 'tanggal_akta'], 'required'],
            [['penyedia_id', 'created_by', 'updated_by'], 'integer'],
            [['tanggal_akta', 'file_akta', 'created_at', 'updated_at'], 'string'],
            [['jenis_akta', 'nomor_akta', 'notaris'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'penyedia_id' => 'Penyedia',
            'jenis_akta' => 'Jenis Akta',
            'nomor_akta' => 'Nomor Akta',
            'tanggal_akta' => 'Tanggal Akta',
            'notaris' => 'Notaris',
            'file_akta' => 'File Akta',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
    public function autoDeleteFile() {
        $filePath = Yii::getAlias('@uploads') . $this->file_akta;
        if (file_exists($filePath) && !empty($this->file_akta)) {
            unlink($filePath);
        }
    }
    public function init() {
        parent::init();
        $this->on(self::EVENT_AFTER_DELETE, [$this, 'autoDeleteFile']);
    }
    public function beforeSave($insert) {
        if ($insert) {
            $this->file_akta = !empty($this->file_akta) ? $this->upload($this->file_akta, 'file_akta_' . $this->penyedia_id . '_' . time()) : '';
        } else {
            $this->file_akta = self::isBase64Encoded($this->file_akta) ? $this->upload($this->file_akta, 'file_akta_' . $this->penyedia_id . '_' . time()) : $this->file_akta;
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
