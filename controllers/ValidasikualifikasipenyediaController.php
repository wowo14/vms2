<?php
namespace app\controllers;
use app\models\{PenawaranPengadaan, TemplateChecklistEvaluasi, ValidasiKualifikasiPenyedia, ValidasiKualifikasiPenyediaDetail, ValidasiKualifikasiPenyediaSearch};
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\{Html, HtmlPurifier};
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
    public function actionGeneratekesimpulan($params) { //hashurl ['vendor_id'=>1, 'paket_pengadaan_id'=>1,'callback'=>'callbackkesimpulan']
        $params = $this->decodeurl($params);
        if (!$params->vendor_id || !$params->paket_pengadaan_id) {
            throw new NotFoundHttpException('Params vendor_id or paket_pengadaan_id is not found');
        }
        $vkp = ValidasiKualifikasiPenyedia::collectAll(['penyedia_id' => $params->vendor_id, 'paket_pengadaan_id' => $params->paket_pengadaan_id]);
        if ($vkp->isEmpty()) {
            throw new NotFoundHttpException('Query collections vendor_id or paket_pengadaan_id is not found');
        }
        $tmp = TemplateChecklistEvaluasi::where(['template' => 'Ceklist_Evaluasi_Kesimpulan'])->one();
        if (!$tmp) {
            throw new NotFoundHttpException('Template Ceklist_Evaluasi_Kesimpulan not found');
        }
        $isTmpNull = $vkp->contains(function ($e) use ($tmp) {
            return $e->template == $tmp->id;
        });
        if (!$isTmpNull) { //generate new kesimpulan
            $model = new ValidasiKualifikasiPenyedia();
            $model->template = $tmp->id;
            $model->penyedia_id = $params->vendor_id;
            $model->paket_pengadaan_id = $params->paket_pengadaan_id;
            $model->is_active = 1;
            $model->keperluan = 'Kesimpulan';
            if ($model->save()) {
                $hasil = [];
                if ($tmp->element) {
                    $ar_element = explode(',', $tmp->element);
                }
                foreach (json_decode($tmp->detail->uraian, true) as $v) {
                    $c = ['uraian' => $v['uraian']];
                    if ($tmp->element) {
                        foreach ($ar_element as $element) {
                            if ($element) {
                                $c[$element] = '';
                            }
                            if ($element == 'komentar') {
                                $c[$element] = 'proses validasi administrasi'; //callback logic kesimpulan
                            }
                        }
                    }
                    $c['sesuai'] = '';
                    $hasil[] = $c;
                }
                $detail = new ValidasiKualifikasiPenyediaDetail();
                $detail->header_id = $model->id;
                $detail->hasil = json_encode($hasil);
                $detail->save(false);
            }
            if ($model->getErrors()) {
                Yii::$app->session->setFlash('error', "Gagal membuat kesimpulan penilaian kualifikasi penyedia, error: " . json_encode($model->getErrors()));
            } else {
                Yii::$app->session->setFlash('success', "Berhasil membuat kesimpulan penilaian kualifikasi penyedia");
            }
        } else { //load existing data and detail then update kesimpulan
            $model = ValidasiKualifikasiPenyedia::where(['penyedia_id' => $params->vendor_id, 'paket_pengadaan_id' => $params->paket_pengadaan_id, 'template' => $tmp->id])->one();
            if (!$model) {
                throw new NotFoundHttpException('Data kesimpulan not found');
            }
            $details = $model->details[0];
            if ($details) { // update kesimpulan
                $rr = json_decode($details->hasil, true);
                $r = ValidasiKualifikasiPenyedia::getCalculated($params->paket_pengadaan_id);
                $r = collect($r)->where('penyedia_id', $params->vendor_id)->where('paket_pengadaan_id', $params->paket_pengadaan_id)->first();
                $hasil = collect($rr)->map(function ($e)use($r) {
                    $e['sesuai'] = '';
                    $e['komentar'] = $r['total_sesuai']==$r['total_element']?'Lolos Administrasi Validasi Dokumen':'Tidak Lolos Administrasi Validasi Dokumen';
                    return $e;
                })->toArray();
                $details->hasil = json_encode($hasil);
                $details->save();
                if ($details->getErrors()) {
                    Yii::$app->session->setFlash('error', "Gagal update kesimpulan penilaian kualifikasi penyedia, error: " . json_encode($details->getErrors()));
                } else {
                    Yii::$app->session->setFlash('success', "Berhasil update kesimpulan penilaian kualifikasi penyedia");
                }
            }
        }
        return $this->redirect(['index']);
    }
    public function actionAssestment($id) {
        $request = Yii::$app->request;
        $model = ValidasiKualifikasiPenyedia::find()->cache(false)->where(['id' => $id])->one();
        if ($request->isGet) {
            return $this->render('assestment', [
                'model' => $model
            ]);
        }
        if ($request->isPost) {
            $assestment = $request->post('ValidasiKualifikasiPenyedia')['assestment'];
            $pure1 = collect($assestment)->map(function ($e) {
                foreach ($e as $key => $value) {
                    $e[$key] = HtmlPurifier::process($value);
                }
                return $e;
            });
            $pure = $pure1->map(function ($e) { // switchinput
                if (isset($e['sesuai'])) {
                    $e['sesuai'] = 'ya';
                } else {
                    $e['sesuai'] = '';
                }
                if (array_key_exists('sesuai', $e)) {
                    $sesuaiValue = $e['sesuai'] ?? '';
                    unset($e['sesuai']);
                    $e = array_merge(['uraian' => $e['uraian'], 'sesuai' => $sesuaiValue], $e);
                }
                return $e;
            });
            ValidasiKualifikasiPenyediaDetail::updateAll([
                'hasil' => json_encode($pure),
            ], ['header_id' => $id]);
            $parent = ValidasiKualifikasiPenyedia::findOne(['id' => $id]);
            $tmp = TemplateChecklistEvaluasi::where(['template' => 'Ceklist_Evaluasi_Kesimpulan'])->one();
            if (!$tmp) {
                throw new NotFoundHttpException('Template Ceklist_Evaluasi_Kesimpulan not found');
            }
            $others = ValidasiKualifikasiPenyedia::collectAll(['penyedia_id' => $parent->penyedia_id, 'paket_pengadaan_id' => $parent->paket_pengadaan_id]);
            $others->map(function ($e)use($tmp) {
                if($e->template!==$tmp->id){
                    ValidasiKualifikasiPenyediaDetail::hitungTotalSesuai($e->id);
                }
            });
            $this->actionGeneratekesimpulan($this->hashurl([
                'vendor_id' => $model->penyedia_id,
                'paket_pengadaan_id' => $model->paket_pengadaan_id
            ]));
            Yii::$app->session->setFlash('success', 'Data Assestment Berhasil disimpan');
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
                    if ($collect->element) {
                        $ar_element = explode(',', $collect->element);
                    }
                    foreach (json_decode($collect->detail->uraian, true) as $v) {
                        $c = ['uraian' => $v['uraian']];
                        if ($collect->element) {
                            foreach ($ar_element as $element) {
                                if ($element) {
                                    $c[$element] = '';
                                }
                            }
                        }
                        $hasil[] = $c;
                    }
                    $detail = new ValidasiKualifikasiPenyediaDetail();
                    $detail->header_id = $model->id;
                    $detail->hasil = json_encode($hasil);
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
                    if ($collect->element) {
                        $ar_element = explode(',', $collect->element);
                    }
                    foreach (json_decode($collect->detail->uraian, true) as $v) {
                        $c = ['uraian' => $v['uraian']];
                        if ($collect->element) {
                            foreach ($ar_element as $element) {
                                if ($element) {
                                    $c[$element] = '';
                                }
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
                    if ($collect->element) {
                        $ar_element = explode(',', $collect->element);
                    }
                    foreach (json_decode($collect->detail->uraian, true) as $v) {
                        $c = ['uraian' => $v['uraian']];
                        if ($collect->element) {
                            foreach ($ar_element as $element) {
                                if ($element) {
                                    $c[$element] = '';
                                }
                            }
                        }
                        $hasil[] = $c;
                    }
                    $detail = new ValidasiKualifikasiPenyediaDetail();
                    $detail->header_id = $model->id;
                    $detail->hasil = json_encode($hasil);
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
                    if ($collect->element) {
                        $ar_element = explode(',', $collect->element);
                    }
                    foreach (json_decode($collect->detail->uraian, true) as $v) {
                        $c = ['uraian' => $v['uraian']];
                        if ($collect->element) {
                            foreach ($ar_element as $element) {
                                if ($element) {
                                    $c[$element] = '';
                                }
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
            throw new NotFoundHttpException('The requested page or id does not exist.');
        }
    }
    public function actionViewvalidasipenyedia($id) {
        $templates = TemplateChecklistEvaluasi::where(['like', 'template', 'ceklist_evaluasi'])->all();
        $kualifikasi = ValidasiKualifikasiPenyedia::findAll(['penyedia_id' => $id]);
        if(!$kualifikasi){
            throw new NotFoundHttpException('Petugas Belom Memvalidasi Kualifikasi Penyedia');
        }
        $penawaran = PenawaranPengadaan::collectAll(['penyedia_id' => $id]);
        if(!$penawaran){
            throw new NotFoundHttpException('Petugas Belom memasukkan Dokumen Penawaran Penyedia');
        }
        $tabs = collect($templates)->map(function ($e) use ($kualifikasi) {
            foreach ($kualifikasi as $k) {
                if ($k->template == $e->id) {
                    $filteredModels[] = $k;
                }
            }
            $content = '';
            if (!empty($filteredModels)) {
                $rr = json_decode($filteredModels[0]->details[0]->hasil, true);
                $count = $total = 0;
                foreach ($rr as $c) {
                    if (array_key_exists('sesuai', $c)) {
                        if ($c['sesuai'] == 'ya') {
                            $count++;
                        }
                    }
                    if ($c) {
                        $total++;
                    }
                }
                if ($total <> $count) {
                    $content .= 'Alasan Tidak Lulus <br> Persyaratan Tidak Lengkap';
                } else {
                    $col = array_keys($rr[0]);
                    $content .= \yii\grid\GridView::widget([
                        'dataProvider' => new \yii\data\ArrayDataProvider([
                            'allModels' => $rr
                        ]),
                        'columns' => $col
                    ]);
                }
            } else {
                $content .= 'Alasan Tidak Lulus <br> Persyaratan Tidak Lengkap';
            }
            if ($e->jenis_evaluasi == 'Kesimpulan') {
                $col = array_keys($rr[0]);
                $index = array_search('sesuai', $col);
                if ($index !== false) {
                    unset($col[$index]);
                }
                $content = \yii\grid\GridView::widget([
                    'dataProvider' => new \yii\data\ArrayDataProvider([
                        'allModels' => $rr
                    ]),
                    'layout' => '{items}',
                    'columns' => $col
                ]);
            }
            return [
                'label' => $e->jenis_evaluasi,
                'content' => $content,
                'options' => ['id' => 'val' . $e->id . $e->template],
            ];
        })->toArray();
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "ValidasiKualifikasiPenyedia #" . $id,
                'content' => $this->renderAjax('allviewvalidasi', [
                    'tabs' => $tabs,
                    'kualifikasi' => $kualifikasi,
                    'penawaran' => $penawaran,
                    'templates' => $templates,
                ]),
                'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                    Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
            ];
        } else {
            return $this->render('allviewvalidasi', [
                'tabs' => $tabs,
                'kualifikasi' => $kualifikasi,
                'penawaran' => $penawaran,
                'templates' => $templates,
            ]);
        }
        // return $this->render('validasikualifikasipenyedia',['templates' => $templates,'model'=>new ValidasiKualifikasiPenyedia]);
    }
}
