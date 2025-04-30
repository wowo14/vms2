<?php
namespace app\models;
use Yii;
class PaketPengadaanDetails extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public $totalhps;
    public $totalpenawaran;
    public $totalnegosiasi;
    public static function tableName()
    {
        return 'paket_pengadaan_details';
    }
    public function rules()
    {
        return [
            [['paket_id'], 'integer'],
            [['qty','volume'], 'number'],
            [['nama_produk', 'volume', 'satuan'], 'required'],
            [['hps_satuan', 'informasi_harga'], 'number'],
            [['nama_produk', 'satuan', 'durasi', 'sumber_informasi'], 'string', 'max' => 255],
            [['penawaran','negosiasi'],'safe'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'paket_id' => 'Paket ID',
            'nama_produk' => 'Nama Produk',
            'volume' => 'Volume',
            'qty'=>'Qty',
            'satuan' => 'Satuan',
            'hps_satuan'=> 'HPS Satuan',
            'penawaran'=>'Penawaran',
            'negosiasi'=>'Negosiasi',
            'durasi' => 'Durasi',
            // 'harga' => 'Harga',
            'informasi_harga' => 'Informasi Harga',
            'sumber_informasi' => 'Sumber Informasi',
        ];
    }
    public function beforeSave($insert)
    {
        
        $this->nama_produk=preg_replace('/\s+/', ' ', trim($this->nama_produk));
        return parent::beforeSave($insert);
    }
    public function getPaketpengadaan(){
        return $this->hasOne(PaketPengadaan::className(), ['id' => 'paket_id']);
    }
    public static function sumNegosiasi($paket_id) {
        $model = PaketPengadaanDetails::collectAll(['paket_id' => $paket_id]);
        $model = $model->map(function ($e) {
            $d['totalnegosiasi'] = (float)($e->qty ?? 1)
                * (float)($e->volume ?? 1)
                * (float)(Yii::$app->tools->reverseCurrency($e->negosiasi));
            return $d;
        });
        return $model->sum('totalnegosiasi');
    }
    public function getTotalnego() {
        $model = PaketPengadaanDetails::collectAll(['paket_id' => $this->paket_id]);
        $model = $model->map(function ($e) {
            $d['totalnegosiasi'] = (float)($e->qty ?? 1)
                * (float)($e->volume ?? 1)
                * (float)(Yii::$app->tools->reverseCurrency($e->negosiasi));
            return $d;
        });
        return $model->sum('totalnegosiasi');
    }
    public function getTotalpnwrn() {
        $model = PaketPengadaanDetails::collectAll(['paket_id' => $this->paket_id]);
        $model = $model->map(function ($e) {
            $d['totalpenawaran'] = (float)($e->qty ?? 1)
                * (float)($e->volume ?? 1)
                * (float)(Yii::$app->tools->reverseCurrency($e->penawaran));
            return $d;
        });
        return $model->sum('totalpenawaran');
    }
    public function getTotalhpssatuan() {
        $model = PaketPengadaanDetails::collectAll(['paket_id' => $this->paket_id]);
        $model = $model->map(function ($e) {
            $d['totalhps'] = (float)($e->qty ?? 1)
                * (float)($e->volume ?? 1)
                * (float)(Yii::$app->tools->reverseCurrency($e->hps_satuan));
            return $d;
        });
        return $model->sum('totalhps');
    }
}