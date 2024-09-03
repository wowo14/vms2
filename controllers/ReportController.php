<?php
namespace app\controllers;
use app\models\PaketPengadaan;
use app\models\ReportModel;
class ReportController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model=new ReportModel();
        $paketpengadaan=new PaketPengadaan();
        $raw=collect($paketpengadaan::Dashboard());
        $request=\Yii::$app->request;
        if($request->isGet){
        }else if($model->load($request->post())){
            if($model->tahun && $model->kategori=='all'){
                $query=$raw->where('year',$model->tahun)->toArray();
                print_r($query);
            }
            if($model->tahun && $model->metode=='all'){
                $query=$raw->where('year',$model->tahun)->toArray();
                print_r($query);
            }
            if($model->tahun && $model->bidang){}
            if($model->tahun && $model->pejabat){}
        }
        return $this->render('index',[
            'model'=>$model,'raw'=>$raw,'paketpengadaan'=>$paketpengadaan
        ]);
    }
}
