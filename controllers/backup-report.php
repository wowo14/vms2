<?php

namespace app\controllers;

use app\models\PaketPengadaan;
use app\models\ReportModel;
use yii\helpers\VarDumper;

class ReportController extends \yii\web\Controller {
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
                $named = $model->kategori !== 'all' ? $model->kategori : '';
                $bln = $model->bulan == 0 ? '' : $model->bulan;
                $r = $paketpengadaan->bymonths2([
                    'groupby' => 'kategori_pengadaan',
                    'named' => $named,
                    'bln' => $bln
                ]);
                return $this->render('by_months', [
                    'months' => $r['months'],
                    'pivotTable' => $r['pivotTable'],
                    'title' => 'Filter Kategori ' . $model->tahun
                ]);
            }
            if ($model->tahun) {
                $named = $model->metode !== 'all' ? $model->metode : '';
                $bln = $model->bulan == 0 ? '' : $model->bulan;
                $r = $paketpengadaan->bymonths2([
                    'groupby' => 'metode_pengadaan',
                    'named' => $named,
                    'bln' => $bln
                ]);
                return $this->render('by_months', [
                    'months' => $r['months'],
                    'pivotTable' => $r['pivotTable'],
                    'title' => 'Filter Metode ' . $model->tahun
                ]);
            }
            if ($model->tahun) {
                $named = $model->pejabat !== 'all' ? $model->pejabat : '';
                $bln = $model->bulan == 0 ? '' : $model->bulan;
                $r = $paketpengadaan->bymonths2([
                    'groupby' => 'pejabat_pengadaan',
                    'named' => $named,
                    'bln' => $bln
                ]);
                return $this->render('by_months', [
                    'months' => $r['months'],
                    'pivotTable' => $r['pivotTable'],
                    'title' => 'Filter Pejabat Pengadaan ' . $model->tahun
                ]);
            }
            if ($model->tahun) {
                $named = $model->admin !== 'all' ? $model->admin : '';
                $bln = $model->bulan == 0 ? '' : $model->bulan;
                $r = $paketpengadaan->bymonths2([
                    'groupby' => 'admin_pengadaan',
                    'named' => $named,
                    'bln' => $bln
                ]);
                return $this->render('by_months', [
                    'months' => $r['months'],
                    'pivotTable' => $r['pivotTable'],
                    'title' => 'Filter Admin Pengadaan ' . $model->tahun
                ]);
            }
            if ($model->tahun) {
                $named = $model->bidang !== 'all' ? $model->bidang : '';
                $bln = $model->bulan == 0 ? '' : $model->bulan;
                $r = $paketpengadaan->bymonths2([
                    'groupby' => 'bidang_bagian',
                    'named' => $named,
                    'bln' => $bln
                ]);
                return $this->render('by_months', [
                    'months' => $r['months'],
                    'pivotTable' => $r['pivotTable'],
                    'title' => 'Filter Bidang ' . $model->tahun
                ]);
            }
            if ($model->tahun) {
                $bln = $model->bulan == 0 ? '' : $model->bulan;
                $groupby = ($model->pejabat != 'all') ? $model->pejabat : 'pejabat_pengadaan';
                $type = ($model->metode != 'all') ? $model->metode : 'metode_pengadaan';
                $r = $paketpengadaan->byKategori([
                    'groupby' => $groupby,
                    'type' => $type,
                    'bln' => $bln,
                ]);
                return $this->render('by_metode', [
                    'months' => $r['months'],
                    'pivotTable' => $r['pivotTable'],
                    'types' => $r['types'],
                    'title' => 'Jumlah Kegiatan Pengadaan Per Pejabat Pengadaan x Metode ' . $model->tahun
                ]);
            }
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
            //     $r = $paketpengadaan->byKategori(['groupby'=>'bidang_bagian','type'=> 'kategori_pengadaan','bln'=>'']);
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
