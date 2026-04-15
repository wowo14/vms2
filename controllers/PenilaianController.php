<?php
namespace app\controllers;
use Yii;
use app\models\PenilaianPenyedia;
use app\models\PenilaianPenyediaSearch;
use yii\web\Controller;
use yii\web\{Response,NotFoundHttpException};
use yii\filters\VerbFilter;
use yii\helpers\Html;
class PenilaianController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulkdelete' => ['post'],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        $searchModel = new PenilaianPenyediaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionView($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "PenilaianPenyedia #".$id,
                'content' =>$this->renderAjax('view', [
                    'model' => $this->findModel($id),
                ]),
                'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal'])
                    // Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
            ];
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }
    // public function actionCreate()
    // {
    //     $request = Yii::$app->request;
    //     $model = new PenilaianPenyedia();
    //     if($request->isAjax){

    //         Yii::$app->response->format = Response::FORMAT_JSON;
    //         if($model->load($request->post()) && $model->save()){
    //             return [
    //                 'forceReload' => '#crud-datatable-pjax',
    //                 'title' => Yii::t('yii2-ajaxcrud', 'Create New')." PenilaianPenyedia",
    //                 'content' => '<span class="text-success">'.Yii::t('yii2-ajaxcrud', 'Create').' PenilaianPenyedia '.Yii::t('yii2-ajaxcrud', 'Success').'</span>',
    //                 'footer' =>  Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']).
    //                     Html::a(Yii::t('yii2-ajaxcrud', 'Create More'), ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
    //             ];
    //         }else{
    //             return [
    //                 'title' => Yii::t('yii2-ajaxcrud', 'Create New')." PenilaianPenyedia",
    //                 'content' => $this->renderAjax('create', [
    //                     'model' => $model,
    //                 ]),
    //                 'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']).
    //                     Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
    //             ];
    //         }
    //     }else{

    //         if ($model->load($request->post()) && $model->save()){
    //             return $this->redirect(['view', 'id' => $model->id]);
    //         }else{
    //             return $this->render('create', [
    //                 'model' => $model,
    //             ]);
    //         }
    //     }
    // }
    // public function actionUpdate($id)
    // {
    //     $request = Yii::$app->request;
    //     $model = $this->findModel($id);
    //     if($request->isAjax){
    //         Yii::$app->response->format = Response::FORMAT_JSON;
    //         if($model->load($request->post()) && $model->save()){
    //             return [
    //                 'forceReload' => '#crud-datatable-pjax',
    //                 'title' => "PenilaianPenyedia #".$id,
    //                 'content' => $this->renderAjax('view', [
    //                     'model' => $model,
    //                 ]),
    //                 'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']).
    //                     Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id],['class' => 'btn btn-primary', 'role' => 'modal-remote'])
    //             ];
    //         }else{
    //              return [
    //                 'title' => Yii::t('yii2-ajaxcrud', 'Update')." PenilaianPenyedia #".$id,
    //                 'content' => $this->renderAjax('update', [
    //                     'model' => $model,
    //                 ]),
    //                 'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']).
    //                     Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
    //             ];
    //         }
    //     }else{
    //         if ($model->load($request->post()) && $model->save()){
    //             return $this->redirect(['view', 'id' => $model->id]);
    //         }else{
    //             return $this->render('update', [
    //                 'model' => $model,
    //             ]);
    //         }
    //     }
    // }
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $this->findModel($id)->delete();
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            return $this->redirect(['index']);
        }
    }
    public function actionBulkdelete()
    {
        $request = Yii::$app->request;
        $pks = explode(',', $request->post( 'pks' ));
        foreach ( $pks as $pk ){
            $model = $this->findModel($pk);
            $model->delete();
        }
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            return $this->redirect(['index']);
        }
    }
    public function actionEvaluasi()
    {
        $request = Yii::$app->request;
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');
        $vendor_id = $request->get('vendor_id');

        $query = PenilaianPenyedia::find();
        
        if ($tahun && $tahun != 'all') {
            $query->andWhere(['strftime("%Y", tanggal_kontrak)' => (string)$tahun]);
        }
        if ($bulan && $bulan != 'all') {
            $query->andWhere(['strftime("%m", tanggal_kontrak)' => str_pad($bulan, 2, '0', STR_PAD_LEFT)]);
        }
        if ($vendor_id && $vendor_id != 'all') {
            $p = \app\models\Penyedia::findOne($vendor_id);
            if ($p) {
                $query->andWhere(['nama_perusahaan' => $p->nama_perusahaan]);
            }
        }

        $data = $query->all();
        
        $summary = [];
        foreach ($data as $item) {
            $details = json_decode($item->details, true);
            if (!$details) continue;
            
            $provider = $item->nama_perusahaan;
            
            if (!isset($summary[$provider])) {
                $summary[$provider] = [
                    'nama' => $provider,
                    'count' => 0,
                    'total_score' => 0,
                    'avg_score' => 0,
                    'total_nilai' => 0
                ];
            }
            
            $nilaiakhir_str = $details['nilaiakhir'] ?? 0;
            $nilaiakhir_num = 0;
            if (is_string($nilaiakhir_str) && strpos($nilaiakhir_str, '=') !== false) {
                $parts = explode('=', $nilaiakhir_str);
                $nilaiakhir_num = (float) trim(end($parts));
            } else {
                $nilaiakhir_num = (float) $nilaiakhir_str;
            }
            
            $summary[$provider]['count']++;
            $summary[$provider]['total_score'] += $nilaiakhir_num;
            $summary[$provider]['total_nilai'] += $item->nilai_kontrak;
        }
        
        $sort = $request->get('sort', 'rating_desc');
        
        foreach ($summary as &$s) {
            $s['avg_score'] = $s['count'] > 0 ? round($s['total_score'] / $s['count'], 2) : 0;
        }

        // Sorting logic
        uasort($summary, function($a, $b) use ($sort) {
            switch ($sort) {
                case 'rating_asc':
                    return $a['avg_score'] <=> $b['avg_score'];
                case 'rating_desc':
                    return $b['avg_score'] <=> $a['avg_score'];
                case 'count_asc':
                    return $a['count'] <=> $b['count'];
                case 'count_desc':
                    return $b['count'] <=> $a['count'];
                case 'nilai_asc':
                    return $a['total_nilai'] <=> $b['total_nilai'];
                case 'nilai_desc':
                    return $b['total_nilai'] <=> $a['total_nilai'];
                default:
                    return $b['avg_score'] <=> $a['avg_score'];
            }
        });

        return $this->render('evaluasi', [
            'summary' => $summary,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'vendor_id' => $vendor_id,
            'sort' => $sort
        ]);
    }

    public function actionDrillDown($vendor_nama)
    {
        $request = Yii::$app->request;
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');

        $query = PenilaianPenyedia::find()->where(['nama_perusahaan' => $vendor_nama]);
        
        if ($tahun && $tahun != 'all') {
            $query->andWhere(['strftime("%Y", tanggal_kontrak)' => (string)$tahun]);
        }
        if ($bulan && $bulan != 'all') {
            $query->andWhere(['strftime("%m", tanggal_kontrak)' => str_pad($bulan, 2, '0', STR_PAD_LEFT)]);
        }

        $data = $query->all();
        
        return $this->renderAjax('drill_down', [
            'data' => $data,
            'vendor_nama' => $vendor_nama
        ]);
    }

    protected function findModel($id)
    {
        if (($model = PenilaianPenyedia::findOne($id)) !== null)
        {
            return $model;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}