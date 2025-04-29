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
    public function rules()
    {
        return [
            [['tahun','bulan', 'metode', 'kategori','pejabat','admin','bidang'], 'safe'],
        ];
    }
     public function attributeLabels() {
        return [
            'pejabat' => 'Pejabat Pengadaan',
            'admin'=>'Admin Pengadaan',
        ];
    }
}
