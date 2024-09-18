<?php
namespace app\models;
use Yii;
class Attachment extends \yii\db\ActiveRecord {
    use GeneralModelsTrait;
    public static function tableName() {
        return 'attachment';
    }
    public function rules() {
        return [
            [['user_id', 'name', 'uri', 'mime', 'size', 'type'], 'required'],
            [['user_id', 'size', 'jenis_dokumen', 'updated_by', 'created_by'], 'integer'],
            [['created_at', 'updated_at'], 'string'],
            [['name', 'uri', 'mime', 'type'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'uri' => 'Uri',
            'mime' => 'Mime',
            'size' => 'Size',
            'type' => 'Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'jenis_dokumen' => 'Jenis Dokumen',
            'updated_by' => 'Updated By',
            'created_by' => 'Created By',
        ];
    }
    public function getJenisdokumen() {
        return $this->hasOne(Setting::class, ['id' => 'jenis_dokumen'])->cache(self::cachetime(), self::settagdep('tag_setting'));
    }
    public function beforeDelete() {
        if (!parent::beforeDelete()) {
            return false;
        }
        $existingFilePath = Yii::getAlias('@uploads') . $this->name;
        if (file_exists($existingFilePath)) {
            if (unlink($existingFilePath)) {
                Yii::info("File deleted successfully: $existingFilePath");
            } else {
                Yii::error("Error deleting file: $existingFilePath");
            }
        } else {
            Yii::info("File not found: $existingFilePath");
        }
        return true;
    }
    public function beforeSave($insert) {
       
        return parent::beforeSave($insert);
    }
}
