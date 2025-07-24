<?php
namespace app\models;
use yii\base\DynamicModel;
// use yii\base\Model;
class ReportModel extends DynamicModel
{
    use GeneralModelsTrait;
    public $tahun;
    public $bulan;
    public $bulan_awal;
    public $bulan_akhir;
    public $metode;
    public $kategori;
    public $pejabat;
    public $admin;
    public $bidang;
    public $ppkom;
    public function rules()
    {
        return [
            [['tahun','bulan', 'bulan_awal', 'bulan_akhir', 'metode', 'kategori','pejabat','admin','bidang','ppkom'], 'safe'],
        ];
    }
     public function attributeLabels() {
        return [
            'pejabat' => 'Pejabat Pengadaan',
            'admin'=>'Admin Pengadaan',
            'ppkom'=>'Pejabat Pembuat Komitmen',
            'bulan_awal' => 'Periode Awal',
            'bulan_akhir' => 'Periode Akhir',
        ];
    }
}
