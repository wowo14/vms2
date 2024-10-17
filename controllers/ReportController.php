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
            if($model->tahun && $model->kategori=='all'){
                $query=$raw->where('year',$model->tahun)->toArray();
                VarDumper::dump($query,$depth = 10, $highlight = true);
            }
            if($model->tahun && $model->metode=='all'){
                $r = $paketpengadaan->byMetode();
                return $this->render('by_metode',[
                    'months'=>$r['months'],
                    'pivotTable'=> $r['pivotTable'],
                    'metodePengadaanTypes'=>$r['metodePengadaanTypes']
                ]);
            }
            // if($model->tahun && $model->bidang){}
            // if($model->tahun && $model->pejabat){}
        }
    }
}
