<?php
namespace app\controllers;
use app\models\PaketPengadaan;
use app\models\ReportModel;
use yii\helpers\VarDumper;
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
            if ($model->tahun) {
                if ($model->metode != 'all') {
                    if ($model->bulan) {
                        if ($model->pejabat !== 'all') {
                            $r = $paketpengadaan->byKategori(['groupby' => $model->pejabat, 'type' => $model->metode, 'bln' => $model->bulan]);
                        } else {
                            $r = $paketpengadaan->byKategori(['groupby' => 'pejabat_pengadaan', 'type' => $model->metode, 'bln' => $model->bulan]);
                        }
                    } else {
                        if ($model->pejabat !== 'all') {
                            $r = $paketpengadaan->byKategori(['groupby' => $model->pejabat, 'type' => $model->metode, 'bln' => '']);
                        } else {
                            $r = $paketpengadaan->byKategori(['groupby' => 'pejabat_pengadaan', 'type' => $model->metode, 'bln' => '']);
                        }
                    }
                } else {
                    if ($model->bulan) {
                        if ($model->pejabat !== 'all') {
                            $r = $paketpengadaan->byKategori(['groupby' => $model->pejabat, 'type' => 'metode_pengadaan', 'bln' => $model->bulan]);
                        } else {
                            $r = $paketpengadaan->byKategori(['groupby' => 'pejabat_pengadaan', 'type' => 'metode_pengadaan', 'bln' => $model->bulan]);
                        }
                    } else {
                        if ($model->pejabat !== 'all') {
                            $r = $paketpengadaan->byKategori(['groupby' => $model->pejabat, 'type' => 'metode_pengadaan', 'bln' => '']);
                        } else {
                            $r = $paketpengadaan->byKategori(['groupby' => 'pejabat_pengadaan', 'type' => 'metode_pengadaan', 'bln' => '']);
                        }
                    }
                }
                return $this->render('by_metode', [
                    'months' => $r['months'],
                    'pivotTable' => $r['pivotTable'],
                    'types' => $r['types'],
                    'title' => 'Jumlah Kegiatan Pengadaan Per Pejabat Pengadaan x Metode ' . $model->tahun
                ]);
            }
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
            return $this->render('_pivot1', [
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
            if ($model->tahun) {
                if ($model->kategori != 'all') {
                    if ($model->bulan) {
                        if ($model->admin !== 'all') {
                            $r = $paketpengadaan->byKategori(['groupby' => $model->admin, 'type' => $model->kategori, 'bln' => $model->bulan]);
                        } else {
                            $r = $paketpengadaan->byKategori(['groupby' => 'admin_pengadaan', 'type' => $model->kategori, 'bln' => $model->bulan]);
                        }
                    } else {
                        if ($model->admin !== 'all') {
                            $r = $paketpengadaan->byKategori(['groupby' => $model->admin, 'type' => $model->kategori, 'bln' => '']);
                        } else {
                            $r = $paketpengadaan->byKategori(['groupby' => 'admin_pengadaan', 'type' => $model->kategori, 'bln' => '']);
                        }
                    }
                } else {
                    if ($model->bulan) {
                        if ($model->admin !== 'all') {
                            $r = $paketpengadaan->byKategori(['groupby' => $model->admin, 'type' => 'kategori_pengadaan', 'bln' => $model->bulan]);
                        } else {
                            $r = $paketpengadaan->byKategori(['groupby' => 'admin_pengadaan', 'type' => 'kategori_pengadaan', 'bln' => $model->bulan]);
                        }
                    } else {
                        if ($model->admin !== 'all') {
                            $r = $paketpengadaan->byKategori(['groupby' => $model->admin, 'type' => 'kategori_pengadaan', 'bln' => '']);
                        } else {
                            $r = $paketpengadaan->byKategori(['groupby' => 'admin_pengadaan', 'type' => 'kategori_pengadaan', 'bln' => '']);
                        }
                    }
                }
                return $this->render('by_metode', [
                    'months' => $r['months'],
                    'pivotTable' => $r['pivotTable'],
                    'types' => $r['types'],
                    'title' => 'Jumlah Kegiatan Pengadaan Per admin Pengadaan x Kategori ' . $model->tahun
                ]);
            }
        }
    }
    public function actionIndex() {
        $model = new ReportModel();
        $paketpengadaan = new PaketPengadaan();
        $request = \Yii::$app->request;
        if ($request->isGet) {
            return $this->render('index', [
                'model' => $model,
                'raw' => $paketpengadaan->getrawData(),
                'paketpengadaan' => $paketpengadaan
            ]);
        } else if ($model->load($request->post())) {
            if ($model->tahun) {
                $params = [
                    'groupby' => '',
                    'named' => '',
                    'bln' => $model->bulan == 0 ? '' : $model->bulan
                ];
                if ($model->kategori !== 'all') {
                    $params['groupby'] = 'kategori_pengadaan';
                    $params['named'] = $model->kategori;
                }
                if ($model->metode !== 'all') {
                    $params['groupby'] = 'metode_pengadaan';
                    $params['named'] = $model->metode;
                }
                if ($model->pejabat !== 'all') {
                    $params['groupby'] = 'pejabat_pengadaan';
                    $params['named'] = $model->pejabat;
                }
                if ($model->admin !== 'all') {
                    $params['groupby'] = 'admin_pengadaan';
                    $params['named'] = $model->admin;
                }
                if ($model->bidang !== 'all') {
                    $params['groupby'] = 'bidang_bagian';
                    $params['named'] = $model->bidang;
                }
                if (empty($params['groupby'])) {
                    $params['groupby'] = 'metode_pengadaan';
                }
                $r = $paketpengadaan->bymonths2($params);
                return $this->render('by_months', [
                    'months' => $r['months'],
                    'pivotTable' => $r['pivotTable'],
                    'title' => 'Filtered Report for ' . $model->tahun
                ]);
            }
            return $this->render('index', [
                'model' => $model,
                'raw' => $paketpengadaan->getrawData(),
                'paketpengadaan' => $paketpengadaan
            ]);
        }
    }
}
