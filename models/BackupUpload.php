<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
class BackupUpload extends Model
{
    public $backupFile;
    public function rules()
    {
        return [
            [['backupFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'sqlite', 'checkExtensionByMimeType' => false],
        ];
    }
    public function attributeLabels()
    {
        return [
            'backupFile' => 'Upload Backup File',
        ];
    }
}
