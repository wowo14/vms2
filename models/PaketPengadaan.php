<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\db\Expression;

class PaketPengadaan extends \yii\db\ActiveRecord {
    use GeneralModelsTrait;
    // public $oldrecord;
    // public $statusPengadaan;
    public static function tableName() {
        return 'paket_pengadaan';
    }
    public function rules() {
        return [
            [['nomor', 'tanggal_paket', 'tanggal_dpp', 'tanggal_persetujuan', 'nomor_persetujuan', 'nama_paket', 'tahun_anggaran', 'kode_program', 'kode_kegiatan', 'kode_rekening', 'ppkom', 'unit', 'pagu', 'metode_pengadaan', 'kategori_pengadaan'], 'required'],
            [['tanggal_paket', 'created_at', 'updated_at', 'tanggal_reject', 'alasan_reject', 'addition'], 'string'],
            [['pagu'], 'number'],
            [['linksirup'], 'safe'],
            [['created_by', 'admin_ppkom', 'tahun_anggaran', 'approval_by', 'unit'], 'integer'],
            [['nomor', 'kategori_pengadaan', 'nama_paket', 'kode_program', 'kode_kegiatan', 'kode_rekening', 'ppkom', 'metode_pengadaan'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'nomor' => 'Nomor DPP',
            'nomor_persetujuan' => 'Nomor Persetujuan',
            'tanggal_dpp' => 'Tanggal DPP',
            'tanggal_persetujuan' => 'Tanggal Persetujuan',
            'tanggal_paket' => 'Tanggal Paket',
            'nama_paket' => 'Nama Paket',
            'kode_program' => 'Kode Program',
            'kode_kegiatan' => 'Kode Kegiatan',
            'kode_rekening' => 'Kode Rekening',
            'ppkom' => 'Ppkom',
            'linksirup' => 'Link Paket',
            'admin_ppkom' => 'Admin Ppkom',
            'pagu' => 'Pagu Paket',
            'metode_pengadaan' => 'Metode Pengadaan', //EPL,PL,E-Purchasing,
            'kategori_pengadaan' => 'Kategori Pengadaan', //barang/jasa, konstruksi, konsultansi
            'created_by' => 'Created By',
            'created_at' => 'Created at',
            'updated_at' => 'Updated at',
            'tahun_anggaran' => 'Tahun Anggaran',
            'approval_by' => 'Approval By', //null->belom,ditolak oleh ,<>0->diterima oleh
            'alasan_reject' => 'Alasan Reject', //not null ditolak
            'tanggal_reject' => 'Tanggal Reject', //not null ditolak
            'pemenang' => 'Pemenang', // id vendor pemenang
            'addition' => 'Addition', // kolom tambahan
            'unit' => 'Unit_Bidang_Bagian',
            'dibatalkan' => 'Dibatalkan Oleh',
            'alasan_dibatalkan' => 'Alasan Dibatalkan',
            'berita_acara_pembatalan'=>'Berita Acara Pembatalan',
            'tanggal_dibatalkan'=>'Tanggal Pembatalan',
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
    public function getUnitnya() {
        return $this->hasOne(Unit::class, ['id' => 'unit'])->cache(self::cachetime(), self::settagdep('tag_unit'));
    }
    public function getKurirnya() {
        return $this->hasOne(Pegawai::class, ['id_user' => 'created_by'])->cache(self::cachetime(), self::settagdep('tag_pegawai'));
    }
    public function getAttachments() {
        return $this->hasMany(Attachment::class, ['user_id' => 'id'])
            ->andWhere(['<>', 'jenis_dokumen', 0])
            ->cache(self::cachetime(), self::settagdep('tag_attachment'));
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
    public function getPenawaranpenyedia() {
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
    public function getHistorireject() {
        return $this->hasOne(HistoriReject::class, ['paket_id' => 'id'])->orderBy(['id' => SORT_DESC])->cache(self::cachetime(), self::settagdep('tag_historireject'));
    }
    public function getHistorirejects() {
        return $this->hasMany(HistoriReject::class, ['paket_id' => 'id'])->cache(self::cachetime(), self::settagdep('tag_historireject'));
    }
    public function getCtedetails() { //Query
        return (new Query())
            ->select([
                'paket_id',
                'hps'       => new Expression('SUM(qty * volume * hps_satuan)'),
                'penawaran' => new Expression('SUM(qty * volume * penawaran)'),
                'hasilnego' => new Expression('SUM(qty * volume * negosiasi)'),
            ])
            ->from(PaketPengadaanDetails::tableName())
            ->groupBy('paket_id');
    }
    public function getDashboard() {
        return self::where(['not', ['paket_pengadaan.id' => null]])
            ->joinWith(['dpp d', 'penawaranpenyedia.negosiasi n', 'pejabatppkom ppkom', 'dpp.pejabat p', 'dpp.staffadmin s', 'dpp.unit u'])
            ->leftJoin(['pd' => $this->ctedetails], 'pd.paket_id = paket_pengadaan.id')
            ->select([
                new Expression("strftime('%Y', paket_pengadaan.tanggal_paket) as year"),
                new Expression("strftime('%m', paket_pengadaan.tanggal_paket) as month"),
                'paket_pengadaan.nama_paket',
                'paket_pengadaan.metode_pengadaan',
                'paket_pengadaan.kategori_pengadaan',
                'paket_pengadaan.pagu',
                'p.nama as pejabat_pengadaan',
                's.nama as admin_pengadaan',
                'ppkom.nama as pejabat_ppkom',
                'u.unit as bidang_bagian',
                new Expression("COALESCE((pd.hps), 0) AS hps"),
                new Expression("COALESCE((pd.penawaran), 0) AS penawaran"),
                new Expression("COALESCE((pd.hasilnego), 0) AS hasilnego"),
                'paket_pengadaan.pemenang'
            ])
            ->andWhere(['not', ['d.bidang_bagian' => null]])
            // ->orWhere(['n.penyedia_accept' => 1,'n.pp_accept'=>1])
            ->groupBy(['year', 'month', 'paket_pengadaan.id'])
            ->orderBy('year', 'id')
            ->asArray()
            ->all();
    }
    public function getNotifpaketbaru() {
        return self::where(['not', ['paket_pengadaan.id' => null]])
            ->joinWith('dpp d')
            ->andWhere(['is', 'd.paket_id', null])
            ->orderBy('id', 'desc')
            ->asArray()->count();
    }
    public function getPaketreject() {
        return self::where(['IS NOT', 'tanggal_reject', null])
            ->andWhere(['!=', 'tanggal_reject', ''])
            ->andWhere(['IS NOT', 'alasan_reject', null])
            ->andWhere(['!=', 'alasan_reject', ''])
            ->count();
    }
    public function groupedData($field, $collection) { // part of dashboard
        return $collection->groupBy(function ($item) use ($field) {
            return $item['year'] . '#' . $item[$field];
        })
            ->map(function ($group, $key) use ($field) {
                list($year, $value) = explode('#', $key);
                return [
                    'year' => $year,
                    $field => $value,
                    'jml' => $group->count(),
                    'ammount' => $group->sum('pagu'),
                ];
            })
            ->values()
            ->toArray();
    }
    public function groupedDataBymonth($field, $collection) { // part of dashboard
        return $collection->groupBy(function ($item) use ($field) {
            //filter grab year and month
            // return $item['year'] . '#' . $item[$field];
        })
            ->map(function ($group, $key) use ($field) {
                list($year, $value) = explode('#', $key);
                return [
                    'year' => $year,
                    $field => $value,
                    'jml' => $group->count(),
                    'ammount' => $group->sum('pagu'),
                ];
            })
            ->values()
            ->toArray();
    }
    // public function afterFind() {
    //     $this->oldrecord = clone $this;
    //     parent::afterFind();
    // }
    public function beforeSave($insert) {
        if ($insert) {
            $this->tanggal_paket = date('Y-m-d H:i:s', time());
            // $exist = self::where(['nama_paket' => $this->nama_paket])->exists();
            // if ($exist) {
            //     Yii::$app->session->setFlash('error', 'Paket Pengadaan sudah ada');
            //     return false;
            // }
            $hasil = [];
            $template = TemplateChecklistEvaluasi::where(['like', 'template', 'Ceklist_Kelengkapan_DPP'])->one();
            if ($template) {
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
                $this->addition = json_encode($hasil);
            }
        } else { //update
        }
        self::invalidatecache('tag_' . self::getModelname());
        Dpp::invalidatecache('tag_' . Dpp::getModelname());
        return parent::beforeSave($insert);
    }
    public function getrawData() {
        $rawSettingkategori = collect(Setting::where(['type' => 'kategori_pengadaan'])->all())->pluck('id', 'value')->toArray();
        $rawSettingmetode = collect(Setting::where(['type' => 'metode_pengadaan'])->all())->pluck('id', 'value')->toArray();
        return collect(self::where(['not', ['pp.id' => null]])
            ->alias('pp')
            ->joinWith([
                'dpp d',
                'penawaranpenyedia.negosiasi n',
                'pejabatppkom ppkom',
                'dpp.pejabat p',
                'dpp.staffadmin s',
                'dpp.unit u',
            ])
            /* JOIN sub‑query hasil agregat */
            ->leftJoin(['det' => $this->ctedetails], 'det.paket_id = pp.id')
            ->select([
                new Expression("strftime('%Y', pp.tanggal_paket)  AS year"),
                new Expression("CAST(strftime('%m', pp.tanggal_paket) AS INTEGER) AS month"),
                'pp.id',
                'pp.nama_paket',
                'pp.metode_pengadaan',
                'pp.kategori_pengadaan',
                'pp.pagu',
                'p.id   AS pejabat_pengadaan_id',
                'p.nama AS pejabat_pengadaan',
                's.id   AS admin_pengadaan_id',
                's.nama AS admin_pengadaan',
                'ppkom.id   AS pejabat_ppkom_id',
                'ppkom.nama AS pejabat_ppkom',
                'u.id   AS bidang_bagian_id',
                'u.unit AS bidang_bagian',
                // ambil agregat dari sub‑query
                new Expression('COALESCE(det.hps,       0) AS hps'),
                new Expression('COALESCE(det.penawaran, 0) AS penawaran'),
                new Expression('COALESCE(det.hasilnego, 0) AS hasilnego'),
                'pp.pemenang',
            ])
            ->andWhere(['not', ['d.bidang_bagian' => null]])
            ->andWhere([
                'or',
                ['pp.tanggal_reject' => null],
                ['pp.tanggal_reject' => ''],
            ])
            ->andWhere([
                'or',
                ['pp.alasan_reject' => null],
                ['pp.alasan_reject' => ''],
            ])
            ->groupBy('pp.id')
            ->orderBy('pp.id')
            ->asArray()
            ->all())->map(function ($e) use ($rawSettingkategori, $rawSettingmetode) {
            // $e['metode_pengadaan']=$e['metode_pengadaan']=== 'E-Purchasing'? 'E-Katalog': $e['metode_pengadaan'];
            $e['metode_pengadaan_id'] = $rawSettingmetode[$e['metode_pengadaan']];
            $e['kategori_pengadaan_id'] = $rawSettingkategori[$e['kategori_pengadaan']];
            return $e;
        });
    }
    private function createPivotTable($data, $params, $groupKey, $bln) { //($data, $params['groupby'], $params['type'], $params['bln']);
        if (isset($bln) && !empty($bln)) {
            $data = $data->where('month', $bln);
            $months = [$bln];
        } else {
            $months = $data->pluck('month')->unique()->sort()->values();
        }
        // Yii::error('months: ' . $months);
        if ((int)$groupKey) {
            $data = $data->groupBy('kategori_pengadaan');
            $types = $data->get($groupKey);
        } else {
            $types = $data->pluck($groupKey)->unique()->sort()->values();
        }
        // Yii::error('types: ' . $types);
        if ((int)$params) {
            // Yii::error('integer' . $params);
            $group = $data->groupBy('pejabat_pengadaan_id');
            $groupedData = $group->get($params);
        } else {
            $groupedData = $data->groupBy($params);
        }
        // Yii::error('groupedData: ' . $groupedData);
        if ($groupedData) {
            return $groupedData;
        }
        die;
        $pivotTable = collect($groupedData)->map(function ($rows, $adminName) use ($months, $types, $groupKey) {
            $row = ['name' => $adminName, 'total' => 0];
            foreach ($months as $month) {
                $monthData = $rows->where('month', $month);
                foreach ($types as $type) {
                    $count = $monthData->where($groupKey, $type)->count();
                    $row[$month][$type] = $count;
                    $row['total'] += $count;
                }
            }
            return $row;
        });
        // Calculate total row
        $totalRow = ['name' => 'Total', 'total' => 0];
        foreach ($months as $month) {
            foreach ($types as $type) {
                $totalRow[$month][$type] = $pivotTable->sum(function ($row) use ($month, $type) {
                    return $row[$month][$type] ?? 0;
                });
                $totalRow['total'] += $totalRow[$month][$type];
            }
        }
        $pivotTable->put('Total', $totalRow);
        return ['months' => $months, 'types' => $types, 'pivotTable' => $pivotTable];
    }
    public function byKategori($params) { //[groupby,type,bln]
        $data = $this->getrawData();
        return $data;
        die;
        // Yii::error('kategori called # raw data: ' . $data);
        return $this->createPivotTable($data, $params['groupby'], $params['type'], $params['bln']);
    }
    public function metodebulan($params) { // collection sudah diterima dpp / complete
        $data = $this->getrawData()->values();
        $data = $data->filter(function ($item) {
            return !empty($item['pejabat_pengadaan_id']) || !empty($item['admin_pengadaan_id']);
        });
        if ($params['tahun']) {
            $data = $data->where('year', $params['tahun']);
        }
        if ($params['bln'] && $params['bln'] != 0) {
            $data = $data->where('month', $params['bln'])
                ->groupBy('month')
                ->get($params['bln']);
        }
        if ($params['metode'] && $params['metode'] !== 'all') {
            $data = $data->filter(function ($item) use ($params) {
                return $item['metode_pengadaan_id'] == $params['metode'];
            });
        }
        // $data = $data->groupBy('metode_pengadaan_id');
        if ($params['pejabat'] && $params['pejabat'] !== 'all') {
            $data = $data->groupBy('pejabat_pengadaan_id')
                ->get($params['pejabat']);
        }
        if ($data === null) {
            Yii::error('Data after filters is null.');
            return [];
        }
        // Yii::error(json_encode($data));
        return $data->map(function ($e) {
            if ($e['month'] !== 0) {
                $e['bulan'] = $this->getMonths()[$e['month']];
            }
            return $e;
        })
            ->values();
    }
    public function kategoribulan($params) { // collection sudah assign / diterima
        $data = $this->getrawData();
        $data = $data->filter(function ($item) {
            return !empty($item['pejabat_pengadaan_id']) || !empty($item['admin_pengadaan_id']);
        });
        if ($params['tahun']) {
            $data = $data->where('year', $params['tahun']);
        }
        if ($params['bln'] && $params['bln'] != 0) {
            $data = $data->where('month', $params['bln'])
                ->groupBy('month')->get($params['bln']);
        }
        if ($params['kategori'] && $params['kategori'] !== 'all') {
            $data = $data->groupBy('kategori_pengadaan_id')
                ->get($params['kategori']);
        }
        if ($params['pejabat'] && $params['pejabat'] !== 'all') {
            $data = $data->groupBy('pejabat_pengadaan_id')
                ->get($params['pejabat']);
        }
        return $data->map(function ($e) {
            if ($e['month'] !== 0) {
                $e['bulan'] = $this->getMonths()[$e['month']];
            }
            return $e;
        })
            ->sortBy('bulan')
            ->values();
    }
    public function getFilteredData($filters = null) { //collection all rekap
        $data = $this->getrawData();
        if ($filters && $filters->isNotEmpty()) {
            $data = $data->filter(function ($item) use ($filters) {
                foreach ($filters as $key => $value) {
                    if (!array_key_exists($key, $item)) {
                        return false;
                    }
                    // Paksa jadi string biar bandingannya reliable
                    if ((string) $item[$key] != (string) $value) {
                        return false;
                    }
                }
                return true;
            });
        }
        $data = $data->map(function ($e) {
            $e['bulan'] = $e['month'] . ' ' . $this->getMonths()[$e['month']];
            return $e;
        });
        return $data->values(); // reset index
    }
}
