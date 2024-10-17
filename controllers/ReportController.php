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
        $raw=collect($paketpengadaan::Dashboard());
        $request=\Yii::$app->request;
        if($request->isGet){
            return $this->render('index',[
                'model'=>$model,'raw'=>$raw,'paketpengadaan'=>$paketpengadaan
            ]);
        }else if($model->load($request->post())){
            if($model->tahun && $model->metode=='all' && $model->bulan=='all'){
                $r = $paketpengadaan->byMetode2('metode_pengadaan');
                return $this->render('by_m2',[
                    'months'=>$r['months'],
                    'pivotTable'=> $r['pivotTable'],
                ]);
            }
            if($model->tahun && $model->kategori=='all' && $model->bulan=='all'){
                $r = $paketpengadaan->byMetode2('kategori_pengadaan');
                return $this->render('by_m2',[
                    'months'=>$r['months'],
                    'pivotTable'=> $r['pivotTable'],
                ]);
            }
            if($model->tahun && $model->metode=='all' && $model->bidang=='all'){
                $r = $paketpengadaan->byMetode('bidang_bagian');
                return $this->render('by_metode',[
                    'months'=>$r['months'],
                    'pivotTable'=> $r['pivotTable'],
                    'metodePengadaanTypes'=>$r['metodePengadaanTypes']
                ]);
            }
            if($model->tahun && $model->metode=='all' && $model->pejabat=='all'){
                $r = $paketpengadaan->byMetode('pejabat_pengadaan');
                return $this->render('by_metode',[
                    'months'=>$r['months'],
                    'pivotTable'=> $r['pivotTable'],
                    'metodePengadaanTypes'=>$r['metodePengadaanTypes']
                ]);
            }
        }
    }
}
