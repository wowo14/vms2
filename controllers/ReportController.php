<?php
namespace app\controllers;
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
            return $this->actionMetodecount($result);
            // return $this->render('_pivot_metode', [
            //     'model' => $result
            // ]);
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
                'raw' => $paketpengadaan->getrawData(),
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
    public function actionMetodecount($raw) {
        return $this->render('_pivot_metodecount', [
            'data' => $raw
        ]);
    }
    public function actionDppmasuk() {
        $model = new ReportModel();
        $paketpengadaan = new PaketPengadaan();
        $request = \Yii::$app->request;
        if ($request->isGet) {
            return $this->render('index', [
                'action' => \yii\helpers\Url::to(['report/dppmasuk']),
                'model' => $model,
                'raw' => $paketpengadaan->getrawData(),
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
            // print_r($raw);
            return $this->render('_pivot_dppmasuk', [
                'model' => $raw,
                'months' => $model->getMonths()
            ]);
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
}
