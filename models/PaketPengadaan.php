<?php
namespace app\models;
use Yii;
class PaketPengadaan extends \yii\db\ActiveRecord {
    use GeneralModelsTrait;
    public $oldrecord;
    public $statusPengadaan;
    public static function tableName() {
        return 'paket_pengadaan';
    }
    public function rules() {
        return [
            [['nomor', 'tanggal_paket','tanggal_dpp','tanggal_persetujuan','nomor_persetujuan', 'nama_paket','tahun_anggaran','kode_program', 'kode_kegiatan', 'kode_rekening', 'ppkom','unit','pagu','metode_pengadaan','kategori_pengadaan'], 'required'],
            [['tanggal_paket','tanggal_reject', 'alasan_reject','addition'], 'string'],
            [['pagu'], 'number'],
            [['nama_paket'], 'unique'],
            [['created_by', 'tahun_anggaran', 'approval_by','unit'], 'integer'],
            [['nomor', 'kategori_pengadaan', 'nama_paket', 'kode_program', 'kode_kegiatan', 'kode_rekening', 'ppkom', 'metode_pengadaan'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'nomor' => 'Nomor DPP',
            'nomor_persetujuan'=>'Nomor Persetujuan',
            'tanggal_dpp' => 'Tanggal DPP',
            'tanggal_persetujuan' => 'Tanggal Persetujuan',
            'tanggal_paket' => 'Tanggal Paket',
            'nama_paket' => 'Nama Paket',
            'kode_program' => 'Kode Program',
            'kode_kegiatan' => 'Kode Kegiatan',
            'kode_rekening' => 'Kode Rekening',
            'ppkom' => 'Ppkom',
            'pagu' => 'Pagu Paket',
            'metode_pengadaan' => 'Metode Pengadaan', //EPL,PL,E-Purchasing,
            'kategori_pengadaan' => 'Kategori Pengadaan', //barang/jasa, konstruksi, konsultansi
            'created_by' => 'Created By',
            'tahun_anggaran' => 'Tahun Anggaran',
            'approval_by' => 'Approval By', //null->belom,ditolak oleh ,<>0->diterima oleh
            'alasan_reject' => 'Alasan Reject', //not null ditolak
            'tanggal_reject' => 'Tanggal Reject', //not null ditolak
            'pemenang'=>'Pemenang', // id vendor pemenang
            'addition'=>'Addition', // kolom tambahan
            'unit' => 'Unit_Bidang_Bagian',
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
    public function getUnitnya(){
        return $this->hasOne(Unit::class, ['id' => 'unit'])->cache(self::cachetime(), self::settagdep('tag_unit'));
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
            $hasil = [];
            $template = TemplateChecklistEvaluasi::where(['like', 'template', 'Ceklist_Kelengkapan_DPP'])->one();
            if($template){
                if ($template->element) {
                    $ar_element = explode(',', $template->element);
                }
                foreach (json_decode($template->detail->uraian, true) as $v) {
                    $c = ['uraian' => $v['uraian']];
                    if ($template->element) {
                        foreach ($ar_element as $element) {
                            if ($element) {
                                $c[$element] = '';
                            }
                        }
                    }
                    $hasil['template'][] = $c;
                }
                $this->addition=json_encode($hasil);
            }
        } else {//update
        }
        self::invalidatecache('tag_' . self::getModelname());
        Dpp::invalidatecache('tag_' . Dpp::getModelname());
        return parent::beforeSave($insert);
    }
    public function getKurirnya(){
        return $this->hasOne(Pegawai::class,['id_user'=>'created_by'])->cache(self::cachetime(), self::settagdep('tag_pegawai'));
    }
    public function getAttachments() {
        return $this->hasMany(Attachment::class, ['user_id' => 'id'])->cache(self::cachetime(), self::settagdep('tag_attachment'));
    }
    public function getRequiredlampiran() { //array id
        return collect(self::settingType('jenis_dokumen'))->where('param', 'lampiran')->pluck('id')->toArray();
    }
    public function getDpp() {
        return $this->hasOne(Dpp::class, ['paket_id' => 'id'])->cache(self::cachetime(), self::settagdep('tag_dpp'));
    }
    public function getSubmitedpenawaran() {
        return $this->hasMany(PenawaranPengadaan::class, ['paket_id' => 'id'])->cache(self::cachetime(), self::settagdep('tag_penawaranpengadaan'));
    }
    public function getPenawaranpenyedia(){
        return $this->hasOne(PenawaranPengadaan::class, ['paket_id' => 'id'])->cache(self::cachetime(), self::settagdep('tag_penawaranpengadaan'));
    }
    public function getPejabatppkom() {
        return $this->hasOne(Pegawai::class, ['id' => 'ppkom'])->cache(self::cachetime(), self::settagdep('tag_pegawai'));
    }
    public function getProgramnya() {
        return $this->hasOne(ProgramKegiatan::class, ['code' => 'kode_program'])->cache(self::cachetime(), self::settagdep('tag_programkegiatan'));
    }
    public function getKegiatannya() {
        return $this->hasOne(ProgramKegiatan::class, ['code' => 'kode_kegiatan'])->cache(self::cachetime(), self::settagdep('tag_programkegiatan'));
    }
    public function getRekeningnya() {
        return $this->hasOne(KodeRekening::class, ['kode' => 'kode_rekening'])->cache(self::cachetime(), self::settagdep('tag_koderekening'));
    }
    public function getHistorireject(){
        return $this->hasOne(HistoriReject::class, ['paket_id' => 'id'])->orderBy(['id' => SORT_DESC])->cache(self::cachetime(), self::settagdep('tag_historireject'));
    }
    public function getHistorirejects(){
        return $this->hasMany(HistoriReject::class, ['paket_id' => 'id'])->cache(self::cachetime(), self::settagdep('tag_historireject'));
    }
}
