<?php
namespace app\controllers;
use app\models\{Setting,Negosiasi,PenugasanPemilihanpenyedia,Unit,HistoriReject,ReviewDpp, Dpp, DppSearch, PaketPengadaanDetails, PenawaranPengadaan, TemplateChecklistEvaluasi, ValidasiKualifikasiPenyedia};
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\filters\VerbFilter;
use yii\helpers\{ArrayHelper,Html};
use yii\web\{BadRequestHttpException,Response, NotFoundHttpException};
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
        $model = Dpp::last(['paket_id'=>$idpaket]);
        // if(!$model::isAdmin()){
        //     throw new BadRequestHttpException('Unauthorized access');
        // }
        $model->paketpengadaan->pemenang=$idvendor;
        $pn1=$model->paketpengadaan->penawaranpenyedia;
        if($pn1){
            if($pn1->negosiasi){
                $pn1->negosiasi->accept=1;
                $pn1->negosiasi->save();
            }else{
                $negosiasi=new Negosiasi();
                $negosiasi->ammount=$pn1->nilai_penawaran;
                $negosiasi->penawaran_id=$pn1->id;
                $negosiasi->accept=1;
                $negosiasi->save();
            }
        }else{
            throw new NotFoundHttpException('Penawaran penyedia tidak ditemukan');
        }
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
        $penawaran=PenawaranPengadaan::collectAll(['paket_id' => $model->paket_id]);
        if(count($penawaran->toArray())>0){
            return $this->render('tab', [
                'model' => $model,'penawaran'=>$penawaran
            ]);
        }else{
            Yii::$app->session->setFlash('warning', 'Tidak ada penawaran yang tersedia');
            return $this->redirect('index');
        }
    }
    public function actionListpemenang($params){
        $tmp = TemplateChecklistEvaluasi::last(['template' => 'Ceklist_Evaluasi_Kesimpulan']);
        if (!$tmp) {
            return [];
        }
        $lolos = ValidasiKualifikasiPenyedia::where(['template' => $tmp->id, 'paket_pengadaan_id' => $params['paket_pengadaan_id']])
            ->joinWith('detail')
            ->asArray()
            ->all();
        $filtered = array_filter($lolos, function ($item) {
            return $item['detail']['hasil'] === '[{"uraian":"Catatan Oleh Pejabat Pengadaan","komentar":"Lolos Administrasi Validasi Dokumen","sesuai":""}]';
        });
        $mapPenawaran = array_map(function ($e) {
            return PenawaranPengadaan::find()
                ->where(['paket_id' => $e['paket_pengadaan_id'], 'penyedia_id' => $e['penyedia_id']])
                ->select(['paket_id','penyedia_id',
                    new Expression('COALESCE(negosiasi.ammount, nilai_penawaran) as nilai_penawaran'),
                    'nilai_penawaran as _nilai_penawaran'
                ])
                ->joinWith('negosiasi')
                ->one();
        }, $filtered);
        usort($mapPenawaran, function ($a, $b) {
            return $a['nilai_penawaran'] <=> $b['nilai_penawaran'];
        });
        return array_values($mapPenawaran);
    }
    public function actionView($id) {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "Dpp #" . $id,
                'content' => $this->renderAjax('view', [
                    'model' => $model,
                ]),
                'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                ( $model->paketpengadaan->pemenang?'':
                Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'data-target' => '#' . $model->hash, 'role' => 'modal-remote'])
                )
            ];
        } else {
            return $this->render('view', [
                'model' => $model,
            ]);
        }
    }
    public function actionAssign() {
        $request = Yii::$app->request;
        $pks = explode(',', $request->post('pks'));
        foreach ($pks as $pk) {
            $model = $this->findModel($pk);
            if ($model) {
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
    public function actionPenugasan($id){
        $penugasan=$this->findModel($id)->penugasan;
        if($penugasan){
            $anotherController = new PenugasanController('penugasan', $this->module);
           return $result = $anotherController->actionUpdate($id=$penugasan->id);
        }else{
            $anotherController = new PenugasanController('penugasan', $this->module);
           return $result = $anotherController->actionCreate($iddpp=$id);
        }
    }
    public function actionCetakpenugasan($id){
        $dpp = $this->findModel($id);
        $model = $dpp->paketpengadaan;
        $chief = \app\models\Pegawai::findOne($dpp::profile('kepalapengadaan'));
        $dataCeklist = [
            'unit' => Unit::findOne(json_decode($model->addition, true)['unit'] ?? $model->unit)->unit ?? '',
            'paket' => $model->nama_paket ?? '',
            'details' => collect(json_decode($model->addition, true)['template'])->map(function ($e) {
                $e['sesuai'] = isset($e['sesuai']) && $e['sesuai'] == 1 ? 'Ya' : 'Tidak';
                return $e;
            })->toArray(),
            'logogresik' => Yii::getAlias('@webroot/images/logogresik.png', true),
            'logors' => Yii::getAlias('@webroot/images/logors.png', true),
            'kepalapengadaan' => $chief->nama ?? '',
            'nipkepalapengadaan' => $chief->nip ?? '',
            'admin' => $dpp->staffadmin->nama ?? '',
            'nipadmin' => $dpp->staffadmin->nip ?? '',
            'kurir' => $model->kurirnya->nama ?? '',
            'nipkurir' => $model->kurirnya->nip ?? '',
        ];
        $content1 = $this->renderPartial('/paketpengadaan/_printceklistadmin', [
            'data' => $dataCeklist,
            'model' => $model,
            'title' => 'Ceklist Kelengkapan DPP'
        ]);
        // Data for second report (Cetak Penugasan)
        $penugasan = $dpp->penugasan;
        $paketpengadaan = $dpp->paketpengadaan;
        $dataPenugasan = [
            'nomor_tugas' => $penugasan->nomor_tugas ?? '',
            'nomorpersetujuan' => $paketpengadaan->nomor_persetujuan ?? '',
            'tanggalpersetujuan' => $paketpengadaan->tanggal_persetujuan ?? '',
            'perihal' => $paketpengadaan->nama_paket ?? '',
            'nomordpp' => $paketpengadaan->nomor ?? '',
            'tanggaldpp' => $paketpengadaan->tanggal_dpp ?? '',
            'bidang' => $paketpengadaan->unitnya->unit ?? '',
            'paketpengadaan' => $paketpengadaan->nama_paket ?? '',
            'logogresik' => Yii::getAlias('@webroot/images/logogresik.png', true),
            'logors' =>Yii::getAlias('@webroot/images/logors.png', true),
            'kepalapengadaan' => $chief->nama ?? '',
            'nipkepalapengadaan' => $chief->nip ?? '',
            'admin' => $dpp->staffadmin->nama ?? '',
            'pejabat' => $dpp->pejabat->nama ?? ''
        ];
        $content2 = $this->renderPartial('_suratpenugasan', [
            'data' => $dataPenugasan,
            'model' => $dpp
        ]);
        $pdf = Yii::$app->pdf;
        $pdf->orientation = 'L';  // Landscape orientation
         $combinedContent = "
            <table style='width:100%;'>
                <tr>
                    <td style='width:50%; vertical-align:top;'>
                        $content2
                    </td>
                    <td style='width:50%; vertical-align:top;'>
                        $content1
                    </td>
                </tr>
            </table>
        ";
        $pdf->content = $combinedContent;
        $pdf->cssFile = '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css';
        // Render the final PDF
        return $pdf->render();
    }
    public function actionCetakpenugasanold($id){
        $dpp=$this->findModel($id);
        $penugasan=$dpp->penugasan;
        $paketpengadaan=$dpp->paketpengadaan;
        $chief=\app\models\Pegawai::findOne($dpp::profile('kepalapengadaan'));
        $data=[
            'nomor_tugas'=>$penugasan->nomor_tugas??'',
            'nomorpersetujuan'=>$paketpengadaan->nomor_persetujuan??'',
            'tanggalpersetujuan'=>$paketpengadaan->tanggal_persetujuan??'',
            'perihal'=>$paketpengadaan->nama_paket??'',
            'nomordpp'=>$paketpengadaan->nomor??'',
            'tanggaldpp'=>$paketpengadaan->tanggal_dpp??'',
            'bidang'=>$paketpengadaan->unitnya->unit??'',
            'paketpengadaan'=>$paketpengadaan->nama_paket??'',
            'logogresik'=>Yii::getAlias('@webroot/images/logogresik.png', true),
            'logors'=>Yii::getAlias('@webroot/images/logors.png', true),
            'kepalapengadaan'=>$chief->nama??'',
            'nipkepalapengadaan'=>$chief->nip??'',
            'admin'=>$dpp->staffadmin->nama??'',
            'pejabat'=>$dpp->pejabat->nama??'',
        ];
        $cetakan=$this->renderPartial('_suratpenugasan', ['data'=>$data,'model'=>$dpp]);
        $pdf=Yii::$app->pdf;
        $pdf->content=$cetakan;
        $pdf->cssFile = '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css';
        return $pdf->render();
    }
    public function actionCetaklampiran($id){
        $dpp=$this->findModel($id);
        $paketpengadaan=$dpp->paketpengadaan;
        $pdf=Yii::$app->pdf;
        $pdf->marginTop=2;
        $pdf->marginBottom=2;
        $pdf->marginHeader=2;
        $pdf->marginFooter=2;
        $pdf->marginLeft=2;
        $pdf->marginRight=2;
        $pdf->format='A4';
        $pdf->content=$this->renderPartial('_cetaklampiran', ['paketpengadaan'=>$paketpengadaan]);
        $pdf->cssFile = '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css';
        return $pdf->render();
    }
    public function actionAssignadmin() {
        $request = Yii::$app->request;
        $pks = explode(',', $request->post('pks'));
        foreach ($pks as $pk) {
            $model = $this->findModel($pk);
            if ($model) {
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
        $paket = $this->findModel($id)->paketpengadaan;
        if ($request->isGet) {
            return $this->render('_frm_reject', ['model' => $paket]);
        } elseif ($request->isPost) {
            //update paketpengadaan
            if($paket->load($request->post())){
                $paket->save();
                Dpp::invalidatecache('tag_' . Dpp::getModelname());
                $paket::invalidatecache('tag_' . $paket::getModelname());
            }
            //update historireject
            $data=$_POST['PaketPengadaan'];
            $data['paket_id']=$data['id'];
            unset($data['id']);
            $historyreject= new HistoriReject();
            // $historyreject= HistoriReject::where(['paket_id' =>$data['paket_id']])->one()??new HistoriReject();
                $historyreject->attributes=$data;
                if ($historyreject->tanggal_reject && $historyreject->alasan_reject) {
                    $historyreject->save();
                    Dpp::invalidatecache('tag_' . Dpp::getModelname());
                    $historyreject::invalidatecache('tag_' . $historyreject::getModelname());
                } else {
                    throw new BadRequestHttpException('Data Gagal disimpan');
                }
                return $this->redirect(['index']);
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
            $logogresik= Yii::getAlias('@webroot') . '/images/logogresik.png';
            $logors= Yii::getAlias('@webroot') . '/images/logors.png';
            $pdf = Yii::$app->pdf;
            $pdf->content = $this->renderPartial('_reviewdpp', [
                'model' => $model,
                'logogresik' => $logogresik,
                'logors' => $logors,
                'template' => $model->reviews ?? []
            ]);
            $pdf->cssInline = ".center{text-align:center}.border1solid {border: #eee 1px solid;}";
            return $pdf->render();
        } else {
            Yii::$app->session->setFlash('warning', 'Belum ada review dpp');
            return $this->redirect(['index']);
        }
    }
    public function actionCeklistadmin($id){
        $request = Yii::$app->request;
        $title="Kelengkapan DPP";
        $dpp=$this->findModel($id);
        $model=$dpp->paketpengadaan; //model paketpengadaan
        $set=Setting::where(['type'=>'sequence','param'=>'nomortugas','active'=>1])->one();
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
            $dataPaket=$model::collectAll(['approval_by' => null,'pemenang'=>null,'id'=>$model->id])->pluck('nomornamapaket', 'id')->toArray();
            //penugasan
            $query = Dpp::where(['is', 'pp.pemenang', null])
                    ->joinWith(['paketpengadaan pp']);
            if ($id !== null) {
                $query->andWhere(['dpp.id' => $id]);
            }
            $datapenugasan=[
                'dpp'=> ArrayHelper::map($query->all(),'id','nomordpp'),
                'pejabat'=>$model::getAllpetugas(),
                'admin'=>$model::getAlladmin(),
            ];
            //endpenugasan
            //auto generate
            $nomortugas=(int)$set->value+1;
            return $this->render('ceklistadmin', [
                'datapenugasan' => $datapenugasan,'nomortugas'=>$nomortugas,
                'modelpenugasan'=>PenugasanPemilihanpenyedia::first(['dpp_id'=>$dpp->id]),
                'dpp'=>$dpp,'model'=>$model,'dataPaket'=>$dataPaket,'temp'=>$temp,'title'=>$title]);
        }
        if($request->isPost){
            $template = $request->post('CeklistModel')['template'];
            $pure1 = collect($template)->map(function ($e)use($model) {
                foreach ($e as $key => $value) {
                    $e[$key] = $model->getPurifier($value);
                }
                if (isset($e['sesuai']) && $e['sesuai'] === 'on') {
                    $e['sesuai'] = 1;
                }
                return $e;
            });
            $model->addition=json_encode([
                'unit'=>$_POST['CeklistModel']['unit'],
                'id'=>$_POST['CeklistModel']['paket_id'],
                'template'=>$pure1
            ],JSON_UNESCAPED_SLASHES);
            $model->save();
            $dpp->nomor_dpp=$_POST['CeklistModel']['nomor_dpp'];
            $dpp->bidang_bagian=$_POST['CeklistModel']['unit'];
            $dpp->pejabat_pengadaan=$_POST['CeklistModel']['pejabat'];
            $dpp->admin_pengadaan=$_POST['CeklistModel']['admin'];
            $dpp->save();
            $modelpenugasan = PenugasanPemilihanpenyedia::where(['dpp_id'=>$dpp->id])->one()??new PenugasanPemilihanpenyedia();
            $modelpenugasan->dpp_id=$dpp->id;
            $modelpenugasan->tanggal_tugas=$_POST['CeklistModel']['tanggal_tugas'];
            $modelpenugasan->nomor_tugas=$_POST['CeklistModel']['nomor_tugas'];
            $modelpenugasan->pejabat=$_POST['CeklistModel']['pejabat'];
            $modelpenugasan->admin=$_POST['CeklistModel']['admin'];
            $modelpenugasan->save();
            $set->value=$_POST['CeklistModel']['nomor_tugas'];
            $set->save();
            Yii::$app->session->setFlash('success', 'Kelengkapan DPP Berhasil Ditambahkan');
            return $this->redirect(['/dpp/index']);
        }
    }
    public function actionPrintceklistadmin($id){
        $dpp=$this->findModel($id);
        $model=$dpp->paketpengadaan;
        $title='Ceklist Kelengkapan DPP';
        $chief=\app\models\Pegawai::findOne($dpp::profile('kepalapengadaan'));
        $data=[
            'unit'=>Unit::findOne(json_decode($model->addition,true)['unit']??$model->unit)->unit??'',
            'paket'=>$model->nama_paket??'',
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
            'kepalapengadaan'=>$chief->nama??'',
            'nipkepalapengadaan'=>$chief->nip??'',
            'admin'=>$dpp->staffadmin->nama??'',
            'nipadmin'=>$dpp->staffadmin->nip??'',
            'kurir'=>$model->kurirnya->nama??'',
            'nipkurir'=>$model->kurirnya->nip??'',
        ];
        $cetakan=$this->renderPartial('/paketpengadaan/_printceklistadmin', ['data'=>$data,'model'=>$model,'title'=>$title]);
        $pdf=Yii::$app->pdf;
        $pdf->content=$cetakan;
        $pdf->cssFile = '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css';
        return $pdf->render();
    }
    public function actionFormreview($id) {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $r = file_get_contents(Yii::getAlias('@app/uraianreviewdpp.json'));
        $template = (json_decode($r));
        $reviews=$model->reviews;
        $histori=$model->paketpengadaan->historireject;
        if($histori){
            $reviews->keterangan=$histori->alasan_reject;
            $reviews->kesimpulan=$histori->kesimpulan;
            $reviews->tgl_dikembalikan=$histori->tanggal_dikembalikan;
            $reviews->tanggapan_ppk=$histori->tanggapan_ppk;
            $reviews->file_tanggapan=$histori->file_tanggapan;
        }
        if ($request->isGet) {
            return $this->render('_frmreviewdpp', [
                'model' => $model,
                'reviews' => $reviews ?? new ReviewDpp,
                'template' => $template->data ?? [],
            ]);
        }
        if ($request->isPost) {
            $paket=$model->paketpengadaan;
            $historireject=$paket->historireject;
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
            if ($model->load($request->post()) && $model->save()) {
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
        if($model->paketpengadaan->pemenang){
            Yii::$app->session->setFlash('warning', 'Pemenang sudah ditentukan');
            return $this->goBack(Yii::$app->request->referrer ?: ['//dpp/index']);
        }
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load($request->post()) && $model->save()) {
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
        $model=$this->findModel($id);
        if($model->paketpengadaan->pemenang){
                Yii::$app->session->setFlash('warning', 'PaketPengadaan Sudah ada Pemenang');
                return $this->redirect('index');
            }
        $model->paketpengadaan->addition=null;
        $model->paketpengadaan->save();
        $model->unlinkAll('reviews',true);
        $model->unlinkAll('penugasan',true);
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
            if($model->paketpengadaan->pemenang){
                Yii::$app->session->setFlash('warning', 'PaketPengadaan Sudah ada Pemenang');
                return $this->redirect('index');
            }
            $model->paketpengadaan->addition=null;
            $model->paketpengadaan->save();
            $model->unlinkAll('reviews',true);
            $model->unlinkAll('penugasan',true);
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
