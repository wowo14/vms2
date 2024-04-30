<?php
namespace app\controllers;
use app\models\{KodeRekening, ProgramKegiatan, ProgramKegiatanSearch};
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\{Response, NotFoundHttpException};class ProgramkegiatanController extends Controller {
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
        $searchModel = new ProgramKegiatanSearch();
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
                'title' => "ProgramKegiatan #" . $id,
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
    public function actionTree() {
        $filePath = Yii::getAlias('@runtime' . '/programkegiatantree.json');
        $is_validJson = false;
        if (file_exists($filePath)) {
            $jsonContents = file_get_contents($filePath);
            if (!empty($jsonContents)) {
                $jsonData = json_decode($jsonContents);
                if ($jsonData !== null) {
                    $is_validJson = true;
                }
            }
        }
        if ($is_validJson) {
            return file_get_contents($filePath);
        } else {
            $data2 = ProgramKegiatan::getTree();
            file_put_contents($filePath, json_encode($data2, JSON_UNESCAPED_UNICODE));
            chmod($filePath, 0644);
            return json_encode($data2, JSON_UNESCAPED_UNICODE);
        }
    }
    public function actionImport() {
        if (isset($_POST)) {
            if (!empty($_FILES)) {
                $tempFile = $_FILES['ProgramKegiatan']['tmp_name']['file'];
                $fileTypes = array('xls', 'xlsx');
                $fileParts = pathinfo($_FILES['ProgramKegiatan']['name']['file']);
                if (in_array(@$fileParts['extension'], $fileTypes)) {
                    if ($fileParts['extension'] == 'xlsx') {
                        $inputFileType = 'Xlsx';
                    } else {
                        $inputFileType = 'Xls';
                    }
                    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                    $spreadsheet = $reader->load($tempFile);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $highestRow = $worksheet->getHighestRow();
                    $highestColumn = $worksheet->getHighestColumn();
                    $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
                    $inserted = 0;
                    $errorCount = 0;
                    for ($row = 2; $row <= $highestRow; ++$row) {
                        $model2 = new ProgramKegiatan;
                        $model2->id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                        $model2->code = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                        $model2->desc = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                        $model2->type = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                        $model2->tahun_anggaran = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                        $model2->is_active = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                        try {
                            if ($model2->save(false)) {
                                $inserted++;
                            }
                        } catch (\yii\db\Exception $e) {
                            $errorCount++;
                            Yii::$app->session->setFlash('error', "($errorCount)Error saving model");
                        }
                    }
                    Yii::$app->session->setFlash('success', ($inserted) . ' row inserted');
                } else {
                    Yii::$app->session->setFlash('warning', "Wrong file type (xlsx, xls) only");
                }
            }
            $searchModel = new ProgramKegiatanSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            // $dataProvider->pagination->pageSize=10;
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]);
        }
    }
    public function actionChild($param) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        $selected = '';
        if (isset($_POST['depdrop_parents']) && (end($_POST['depdrop_parents'])) !== null) {
            $tahun = end($_POST['depdrop_parents']);
            $data = [];
            switch ($param) {
                case 'tahun':
                    $data = ProgramKegiatan::find()
                        ->where(['type' => 'program', 'tahun_anggaran' => $tahun, 'is_active' => 1])
                        ->asArray()
                        ->all();
                    break;
                case 'program':
                    $data = ProgramKegiatan::find()
                        ->where(['type' => 'kegiatan', 'tahun_anggaran' => $tahun, 'is_active' => 1])
                        ->asArray()
                        ->all();
                    break;
                case 'koderekening':
                    $rek = new KodeRekening();
                    $data = collect($rek->coacode)
                        ->filter(fn ($v) => $v->tahun_anggaran == $tahun)
                        ->map(fn ($el) => ['code' => $el['kode'], 'desc' => $el['rekening']])
                        ->toArray();
                    break;
            }
            foreach ($data as $i => $account) {
                $out[] = ['id' => $account['code'], 'name' => $account['code'] . ' - ' . $account['desc']];
                $selected = ($i == 0) ? $account['code'] : $selected;
            }
        }
        return ['output' => $out, 'selected' => $selected];
    }
    public function actionCopyto() {
        $request = Yii::$app->request;
        $model = new ProgramKegiatan();
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                $optionsTahun = $model::optiontahunanggaran();
                $filterExist = collect($optionsTahun)->filter(function ($th) use ($model) {
                    return $model::where(['tahun_anggaran' => $th])->exists();
                });
                $filternon = collect($optionsTahun)->filter(function ($th) use ($model) {
                    return !$model::where(['tahun_anggaran' => $th])->exists();
                });
                $opttahun = ['from' => $filterExist, 'to' => $filternon];
                return [
                    'title' => "Copy to",
                    'content' => $this->renderAjax('_formcopyto', [
                        'model' => $model, 'opttahun' => $opttahun
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            }
            if ($model->load($request->post())) {
                $req = $request->post('ProgramKegiatan')['tahun_anggaran']; // array from multipleinput
                $res = [];
                foreach ($req as $key => $val) {
                    $res = $model::copyto($val['from'], $val['to']);
                }
                if ($res['status'] == 'success') {
                    return [
                        'forceReload' => '#crud-datatable' . $model->hash . '-pjax',
                        'title' => $res['status'],
                        'content' => $res['message'],
                        // 'forceClose' => true,
                        'footer' => ''
                    ];
                } else {
                    return [
                        'title' => $res['status'],
                        'content' => $res['message'],
                    ];
                }
                Yii::$app->session->setFlash($res['status'], $res['message']);
            }
        }
    }
    public function actionCreate() {
        $request = Yii::$app->request;
        $model = new ProgramKegiatan();
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " ProgramKegiatan",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Create'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " ProgramKegiatan",
                    'content' => '<span class="text-success">' . Yii::t('yii2-ajaxcrud', 'Create') . ' ProgramKegiatan ' . Yii::t('yii2-ajaxcrud', 'Success') . '</span>',
                    'footer' =>  Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Create More'), ['create'], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " ProgramKegiatan",
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
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " ProgramKegiatan #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "ProgramKegiatan #" . $id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " ProgramKegiatan #" . $id,
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
        if (($model = ProgramKegiatan::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
