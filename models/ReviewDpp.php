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
            [['tanggal_review', 'file_tanggapan', 'file_reject', 'created_at', 'updated_at', 'tgl_dikembalikan'], 'safe'],
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
            'file_reject' => 'File upload lampiran reject',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
    public function autoDeleteFile() {
        $filePath = Yii::getAlias('@uploads') . $this->file_tanggapan;
        if (file_exists($filePath) && !empty($this->file_tanggapan)) {
            unlink($filePath);
        }
        //file reject
        $filePathReject = Yii::getAlias('@uploads') . $this->file_reject;
        if (file_exists($filePathReject) && !empty($this->file_reject)) {
            unlink($filePathReject);
        }
    }
    public function init() {
        parent::init();
        $this->on(self::EVENT_AFTER_DELETE, [$this, 'autoDeleteFile']);
    }
    public function beforeSave($insert) {
        $this->file_tanggapan = !empty($this->file_tanggapan) && self::isBase64Encoded($this->file_tanggapan) ? $this->upload($this->file_tanggapan, 'file_tanggapan' . '_' . time()) : $this->file_tanggapan;
        $this->file_reject = !empty($this->file_reject) && self::isBase64Encoded($this->file_reject) ? $this->upload($this->file_reject, 'file_reject' . '_' . time()) : $this->file_reject;
        $this->tanggal_review = date('Y-m-d H:i:s');
        return parent::beforeSave($insert);
    }
}
