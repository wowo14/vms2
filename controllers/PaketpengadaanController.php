<?php
namespace app\controllers;
use app\models\Unit;
use app\models\{TemplateChecklistEvaluasi,Attachment, Dpp, PaketPengadaanDetails, PaketPengadaanSearch, PaketPengadaan};
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\{ArrayHelper,Html,HtmlPurifier};
use yii\web\{ServerErrorHttpException, Response, NotFoundHttpException};/**
 A Evaluasi Administrasi
T Evaluasi Teknis
ST Skor Teknis
P Penawaran
PT Penawaran Terkoreksi
HN Hasil Negosiasi
SH Skor Harga
SA Skor Akhir
B Pembuktian Kualifikasi
K Evaluasi Kualifikasi
SK Skor Kualifikasi
SB Skor Pembuktian
H Evaluasi Harga/Biaya
P Pemenang
PK Pemenang Berkontrak
 */
class PaketpengadaanController extends Controller {
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
        if($model->pemenang){
            Yii::$app->session->setFlash('warning', 'Pemenang sudah ditentukan');
            return $this->redirect(['index']);
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
                        // Yii::$app->tools->resizeImageToMaxSize($destination, 512 * 1024);
                        $newFilename=Yii::$app->tools->convertavif($destination, Yii::getAlias('@uploads'), 90);
                    }
                    $attachment = new Attachment();
                    $attachment->name = $newFilename;
                    $attachment->uri = Yii::getAlias('@web/uploads/') . $newFilename;
                    $attachment->user_id = $model->id; //based on paket_id
                    $attachment->mime = mime_content_type(Yii::getAlias('@uploads').$newFilename)?:$fileType;
                    $attachment->type = mime_content_type(Yii::getAlias('@uploads').$newFilename)?:$fileType;
                    $attachment->size = filesize(Yii::getAlias('@uploads').$newFilename)?:$fileSize;
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
    public function actionDpp() {
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
    public function actionImport() {
        if (isset($_POST)) {
            if (!empty($_FILES)) {
                $tempFile = $_FILES['PaketPengadaan']['tmp_name']['file'];
                $fileTypes = array('xls', 'xlsx');
                $fileParts = pathinfo($_FILES['PaketPengadaan']['name']['file']);
                if (in_array(@$fileParts['extension'], $fileTypes)) {
                    $fileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($tempFile);
                    $objPHPExcelReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($fileType);
                    $spreadsheet = $objPHPExcelReader->load($tempFile);
                    // $worksheet = $spreadsheet->getActiveSheet();
                    // $highestRow = $worksheet->getHighestRow();
                    $inserted = 0;
                    $errorCount = 0;
                    $parentSheet = $spreadsheet->getSheet(0); // Assuming Sheet1 is the first sheet
                    $highestRowParent = $parentSheet->getHighestRow();
                    for ($row = 2; $row <= $highestRowParent; ++$row) {
                        $parentModel = new PaketPengadaan;
                        $parentModel->nomor = $parentSheet->getCellByColumnAndRow(2, $row)->getValue();
                        $parentModel->tanggal_paket = $parentSheet->getCellByColumnAndRow(3, $row)->getValue();
                        $parentModel->nama_paket = $parentSheet->getCellByColumnAndRow(4, $row)->getValue();
                        $parentModel->kode_program = $parentSheet->getCellByColumnAndRow(5, $row)->getValue();
                        $parentModel->kode_kegiatan = $parentSheet->getCellByColumnAndRow(6, $row)->getValue();
                        $parentModel->kode_rekening = $parentSheet->getCellByColumnAndRow(7, $row)->getValue();
                        $parentModel->ppkom = $parentSheet->getCellByColumnAndRow(8, $row)->getValue();
                        $parentModel->pagu = $parentSheet->getCellByColumnAndRow(9, $row)->getValue();
                        $parentModel->metode_pengadaan = $parentSheet->getCellByColumnAndRow(10, $row)->getValue();
                        $parentModel->tahun_anggaran = $parentSheet->getCellByColumnAndRow(11, $row)->getValue();
                        if ($parentModel->save(false)) {
                            $childSheet = $spreadsheet->getSheet(1); // Assuming Sheet2 is the second sheet
                            $highestRowChild = $childSheet->getHighestRow();
                            for ($childRow = 2; $childRow <= $highestRowChild; ++$childRow) {
                                $childModel = new PaketPengadaanDetails;
                                $childModel->paket_id = $parentModel->id;
                                $childModel->nama_produk = $childSheet->getCellByColumnAndRow(2, $childRow)->getValue();
                                $childModel->volume = $childSheet->getCellByColumnAndRow(3, $childRow)->getValue();
                                $childModel->satuan = $childSheet->getCellByColumnAndRow(4, $childRow)->getValue();
                                $childModel->durasi = $childSheet->getCellByColumnAndRow(5, $childRow)->getValue();
                                $childModel->harga = $childSheet->getCellByColumnAndRow(6, $childRow)->getValue();
                                $childModel->informasi_harga = $childSheet->getCellByColumnAndRow(7, $childRow)->getValue();
                                $childModel->hps = $childSheet->getCellByColumnAndRow(8, $childRow)->getValue();
                                $childModel->jumlah = $childSheet->getCellByColumnAndRow(9, $childRow)->getValue();
                                $childModel->sumber_informasi = $childSheet->getCellByColumnAndRow(10, $childRow)->getValue();
                                $childModel->save(false);
                                $inserted++;
                            }
                        }
                    }
                    Yii::$app->session->setFlash('success', ($inserted) . ' row inserted');
                } else {
                    Yii::$app->session->setFlash('warning', "Wrong file type (xlsx, xls) only");
                }
            }
            return $this->redirect(['index']);
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
                ($model->pemenang?'':
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
            if ($request->isGet) {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " PaketPengadaan",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Create'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
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
    public function actionCeklistadmin($id){
        $request = Yii::$app->request;
        $title="Kelengkapan DPP";
        $model=PaketPengadaan::find()->cache(false)->where(['id'=>$id])->one();
        if($request->isGet){
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
            if(!$model->addition){
                $data=['template'=>$temp];
                $model->addition=json_encode($data, JSON_UNESCAPED_SLASHES);
                $model->save();
            }
            $dataPaket=PaketPengadaan::collectAll(['approval_by' => null,'pemenang'=>null,'id'=>$id])->pluck('nomornamapaket', 'id')->toArray();
            return $this->render('_checklistadmin', ['model'=>$model,'dataPaket'=>$dataPaket,'temp'=>$temp,'title'=>$title]);
        }
        if($request->isPost){
            $template = $request->post('PaketPengadaan')['template'];
            $pure1 = collect($template)->map(function ($e) {
                foreach ($e as $key => $value) {
                    $e[$key] = HtmlPurifier::process($value);
                }
                if(key_exists('sesuai',$e)){
                    if($e['sesuai']=='on'){
                        $e['sesuai']=1;
                    }
                }
                return $e;
            });
            $model->addition=json_encode([
                'unit'=>$_POST['PaketPengadaan']['unit'],
                'id'=>$_POST['PaketPengadaan']['id'],
                'template'=>$pure1
            ],JSON_UNESCAPED_SLASHES);
            $model->save();
            Yii::$app->session->setFlash('success', 'Kelengkapan DPP Berhasil Ditambahkan');
            return $this->redirect('index');
        }
    }
    public function actionPrintceklistadmin($id){
        $model=$this->findModel($id);
        $title='Ceklist Kelengkapan DPP';
        $data=[
            'unit'=>Unit::findOne(json_decode($model->addition,true)['unit'])->unit,
            'paket'=>$model->nama_paket,
            'details'=>collect(json_decode($model->addition,true)['template'])->map(function ($e) {
                if(key_exists('sesuai',$e)){
                    $e['sesuai'] = $e['sesuai'] == 1 ? 'Ya' : 'Tidak';
                }else{
                    // $e['sesuai'] = 'Tidak';
                }
                return $e;
            })->toArray(),
            'logogresik'=>Yii::getAlias('@webroot/images/logogresik.png', true),
            'logors'=>Yii::getAlias('@webroot/images/logors.png', true),
            'kepalapengadaan'=>null,
            'nipkepalapengadaan'=>'',
            'admin'=>null,
            'nipadmin'=>''
        ];
        $cetakan=$this->renderPartial('_printceklistadmin', ['data'=>$data,
        'model'=>$model,'title'=>$title]);
        $pdf=Yii::$app->pdf;
        $pdf->content=$cetakan;
        $pdf->cssFile = '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css';
        return $pdf->render();
    }
    public function actionKirimulang($id) {
        $model = $this->findModel($id);
        if (Yii::$app->request->isGet) {
            return $this->render('/historireject/update', ['model' => $model->historireject,]);
        }
        if (Yii::$app->request->isPost) {
            $histori=$model->historireject;
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
        if($model->pemenang){
            Yii::$app->session->setFlash('warning', 'PaketPengadaan Sudah ada Pemenang');
            return $this->redirect('index');
        }
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " PaketPengadaan #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
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
        $model=$this->findModel($id);
        if($model->pemenang){
                Yii::$app->session->setFlash('warning', 'PaketPengadaan Sudah ada Pemenang');
                return $this->redirect('index');
            }
        $model->unlinkAll('details', true);
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
            if($model->pemenang){
                Yii::$app->session->setFlash('warning', 'PaketPengadaan Sudah ada pemenang');
                return $this->redirect('index');
            }
            $model->unlinkAll('details', true);
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
