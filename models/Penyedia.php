<?php
namespace app\models;
use Yii;
class Penyedia extends \yii\db\ActiveRecord {
    use GeneralModelsTrait;
    public static function tableName() {
        return 'penyedia';
    }
    public function rules() {
        return [
            [['npwp', 'nama_perusahaan', 'alamat_perusahaan', 'nomor_telepon'], 'required'],
            [['tanggal_pendirian', 'created_at', 'updated_at'], 'string'],
            [['active', 'is_cabang', 'created_by', 'updated_by'], 'integer'],
            [['npwp', 'nama_perusahaan', 'alamat_perusahaan', 'nomor_telepon', 'email_perusahaan', 'kategori_usaha', 'akreditasi', 'propinsi', 'kota', 'kode_pos', 'mobile_phone', 'website', 'alamat_kantorpusat', 'telepon_kantorpusat', 'fax_kantorpusat', 'email_kantorpusat'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'npwp' => 'Npwp',
            'nama_perusahaan' => 'Nama Perusahaan',
            'alamat_perusahaan' => 'Alamat Perusahaan',
            'nomor_telepon' => 'Nomor Telepon',
            'email_perusahaan' => 'Email Perusahaan',
            'tanggal_pendirian' => 'Tanggal Pendirian',
            'kategori_usaha' => 'Kategori Usaha',
            'akreditasi' => 'Akreditasi',
            'active' => 'Active',
            'propinsi' => 'Propinsi',
            'kota' => 'Kota',
            'kode_pos' => 'Kode Pos',
            'mobile_phone' => 'Mobile Phone',
            'website' => 'Website',
            'is_cabang' => 'Is Cabang',
            'alamat_kantorpusat' => 'Alamat Kantorpusat',
            'telepon_kantorpusat' => 'Telepon Kantorpusat',
            'fax_kantorpusat' => 'Fax Kantorpusat',
            'email_kantorpusat' => 'Email Kantorpusat',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function beforeSave($insert) {
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
