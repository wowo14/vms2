<?php
namespace app\models;
use Yii;
use yii\db\AfterSaveEvent;
class ValidasiKualifikasiPenyediaDetail extends \yii\db\ActiveRecord {
    use GeneralModelsTrait;
    public static function tableName() {
        return 'validasi_kualifikasi_penyedia_detail';
    }
    public function rules() {
        return [
            [['header_id'], 'required'],
            [['header_id', 'created_by', 'updated_by'], 'integer'],
            [['hasil_pembuktian', 'created_at', 'updated_at'], 'string'],
            [['uraian', 'hasil_evaluasi', 'hasil'], 'string'],
        ];
    }
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'header_id' => 'Header ID',
            'uraian' => 'Uraian',
            'hasil_evaluasi' => 'Hasil Evaluasi',
            'hasil_pembuktian' => 'Hasil Pembuktian',
            'hasil' => 'Hasil',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
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
    public static function hitungTotalSesuai($header_id) {
        $cek = self::find()->where(['header_id' => $header_id])->one();
        if ($cek !== null) {
            $count = collect(json_decode($cek->hasil, true))->filter(function ($e) {
                return isset($e['sesuai']) && $e['sesuai'] == 'ya';
            })->count();
            self::updateAll(['total_sesuai' => $count], ['header_id' => $header_id]);
            Yii::error(json_encode('cek ' . $count . ' header=' . $header_id));
        }
    }
}