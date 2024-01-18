<?php
namespace app\models;
use Yii;
class Contacts extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'contacts';
    }
    public function rules()
    {
        return [
            [['nama', 'nik', 'alamat', 'telepon', 'jabatan', 'is_vendor', 'is_active'], 'required'],
            [['unit', 'is_vendor', 'is_internal', 'is_active', 'created_by', 'updated_by', 'user_id', 'penyedia_id'], 'integer'],
            [['created_at', 'updated_at'], 'string'],
            [['nama', 'nik', 'alamat', 'email', 'telepon', 'nip', 'jabatan', 'instansi', 'password'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nama' => 'Nama',
            'nik' => 'Nik',
            'alamat' => 'Alamat',
            'email' => 'Email',
            'telepon' => 'Telepon',
            'nip' => 'Nip',
            'jabatan' => 'Jabatan',
            'instansi' => 'Instansi',
            'unit' => 'Unit',
            'is_vendor' => 'Is Vendor',
            'is_internal' => 'Is Internal',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'user_id' => 'User ID',
            'penyedia_id' => 'Penyedia ID',
            'password' => 'Password',
        ];
    }
}