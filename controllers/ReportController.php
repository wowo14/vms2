<?php
namespace app\controllers;
use kartik\mpdf\Pdf;
use yii\base\DynamicModel;
use app\models\ReportModel;
use app\models\PaketPengadaan;
class ReportController extends Controller {
    public function actionMetode() {
        $model = new ReportModel();
        $paketpengadaan = new PaketPengadaan();
        $request = \Yii::$app->request;
        if ($request->isGet) {
            return $this->render('_frm_by_metode', [
                'model' => $model,
                'raw' => $paketpengadaan->getrawData(),
                'paketpengadaan' => $paketpengadaan
            ]);
        } else if ($model->load($request->post())) {
            $params = [
                'tahun' => $model->tahun ?? '',
                'bln' => $model->bulan,
                'metode' => $model->metode,
                'pejabat' => $model->pejabat
            ];
            $result = $paketpengadaan->metodebulan($params);
            // return $this->actionMetodecount($result);
            return $this->render('_pivot_metode', [
                'model' => $result
            ]);
        }
    }
    public function actionKategori() {
        $model = new ReportModel();
        $paketpengadaan = new PaketPengadaan();
        $request = \Yii::$app->request;
        if ($request->isGet) {
            return $this->render('_frm_by_kategori', [
                'model' => $model,
                'raw' => $paketpengadaan->getrawData(),
                'paketpengadaan' => $paketpengadaan
            ]);
        } else if ($model->load($request->post())) {
            $params = [
                'tahun' => $model->tahun ?? '',
                'bln' => $model->bulan,
                'kategori' => $model->kategori,
                'pejabat' => $model->pejabat
            ];
            $result = $paketpengadaan->kategoribulan($params);
            return $this->render('_pivot_kategori', [
                'model' => $result
            ]);
        }
    }
    public function actionAdminpengadaan() {
        $model = new ReportModel();
        $paketpengadaan = new PaketPengadaan();
        $request = \Yii::$app->request;
        if ($request->isGet) {
            return $this->render('_frm_admin_by_kategori', [
                'model' => $model,
                'raw' => $paketpengadaan->getrawData(),
                'paketpengadaan' => $paketpengadaan
            ]);
        } else if ($model->load($request->post())) {
            $params = [
                'tahun' => $model->tahun ?? '',
                'bln' => $model->bulan,
                'metode' => $model->metode,
                'admin' => $model->admin
            ];
            $result = $paketpengadaan->kategoribulan($params);
            return $this->render('_pivot_admin', [
                'model' => $result
            ]);
        }
    }
    public function actionBidangmetode() {
        $model = new ReportModel();
        $paketpengadaan = new PaketPengadaan();
        $request = \Yii::$app->request;
        if ($request->isGet) {
            return $this->render('_frm_metode_by_bidang', [
                'model' => $model,
                'raw' => $paketpengadaan->getrawData(),
                'paketpengadaan' => $paketpengadaan
            ]);
        } else if ($model->load($request->post())) {
            $params = [
                'tahun' => $model->tahun ?? '',
                'bln' => $model->bulan,
                'metode' => $model->metode,
                'bidang' => $model->bidang
            ];
            $result = $paketpengadaan->kategoribulan($params);
            return $this->render('_pivot_bidang_metode', [
                'model' => $result
            ]);
        }
    }
    public function actionIndex() { //rekap
        $model = new ReportModel();
        $paketpengadaan = new PaketPengadaan();
        $request = \Yii::$app->request;
        if ($request->isGet) {
            return $this->render('index', [
                'action' => \yii\helpers\Url::to(['report/index']),
                'model' => $model,
                'raw' => $paketpengadaan->getExistingYears(),
                'paketpengadaan' => $paketpengadaan
            ]);
        } else if ($model->load($request->post())) {
            $filters = collect([
                'year'   => $model->tahun,
                'month'   => $model->bulan == 0 ? null : $model->bulan,
                'kategori_pengadaan_id' => $model->kategori !== 'all' ? $model->kategori : null,
                'metode_pengadaan_id'   => $model->metode !== 'all' ? $model->metode : null,
                'pejabat_pengadaan_id'  => $model->pejabat !== 'all' ? $model->pejabat : null,
                'admin_pengadaan_id'    => $model->admin !== 'all' ? $model->admin : null,
                'bidang_bagian_id'      => $model->bidang !== 'all' ? $model->bidang : null,
            ])->filter();
            $raw = $paketpengadaan->getFilteredData($filters);
            $rows = collect($raw)->map(function ($item, $i) {
                return [
                    'no'                => $i + 1,
                    'paket_id'        => $item['id'],
                    'nama_paket'        => $item['nama_paket'],
                    'metode'            => $item['metode_pengadaan'],
                    'kategori'          => $item['kategori_pengadaan'],
                    'pagu'              => $item['pagu'],
                    'hps'               => $item['hps'],
                    'penawaran'         => $item['penawaran'],
                    'hasilnego'         => $item['hasilnego'],
                    'admin'             => $item['admin_pengadaan'],
                    'pejabat'           => $item['pejabat_pengadaan'],
                    'ppkom'             => $item['pejabat_ppkom'],
                    'bidang'            => $item['bidang_bagian'],
                    'tahun'             => $item['year'],
                    'bulan'             => $item['month'],
                ];
            })->all();
            $title = 'Rekap Pengadaan ' . $model->tahun;
            $dataProvider = new \yii\data\ArrayDataProvider([
                'allModels'  => $rows,
                'pagination' => false,
                'sort'       => [
                    'attributes' => [
                        'tahun',
                        'bulan',
                        'paket_id',
                        'nama_paket',
                        'metode',
                        'kategori',
                        'pagu',
                        'hps',
                        'penawaran',
                        'hasilnego',
                        'admin',
                        'pejabat',
                        'ppkom',
                        'bidang'
                    ],
                    'defaultOrder' => ['tahun' => SORT_DESC, 'bulan' => SORT_DESC, 'paket_id' => SORT_DESC],
                ],
            ]);
            return $this->render('_rekap-paket', [
                'dataProvider' => $dataProvider,
                'title' => $title
            ]);
            // return $this->render('_report_dppmasuk', [
            //     'model' => $model,
            //     'rawData' => $raw,
            //     'paketpengadaan' => $paketpengadaan
            // ]);
        }
    }
    public function actionDppreject(){
        $model = new ReportModel();
        $paketpengadaan = new PaketPengadaan();
        $request = \Yii::$app->request;
        if ($request->isGet) {
            return $this->render('index', [
                'action' => \yii\helpers\Url::to(['//pivot-report/report-reject']),
                'model' => $model,
                'raw' => $paketpengadaan->getExistingYears(),
                'paketpengadaan' => $paketpengadaan
            ]);
        }
        // if($request->isPost){
        //     $params = [
        //         'tahun' => $model->tahun ?? '',
        //         'bln' => $model->bulan,
        //     ];
        //     $result = $paketpengadaan->getPaketRejects($params);
        //     return $this->render('_pivot_reject', [
        //         'data' => $result,
        //     ]);
        // }
    }
    public function actionMetodecount() {
        $model = new ReportModel();
        $paketpengadaan = new PaketPengadaan();
        $request = \Yii::$app->request;
        if ($request->isGet) {
            return $this->render('_frm_by_metode', [
                'model' => $model,
                'raw' => $paketpengadaan->getrawData(),
            ]);
        } else if ($model->load($request->post())) {
            $params = [
                'tahun' => $model->tahun ?? '',
                'bln' => $model->bulan,
                'metode' => $model->metode,
                'pejabat' => $model->pejabat
            ];
            $result = $paketpengadaan->metodebulan($params);
            if ($request->post('type') === 'grid') {
                return $this->render('_pivot_metodecount', [
                    'data' => $result,
                ]);
            }
            if ($request->post('type') === 'pdf') {
                $pdf = new Pdf([
                    'mode' => Pdf::MODE_UTF8,
                    'format' => Pdf::FORMAT_A4,
                    'orientation' => Pdf::ORIENT_PORTRAIT,
                    'destination' => Pdf::DEST_BROWSER,
                    'content' => $this->renderPartial('_pivot_metodecount', [
                        'data' => $result,
                    ]),
                    'options' => [
                        'title' => 'Metode Pengadaan',
                        'subject' => 'Metode Pengadaan',
                    ],
                    'methods' => [
                        'SetHeader' => ['Metode Pengadaan'],
                        'SetFooter' => ['{PAGENO}'],
                    ]
                ]);
                return $pdf->render();
            }
        }
    }
    public function actionDppmasuk() {
        $model = new ReportModel();
        $paketpengadaan = new PaketPengadaan();
        $request = \Yii::$app->request;
        if ($request->isGet) {
            return $this->render('index', [
                'action' => \yii\helpers\Url::to(['report/dppmasuk']),
                'model' => $model,
                'raw' => $paketpengadaan->getExistingYears(),
                'paketpengadaan' => $paketpengadaan
            ]);
        } else if ($model->load($request->post())) {
            $filters = collect([
                'year'   => $model->tahun,
                'month'   => $model->bulan == 0 ? null : $model->bulan,
                'kategori_pengadaan_id' => $model->kategori !== 'all' ? $model->kategori : null,
                'metode_pengadaan_id'   => $model->metode !== 'all' ? $model->metode : null,
                'pejabat_pengadaan_id'  => $model->pejabat !== 'all' ? $model->pejabat : null,
                'admin_pengadaan_id'    => $model->admin !== 'all' ? $model->admin : null,
                'bidang_bagian_id'      => $model->bidang !== 'all' ? $model->bidang : null,
            ])->filter();
            $raw = $paketpengadaan->getFilteredData($filters);
            if ($request->post('type') === 'pdf') {
                $pdf = new Pdf([
                    'mode' => Pdf::MODE_UTF8,
                    'format' => Pdf::FORMAT_A4,
                    'orientation' => Pdf::ORIENT_PORTRAIT,
                    'destination' => Pdf::DEST_BROWSER,
                    'content' => $this->renderPartial('_pdf_dppmasuk', [
                        'model' => $raw,
                        'months' => $model->getMonths()
                    ]),
                    'options' => [
                        'title' => 'DPP Masuk',
                        'subject' => 'DPP Masuk',
                    ],
                    'methods' => [
                        'SetHeader' => ['DPP Masuk'],
                        'SetFooter' => ['{PAGENO}'],
                    ]
                ]);
                return $pdf->render();
            }
            if ($request->post('type') === 'grid') {
                return $this->render('_pivot_dppmasuk', [
                    'model' => $raw,
                    'months' => $model->getMonths()
                ]);
            }
        }
    }
    public function createPenetapanModel($params = []) {
        $model = DynamicModel::validateData($params, [
            [['nama_paket', 'metode_pengadaan', 'pemenang_nama', 'pemenang_alamat', 'pemenang_npwp'], 'string'],
            [['harga_penawaran', 'harga_negosiasi'], 'number'],
            [['tanggal_surat'], 'date', 'format' => 'php:Y-m-d'],
            [['nomor_surat', 'pejabat_nama', 'pejabat_nip'], 'string'],
        ]);
        return $model;
    }
    public function actionPreviewSurat($id) {
        $paket = PaketPengadaan::findOne($id);
        $model = $this->createPenetapanModel([
            'nama_paket'       => $paket->nama_paket,
            'metode_pengadaan' => $paket->metode_pengadaan,
            'pemenang_nama'    => $paket->penawaranpenyedia->vendor->nama_perusahaan ?? '-',
            'pemenang_alamat'  => $paket->penawaranpenyedia->vendor->alamat_perusahaan ?? '-',
            'pemenang_npwp'    => $paket->penawaranpenyedia->vendor->npwp ?? '-',
            'harga_penawaran'  => $paket->details->totalpnwrn ?? 0,
            'harga_negosiasi'  => $paket->details->totalnego ?? 0,
            'nomor_surat'      => '027/XXX/GRESIK/' . date('Y'),
            'tanggal_surat'    => date('Y-m-d'),
            'pejabat_nama'     => $paket->dpp->pejabat->nama ?? '-',
            'pejabat_nip'      => $paket->dpp->pejabat->nip ?? '-',
        ]);
        return $this->render('template_penetapan', ['model' => $model]);
    }
    public function actionPenilaianPenyedia() {
        $model = new ReportModel();
        $paketpengadaan = new PaketPengadaan(); // For years retrieval
        $request = \Yii::$app->request;

        if ($request->isGet) {
            return $this->render('index', [
                'action' => \yii\helpers\Url::to(['report/penilaian-penyedia']),
                'model' => $model,
                'raw' => $paketpengadaan->getExistingYears(), // Reuse this for year dropdown
                'paketpengadaan' => $paketpengadaan
            ]);
        } else if ($model->load($request->post())) {
            // Build Query
            $query = \app\models\PenilaianPenyedia::find()
                ->alias('p')
                ->joinWith(['dpp d' => function($q) {
                    $q->joinWith(['paketpengadaan pp']);
                }]);

            // Apply Filters
            if ($model->tahun) {
                $query->andWhere(['pp.tahun_anggaran' => $model->tahun]);
            }
            if ($model->bulan && $model->bulan != 0) {
                 $query->andWhere(new \yii\db\Expression("strftime('%m', p.tanggal_kontrak) = :month", [':month' => sprintf('%02d', $model->bulan)]));
            }
            if ($model->kategori && $model->kategori !== 'all') {
                $setting = \app\models\Setting::findOne($model->kategori);
                if ($setting) {
                    $query->andWhere(['pp.kategori_pengadaan' => $setting->value]);
                }
            }
            if ($model->metode && $model->metode !== 'all') {
                $setting = \app\models\Setting::findOne($model->metode);
                if ($setting) {
                    $query->andWhere(['p.metode_pemilihan' => $setting->value]);
                }
            }
            if ($model->pejabat && $model->pejabat !== 'all') {
                $query->andWhere(['d.pejabat_pengadaan' => $model->pejabat]);
            }
            if ($model->pejabat && $model->pejabat !== 'all' && $model::isAdmin()) {
                $query->andWhere(['d.pejabat_pengadaan' => $model->pejabat]);
            }
            if ($model->ppkom && $model->ppkom !== 'all' && $model::isAdmin()) {
                $query->andWhere(['pp.ppkom' => $model->ppkom]);
            }
            if($model::isPP()){
                $query->andWhere(['d.pejabat_pengadaan' => \Yii::$app->user->identity->userpegawai->id]);
            }
            if($model::isPPK()){
                $query->andWhere(['pp.ppkom' => \Yii::$app->user->identity->userpegawai->id]);
            }
            if ($model->admin && $model->admin !== 'all') {
                $query->andWhere(['d.admin_pengadaan' => $model->admin]);
            }
            if ($model->bidang && $model->bidang !== 'all') {
                $query->andWhere(['d.bidang_bagian' => $model->bidang]);
            }

            $data = $query->all();
            
            // Format data for the view
            $rows = [];
            foreach ($data as $i => $item) {
                // Parse details
                $details = [];
                if ($item->details) {
                    $details = json_decode($item->details, true);
                }
                
                $scores = $details['skor'] ?? [];
                
                // Filter: Only include if it has 5 scores (PP Assessment)
                if (count($scores) !== 5) {
                    continue;
                }

                $total = $details['total'] ?? 0;
                $rata = $details['nilaiakhir'] ?? 0;
                $eval = $details['hasil_evaluasi'] ?? '-';
                $ket = $details['ulasan_pejabat_pengadaan'] ?? '';

                $rows[] = [
                    'id' => $item->id,
                    'no' => count($rows) + 1,
                    'nama_penyedia' => $item->nama_perusahaan,
                    'alamat' => $item->alamat_perusahaan,
                    'kategori' => $item->dpp->paketpengadaan->kategori_pengadaan ?? '-',
                    'nama_kegiatan' => $item->paket_pekerjaan,
                    'bidang' => $item->unit_kerja,
                    'metode' => $item->metode_pemilihan,
                    'tanggal_kontrak' => $item->tanggal_kontrak,
                    'nilai_kontrak' => $item->nilai_kontrak,
                    'pejabat_pengadaan' => $item->dpp->pejabat->nama ?? '-',
                    'scores' => $scores,
                    'total' => $total,
                    'rata' => $rata,
                    'hasil_evaluasi' => $eval,
                    'keterangan' => $ket
                ];
            }
            
            $dataProvider = new \yii\data\ArrayDataProvider([
                'allModels' => $rows,
                'pagination' => false,
                'sort' => [
                    'attributes' => ['nama_penyedia', 'tanggal_kontrak', 'nilai_kontrak', 'pejabat_pengadaan'],
                ],
            ]);

            $title = 'Rekapitulasi Penilaian Penyedia Barang Oleh Pejabat Pengadaan';

            return $this->render('_rekap_penilaian', [
                'dataProvider' => $dataProvider,
                'title' => $title
            ]);
        }
    }

