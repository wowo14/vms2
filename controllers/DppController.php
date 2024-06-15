<?php
namespace app\controllers;
use app\models\{ReviewDpp, Dpp, DppSearch, PaketPengadaanDetails, PenawaranPengadaan, TemplateChecklistEvaluasi, ValidasiKualifikasiPenyedia};
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;
use yii\web\{Response, NotFoundHttpException};
class DppController extends Controller {
    private $_pageSize = 1;
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'bulkdelete' => ['post'],
                ],
            ],
        ];
    }
    public function actionPemenang($idvendor,$idpaket){// id vendor
        $model = Dpp::where(['paket_id'=>$idpaket])->one();
        $model->paketpengadaan->pemenang=$idvendor;
        $model->paketpengadaan->save();
        Yii::$app->session->setFlash('success', 'Pemenang berhasil diterapkan');
        return $this->goBack(Yii::$app->request->referrer ?: ['//dpp/index']);
    }
    public function actionDetailpaket() {
        if (isset($_POST['expandRowKey'])) {
            $model = $this->findModel($_POST['expandRowKey']);
            $query = PaketPengadaanDetails::where(['paket_id' => $model->paket_id]);
            $model = new ActiveDataProvider([
                'query' => $query,
                'sort' => false,
            ]);
            $model->pagination = false;
            return $this->renderAjax('/paketpengadaan/expand', ['dataProvider' => $model]);
        } else {
            return '<div class="alert alert-danger">No data found</div>';
        }
    }
    public function actionIndex() {
        $searchModel = new DppSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionTab($id) {
        $model = $this->findModel($id);
        return $this->render('tab', [
            'model' => $model
        ]);
    }
    public function actionListpemenang($params) { //[paket_pengadaan_id]
        $tmp = TemplateChecklistEvaluasi::where(['template' => 'Ceklist_Evaluasi_Kesimpulan'])->one();
        $lolos = ValidasiKualifikasiPenyedia::find()->joinWith('detail')
            ->where(['template' => $tmp->id, 'paket_pengadaan_id' => $params['paket_pengadaan_id']])->asArray()->all();
        $filtered = collect($lolos)->where('detail.hasil', '[{"uraian":"Catatan Oleh Pejabat Pengadaan","komentar":"Lolos Administrasi Validasi Dokumen","sesuai":""}]');
        $mapPenawaran = $filtered->map(function ($e) {
            return PenawaranPengadaan::where(['paket_id' => $e['paket_pengadaan_id'], 'penyedia_id' => $e['penyedia_id']])->one();
        })->sortBy('nilai_penawaran')->values()->all();// nilai penawaran terendah
        return $mapPenawaran;
    }
    public function actionView($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "Dpp #" . $id,
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
    public function actionAssign() {
        $request = Yii::$app->request;
        $pks = explode(',', $request->post('pks'));
        foreach ($pks as $pk) {
            $model = $this->findModel($pk);
            if ($model::isAdmin()) {
                $model->pejabat_pengadaan = $request->post('pejabat_pengadaan');
                $model->save(false);
            } elseif ($model) {
                $model->pejabat_pengadaan = $request->post('pejabat_pengadaan');
                $model->save(false);
            } else {
                Yii::$app->session->setFlash('warning', 'Sudah ditugaskan');
            }
        }
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            return $this->redirect(['index']);
        }
    }
    public function actionAssignadmin() {
        $request = Yii::$app->request;
        $pks = explode(',', $request->post('pks'));
        foreach ($pks as $pk) {
            $model = $this->findModel($pk);
            if ($model::isAdmin()) {
                $model->admin_pengadaan = $request->post('admin_pengadaan');
                $model->save(false);
            } elseif ($model) {
                $model->admin_pengadaan = $request->post('admin_pengadaan');
                $model->save(false);
            } else {
                Yii::$app->session->setFlash('warning', 'Sudah ditugaskan');
            }
        }
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            return $this->redirect(['index']);
        }
    }
    public function actionReject($id) {
        $request = Yii::$app->request;
        if ($request->isGet) {
            $paket = $this->findModel($id)->paketpengadaan;
            return $this->render('_frm_reject', ['model' => $paket]);
        } elseif ($request->isPost) {
            $model = $this->findModel($id)->paketpengadaan;
            if ($model->load($request->post())) {
                if ($model->tanggal_reject && $model->alasan_reject) {
                    $model->save();
                    Dpp::invalidatecache('tag_' . Dpp::getModelname());
                    $model::invalidatecache('tag_' . $model::getModelname());
                } else {
                    throw new BadRequestHttpException('Data Tidak Lengkap');
                }
                return $this->redirect(['index']);
            }
        }
    }
    public function actionApprove($id) {
        $model = $this->findModel($id);
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Approve Dpp",
                    'content' => $this->renderAjax('_formapprove', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Approve'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            } elseif ($model->load($request->post())) {
                if ($model->nomor_persetujuan && $model->nomor_dpp) {
                    $model->is_approved = 1;
                    $model->save();
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => "Approve Dpp",
                        'content' => '<span class="text-success">Approve Dpp ' . Yii::t('yii2-ajaxcrud', 'Success') . '</span>',
                        'footer' =>  Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal'])
                    ];
                } else {
                    throw new BadRequestHttpException('nomor_persetujuan harus diisi');
                }
            }
        }
    }
    public function actionCetak($id) {
        $model = $this->findModel($id);
        $pdf = Yii::$app->pdf;
        $pdf->content = $this->renderPartial('_reviewdpp', [
            'model' => $model,
        ]);
        return $pdf->render();
    }
    public function actionReviewdpp($id) {
        $model = $this->findModel($id);
        if ($model->reviews) {
            $pdf = Yii::$app->pdf;
            $pdf->content = $this->renderPartial('_reviewdpp', [
                'model' => $model, 'template' => $model->reviews ?? []
            ]);
            $pdf->cssInline = ".center{text-align:center}.border1solid {border: #eee 1px solid;}";
            return $pdf->render();
        } else {
            Yii::$app->session->setFlash('warning', 'Belum ada review dpp');
            return $this->redirect(['index']);
        }
    }
    public function actionFormreview($id) {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $r = file_get_contents(Yii::getAlias('@app/uraianreviewdpp.json'));
        $template = (json_decode($r));
        if ($request->isGet) {
            return $this->render('_frmreviewdpp', [
                'model' => $model,
                'reviews' => $model->reviews ?? new ReviewDpp, 'template' => $template->data ?? [],
            ]);
        }
        if ($request->isPost) {
            $rr = ReviewDpp::where(['dpp_id' => $model->id])->orderBy('id desc')->one();
            if ($rr) {
                $oldfile = $rr->file_tanggapan;
                if (file_exists(Yii::getAlias('@uploads') . $oldfile) && !empty($oldfile) && ($rr->isBase64Encoded($_POST['ReviewDpp']['file_tanggapan']))) {
                    unlink(Yii::getAlias('@uploads') . $oldfile);
                }
                $rr->uraian = json_encode($_POST['ReviewDpp']['uraian'], JSON_UNESCAPED_SLASHES);
                $rr->keterangan = $_POST['ReviewDpp']['keterangan'];
                $rr->kesimpulan = $_POST['ReviewDpp']['kesimpulan'];
                $rr->tanggapan_ppk = $_POST['ReviewDpp']['tanggapan_ppk'];
                $rr->file_tanggapan = $_POST['ReviewDpp']['file_tanggapan'];
                $rr->dpp_id = $model->id;
                $rr->pejabat = Yii::$app->user->id;
                $rr->save();
            } else {
                $rr = new ReviewDpp;
                $rr->uraian = json_encode($_POST['ReviewDpp']['uraian'], JSON_UNESCAPED_SLASHES);
                $rr->keterangan = $_POST['ReviewDpp']['keterangan'];
                $rr->kesimpulan = $_POST['ReviewDpp']['kesimpulan'];
                $rr->tanggapan_ppk = $_POST['ReviewDpp']['tanggapan_ppk'];
                $rr->file_tanggapan = $_POST['ReviewDpp']['file_tanggapan'];
                $rr->dpp_id = $model->id;
                $rr->pejabat = Yii::$app->user->id;
                $rr->save();
            }
            $model->status_review = 1;
            $model->save();
            Yii::$app->session->setFlash('success', 'Review DPP Berhasil');
            return $this->redirect(['index']);
        }
    }
    public function actionCreate() {
        $request = Yii::$app->request;
        $model = new Dpp();
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " Dpp",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Create'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " Dpp",
                    'content' => '<span class="text-success">' . Yii::t('yii2-ajaxcrud', 'Create') . ' Dpp ' . Yii::t('yii2-ajaxcrud', 'Success') . '</span>',
                    'footer' =>  Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Create More'), ['create'], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " Dpp",
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
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " Dpp #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Dpp #" . $id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " Dpp #" . $id,
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
        if (($model = Dpp::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
