<?php
namespace app\controllers;
use app\models\{DraftRab, DraftRabDetail, DraftUsulan, DraftUsulanDetails, DraftUsulanSearch, Produk};
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\{Response, NotFoundHttpException};class DraftusulanController extends Controller {
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
    public function actionIndex() {
        $searchModel = new DraftUsulanSearch();
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
                'title' => "DraftUsulan #" . $id,
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
        $model = new DraftUsulan();
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " DraftUsulan",
                    'content' => '<span class="text-success">' . Yii::t('yii2-ajaxcrud', 'Create') . ' DraftUsulan ' . Yii::t('yii2-ajaxcrud', 'Success') . '</span>',
                    'footer' =>  Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Create More'), ['create'], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " DraftUsulan",
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
            if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "DraftUsulan #" . $id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " DraftUsulan #" . $id,
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
    public function actionCollectible() {
        $request = Yii::$app->request;
        if ($request->post()) {
            $pks = $request->post('selection');
            if (empty($pks) || $pks == '' || $pks == null) {
                Yii::$app->session->setFlash('danger', 'tidak ada data yang dipilih');
                return $this->redirect(['draftusulan/index']);
            } else {
                $data = DraftUsulanDetails::where(['in', 'header_id', $pks])->andWhere(['is_submitted' => 0])->asArray()->all();
                if (!$data) {
                    Yii::$app->session->setFlash('warning', 'Data sudah diusulkan');
                    return $this->redirect(['draftusulan/index']);
                }
                $head = collect($pks)->map(fn ($e) => DraftUsulan::where(['id' => $e])->asArray()->one());
                $data = collect($data)->map(
                    function ($e) use ($head) {
                        foreach ($head as $h) {
                            if ($e['header_id'] == $h['id']) {
                                $e['tahun_anggaran'] = $h['tahun_anggaran'];
                            }
                        }
                        $e['id_produk'] = $e['produk_id'];
                        return $e;
                    }
                )->values()->all();
                $optionSatuan = DraftUsulan::optionsSettingType('satuans', ['param', 'param']);
                $optionsProduk = collect($data)->map(fn ($r) => Produk::findOne($r['produk_id']))->pluck('nama_produk', 'id')->toArray();
                $optionsRab = collect(DraftRab::where(['tahun_anggaran' => $head[0]['tahun_anggaran']])->select('id,uraian_anggaran')->all())->pluck('uraian_anggaran', 'id')->toArray();
            }
        }
        $model = new DraftUsulan;
        return $this->render('collectible', ['data' => $data, 'optionSatuan' => $optionSatuan, 'optionsProduk' => $optionsProduk, 'optionsRab' => $optionsRab, 'model' => $model]);
    }
    public function actionSaveusulan() {
        $request = Yii::$app->request;
        if ($request->post()) {
            $data = ($request->post('DraftUsulan')['detailusulan']);
            $groupedData = collect($data)->groupBy('rab_id')->map(function ($group) {
                return $group->groupBy('produk_id')->map(function ($subGroup) {
                    return [
                        'rab_id' => $subGroup[0]['rab_id'],
                        'produk_id' => $subGroup[0]['produk_id'],
                        'harga_satuan' => Produk::findOne($subGroup[0]['produk_id'])->hargapasar,
                        'id' => $subGroup[0]['id'],
                        'qty_usulan' => $subGroup->sum('qty_usulan'),
                        'tahun_anggaran' => $subGroup[0]['tahun_anggaran'],
                        'satuan' => $subGroup[0]['satuan'],
                    ];
                });
            });
            $result = $groupedData->flatMap(function ($subGroups) {
                return $subGroups->values();
            })->all();
            $transaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($result as $val) {
                    $existingRecord = DraftRabDetail::where([
                        'rab_id' => $val['rab_id'], 'produk_id' => $val['produk_id'],
                        'volume' => $val['qty_usulan'], 'satuan' => $val['satuan'], 'harga_satuan' => $val['harga_satuan'],
                        'reff_usulan' => json_encode(['draft_usulan_id' => $val['id'], 'tahun_anggaran' => $val['tahun_anggaran']], JSON_UNESCAPED_SLASHES)
                    ])->exists();
                    if (!$existingRecord) {
                        $model = new DraftRabDetail();
                        $model->rab_id = $val['rab_id'];
                        $model->produk_id = $val['produk_id'];
                        $model->volume = $val['qty_usulan'];
                        $model->satuan = $val['satuan'];
                        $model->harga_satuan = $val['harga_satuan'];
                        $model->reff_usulan = json_encode(['draft_usulan_id' => $val['id'], 'tahun_anggaran' => $val['tahun_anggaran']], JSON_UNESCAPED_SLASHES);
                        $model->save();
                        collect($data)->map(function ($elm) {
                            DraftUsulanDetails::updateAll(
                                ['is_submitted' => 1, 'rab_id' => $elm['rab_id'], 'satuan' => $elm['satuan']],
                                ['id' => $elm['id']]
                            );
                        });
                        $parent = $model->parent;
                        if ($parent->sisa_anggaran === null || $parent->sisa_anggaran === 0) {
                            $parent->sisa_anggaran = $parent->jumlah_anggaran;
                        }
                        $parent->sisa_anggaran -= ($model->volume * $model->harga_satuan);
                        $parent->save();
                    }
                }
                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Data diusulkan');
                return $this->redirect(['draftusulan/index']);
            } catch (\Throwable $th) {
                $transaction->rollBack();
                throw $th;
            }
        }
    }
    public function actionExpand() { //7167927860
        if (isset($_POST['expandRowKey'])) {
            $model = $this->findModel($_POST['expandRowKey']);
            $query = DraftUsulanDetails::where(['header_id' => $model->id]);
            $model = new ActiveDataProvider([
                'query' => $query,
                'sort' => false,
            ]);
            $model->pagination = false;
            return $this->renderAjax('/draftusulan/expand', ['dataProvider' => $model]);
        } else {
            return '<div class="alert alert-danger">No data found</div>';
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
        if (($model = DraftUsulan::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