    public function actionPenilaianPenyediaPpk() {
        $model = new ReportModel();
        $paketpengadaan = new PaketPengadaan(); // For years retrieval
        $request = \Yii::$app->request;

        if ($request->isGet) {
            return $this->render('index', [
                'action' => \yii\helpers\Url::to(['report/penilaian-penyedia-ppk']),
                'model' => $model,
                'raw' => $paketpengadaan->getExistingYears(), // Reuse this for year dropdown
                'paketpengadaan' => $paketpengadaan
            ]);
        } else if ($model->load($request->post())) {
            $query = \app\models\PenilaianPenyedia::find()
                ->alias('p')
                ->joinWith(['dpp d' => function($q) {
                    $q->joinWith(['paketpengadaan pp']);
                }]);

            // Apply Filters
            if ($model->tahun) {
                $query->andWhere(['pp.tahun_anggaran' => $model->tahun]);
            }
            if ($model->bulan && $model->bulan != 0) {
                 $query->andWhere(new \yii\db\Expression("strftime('%m', p.tanggal_kontrak) = :month", [':month' => sprintf('%02d', $model->bulan)]));
            }
            if ($model->kategori && $model->kategori !== 'all') {
                $setting = \app\models\Setting::findOne($model->kategori);
                if ($setting) {
                    $query->andWhere(['pp.kategori_pengadaan' => $setting->value]);
                }
            }
            if ($model->metode && $model->metode !== 'all') {
                $setting = \app\models\Setting::findOne($model->metode);
                if ($setting) {
                    $query->andWhere(['p.metode_pemilihan' => $setting->value]);
                }
            }
            if ($model->pejabat && $model->pejabat !== 'all' && $model::isAdmin()) {
                $query->andWhere(['d.pejabat_pengadaan' => $model->pejabat]);
            }
            if ($model->ppkom && $model->ppkom !== 'all' && $model::isAdmin()) {
                $query->andWhere(['pp.ppkom' => $model->ppkom]);
            }
            if($model::isPP()){
                $query->andWhere(['d.pejabat_pengadaan' => \Yii::$app->user->identity->userpegawai->id]);
            }
            if($model::isPPK()){
                $query->andWhere(['pp.ppkom' => \Yii::$app->user->identity->userpegawai->id]);
            }
            if ($model->admin && $model->admin !== 'all') {
                $query->andWhere(['d.admin_pengadaan' => $model->admin]);
            }
            if ($model->bidang && $model->bidang !== 'all') {
                $query->andWhere(['d.bidang_bagian' => $model->bidang]);
            }
            
            $data = $query->all();
            
            // Format data for the view
            $rows = [];
            foreach ($data as $i => $item) {
                // Parse details
                $details = [];
                if ($item->details) {
                    $details = json_decode($item->details, true);
                }
                
                $scores = $details['skor'] ?? [];
                
                // Weights: 20%, 20%, 30%, 30%
                $weights = [0.2, 0.2, 0.3, 0.3];
                $weightedScores = [];
                $nilaiKinerja = 0;
                
                for($k=0; $k<4; $k++) {
                    $s = isset($scores[$k]) ? floatval($scores[$k]) : 0;
                    $w = $s * ($weights[$k] ?? 0);
                    $weightedScores[] = $w;
                    $nilaiKinerja += $w;
                }
                
                // Override calculated if stored in details
                if (isset($details['nilai_kinerja'])) {
                    $nilaiKinerja = $details['nilai_kinerja'];
                }

                $eval = $details['hasil_evaluasi'] ?? '-';
                $ket = $details['keterangan'] ?? ($details['ulasan_pejabat_pengadaan'] ?? ''); // Fallback

                $rows[] = [
                    'id' => $item->id,
                    'no' => count($rows) + 1,
                    'nama_penyedia' => $item->nama_perusahaan,
                    'alamat' => $item->alamat_perusahaan,
                    'kategori' => $item->dpp->paketpengadaan->kategori_pengadaan ?? '-',
                    'nama_kegiatan' => $item->paket_pekerjaan,
                    'bidang' => $item->unit_kerja,
                    'metode' => $item->metode_pemilihan,
                    'tanggal_kontrak' => $item->tanggal_kontrak,
                    'nilai_kontrak' => $item->nilai_kontrak,
                    'ppk_nama' => $item->dpp->paketpengadaan->pejabatppkom->nama ?? ($item->pejabat_pembuat_komitmen ?? '-'),
                    'scores' => $scores,
                    'weighted_scores' => $weightedScores,
                    'nilai_kinerja' => $nilaiKinerja,
                    'hasil_evaluasi' => $eval,
                    'keterangan' => $ket
                ];
            }
            $dataProvider = new \yii\data\ArrayDataProvider([
                'allModels' => $rows,
                'pagination' => false,
                'sort' => [
                    'attributes' => ['nama_penyedia', 'tanggal_kontrak', 'nilai_kontrak', 'ppk_nama'],
                ],
            ]);
            
            $title = 'Rekapitulasi Penilaian Penyedia Barang Oleh Pejabat Pembuat Komitmen';

            return $this->render('_rekap_penilaian_ppk', [
                'dataProvider' => $dataProvider,
                'title' => $title
            ]);
        }
    }

