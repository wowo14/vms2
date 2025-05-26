<?php
namespace app\controllers;
use Yii;
use yii\helpers\Html;
use yii\web\Controller;
use app\models\Attachment;
use yii\filters\VerbFilter;
use app\models\GaleryDasarhukum;
use app\models\GaleryDasarhukumSearch;
use yii\web\{Response, NotFoundHttpException};
class DasarhukumController extends Controller {
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
        $searchModel = new GaleryDasarhukumSearch();
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
                'title' => "GaleryDasarhukum #" . $id,
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
        $model = new GaleryDasarhukum();
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load($request->post()) && $model->save()) {
                if (!empty($_FILES['GaleryDasarhukum']['name']['foto']) || !empty($_FILES['GaleryDasarhukum']['name']['file_pdf'])) {
                    foreach ($_FILES['GaleryDasarhukum']['name'] as $index => $fileName) {
                        $fileType = $_FILES['GaleryDasarhukum']['type'][$index];
                        $fileTmpName = $_FILES['GaleryDasarhukum']['tmp_name'][$index];
                        $fileError = $_FILES['GaleryDasarhukum']['error'][$index];
                        $fileSize = $_FILES['GaleryDasarhukum']['size'][$index];
                        if (empty($fileName)) {
                            continue;
                        }
                        if ($fileError === 0) {
                            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                            $newFilename = uniqid() . '.' . $extension;
                            $destination = Yii::getAlias('@uploads/' . $newFilename);
                            // Hapus attachment lama jika ada
                            $att = Attachment::where(['type' => $index, 'jenis_dokumen' => 0, 'user_id' => $model->id])->one();
                            if ($att) {
                                $att->delete();
                            }
                            if (move_uploaded_file($fileTmpName, $destination)) {
                                if (in_array(strtolower($extension), ['png', 'jpg', 'jpeg', 'gif'])) {
                                    Yii::$app->tools->resizeImageToMaxSize($destination, 512 * 1024);
                                }
                                $attachment = new Attachment();
                                $attachment->name = $newFilename;
                                $attachment->uri = Yii::getAlias('@web/uploads/') . $newFilename;
                                $attachment->user_id = $model->id;
                                $attachment->mime = mime_content_type($destination) ?: $fileType;
                                $attachment->type = $index;
                                $attachment->size = filesize($destination) ?: $fileSize;
                                $attachment->jenis_dokumen = 0;
                                if ($attachment->save()) {
                                    if ($index === 'foto') {
                                        $model->foto = $attachment->uri;
                                    } elseif ($index === 'file_pdf') {
                                        $model->file_pdf = $attachment->uri;
                                    }
                                } else {
                                    Yii::$app->session->setFlash('error', 'Error saving attachment: ' . json_encode($attachment->errors));
                                }
                            } else {
                                Yii::$app->session->setFlash('error', "Failed to move uploaded file: $fileName");
                            }
                        } else {
                            Yii::$app->session->setFlash('error', "Error uploading file $fileName. Error code: $fileError");
                        }
                    }
                    $model->save(false); // false untuk skip validation kedua kalinya
                }
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " GaleryDasarhukum",
                    'content' => '<span class="text-success">' . Yii::t('yii2-ajaxcrud', 'Create') . ' GaleryDasarhukum ' . Yii::t('yii2-ajaxcrud', 'Success') . '</span>',
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), [
                        'class' => 'btn btn-default pull-left',
                        'data-dismiss' => 'modal'
                    ]) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Create More'), ['create'], [
                            'class' => 'btn btn-primary',
                            'role' => 'modal-remote'
                        ])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " GaleryDasarhukum",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            }
        } else {
            if ($model->load($request->post()) && $model->save()) {
                if (!empty($_FILES['GaleryDasarhukum']['name']['foto']) || !empty($_FILES['GaleryDasarhukum']['name']['file_pdf'])) {
                    foreach ($_FILES['GaleryDasarhukum']['name'] as $index => $fileName) {
                        $fileType = $_FILES['GaleryDasarhukum']['type'][$index];
                        $fileTmpName = $_FILES['GaleryDasarhukum']['tmp_name'][$index];
                        $fileError = $_FILES['GaleryDasarhukum']['error'][$index];
                        $fileSize = $_FILES['GaleryDasarhukum']['size'][$index];
                        if (empty($fileName)) {
                            continue;
                        }
                        if ($fileError === 0) {
                            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                            $newFilename = uniqid() . '.' . $extension;
                            $destination = Yii::getAlias('@uploads/' . $newFilename);
                            // Hapus attachment lama jika ada
                            $att = Attachment::where(['type' => $index, 'jenis_dokumen' => 0, 'user_id' => $model->id])->one();
                            if ($att) {
                                $att->delete();
                            }
                            if (move_uploaded_file($fileTmpName, $destination)) {
                                if (in_array(strtolower($extension), ['png', 'jpg', 'jpeg', 'gif'])) {
                                    Yii::$app->tools->resizeImageToMaxSize($destination, 512 * 1024);
                                }
                                $attachment = new Attachment();
                                $attachment->name = $newFilename;
                                $attachment->uri = Yii::getAlias('@web/uploads/') . $newFilename;
                                $attachment->user_id = $model->id;
                                $attachment->mime = mime_content_type($destination) ?: $fileType;
                                $attachment->type = $index;
                                $attachment->size = filesize($destination) ?: $fileSize;
                                $attachment->jenis_dokumen = 0;
                                if ($attachment->save()) {
                                    if ($index === 'foto') {
                                        $model->foto = $attachment->uri;
                                    } elseif ($index === 'file_pdf') {
                                        $model->file_pdf = $attachment->uri;
                                    }
                                } else {
                                    Yii::$app->session->setFlash('error', 'Error saving attachment: ' . json_encode($attachment->errors));
                                }
                            } else {
                                Yii::$app->session->setFlash('error', "Failed to move uploaded file: $fileName");
                            }
                        } else {
                            Yii::$app->session->setFlash('error', "Error uploading file $fileName. Error code: $fileError");
                        }
                    }
                    $model->save(false); // false untuk skip validation kedua kalinya
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
        $oldmodel = clone $model;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load($request->post()) && $model->save()) {
                if (!empty($_FILES['GaleryDasarhukum']['name']['foto']) || !empty($_FILES['GaleryDasarhukum']['name']['file_pdf'])) {
                    foreach ($_FILES['GaleryDasarhukum']['name'] as $index => $fileName) {
                        $fileType = $_FILES['GaleryDasarhukum']['type'][$index];
                        $fileTmpName = $_FILES['GaleryDasarhukum']['tmp_name'][$index];
                        $fileError = $_FILES['GaleryDasarhukum']['error'][$index];
                        $fileSize = $_FILES['GaleryDasarhukum']['size'][$index];
                        if (empty($fileName)) {
                            continue;
                        }
                        if ($fileError === 0) {
                            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                            $newFilename = uniqid() . '.' . $extension;
                            $destination = Yii::getAlias('@uploads/' . $newFilename);
                            // Hapus attachment lama jika ada
                            $att = Attachment::where(['type' => $index, 'jenis_dokumen' => 0, 'user_id' => $model->id])->one();
                            if ($att) {
                                $att->delete();
                            }
                            if (move_uploaded_file($fileTmpName, $destination)) {
                                if (in_array(strtolower($extension), ['png', 'jpg', 'jpeg', 'gif'])) {
                                    Yii::$app->tools->resizeImageToMaxSize($destination, 512 * 1024);
                                }
                                $attachment = new Attachment();
                                $attachment->name = $newFilename;
                                $attachment->uri = Yii::getAlias('@web/uploads/') . $newFilename;
                                $attachment->user_id = $model->id;
                                $attachment->mime = mime_content_type($destination) ?: $fileType;
                                $attachment->type = $index;
                                $attachment->size = filesize($destination) ?: $fileSize;
                                $attachment->jenis_dokumen = 0;
                                if ($attachment->save()) {
                                    if ($index === 'foto') {
                                        $model->foto = $attachment->uri;
                                    } elseif ($index === 'file_pdf') {
                                        $model->file_pdf = $attachment->uri;
                                    }
                                } else {
                                    Yii::$app->session->setFlash('error', 'Error saving attachment: ' . json_encode($attachment->errors));
                                }
                            } else {
                                Yii::$app->session->setFlash('error', "Failed to move uploaded file: $fileName");
                            }
                        } else {
                            Yii::$app->session->setFlash('error', "Error uploading file $fileName. Error code: $fileError");
                        }
                    }
                    $model->save(false); // false untuk skip validation kedua kalinya
                } else {
                    $model->foto = $oldmodel->foto;
                    $model->file_pdf = $oldmodel->file_pdf;
                    $model->save(false);
                }
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "GaleryDasarhukum #" . $id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " GaleryDasarhukum #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            }
        } else {
            if ($model->load($request->post()) && $model->save()) {
                if (!empty($_FILES['GaleryDasarhukum']['name']['foto']) || !empty($_FILES['GaleryDasarhukum']['name']['file_pdf'])) {
                    foreach ($_FILES['GaleryDasarhukum']['name'] as $index => $fileName) {
                        $fileType = $_FILES['GaleryDasarhukum']['type'][$index];
                        $fileTmpName = $_FILES['GaleryDasarhukum']['tmp_name'][$index];
                        $fileError = $_FILES['GaleryDasarhukum']['error'][$index];
                        $fileSize = $_FILES['GaleryDasarhukum']['size'][$index];
                        if (empty($fileName)) {
                            continue;
                        }
                        if ($fileError === 0) {
                            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                            $newFilename = uniqid() . '.' . $extension;
                            $destination = Yii::getAlias('@uploads/' . $newFilename);
                            // Hapus attachment lama jika ada
                            $att = Attachment::where(['type' => $index, 'jenis_dokumen' => 0, 'user_id' => $model->id])->one();
                            if ($att) {
                                $att->delete();
                            }
                            if (move_uploaded_file($fileTmpName, $destination)) {
                                if (in_array(strtolower($extension), ['png', 'jpg', 'jpeg', 'gif'])) {
                                    Yii::$app->tools->resizeImageToMaxSize($destination, 512 * 1024);
                                }
                                $attachment = new Attachment();
                                $attachment->name = $newFilename;
                                $attachment->uri = Yii::getAlias('@web/uploads/') . $newFilename;
                                $attachment->user_id = $model->id;
                                $attachment->mime = mime_content_type($destination) ?: $fileType;
                                $attachment->type = $index;
                                $attachment->size = filesize($destination) ?: $fileSize;
                                $attachment->jenis_dokumen = 0;
                                if ($attachment->save()) {
                                    if ($index === 'foto') {
                                        $model->foto = $attachment->uri;
                                    } elseif ($index === 'file_pdf') {
                                        $model->file_pdf = $attachment->uri;
                                    }
                                } else {
                                    Yii::$app->session->setFlash('error', 'Error saving attachment: ' . json_encode($attachment->errors));
                                }
                            } else {
                                Yii::$app->session->setFlash('error', "Failed to move uploaded file: $fileName");
                            }
                        } else {
                            Yii::$app->session->setFlash('error', "Error uploading file $fileName. Error code: $fileError");
                        }
                    }
                    $model->save(false); // false untuk skip validation kedua kalinya
                } else {
                    $model->foto = $oldmodel->foto;
                    $model->file_pdf = $oldmodel->file_pdf;
                    $model->save(false);
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
        $model=$this->findModel($id);
        foreach ($model->attachments as $attachment) {
            $attachment->delete(); // Ini akan memanggil beforeDelete()
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
            foreach ($model->attachments as $attachment) {
                $attachment->delete(); // Ini akan memanggil beforeDelete()
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
        if (($model = GaleryDasarhukum::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
