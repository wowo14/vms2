<?php
namespace app\controllers;
use app\models\PaketPengadaan;
use app\models\ReportModel;
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
