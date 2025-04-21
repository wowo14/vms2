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
            return $this->render('_report_dppmasuk', [
                'model' => $model,
                'rawData' => $raw,
                'paketpengadaan' => $paketpengadaan
            ]);
        }
    }
}
