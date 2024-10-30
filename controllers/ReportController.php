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
                // Initialize query parameters
                $params = [
                    'groupby' => '',
                    'named' => '',
                    'bln' => $model->bulan == 0 ? '' : $model->bulan
                ];
                // Check filter conditions in order of priority
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
                // If no specific filter is set, apply default grouping logic
                if (empty($params['groupby'])) {
                    $params['groupby'] = 'metode_pengadaan';  // Default group
                }
                // Fetch the results based on the filters
                $r = $paketpengadaan->bymonths2($params);
                // Render the view with the filtered data
                return $this->render('by_months', [
                    'months' => $r['months'],
                    'pivotTable' => $r['pivotTable'],
                    'title' => 'Filtered Report for ' . $model->tahun
                ]);
            }
            // if ($model->tahun) {
            //     if($model->metode!='all'){
            //         if($model->bulan){
            //             $r = $paketpengadaan->byMetode(['groupby' => 'pejabat_pengadaan', 'type' => $model->metode, 'bln' => $model->bulan]);
            //         }else{
            //             $r = $paketpengadaan->byMetode(['groupby' => 'pejabat_pengadaan', 'type' => $model->metode, 'bln' => '']);
            //         }
            //     }else{
            //         if ($model->bulan) {
            //             $r = $paketpengadaan->byMetode(['groupby' => 'pejabat_pengadaan', 'type' => 'metode_pengadaan', 'bln' => $model->bulan]);
            //         }else{
            //             $r = $paketpengadaan->byMetode(['groupby' => 'pejabat_pengadaan', 'type' => 'metode_pengadaan', 'bln' => '']);
            //         }
            //     }
            //     //===
            //     if($model->pejabat!='all'){
            //         if($model->bulan){
            //             $r = $paketpengadaan->byMetode(['groupby' => $model->pejabat, 'type' => 'metode_pengadaan', 'bln' => $model->bulan]);
            //         }else{
            //             $r = $paketpengadaan->byMetode(['groupby' => $model->pejabat, 'type' => 'metode_pengadaan', 'bln' => '']);
            //         }
            //     }else{
            //         if ($model->bulan) {
            //             $r = $paketpengadaan->byMetode(['groupby' => $model->pejabat, 'type' => 'metode_pengadaan', 'bln' => $model->bulan]);
            //         }else{
            //             $r = $paketpengadaan->byMetode(['groupby' => $model->pejabat, 'type' => 'metode_pengadaan', 'bln' => '']);
            //         }
            //     }
            //     return $this->render('by_metode', [
            //         'months' => $r['months'],
            //         'pivotTable' => $r['pivotTable'],
            //         'types' => $r['types'],
            //         'title' => 'Jumlah Kegiatan Pengadaan Per Pejabat Pengadaan x Metode ' . $model->tahun
            //     ]);
            // }
            // if ($model->tahun && $model->metode == 'all' && $model->admin == 'all') {
            //     $r = $paketpengadaan->byMetode('admin_pengadaan');
            //     return $this->render('by_metode', [
            //         'months' => $r['months'],
            //         'pivotTable' => $r['pivotTable'],
            //         'types' => $r['types'],
            //         'title' => 'Jumlah Kegiatan Pengadaan Per Admin Pengadaan x Metode ' . $model->tahun
            //     ]);
            // }
            // if ($model->tahun && $model->metode == 'all' && $model->bidang == 'all') {
            //     $r = $paketpengadaan->byMetode('bidang_bagian');
            //     return $this->render('by_metode', [
            //         'months' => $r['months'],
            //         'pivotTable' => $r['pivotTable'],
            //         'types' => $r['types'],
            //         'title' => 'Jumlah Kegiatan Pengadaan Per Bidang x Metode ' . $model->tahun
            //     ]);
            // }
            // if ($model->tahun && $model->kategori == 'all' && $model->pejabat == 'all') {
            //     $r = $paketpengadaan->byKategori('pejabat_pengadaan');
            //     return $this->render('by_metode', [
            //         'months' => $r['months'],
            //         'pivotTable' => $r['pivotTable'],
            //         'types' => $r['types'],
            //         'title' => 'Jumlah Kegiatan Pengadaan Per Pejabat Pengadaan x kategori ' . $model->tahun
            //     ]);
            // }
            // if ($model->tahun && $model->kategori == 'all' && $model->admin == 'all') {
            //     $r = $paketpengadaan->byKategori('admin_pengadaan');
            //     return $this->render('by_metode', [
            //         'months' => $r['months'],
            //         'pivotTable' => $r['pivotTable'],
            //         'types' => $r['types'],
            //         'title' => 'Jumlah Kegiatan Pengadaan Per admin Pengadaan x Kategori ' . $model->tahun
            //     ]);
            // }
            // if ($model->tahun && $model->kategori == 'all' && $model->bidang == 'all') {
            //     $r = $paketpengadaan->byKategori(['groupby' => 'bidang_bagian', 'type' => 'kategori_pengadaan', 'bln' => '']);
            //     return $this->render('by_metode', [
            //         'months' => $r['months'],
            //         'pivotTable' => $r['pivotTable'],
            //         'types' => $r['types'],
            //         'title' => 'Jumlah Kegiatan Pengadaan Per Bidang x Kategori ' . $model->tahun
            //     ]);
            // }
            return $this->render('index', [
                'model' => $model,
                'raw' => $paketpengadaan->getrawData(),
                'paketpengadaan' => $paketpengadaan
            ]);
        }
    }
}
