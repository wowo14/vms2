<?php
namespace app\controllers;
use Yii;
use app\models\TemplateChecklistEvaluasiDetail;
use app\models\TemplateChecklistEvaluasiDetailSearch;
use yii\web\{Response, NotFoundHttpException};
use yii\filters\VerbFilter;
use yii\helpers\Html;class TcedController extends Controller {
    public function behaviors() {
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
    public function actionIndex() {
        $searchModel = new TemplateChecklistEvaluasiDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionView($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "TemplateChecklistEvaluasiDetail #" . $id,
                'content' => $this->renderAjax('view', [
                    'model' => $model = $this->findModel($id),
                ]),
                'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                    Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
            ];
        } else {
            return $this->render('view', [
                'model' => $model = $this->findModel($id),
            ]);
        }
    }
    public function actionCreate() {
        $request = Yii::$app->request;
        $model = new TemplateChecklistEvaluasiDetail();
        if ($request->isAjax) {            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " TemplateChecklistEvaluasiDetail",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Create'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " TemplateChecklistEvaluasiDetail",
                    'content' => '<span class="text-success">' . Yii::t('yii2-ajaxcrud', 'Create') . ' TemplateChecklistEvaluasiDetail ' . Yii::t('yii2-ajaxcrud', 'Success') . '</span>',
                    'footer' =>  Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Create More'), ['create'], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " TemplateChecklistEvaluasiDetail",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            }
        } else {            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
    }
    public function actionUpdate($id) {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " TemplateChecklistEvaluasiDetail #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "TemplateChecklistEvaluasiDetail #" . $id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " TemplateChecklistEvaluasiDetail #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            }
        } else {
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }
    public function actionDelete($id) {
        $request = Yii::$app->request;
        $this->findModel($id)->delete();
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            return $this->redirect(['index']);
        }
    }
    public function actionBulkdelete() {
        $request = Yii::$app->request;
        $pks = explode(',', $request->post('pks'));
        foreach ($pks as $pk) {
            $model = $this->findModel($pk);
            $model->delete();
        }
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            return $this->redirect(['index']);
        }
    }
    protected function findModel($id) {
        if (($model = TemplateChecklistEvaluasiDetail::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
