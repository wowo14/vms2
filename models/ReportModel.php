<?php
namespace app\models;
use yii\base\DynamicModel;
// use yii\base\Model;
class ReportModel extends DynamicModel
{
    use GeneralModelsTrait;
    public $tahun;
    public $bulan;
    public $metode;
    public $kategori;
    public $pejabat;
    public $admin;
    public $bidang;
    public $ppkom;
    public function rules()
    {
        return [
            [['tahun','bulan', 'metode', 'kategori','pejabat','admin','bidang','ppkom'], 'safe'],
        ];
    }
     public function attributeLabels() {
        return [
            'pejabat' => 'Pejabat Pengadaan',
            'admin'=>'Admin Pengadaan',
            'ppkom'=>'Pejabat Pembuat Komitmen'
        ];
    }
}
