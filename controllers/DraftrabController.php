<?php
namespace app\controllers;
use app\models\{DraftRab, DraftRabDetail, DraftRabSearch};
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\{Response, NotFoundHttpException};class DraftrabController extends Controller {
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
    public function actionBukurekap() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $hierarchy = collect();
        foreach (DraftRab::find()->cache(DraftRab::cachetime(), DraftRab::settagdep('tag_' . DraftRab::getModelname()))->all() as $item) {
            $programKey = $item->kode_program;
            $kegiatanKey = $item->kode_kegiatan;
            $rekeningKey = $item->kode_rekening;
            $hierarchy->put($programKey, collect([
                'kode' => $item->kode_program,
                'uraian' => $item->nama_program,
                'volume' => '',
                'satuan' => '',
                'harga_satuan' => '',
                'jumlah' => $item->jumlah_anggaran,
                'sub' => collect(),
            ]));
            if (!$hierarchy[$programKey]['sub']->has($kegiatanKey)) {
                $hierarchy[$programKey]['sub']->put($kegiatanKey, collect([
                    'kode' => $item->kode_kegiatan,
                    'uraian' => $item->nama_kegiatan,
                    'volume' => '',
                    'satuan' => '',
                    'harga_satuan' => '',
                    'jumlah' => $item->jumlah_anggaran,
                    'sub' => collect(),
                ]));
            }
            if (!$hierarchy[$programKey]['sub'][$kegiatanKey]['sub']->has($rekeningKey)) {
                $hierarchy[$programKey]['sub'][$kegiatanKey]['sub']->put($rekeningKey, collect([
                    'kode' => $item->kode_rekening,
                    'uraian' => $item->uraian_anggaran,
                    'volume' => '',
                    'satuan' => '',
                    'harga_satuan' => '',
                    'jumlah' => $item->jumlah_anggaran,
                    'sub' => collect(),
                ]));
            }
            // Add details to the hierarchy
            foreach ($item->details as $detail) {
                $detailNode = collect([
                    'kode' => $detail->produk_id,
                    'uraian' => $detail->produk_id,
                    'volume' => $detail->volume,
                    'satuan' => $detail->satuan,
                    'harga_satuan' => $detail->harga_satuan,
                    'jumlah' => round($detail->harga_satuan * $detail->volume, 2),
                ]);
                $hierarchy[$programKey]['sub'][$kegiatanKey]['sub'][$rekeningKey]['sub']->push($detailNode);
            }
        }
        echo json_encode($hierarchy->toArray(), JSON_PRETTY_PRINT);
    }
    public function actionDetails() {
        if (isset($_POST['expandRowKey'])) {
            $modelstaff = DraftRab::findOne($_POST['expandRowKey']);
            $query = DraftRabDetail::where(['rab_id' => $_POST['expandRowKey']]);
            $model = new ActiveDataProvider([
                'query' => $query,
                'sort' => [
                    'defaultOrder' => [
                        'id' => SORT_DESC
                    ]
                ]
            ]);
            $model->pagination = false;
            return $this->renderAjax('expand', ['dataProvider' => $model, 'modelstaff' => $modelstaff, 'iddetail' => $_POST['expandRowKey']]);
        } else {
            return '<div class="alert alert-danger">No data found</div>';
        }
    }
    public function actionRekap() {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            if ($request->isGet) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'title' => "Rekap Tahun",
                    'content' => $this->renderAjax('_frm_tahun'),
                    'footer' => ''
                ];
            }
        } elseif ($request->isPost) {
            $model = DraftRab::where(['tahun_anggaran' => $request->post('tahun')])->all();
            if (!$model) {
                throw new NotFoundHttpException('Data ' . $request->post('tahun') . ' Tidak Ditemukan');
            }
            return $this->render('rekap', ['model' => $model]);
        }
    }
    public function actionAjukan($id) {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if ($request->isAjax) {
            if ($request->isGet) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'title' => "Ajukan Draft",
                    'content' => $this->renderAjax('_frm_ajukan', ['model' => $model]),
                    'footer' => ''
                ];
            }
        }
    }
    public function actionIndex() {
        $searchModel = new DraftRabSearch();
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
                'title' => "DraftRab #" . $id,
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
        $model = new DraftRab();
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " DraftRab",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Create'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " DraftRab",
                    'content' => '<span class="text-success">' . Yii::t('yii2-ajaxcrud', 'Create') . ' DraftRab ' . Yii::t('yii2-ajaxcrud', 'Success') . '</span>',
                    'footer' =>  Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Create More'), ['create'], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " DraftRab",
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
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " DraftRab #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "DraftRab #" . $id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " DraftRab #" . $id,
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
        if (($model = DraftRab::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
