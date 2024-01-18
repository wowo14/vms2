<?php
namespace app\models;
use Yii;
class PaketPengadaan extends \yii\db\ActiveRecord {
    use GeneralModelsTrait;
    public $oldrecord;
    public static function tableName() {
        return 'paket_pengadaan';
    }
    public function behaviors() {
        return ['fileBehavior' => [
            'class' => \nemmo\attachments\behaviors\FileBehavior::class
        ]];
    }
    public function rules() {
        return [
            [['nomor', 'tanggal_paket', 'nama_paket'], 'required'],
            [['tanggal_paket'], 'string'],
            [['pagu'], 'number'],
            [['nama_paket'], 'unique'],
            [['created_by', 'tahun_anggaran', 'approval_by'], 'integer'],
            [['nomor', 'nama_paket', 'kode_program', 'kode_kegiatan', 'kode_rekening', 'ppkom', 'metode_pengadaan'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'nomor' => 'Nomor',
            'tanggal_paket' => 'Tanggal Paket',
            'nama_paket' => 'Nama Paket',
            'kode_program' => 'Kode Program',
            'kode_kegiatan' => 'Kode Kegiatan',
            'kode_rekening' => 'Kode Rekening',
            'ppkom' => 'Ppkom',
            'pagu' => 'Pagu',
            'metode_pengadaan' => 'Metode Pengadaan',
            'created_by' => 'Created By',
            'tahun_anggaran' => 'Tahun Anggaran',
            'approval_by' => 'Approval By',
        ];
    }
    public function getListpaketoutstanding() {
        return PaketPengadaan::where(['approval_by' => null])->all();
    }
    public function getNomornamapaket() {
        return $this->nomor . '||' . $this->nama_paket;
    }
    public function getDetails() {
        return $this->hasMany(PaketPengadaanDetails::class, ['paket_id' => 'id'])->cache(self::cachetime(), self::settagdep('tag_paketpengadaandetails'));
    }
    public function afterFind() {
        $this->oldrecord = clone $this;
        parent::afterFind();
    }
    public function beforeSave($insert) {
        if ($insert) {
            $this->created_by = Yii::$app->user->identity->id;
            $this->tanggal_paket = date('Y-m-d H:i:s', time());
            $exist = self::where(['nama_paket' => $this->nama_paket])->exists();
            if ($exist) {
                Yii::$app->session->setFlash('error', 'Paket Pengadaan sudah ada');
                return false;
            }
        } else {
        }
        return parent::beforeSave($insert);
    }
    public function getAttachments() {
        return $this->hasMany(Attachment::class, ['user_id' => 'id'])->cache(self::cachetime(), self::settagdep('tag_attachment'));
    }
}