    public function actionStatistikPenyedia() {
        $model = new ReportModel();
        $paketpengadaan = new PaketPengadaan();
        $request = \Yii::$app->request;

        if ($request->isGet) {
            return $this->render('index', [
                'action' => \yii\helpers\Url::to(['report/statistik-penyedia']),
                'model' => $model,
                'raw' => $paketpengadaan->getExistingYears(),
                'paketpengadaan' => $paketpengadaan
            ]);
        } else if ($model->load($request->post())) {
            // Build Query - Group by vendor to get statistics
            $query = \app\models\PenilaianPenyedia::find()
                ->alias('p')
                ->joinWith(['dpp d' => function($q) {
                    $q->joinWith(['paketpengadaan pp']);
                }])
                ->select([
                    'p.nama_perusahaan',
                    'p.alamat_perusahaan',
                    new \yii\db\Expression('COUNT(DISTINCT p.id) as jumlah_kontrak'),
                    new \yii\db\Expression('GROUP_CONCAT(DISTINCT pp.ppkom) as ppk_ids'),
                    new \yii\db\Expression('SUM(p.nilai_kontrak) as total_nilai_kontrak'),
                    new \yii\db\Expression('AVG(CAST(json_extract(p.details, "$.nilaiakhir") AS REAL)) as rata_nilai_evaluasi'),
                    new \yii\db\Expression('p.unit_kerja'),
                    new \yii\db\Expression('p.metode_pemilihan'),
                ])
                ->groupBy(['p.nama_perusahaan', 'p.alamat_perusahaan']);

            // Apply Filters
            if ($model->tahun) {
                $query->andWhere(['pp.tahun_anggaran' => $model->tahun]);
            }
            if ($model->bulan && $model->bulan != 0) {
                $query->andWhere(new \yii\db\Expression("strftime('%m', p.tanggal_kontrak) = :month", [':month' => sprintf('%02d', $model->bulan)]));
            }
            if ($model->kategori && $model->kategori !== 'all') {
                $setting = \app\models\Setting::findOne($model->kategori);
                if ($setting) {
                    $query->andWhere(['pp.kategori_pengadaan' => $setting->value]);
                }
            }
            if ($model->metode && $model->metode !== 'all') {
                $setting = \app\models\Setting::findOne($model->metode);
                if ($setting) {
                    $query->andWhere(['p.metode_pemilihan' => $setting->value]);
                }
            }
            if ($model->pejabat && $model->pejabat !== 'all' && $model::isAdmin()) {
                $query->andWhere(['d.pejabat_pengadaan' => $model->pejabat]);
            }
            if ($model->ppkom && $model->ppkom !== 'all' && $model::isAdmin()) {
                $query->andWhere(['pp.ppkom' => $model->ppkom]);
            }
            if($model::isPP()){
                $query->andWhere(['d.pejabat_pengadaan' => \Yii::$app->user->identity->userpegawai->id]);
            }
            if($model::isPPK()){
                $query->andWhere(['pp.ppkom' => \Yii::$app->user->identity->userpegawai->id]);
            }
            if ($model->admin && $model->admin !== 'all') {
                $query->andWhere(['d.admin_pengadaan' => $model->admin]);
            }
            if ($model->bidang && $model->bidang !== 'all') {
                $query->andWhere(['d.bidang_bagian' => $model->bidang]);
            }

            $data = $query->asArray()->all();
            
            // Format data for the view
            $rows = [];
            foreach ($data as $i => $item) {
                // Get PPK names from IDs
                $ppkIds = array_filter(array_unique(explode(',', $item['ppk_ids'] ?? '')));
                $ppkNames = [];
                foreach ($ppkIds as $ppkId) {
                    $pegawai = \app\models\Pegawai::findOne($ppkId);
                    if ($pegawai) {
                        $ppkNames[] = $pegawai->nama;
                    }
                }

                $rows[] = [
                    'no' => $i + 1,
                    'nama_penyedia' => $item['nama_perusahaan'],
                    'alamat' => $item['alamat_perusahaan'],
                    'unit_bidang' => $item['unit_kerja'],
                    'metode' => $item['metode_pemilihan'],
                    'jumlah_kontrak' => $item['jumlah_kontrak'],
                    'total_nilai_kontrak' => $item['total_nilai_kontrak'],
                    'rata_nilai_evaluasi' => round($item['rata_nilai_evaluasi'] ?? 0, 2),
                    'ppk' => implode(', ', $ppkNames),
                ];
            }
            
            $dataProvider = new \yii\data\ArrayDataProvider([
                'allModels' => $rows,
                'pagination' => false,
                'sort' => [
                    'attributes' => ['nama_penyedia', 'jumlah_kontrak', 'total_nilai_kontrak', 'rata_nilai_evaluasi'],
                    'defaultOrder' => ['jumlah_kontrak' => SORT_DESC],
                ],
            ]);

            $title = 'Statistik Penyedia Berdasarkan Kontrak';

            // Handle PDF or HTML output
            if ($request->post('type') === 'pdf') {
                $pdf = new \kartik\mpdf\Pdf([
                    'mode' => \kartik\mpdf\Pdf::MODE_UTF8,
                    'format' => \kartik\mpdf\Pdf::FORMAT_A4,
                    'orientation' => \kartik\mpdf\Pdf::ORIENT_LANDSCAPE,
                    'destination' => \kartik\mpdf\Pdf::DEST_BROWSER,
                    'content' => $this->renderPartial('_pdf_statistik_penyedia', [
                        'dataProvider' => $dataProvider,
                        'title' => $title,
                        'rows' => $rows
                    ]),
                    'options' => [
                        'title' => $title,
                        'subject' => $title,
                    ],
                    'methods' => [
                        'SetHeader' => [$title],
                        'SetFooter' => ['{PAGENO}'],
                    ]
                ]);
                return $pdf->render();
            }

            return $this->render('_statistik_penyedia', [
                'dataProvider' => $dataProvider,
                'title' => $title,
                'rows' => $rows
            ]);
        }
    }
}
