<?php
namespace app\controllers;
use app\models\{Dpp,PenugasanPemilihanpenyedia,PenugasanPemilihanpenyediaSearch};
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\{ArrayHelper,Html};
use yii\web\{Response,NotFoundHttpException};
class PenugasanController extends Controller
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
        $searchModel = new PenugasanPemilihanpenyediaSearch();
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
                'title' => "PenugasanPemilihanpenyedia #".$id,
                'content' =>$this->renderAjax('view', [
                    'model' => $this->findModel($id),
                ]),
                'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']).
                    Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
            ];
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }
    public function actionCreate($iddpp=null)
    {
        $request = Yii::$app->request;
        $model = new PenugasanPemilihanpenyedia();
        $query = Dpp::where(['is', 'pp.pemenang', null])
                ->joinWith(['paketpengadaan pp']);
        if ($iddpp !== null) {
            $query->andWhere(['dpp.id' => $iddpp]);
            $model->dpp_id=$iddpp;
        }
        $data=[
            'dpp'=> ArrayHelper::map($query->all(),'id','nomordpp'),
            'pejabat'=>$model::getAllpetugas(),
            'admin'=>$model::getAlladmin(),
        ];
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New')." PenugasanPemilihanpenyedia",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,'data'=>$data
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']).
                        Html::button(Yii::t('yii2-ajaxcrud', 'Create'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New')." PenugasanPemilihanpenyedia",
                    'content' => '<span class="text-success">'.Yii::t('yii2-ajaxcrud', 'Create').' PenugasanPemilihanpenyedia '.Yii::t('yii2-ajaxcrud', 'Success').'</span>',
                    'footer' =>  Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']).
                        Html::a(Yii::t('yii2-ajaxcrud', 'Create More'), ['create'], ['class' => 'btn btn-primary','data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                ];
            }
        }else{
            if ($model->load($request->post()) && $model->save()){
                return $this->redirect(['view', 'id' => $model->id]);
            }else{
                return $this->render('create', [
                    'model' => $model,'data'=>$data
                ]);
            }
        }
    }
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $query = Dpp::where(['is', 'pp.pemenang', null])
                ->joinWith(['paketpengadaan pp']);
        if ($model->dpp_id !== null) {
            $query->andWhere(['dpp.id' => $model->dpp_id]);
        }
        $data=[
            'dpp'=> ArrayHelper::map(
                        $query->asArray()->all(),
                'id','nomor_dpp'),
            'pejabat'=>$model::getAllpetugas(),
            'admin'=>$model::getAlladmin(),
        ];
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Update')." PenugasanPemilihanpenyedia #".$id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,'data'=>$data
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']).
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "PenugasanPemilihanpenyedia #".$id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']).
                        Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id],['class' => 'btn btn-primary','data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                ];
            }
        }else{
            if ($model->load($request->post()) && $model->save()){
                return $this->redirect(['view', 'id' => $model->id]);
            }else{
                return $this->render('update', [
                    'model' => $model,'data'=>$data
                ]);
            }
        }
    }
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
    protected function findModel($id)
    {
        if (($model = PenugasanPemilihanpenyedia::findOne($id)) !== null)
        {
            return $model;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}