<?php
namespace app\controllers;
use app\models\{Contacts,Pegawai, PegawaiSearch};
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\{NotFoundHttpException, Response};
class PegawaiController extends Controller {
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
        $searchModel = new PegawaiSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionTes(){
        $model=new Contacts();
        $request=Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            print_r($model);
        }else{
            return $this->render('_dialog', [
                'model' => $model
            ]);
        }
    }
    public function actionList_datatable() // datatables server side
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $query = Contacts::where(['is_active' => 1]);
        $columns=['id','nama'];
        $query->select($columns);
        $totalCount = $query->count();
        $page = Yii::$app->request->get('start', 0);
        $pageSize = Yii::$app->request->get('length', 10);
        $order = [];
        if (Yii::$app->request->get('order', []) !== []) {
            $orderColumn = Yii::$app->request->get('order')[0]['column'];
            $orderDir = Yii::$app->request->get('order')[0]['dir'];
            $orderColumns = Yii::$app->request->get('columns');
            $orderColumnName = $orderColumns[$orderColumn]['data'];
            $order[$orderColumnName] = ($orderDir === 'asc') ? SORT_ASC : SORT_DESC;
        }
        $query->orderBy($order);
        $searchValue = Yii::$app->request->get('search')['value']??null;
        if (!empty($searchValue)) {
            $query->andWhere(['or',
                ['like', 'id', $searchValue],
                ['like', 'nama', $searchValue]
            ]);
        }
        $filteredCount = $query->count();
        $query->offset($page)->limit($pageSize);
        $contacts = $query->asArray()->all();
        return [
            'draw' => Yii::$app->request->get('draw'),
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $filteredCount,
            'data' => $contacts,
        ];
    }
    public function actionListpegawai_datatable() // datatables server side
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $query = Pegawai::find();
        $columns=['id','nik'];
        $query->select($columns);
        $totalCount = $query->count();
        $page = Yii::$app->request->get('start', 0);
        $pageSize = Yii::$app->request->get('length', 10);
        $order = [];
        if (Yii::$app->request->get('order', []) !== []) {
            $orderColumn = Yii::$app->request->get('order')[0]['column'];
            $orderDir = Yii::$app->request->get('order')[0]['dir'];
            $orderColumns = Yii::$app->request->get('columns');
            $orderColumnName = $orderColumns[$orderColumn]['data'];
            $order[$orderColumnName] = ($orderDir === 'asc') ? SORT_ASC : SORT_DESC;
        }
        $query->orderBy($order);
        $searchValue = Yii::$app->request->get('search')['value'] ?? null;
        if (!empty($searchValue)) {
            $query->andWhere(['or',
                ['like', 'id', $searchValue],
                ['like', 'nik', $searchValue]
            ]);
        }
        $filteredCount = $query->count();
        $query->offset($page)->limit($pageSize);
        $contacts = $query->asArray()->all();
        return [
            'draw' => Yii::$app->request->get('draw'),
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $filteredCount,
            'data' => $contacts,
        ];
    }
    public function actionNama($q = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (!is_null($q)) {
            $query = Contacts::where(['like', 'nama', $q])
                ->asArray()
                // ->limit(20)
                ->orderBy(['nama' => SORT_ASC])
                ->all();
            foreach ($query as $contact) {
                $out[] = ['id' => $contact['id'],'nik'=>$contact['nik'],'nip'=>$contact['nip'], 'text' => $contact['nama']];
            }
        }
        return ['results' => $out];
    }
    public function actionView($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "Pegawai #" . $id,
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
        $model = new Pegawai();
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " Pegawai",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Create'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " Pegawai",
                    'content' => '<span class="text-success">' . Yii::t('yii2-ajaxcrud', 'Create') . ' Pegawai ' . Yii::t('yii2-ajaxcrud', 'Success') . '</span>',
                    'footer' =>  Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Create More'), ['create'], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " Pegawai",
                    'content' => $this->renderAjax('create', [
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
                return $this->render('create', ['model' => $model,]);
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
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " Pegawai #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Pegawai #" . $id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " Pegawai #" . $id,
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
        if (($model = Pegawai::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
