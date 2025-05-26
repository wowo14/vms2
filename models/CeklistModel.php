<?php
namespace app\models;
use yii\base\Model;
class CeklistModel extends Model
{
    public $nomor_dpp;
    public $nomor_tugas;
    public $tanggal_tugas;
    public $unit;
    public $pejabat;
    public $admin;
    public $paket_id;
    public $template;
    public $nomor_persetujuan;
    public $tanggal_persetujuan;
    public $linksirup;
    public function rules()
    {
        return [
            [['nomor_dpp','nomor_persetujuan', 'nomor_tugas', 'tanggal_tugas','pejabat','admin','paket_id','unit'], 'safe'],
        ];
    }
     public function attributeLabels() {
        return [
            'pejabat' => 'Pejabat Pengadaan',
            'admin'=>'Admin Pengadaan',
            'linksirup'=>'Link Sirup'
        ];
    }
}
