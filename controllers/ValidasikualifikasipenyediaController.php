<?php
namespace app\controllers;
use app\models\TemplateChecklistEvaluasi;
use app\models\ValidasiKualifikasiPenyedia;
use app\models\ValidasiKualifikasiPenyediaDetail;
use app\models\ValidasiKualifikasiPenyediaSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\{Response, NotFoundHttpException};
class ValidasikualifikasipenyediaController extends Controller {
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
        $searchModel = new ValidasiKualifikasiPenyediaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionAssestment($id) {
        $request = Yii::$app->request;
        if ($request->isGet) {
            $model=ValidasiKualifikasiPenyedia::find()->cache(false)->where(['id' => $id])->one();
            return $this->render('assestment', [
                'model' => $model
            ]);
        }
        if($request->isPost){
            $assestment=$request->post('ValidasiKualifikasiPenyedia')['assestment'];
            ValidasiKualifikasiPenyediaDetail::updateAll([
                'hasil' => json_encode($assestment),
            ], ['header_id' => $id]);
            return $this->redirect('index');
        }
    }
    public function actionView($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "ValidasiKualifikasiPenyedia #" . $id,
                'content' => $this->renderAjax('view', [
                    'model' => $this->findModel($id),
                ]),
                'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                    Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
            ];
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }
    public function actionCreate() {
        $request = Yii::$app->request;
        $model = new ValidasiKualifikasiPenyedia();
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " ValidasiKualifikasiPenyedia",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Create'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                if ($model->template) {
                    $hasil = [];
                    $collect = TemplateChecklistEvaluasi::findOne($model->template);
                    $ar_element = explode(',', $collect->element);
                    foreach (json_decode($collect->detail->uraian, true) as $v) {
                        $c = ['uraian' => $v['uraian']];
                        foreach ($ar_element as $element) {
                            if ($element) { $c[$element] = '';}
                        }
                        $hasil[] = $c;
                    }
                    $detail = new ValidasiKualifikasiPenyediaDetail();
                    $detail->header_id = $model->id;
                    $detail->hasil =json_encode($hasil);
                    $detail->save(false);
                }
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " ValidasiKualifikasiPenyedia",
                    'content' => '<span class="text-success">' . Yii::t('yii2-ajaxcrud', 'Create') . ' ValidasiKualifikasiPenyedia ' . Yii::t('yii2-ajaxcrud', 'Success') . '</span>',
                    'footer' =>  Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Create More'), ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " ValidasiKualifikasiPenyedia",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            }
        } else {
            if ($model->load($request->post()) && $model->save()) {
                if ($model->template) {
                    $hasil = [];
                    $collect = TemplateChecklistEvaluasi::findOne($model->template);
                    $ar_element = explode(',', $collect->element);
                    foreach (json_decode($collect->detail->uraian, true) as $v) {
                        $c = ['uraian' => $v['uraian']];
                        foreach ($ar_element as $element) {
                            if ($element) {
                                $c[$element] = '';
                            }
                        }
                        $hasil[] = $c;
                    }
                    $detail = new ValidasiKualifikasiPenyediaDetail();
                    $detail->header_id = $model->id;
                    $detail->hasil = json_encode($hasil);
                    $detail->save(false);
                }
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
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " ValidasiKualifikasiPenyedia #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                if ($model->template) {
                    ValidasiKualifikasiPenyediaDetail::deleteAll(['header_id' => $model->id]);
                    $hasil = [];
                    $collect = TemplateChecklistEvaluasi::findOne($model->template);
                    $ar_element = explode(',', $collect->element);
                    foreach (json_decode($collect->detail->uraian, true) as $v) {
                        $c = ['uraian' => $v['uraian']];
                        foreach ($ar_element as $element) {
                            if ($element) { $c[$element] = '';}
                        }
                        $hasil[] = $c;
                    }
                    $detail = new ValidasiKualifikasiPenyediaDetail();
                    $detail->header_id = $model->id;
                    $detail->hasil =json_encode($hasil);
                    $detail->save(false);
                }
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "ValidasiKualifikasiPenyedia #" . $id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " ValidasiKualifikasiPenyedia #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            }
        } else {
            if ($model->load($request->post()) && $model->save()) {
                if ($model->template) {
                    ValidasiKualifikasiPenyediaDetail::deleteAll(['header_id' => $model->id]);
                    $hasil = [];
                    $collect = TemplateChecklistEvaluasi::findOne($model->template);
                    $ar_element = explode(',', $collect->element);
                    foreach (json_decode($collect->detail->uraian, true) as $v) {
                        $c = ['uraian' => $v['uraian']];
                        foreach ($ar_element as $element) {
                            if ($element) {
                                $c[$element] = '';
                            }
                        }
                        $hasil[] = $c;
                    }
                    $detail = new ValidasiKualifikasiPenyediaDetail();
                    $detail->header_id = $model->id;
                    $detail->hasil = json_encode($hasil);
                    $detail->save(false);
                }
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
        if (($model = ValidasiKualifikasiPenyedia::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
