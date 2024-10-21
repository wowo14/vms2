<?php
namespace app\controllers;
use app\models\{Negosiasi,PenawaranPengadaan,PenawaranPengadaanSearch};
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\{ArrayHelper,Html};
use yii\web\{ForbiddenHttpException,Response, NotFoundHttpException};
class PenawaranpenyediaController extends Controller {
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
        $searchModel = new PenawaranPengadaanSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionView($id) {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        // if(!$model->incompanygrouporadmin){
        //     throw new ForbiddenHttpException(Yii::t('yii2-ajaxcrud', 'You are not allowed to perform this action.'));
        // }
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "PenawaranPengadaan #" . $id,
                'content' => $this->renderAjax('view', [
                    'model' => $model,
                ]),
                'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                    Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
            ];
        } else {
            return $this->render('view', [
                'model' => $model,
            ]);
        }
    }
    public function actionDetailnego() {
        if (isset($_POST['expandRowKey'])) {
            $model = $this->findModel($_POST['expandRowKey']);
            // if(!$model->incompanygrouporadmin){
            //     throw new ForbiddenHttpException(Yii::t('yii2-ajaxcrud', 'You are not allowed to perform this action.'));
            // }
            $query = Negosiasi::where(['penawaran_id' => $model->id]);
            $model = new ActiveDataProvider([
                'query' => $query,
                // 'sort' => false,
            ]);
            $model->pagination = false;
            return $this->renderAjax('/penawaranpenyedia/_detailnego', ['dataProvider' => $model]);
        } else {
            return '<div class="alert alert-danger">No data found</div>';
        }
    }
    public function actionNego($id){
        $request = Yii::$app->request;
        $model = new Negosiasi();
        $penawaran=$this->findModel($id);
        // $paketpengadaan=$penawaran->paketpengadaan;
        // if(!$penawaran->incompanygrouporadmin){
        //     throw new ForbiddenHttpException(Yii::t('yii2-ajaxcrud', 'You are not allowed to perform this action.'));
        // }
        if($penawaran->paketpengadaan->pemenang){
            Yii::$app->session->setFlash('warning', 'Pemenang sudah ditentukan');
            return $this->redirect('index');
        }
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($model->load($request->post()) ){
                if($model->save()){
                    // $this->redirect($request->referrer);
                    return $this->asJson([
                        'success' => true,
                        'message' => 'sukses input nilai nego',
                    ]);
                }else{
                    return [
                        'title' => 'Error Save',
                        'content' => json_encode($model->getErrors()),
                        'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal'])
                    ];
                }
            }else{
                return [
                    'title' => "Nego Penawaran #" . $penawaran->paketpengadaan->nomornamapaket,
                    'content' => $this->renderAjax('_frm_nego', ['model' => $model,'penawaran'=>$penawaran]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                    (!$penawaran->paketpengadaan->details?Html::button(Yii::t('yii2-ajaxcrud', 'Create'), ['class' => 'btn btn-primary', 'type' => 'submit']):'')
                ];
            }
        }
    }
    public function actionCreate() {
        $request = Yii::$app->request;
        $model = new PenawaranPengadaan();
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load($request->post()) && $model->save(false)) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " PenawaranPengadaan",
                    'content' => '<span class="text-success">' . Yii::t('yii2-ajaxcrud', 'Create') . ' PenawaranPengadaan ' . Yii::t('yii2-ajaxcrud', 'Success') . '</span>',
                    'footer' =>  Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Create More'), ['create'], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " PenawaranPengadaan",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            }
        } else {
            if ($model->load($request->post()) && $model->save(false)) {
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
        // if(!$model->incompanygrouporadmin){
        //     throw new ForbiddenHttpException(Yii::t('yii2-ajaxcrud', 'You are not allowed to perform this action.'));
        // }
        if($model->paketpengadaan->pemenang){
            throw new ForbiddenHttpException(Yii::t('yii2-ajaxcrud', 'Pemenang sudah ditentukan'));
        }
        $oldlampiran_penawaran = $model->lampiran_penawaran;
        $oldlampiran_penawaran_harga = $model->lampiran_penawaran_harga;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load($request->post())) {
                if (file_exists(Yii::getAlias('@uploads') . $oldlampiran_penawaran) && !empty($oldlampiran_penawaran) && ($model->isBase64Encoded($model->lampiran_penawaran))) {
                    unlink(Yii::getAlias('@uploads') . $oldlampiran_penawaran);
                }
                if (file_exists(Yii::getAlias('@uploads') . $oldlampiran_penawaran_harga) && !empty($oldlampiran_penawaran_harga) && ($model->isBase64Encoded($model->lampiran_penawaran_harga))) {
                    unlink(Yii::getAlias('@uploads') . $oldlampiran_penawaran_harga);
                }
                $model->save(false);
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "PenawaranPengadaan #" . $id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " PenawaranPengadaan #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            }
        } else {
            if ($model->load($request->post())) {
                if (file_exists(Yii::getAlias('@uploads') . $oldlampiran_penawaran) && !empty($oldlampiran_penawaran) && ($model->isBase64Encoded($model->lampiran_penawaran))) {
                    unlink(Yii::getAlias('@uploads') . $oldlampiran_penawaran);
                }
                if (file_exists(Yii::getAlias('@uploads') . $oldlampiran_penawaran_harga) && !empty($oldlampiran_penawaran_harga) && ($model->isBase64Encoded($model->lampiran_penawaran_harga))) {
                    unlink(Yii::getAlias('@uploads') . $oldlampiran_penawaran_harga);
                }
                $model->save(false);
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
        $model=$this->findModel($id);
        // if(!$model->incompanygrouporadmin){
        //     throw new ForbiddenHttpException(Yii::t('yii2-ajaxcrud', 'You are not allowed to perform this action.'));
        // }
        if($model->paketpengadaan->pemenang){
                Yii::$app->session->setFlash('warning', 'PaketPengadaan Sudah ada Pemenang');
                return $this->redirect('index');
            }
        $model->delete();
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
            // if(!$model->incompanygrouporadmin){
            //     continue;
            // }
            if($model->paketpengadaan->pemenang){
                Yii::$app->session->setFlash('warning', 'PaketPengadaan Sudah ada Pemenang');
                return $this->redirect('index');
            }
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
        if (($model = PenawaranPengadaan::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
