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
    public function rules()
    {
        return [
            [['nomor_dpp', 'nomor_tugas', 'tanggal_tugas','pejabat','admin','paket_id','unit'], 'safe'],
        ];
    }
}
