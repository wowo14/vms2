<?php
namespace app\models;
use Yii;
class ReviewDpp extends \yii\db\ActiveRecord {
    use GeneralModelsTrait;
    public static function tableName() {
        return 'review_dpp';
    }
    public function rules() {
        return [
            [['dpp_id', 'pejabat', 'kesesuaian', 'created_by', 'updated_by'], 'integer'],
            [['tanggal_review', 'file_tanggapan', 'created_at', 'updated_at', 'tgl_dikembalikan'], 'safe'],
            [['uraian', 'keterangan', 'kesimpulan', 'tanggapan_ppk'], 'string'],
        ];
    }
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'dpp_id' => 'Dpp ID',
            'tanggal_review' => 'Tanggal Review',
            'pejabat' => 'Pejabat',
            'uraian' => 'Uraian', //json_array uraian,kesuesuaian,ket
            'kesesuaian' => 'Kesesuaian', //not use
            'keterangan' => 'Keterangan', //review tulisan
            'kesimpulan' => 'Kesimpulan',
            'tanggapan_ppk' => 'Tanggapan PPK',
            'tgl_dikembalikan' => 'Tanggal Dikembalikan',
            'file_tanggapan' => 'File upload lampiran tanggapan',
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
        if ($insert) {
            $this->file_tanggapan = !empty($this->file_tanggapan) && self::isBase64Encoded($this->file_tanggapan) ? $this->upload($this->file_tanggapan, 'file_tanggapan' . '_' . time()) : '';
        } else {
            $this->file_tanggapan = !empty($this->file_tanggapan) && self::isBase64Encoded($this->file_tanggapan) ? $this->upload($this->file_tanggapan, 'file_tanggapan' . '_' . time()) : $this->file_tanggapan;
        }
        $this->tanggal_review = date('Y-m-d H:i:s');
        return parent::beforeSave($insert);
    }
}
