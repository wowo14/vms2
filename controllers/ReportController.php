<?php
namespace app\controllers;
use app\models\PaketPengadaan;
use app\models\ReportModel;
use yii\helpers\VarDumper;
class ReportController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model=new ReportModel();
        $paketpengadaan=new PaketPengadaan();
        $request=\Yii::$app->request;
        if($request->isGet){
            return $this->render('index',[
                'model'=>$model,'raw'=> $paketpengadaan->getrawData(),'paketpengadaan'=>$paketpengadaan
            ]);
        }else if($model->load($request->post())){
            if($model->tahun && $model->kategori=='all' && $model->bulan=='all'){
                $r = $paketpengadaan->bymonths('kategori_pengadaan');
                return $this->render('by_months',[
                    'months'=>$r['months'],
                    'pivotTable'=> $r['pivotTable'],
                    'title'=>'Filter Kategori '. $model->tahun
                ]);
            }
            if ($model->tahun && $model->metode == 'all' && $model->bulan == 'all') {
                $r = $paketpengadaan->bymonths('metode_pengadaan');
                return $this->render('by_months', [
                    'months' => $r['months'],
                    'pivotTable' => $r['pivotTable'],
                    'title' => 'Filter Metode '. $model->tahun
                ]);
            }
            if ($model->tahun && $model->pejabat == 'all' && $model->bulan == 'all') {
                $r = $paketpengadaan->bymonths('pejabat_pengadaan');
                return $this->render('by_months', [
                    'months' => $r['months'],
                    'pivotTable' => $r['pivotTable'],
                    'title' => 'Filter Pejabat Pengadaan '. $model->tahun
                ]);
            }
            if($model->tahun && $model->admin=='all' && $model->bulan=='all'){
                $r = $paketpengadaan->bymonths('admin_pengadaan');
                return $this->render('by_months',[
                    'months'=>$r['months'],
                    'pivotTable'=> $r['pivotTable'],
                    'title' => 'Filter Admin Pengadaan '. $model->tahun
                ]);
            }
            if($model->tahun && $model->bidang=='all' && $model->bulan=='all'){
                $r = $paketpengadaan->bymonths('bidang_bagian');
                return $this->render('by_months',[
                    'months'=>$r['months'],
                    'pivotTable'=> $r['pivotTable'],
                    'title' => 'Filter Bidang '. $model->tahun
                ]);
            }
            if($model->tahun && $model->metode=='all' && $model->pejabat=='all'){
                $r = $paketpengadaan->byMetode('pejabat_pengadaan');
                return $this->render('by_metode',[
                    'months'=>$r['months'],
                    'pivotTable'=> $r['pivotTable'],
                    'types'=>$r['types'],
                    'title' => 'Jumlah Kegiatan Pengadaan Per Pejabat Pengadaan x Metode ' . $model->tahun
                ]);
            }
            if($model->tahun && $model->metode=='all' && $model->admin=='all'){
                $r = $paketpengadaan->byMetode('admin_pengadaan');
                return $this->render('by_metode',[
                    'months'=>$r['months'],
                    'pivotTable'=> $r['pivotTable'],
                    'types'=>$r['types'],
                    'title' => 'Jumlah Kegiatan Pengadaan Per Admin Pengadaan x Metode ' . $model->tahun
                ]);
            }
            if ($model->tahun && $model->metode == 'all' && $model->bidang == 'all') {
                $r = $paketpengadaan->byMetode('bidang_bagian');
                return $this->render('by_metode', [
                    'months' => $r['months'],
                    'pivotTable' => $r['pivotTable'],
                    'types' => $r['types'],
                    'title' => 'Jumlah Kegiatan Pengadaan Per Bidang x Metode ' . $model->tahun
                ]);
            }
            if ($model->tahun && $model->kategori == 'all' && $model->pejabat == 'all') {
                $r = $paketpengadaan->byKategori('pejabat_pengadaan');
                return $this->render('by_metode', [
                    'months' => $r['months'],
                    'pivotTable' => $r['pivotTable'],
                    'types' => $r['types'],
                    'title' => 'Jumlah Kegiatan Pengadaan Per Pejabat Pengadaan x kategori ' . $model->tahun
                ]);
            }
            if ($model->tahun && $model->kategori == 'all' && $model->admin == 'all') {
                $r = $paketpengadaan->byKategori('admin_pengadaan');
                return $this->render('by_metode', [
                    'months' => $r['months'],
                    'pivotTable' => $r['pivotTable'],
                    'types' => $r['types'],
                    'title' => 'Jumlah Kegiatan Pengadaan Per admin Pengadaan x Kategori ' . $model->tahun
                ]);
            }
            if ($model->tahun && $model->kategori == 'all' && $model->bidang == 'all') {
                $r = $paketpengadaan->byKategori('bidang_bagian');
                return $this->render('by_metode', [
                    'months' => $r['months'],
                    'pivotTable' => $r['pivotTable'],
                    'types' => $r['types'],
                    'title' => 'Jumlah Kegiatan Pengadaan Per Bidang x Kategori ' . $model->tahun
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
