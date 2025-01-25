<?php
namespace app\controllers;
use app\models\ProgramKegiatan;
use Yii;
use app\models\Rab;
use app\models\RabSearch;
use yii\web\{Response, NotFoundHttpException};
use yii\filters\VerbFilter;
use yii\helpers\Html;
class RabController extends Controller {
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
        $searchModel = new RabSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionImport() {
        $request = Yii::$app->request;
        if ($request->isPost) {
            if (!empty($_FILES)) {
                if (is_array($_FILES['Rab']['name'])) {
                    foreach ($_FILES['Rab']['name'] as $key => $fileName) {
                        $fileParts = pathinfo($fileName);
                    }
                }
                if (is_string($_FILES['Rab']['name'])) {
                    $fileParts = pathinfo($_FILES['Rab']['name']);
                }
                if (is_string($_FILES['Rab']['tmp_name'])) {
                    $tempFile = $_FILES['Rab']['tmp_name'];
                }
                if (is_array($_FILES['Rab']['tmp_name'])) {
                    foreach ($_FILES['Rab']['tmp_name'] as $key => $val) {
                        $tempFile = $val;
                    }
                }
                $fileTypes = array('xls', 'xlsx');
                if (in_array(@$fileParts['extension'], $fileTypes)) {
                    $fileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($tempFile);
                    $objPHPExcelReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($fileType);
                    $spreadsheet = $objPHPExcelReader->load($tempFile);
                    $inserted = 0;
                    $errorCount = 0;
                    $parentSheet = $spreadsheet->getActiveSheet();
                    $highestRow = $parentSheet->getHighestRow();
                    $highestColumn = $parentSheet->getHighestColumn();
                    $adata = [];
                    for ($childRow = 2; $childRow <= $highestRow; ++$childRow) {
                        $inserted++;
                        $adata[] = [
                            'tahun_anggaran' => $parentSheet->getCellByColumnAndRow(2, $childRow)->getValue(),
                            'kode_program' => $parentSheet->getCellByColumnAndRow(3, $childRow)->getValue(),
                            'kode_kegiatan' => $parentSheet->getCellByColumnAndRow(4, $childRow)->getValue(),
                            'kode_rekening' => $parentSheet->getCellByColumnAndRow(5, $childRow)->getValue(),
                            'uraian_anggaran' => $parentSheet->getCellByColumnAndRow(6, $childRow)->getValue(),
                            'jumlah_anggaran' => $parentSheet->getCellByColumnAndRow(7, $childRow)->getValue(),
                            'nama_program' => ProgramKegiatan::findOne(['tahun_anggaran' => $parentSheet->getCellByColumnAndRow(2, $childRow)->getValue(), 'code' => $parentSheet->getCellByColumnAndRow(3, $childRow)->getValue()])->desc,
                            'nama_kegiatan' => ProgramKegiatan::findOne(['tahun_anggaran' => $parentSheet->getCellByColumnAndRow(2, $childRow)->getValue(), 'code' => $parentSheet->getCellByColumnAndRow(4, $childRow)->getValue()])->desc,
                            'sumber_dana'=>1,
                        ];
                    }
                    if (!empty($adata)) {
                        Yii::$app->db->createCommand()
                            ->batchInsert(
                                'rab',
                                ['tahun_anggaran', 'kode_program', 'kode_kegiatan', 'kode_rekening', 'uraian_anggaran', 'jumlah_anggaran', 'nama_program', 'nama_kegiatan', 'sumber_dana'],
                                $adata
                            )
                            ->execute();
                    }
                    //cache flush
                    Rab::invalidatecache('tag_' . Rab::getModelname());
                    Yii::$app->session->setFlash('success', ($inserted) . ' row inserted');
                    return $this->redirect('index');
                }
            }
        }
    }
    public function actionView($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "Rab #" . $id,
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
        $model = new Rab();
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " Rab",
                    'content' => '<span class="text-success">' . Yii::t('yii2-ajaxcrud', 'Create') . ' Rab ' . Yii::t('yii2-ajaxcrud', 'Success') . '</span>',
                    'footer' =>  Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Create More'), ['create'], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " Rab",
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
            if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Rab #" . $id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " Rab #" . $id,
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
        if (($model = Rab::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
