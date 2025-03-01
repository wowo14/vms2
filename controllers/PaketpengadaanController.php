<?php
namespace app\controllers;
use Yii;
use yii\filters\VerbFilter;
use app\models\KodeRekening;
use yii\data\ActiveDataProvider;
use yii\helpers\{ArrayHelper, Html};
use kartik\grid\EditableColumnAction;
use yii\web\{ServerErrorHttpException, Response, NotFoundHttpException};
use app\models\{Negosiasi, Unit, TemplateChecklistEvaluasi, Attachment, Dpp, PaketPengadaanDetails, PaketPengadaanSearch, PaketPengadaan, ProgramKegiatan, Rab};
use yii\debug\models\search\Log;
class PaketpengadaanController extends Controller {
    public function actions() {
        return ArrayHelper::merge(parent::actions(), [
            'editablenego' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => PaketPengadaanDetails::className(),
                'outputValue' => function ($model, $attribute, $key, $index) {
                    return $model->$attribute;
                },
                'outputMessage' => function ($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editablepenawaran' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => PaketPengadaanDetails::className(),
                'outputValue' => function ($model, $attribute, $key, $index) {
                    return $model->$attribute;
                },
                'outputMessage' => function ($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
        ]);
    }
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
    public function actionLampiran($id) {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if ($model->pemenang) {
            Yii::$app->session->setFlash('warning', 'Pemenang sudah ditentukan');
            return $this->redirect(['index']);
        }
        if ($model->tanggal_reject == '' && $model->alasan_reject == '') {
            if (!$model::isAdmin()) {
                if (!$model::isStaffpp()) {
                    if (isset($model->dpp) ? $model->dpp->pejabat_pengadaan : false) {
                        Yii::$app->session->setFlash('warning', 'PaketPengadaan Sudah Diproses DPP');
                        return $this->redirect('index');
                    }
                }
            }
        }
        if ($request->isGet) {
            return $this->render('lampiran', ['model' => $model]);
        }
        if ($request->isPost) {
            $i = 0;
            foreach ($_FILES['PaketPengadaan']['name']['lampiran'] as $index => $filename) {
                $fileType = $_FILES['PaketPengadaan']['type']['lampiran'][$index]['name'];
                $fileTmpName = $_FILES['PaketPengadaan']['tmp_name']['lampiran'][$index]['name'];
                $fileError = $_FILES['PaketPengadaan']['error']['lampiran'][$index]['name'];
                $fileSize = $_FILES['PaketPengadaan']['size']['lampiran'][$index]['name'];
                $filename = $filename['name'];
                if (empty($filename)) {
                    continue;
                }
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                if ($fileError === 0) {
                    $newFilename = uniqid() . '.' . $extension;
                    $destination = Yii::getAlias('@uploads') . $newFilename;
                    $jenisdokumen = $request->post('PaketPengadaan')['lampiran'][$index]['jenis_dokumen'];
                    $att = Attachment::where(['jenis_dokumen' => $jenisdokumen, 'user_id' => $model->id])->one();
                    if ($att) {
                        $att->delete();
                    }
                    move_uploaded_file($fileTmpName, $destination);
                    if (in_array($extension, ['png', 'jpg', 'jpeg', 'gif'])) {
                        Yii::$app->tools->resizeImageToMaxSize($destination, 512 * 1024);
                        // $newFilename=Yii::$app->tools->convertavif($destination);
                    }
                    $attachment = new Attachment();
                    $attachment->name = $newFilename;
                    $attachment->uri = Yii::getAlias('@web/uploads/') . $newFilename;
                    $attachment->user_id = $model->id; //based on paket_id
                    $attachment->mime = mime_content_type(Yii::getAlias('@uploads') . $newFilename) ?: $fileType;
                    $attachment->type = mime_content_type(Yii::getAlias('@uploads') . $newFilename) ?: $fileType;
                    $attachment->size = filesize(Yii::getAlias('@uploads') . $newFilename) ?: $fileSize;
                    $attachment->jenis_dokumen = $jenisdokumen;
                    if (!$attachment->save()) {
                        $content = "Error saving attachment: " . json_encode($attachment->errors) . "\n";
                        Yii::$app->session->setFlash('error', $content);
                    } else {
                        $i++;
                    }
                } else {
                    $content = "Error uploading file $filename. Error code: $fileError";
                    Yii::$app->session->setFlash('error', $content);
                }
            }
            $i > 0 ? Yii::$app->session->setFlash('success', $i . 'File Berhasil di upload') : '';
            return $this->redirect(['index']);
        }
    }
    public function actionDpp() { // kirim dpp
        $request = Yii::$app->request;
        $pks = explode(',', $request->post('pks'));
        foreach ($pks as $pk) {
            $model = $this->findModel($pk);
            $jenisdokumen = Attachment::collectAll(['user_id' => $model->id])->pluck('jenis_dokumen')->toArray();
            sort($jenisdokumen);
            if ($jenisdokumen !== $model->requiredlampiran) {
                throw new ServerErrorHttpException('Paket Pengadaan: Lampiran belum diupload semua');
            }
            $dp = Dpp::where(['paket_id' => $model->id])->one();
            if ($dp) {
                if ($model->tanggal_reject && $model->alasan_reject) {
                    throw new ServerErrorHttpException('Paket Pengadaan: DPP Ditolak, mohon koreksi/revisi terlebih dahulu');
                }
                throw new ServerErrorHttpException('Paket Pengadaan: DPP sudah diinput');
            }
            $dpp = new Dpp;
            $dpp->paket_id = $model->id;
            $dpp->tanggal_terima = date('Y-m-d H:i:s', time());
            $dpp->bidang_bagian = $model->unit ?? '';
            $dpp->nomor_dpp = $model->nomor ?? '';
            $dpp->tanggal_dpp = $model->tanggal_dpp ?? '';
            $dpp->nomor_persetujuan = $model->nomor_persetujuan ?? '';
            if ($dpp->save()) {
                Yii::$app->session->setFlash('success', 'Paket Pengadaan ' . $model->nama_paket . ' Berhasil ajukan DPP');
            } else {
                $errors = $dpp->getErrors();
                foreach ($errors as $attribute => $errorMessages) {
                    foreach ($errorMessages as $errorMessage) {
                        $msg = "$attribute: $errorMessage\n";
                        Yii::$app->session->setFlash('error', $msg);
                    }
                }
            }
        }
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            return $this->redirect(['index']);
        }
    }
    public function actionDetails() {
        if (isset($_POST['expandRowKey'])) {
            $model = $this->findModel($_POST['expandRowKey']);
            $query = PaketPengadaanDetails::where(['paket_id' => $model->id]);
            $model = new ActiveDataProvider([
                'query' => $query,
                'sort' => false,
            ]);
            $model->pagination = false;
            return $this->renderAjax('expand', ['dataProvider' => $model]);
        } else {
            return '<div class="alert alert-danger">No data found</div>';
        }
    }
    public function actionChild($param) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        $selected = '';
        if (isset($_POST['depdrop_parents']) && (end($_POST['depdrop_parents'])) !== null) {
            $val = end($_POST['depdrop_parents']);
            $data = [];
            $param0 = $param1 = $param2 = '';
            if (!empty($_POST['depdrop_params'])) {
                $param0 = $_POST['depdrop_params'][0] ?? '';
                $param1 = $_POST['depdrop_params'][1] ?? '';
                $param2 = $_POST['depdrop_params'][2] ?? '';
            }
            switch ($param) {
                case 'tahun':
                    $data = ProgramKegiatan::where(['type' => 'program', 'tahun_anggaran' => $val, 'is_active' => 1])
                        ->orderBy('code')
                        ->asArray()
                        ->all();
                    break;
                case 'program':
                    $data = Rab::where(['tahun_anggaran' => $param0, 'kode_program' => $param1])
                        ->select(['kode_kegiatan as code', 'nama_kegiatan as desc'])
                        ->orderBy('id')
                        ->groupBy('kode_kegiatan')
                        ->distinct()
                        ->asArray()
                        ->all();
                    if (empty($data)) {
                        $data = ProgramKegiatan::where(['type' => 'kegiatan', 'tahun_anggaran' => $param0, 'is_active' => 1])
                            ->orderBy('code')
                            ->asArray()
                            ->all();
                    }
                    break;
                case 'koderekening':
                    $data = Rab::where(['tahun_anggaran' => $param0, 'kode_program' => $param1, 'kode_kegiatan' => $param2])
                        ->select(['kode_rekening as code', 'uraian_anggaran as desc'])
                        ->orderBy('id')
                        ->asArray()
                        ->all();
                    if (empty($data)) {
                        $rek = new KodeRekening();
                        $data = collect($rek->coacode)
                            ->filter(fn($v) => $v->tahun_anggaran == $param0)
                            ->map(fn($el) => ['code' => $el['kode'], 'desc' => $el['rekening']])
                            ->sortBy('code')
                            ->toArray();
                    }
                    break;
            }
            foreach ($data as $i => $account) {
                $out[] = ['id' => $account['code'], 'name' => $account['code'] . ' - ' . $account['desc']];
                $selected = ($i == 0) ? $account['code'] : $selected;
            }
        }
        return ['output' => $out, 'selected' => $selected];
    }
    public function actionImportProduct($id) {
        $model = $this->findModel($id);
        if ($model->pemenang) {
            Yii::$app->session->setFlash('warning', 'PaketPengadaan Sudah ada Pemenang');
            return $this->redirect('index');
        }
        if ($model->tanggal_reject == '' && $model->alasan_reject == '') {
            if (!$model::isAdmin()) {
                if (!$model::isStaffpp()) {
                    if (isset($model->dpp) ? $model->dpp->pejabat_pengadaan : false) {
                        Yii::$app->session->setFlash('warning', 'PaketPengadaan Sudah Diproses DPP');
                        return $this->redirect('index');
                    }
                }
            }
        }
        $request = Yii::$app->request;
        if ($request->isPost) {
            if (!empty($_FILES)) {
                $tempFile = $_FILES['produk']['tmp_name'];
                $fileTypes = array('xls', 'xlsx');
                $fileParts = pathinfo($_FILES['produk']['name']);
                if (in_array(@$fileParts['extension'], $fileTypes)) {
                    $fileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($tempFile);
                    $objPHPExcelReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($fileType);
                    $spreadsheet = $objPHPExcelReader->load($tempFile);
                    $inserted = 0;
                    $errorCount = 0;
                    $parentSheet = $spreadsheet->getSheetByName('produk');
                    $highestRow = $parentSheet->getHighestRow();
                    $highestColumn = $parentSheet->getHighestColumn();
                    $existDetails = PaketpengadaanDetails::where(['paket_id' => $model->id])->all();
                    if ($existDetails) {
                        PaketPengadaanDetails::deleteAll(['paket_id' => $model->id]);
                    }
                    $adata = [];
                    for ($childRow = 2; $childRow <= $highestRow; ++$childRow) {
                        // Read data from the sheet
                        $nama_produk = $parentSheet->getCellByColumnAndRow(2, $childRow)->getValue();
                        $qty = $parentSheet->getCellByColumnAndRow(3, $childRow)->getValue();
                        $volume = $parentSheet->getCellByColumnAndRow(4, $childRow)->getValue();
                        $satuan = $parentSheet->getCellByColumnAndRow(5, $childRow)->getValue();
                        $hps_satuan = $parentSheet->getCellByColumnAndRow(6, $childRow)->getValue();
                        $penawaran = $parentSheet->getCellByColumnAndRow(7, $childRow)->getValue();
                        // Check if mandatory fields are valid
                        if (!empty($nama_produk) && !empty($qty) && !empty($volume) && !empty($satuan) && !empty($hps_satuan)) {
                            $inserted++;
                            $adata[] = [
                                'paket_id' => $model->id,
                                'nama_produk' => $nama_produk,
                                'qty' => $qty,
                                'volume' => $volume,
                                'satuan' => $satuan,
                                'hps_satuan' => $hps_satuan,
                                'penawaran' => $penawaran ?? 0,
                            ];
                        } else {
                            // Log or handle the row with missing data
                            $errorCount++;
                        }
                    }
                    if (!empty($adata)) {
                        Yii::$app->db->createCommand()
                            ->batchInsert(
                                'paket_pengadaan_details',
                                ['paket_id', 'nama_produk', 'qty', 'volume', 'satuan', 'hps_satuan', 'penawaran'],
                                $adata
                            )
                            ->execute();
                    }
                    //cache flush
                    $model::invalidatecache('tag_' . $model::getModelname());
                    PaketPengadaanDetails::invalidatecache('tag_' . PaketPengadaanDetails::getModelname());
                    Yii::$app->session->setFlash('success', ($inserted) . ' row inserted');
                    return $this->redirect('index');
                }
            }
        } else {
            return $this->render('_import_product', ['model' => $model]);
        }
    }
    public function actionNegoproduk($id) {
        $paketdetails = PaketPengadaanDetails::findOne(['id' => $id]);
        $request = Yii::$app->request;
        $referrer = $request->referrer;
        if (!$paketdetails) {
            throw new NotFoundHttpException('Produk yang ditawarkan tidak ditemukan.');
        }
        $penawaran = $paketdetails->paketpengadaan->penawaranpenyedia;
        if (!$penawaran) {
            throw new NotFoundHttpException('Paket pengadaan penawaran tidak ditemukan.');
        }
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($paketdetails->load($request->post())) {
                $paketdetails->save();
                $nego = $penawaran->negosiasi ?? new Negosiasi();
                $nego->penawaran_id = $penawaran->id;
                $nego->ammount = PaketPengadaanDetails::sumNegosiasi($paketdetails->paket_id);
                // Merge old details with new
                $oldnegodetail = json_decode($nego->detail, true) ?? []; // Ensure it's an array
                $newdetail = [
                    'id' => $paketdetails->id,
                    'paket_id' => $paketdetails->paket_id,
                    'nama_produk' => $paketdetails->nama_produk,
                    'volume' => $paketdetails->volume ?? 1,
                    'qty' => $paketdetails->qty ?? 1,
                    'satuan' => $paketdetails->satuan,
                    'hps_satuan' => $paketdetails->hps_satuan,
                    'penawaran' => $paketdetails->penawaran,
                    'negosiasi' => $paketdetails->negosiasi,
                    'durasi' => $paketdetails->durasi ?? '',
                    'informasi_harga' => $paketdetails->informasi_harga ?? '',
                    'sumber_informasi' => $paketdetails->sumber_informasi ?? ''
                ];
                $merged = array_merge($oldnegodetail, [$newdetail]);
                // Update nego detail
                $nego->detail = json_encode($merged, JSON_UNESCAPED_SLASHES);
                $nego->pp_accept = $_POST['Negosiasi']['pp_accept'] ?? '';
                $nego->penyedia_accept = $_POST['Negosiasi']['penyedia_accept'] ?? '';
                $nego->accept = ($nego->penyedia_accept == 1 && $nego->pp_accept == 1) ? 1 : null;
                $nego->save(false);
                Yii::$app->session->setFlash('success', 'Sukses input nilai nego');
                // $this->redirect($referrer);
                return [
                    'title' => 'success',
                    'content' => 'Data saved successfully.',
                    // 'forceReload' => '#detailspaket-pjax'
                    // 'redirect' => $referrer,
                ];
            } else {
                return [
                    'title' => "Nego produk #" . $paketdetails->nama_produk,
                    'content' => $this->renderAjax('_frm_negoproduk', ['model' => $paketdetails, 'penawaran' => $penawaran]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Create'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            }
        } else {
            if ($request->isPost) {
                if ($paketdetails->load($request->post())) {
                    $paketdetails->save();
                    // Save to negosiasi
                    $nego = $penawaran->negosiasi ?? new Negosiasi();
                    $nego->penawaran_id = $penawaran->id;
                    $nego->ammount = PaketPengadaanDetails::sumNegosiasi($paketdetails->paket_id);
                    // Merge old details with new
                    $oldnegodetail = json_decode($nego->detail, true) ?? []; // Ensure it's an array
                    $newdetail = [
                        'id' => $paketdetails->id,
                        'paket_id' => $paketdetails->paket_id,
                        'nama_produk' => $paketdetails->nama_produk,
                        'volume' => $paketdetails->volume ?? 1,
                        'qty' => $paketdetails->qty ?? 1,
                        'satuan' => $paketdetails->satuan,
                        'hps_satuan' => $paketdetails->hps_satuan,
                        'penawaran' => $paketdetails->penawaran,
                        'negosiasi' => $paketdetails->negosiasi,
                        'durasi' => $paketdetails->durasi ?? '',
                        'informasi_harga' => $paketdetails->informasi_harga ?? '',
                        'sumber_informasi' => $paketdetails->sumber_informasi ?? ''
                    ];
                    $merged = array_merge($oldnegodetail, [$newdetail]);
                    // Update nego detail
                    $nego->detail = json_encode($merged, JSON_UNESCAPED_SLASHES);
                    $nego->pp_accept = $_POST['Negosiasi']['pp_accept'] ?? '';
                    $nego->penyedia_accept = $_POST['Negosiasi']['penyedia_accept'] ?? '';
                    $nego->accept = ($nego->penyedia_accept == 1 && $nego->pp_accept == 1) ? 1 : null;
                    $nego->save(false);
                    Yii::$app->session->setFlash('success', 'Sukses input nilai nego');
                    // return $this->redirect($referrer);
                    return $this->render('//paketpengadaan/negoproduk', ['id' => $id]);
                }
            } else {
                return $this->render('_frm_negoproduk', ['model' => $paketdetails, 'penawaran' => $penawaran]);
            }
        }
    }
    public function actionIndex() {
        $searchModel = new PaketPengadaanSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionPostpenawaran($id) {
        $request = Yii::$app->request;
        $paketdetails = PaketPengadaanDetails::findOne($id);
        if (!$paketdetails) {
            throw new NotFoundHttpException('Data tidak ditemukan.');
        }
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($paketdetails->load($request->post()) && $paketdetails->save()) {
                // Yii::$app->session->setFlash('success', 'sukses input penawaran');
                // $this->redirect($request->referrer);
                return [
                    'title' => 'Form Penawaran',
                    'content' => 'Sukses input penawaran',
                    // 'forceReload' => '#detailspaket-pjax'
                ];
            } else {
                return [
                    'title' => 'Form Penawaran',
                    'content' => $this->renderAjax('//penawaranpenyedia/_frm_penawaranpenyedia', [
                        'model' => $paketdetails,
                        'penawaran' => $paketdetails->paketpengadaan->penawaranpenyedia,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            }
        } else {
            if ($paketdetails->load($request->post()) && $paketdetails->save()) {
                Yii::$app->session->setFlash('success', 'sukses input penawaran');
                // return $this->redirect($request->referrer);
                return $this->render('//paketpengadaan/postpenawaran', ['id' => $id]);
            } else {
                return $this->render('//penawaranpenyedia/_frm_penawaranpenyedia', ['model' => $paketdetails, 'penawaran' => $paketdetails->paketpengadaan->penawaranpenyedia]);
            }
        }
    }
    public function actionView($id) {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "PaketPengadaan #" . $id,
                'content' => $this->renderAjax('view', [
                    'model' => $model,
                ]),
                'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                    ($model->pemenang ? '' :
                        Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote']))
            ];
        } else {
            return $this->render('view', [
                'model' => $model,
            ]);
        }
    }
    public function actionCreate() {
        $request = Yii::$app->request;
        $model = new PaketPengadaan();
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " PaketPengadaan",
                    'content' => '<span class="text-success">' . Yii::t('yii2-ajaxcrud', 'Create') . ' PaketPengadaan ' . Yii::t('yii2-ajaxcrud', 'Success') . '</span>',
                    'footer' =>  Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Create More'), ['create'], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " PaketPengadaan",
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
    public function actionCeklistadmin($id) {
        $request = Yii::$app->request;
        $title = "Kelengkapan DPP";
        $model = PaketPengadaan::find()->cache(false)->where(['id' => $id])->one();
        if ($request->isGet) {
            $temp = TemplateChecklistEvaluasi::where(['like', 'template', 'Ceklist_Kelengkapan_DPP'])->one();
            if ($temp) {
                $ar_element = $temp->element ? explode(',', $temp->element) : [];
                $details = json_decode($temp->detail->uraian, true);
                $hasil = [];
                foreach ($details as $v) {
                    $c = ['uraian' => $v['uraian']];
                    foreach ($ar_element as $element) {
                        if ($element) {
                            $c[$element] = '';
                        }
                    }
                    $hasil[] = $c;
                }
                $temp = $hasil;
            }
            if (!$model->addition) {
                $data = ['template' => $temp];
                $model->addition = json_encode($data, JSON_UNESCAPED_SLASHES);
                $model->save();
            }
            $dataPaket = $model::collectAll(['approval_by' => null, 'pemenang' => null, 'id' => $id])->pluck('nomornamapaket', 'id')->toArray();
            return $this->render('_checklistadmin', ['model' => $model, 'dataPaket' => $dataPaket, 'temp' => $temp, 'title' => $title]);
        }
        if ($request->isPost) {
            $template = $request->post('PaketPengadaan')['template'];
            $pure1 = collect($template)->map(function ($e) use ($model) {
                foreach ($e as $key => $value) {
                    $e[$key] = $model->getPurifier($value);
                }
                if (key_exists('sesuai', $e)) {
                    if ($e['sesuai'] == 'on') {
                        $e['sesuai'] = 1;
                    }
                }
                return $e;
            });
            $model->addition = json_encode([
                'unit' => $_POST['PaketPengadaan']['unit'],
                'id' => $_POST['PaketPengadaan']['id'],
                'template' => $pure1
            ], JSON_UNESCAPED_SLASHES);
            $model->save();
            Yii::$app->session->setFlash('success', 'Kelengkapan DPP Berhasil Ditambahkan');
            return $this->redirect($request->referrer);
        }
    }
    public function actionPrintceklistadmin($id) {
        $model = $this->findModel($id);
        $title = 'Ceklist Kelengkapan DPP';
        $data = [
            'unit' => Unit::findOne(json_decode($model->addition, true)['unit'])->unit,
            'paket' => $model->nama_paket,
            'details' => collect(json_decode($model->addition, true)['template'])->map(function ($e) {
                if (key_exists('sesuai', $e)) {
                    $e['sesuai'] = $e['sesuai'] == 1 ? 'Ya' : 'Tidak';
                } else {
                    // $e['sesuai'] = 'Tidak';
                }
                return $e;
            })->toArray(),
            'logogresik' => Yii::getAlias('@webroot/images/logogresik.png', true),
            'logors' => Yii::getAlias('@webroot/images/logors.png', true),
            'kepalapengadaan' => null,
            'nipkepalapengadaan' => '',
            'admin' => null,
            'nipadmin' => '',
            'kurir' => $model->kurirnya->nama,
            'nipkurir' => $model->kurirnya->nip,
        ];
        $cetakan = $this->renderPartial('_printceklistadmin', [
            'data' => $data,
            'model' => $model,
            'title' => $title
        ]);
        $pdf = Yii::$app->pdf;
        $pdf->content = $cetakan;
        $pdf->cssFile = '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css';
        return $pdf->render();
    }
    public function actionKirimulang($id) {
        $model = $this->findModel($id);
        if (Yii::$app->request->isGet) {
            return $this->render('/historireject/update', ['model' => $model->historireject,]);
        }
        if (Yii::$app->request->isPost) {
            $histori = $model->historireject;
            $histori->load(Yii::$app->request->post());
            $histori->save();
            $model->alasan_reject = '';
            $model->tanggal_reject = '';
            $model->save();
            Yii::$app->session->setFlash('success', 'PaketPengadaan Berhasil Dikirim -> DPP');
            return $this->redirect('index');
        }
    }
    public function actionUpdate($id) {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if ($model->pemenang) {
            Yii::$app->session->setFlash('warning', 'PaketPengadaan Sudah ada Pemenang');
            return $this->redirect('index');
        }
        //cek dpp udah di assign ?
        if ($model->tanggal_reject == '' && $model->alasan_reject == '') {
            if (!$model::isAdmin()) {
                if (!$model::isStaffpp()) {
                    if (isset($model->dpp) ? $model->dpp->pejabat_pengadaan : false) {
                        Yii::$app->session->setFlash('warning', 'PaketPengadaan Sudah Diproses DPP');
                        return $this->redirect('index');
                    }
                }
            }
        }
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load($request->post()) && $model->save()) {
                if (!empty($_POST['ReviewDpp'])) {
                    $model->dpp->reviews->file_tanggapan = $_POST['ReviewDpp']['file_tanggapan'];
                    $model->dpp->reviews->tgl_dikembalikan = $_POST['ReviewDpp']['tgl_dikembalikan'];
                    $model->dpp->reviews->tanggapan_ppk = $_POST['ReviewDpp']['tanggapan_ppk'];
                    $model->dpp->reviews->save();
                }
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "PaketPengadaan #" . $id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " PaketPengadaan #" . $id,
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
        $model = $this->findModel($id);
        if ($model->pemenang) {
            Yii::$app->session->setFlash('warning', 'PaketPengadaan Sudah ada Pemenang');
            return $this->redirect('index');
        }
        if ($model->tanggal_reject == '' && $model->alasan_reject == '') {
            if (!$model::isAdmin()) {
                if (!$model::isStaffpp()) {
                    if (isset($model->dpp) ? $model->dpp->pejabat_pengadaan : false) {
                        Yii::$app->session->setFlash('warning', 'PaketPengadaan Sudah Diproses DPP');
                        return $this->redirect('index');
                    }
                }
            }
        }
        $model->unlinkAll('details', true);
        if (isset($model->dpp)) {
            $model->dpp->unlinkAll('reviews', true);
            $model->dpp->unlinkAll('penugasan', true);
            $model->dpp->delete();
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
            if ($model->pemenang) {
                Yii::$app->session->setFlash('warning', 'PaketPengadaan Sudah ada pemenang');
                return $this->redirect('index');
            }
            if ($model->tanggal_reject == '' && $model->alasan_reject == '') {
                if (!$model::isAdmin()) {
                    if (!$model::isStaffpp()) {
                        if (isset($model->dpp) ? $model->dpp->pejabat_pengadaan : false) {
                            Yii::$app->session->setFlash('warning', 'PaketPengadaan Sudah Diproses DPP');
                            return $this->redirect('index');
                        }
                    }
                }
            }
            $model->unlinkAll('details', true);
            if (isset($model->dpp)) {
                $model->dpp->unlinkAll('reviews', true);
                $model->dpp->unlinkAll('penugasan', true);
                $model->dpp->delete();
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
        if (($model = PaketPengadaan::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
