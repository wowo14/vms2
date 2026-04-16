<?php
namespace app\controllers;
use Yii;
use app\models\PenilaianPenyedia;
use app\models\PenilaianPenyediaSearch;
use yii\web\Controller;
use yii\web\{Response,NotFoundHttpException};
use yii\filters\VerbFilter;
use yii\helpers\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use kartik\mpdf\Pdf;
class PenilaianController extends Controller
{
    public function behaviors()
    {
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
    public function actionIndex()
    {
        $searchModel = new PenilaianPenyediaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionView($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "PenilaianPenyedia #".$id,
                'content' =>$this->renderAjax('view', [
                    'model' => $this->findModel($id),
                ]),
                'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal'])
                    // Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
            ];
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }
    // public function actionCreate()
    // {
    //     $request = Yii::$app->request;
    //     $model = new PenilaianPenyedia();
    //     if($request->isAjax){

    //         Yii::$app->response->format = Response::FORMAT_JSON;
    //         if($model->load($request->post()) && $model->save()){
    //             return [
    //                 'forceReload' => '#crud-datatable-pjax',
    //                 'title' => Yii::t('yii2-ajaxcrud', 'Create New')." PenilaianPenyedia",
    //                 'content' => '<span class="text-success">'.Yii::t('yii2-ajaxcrud', 'Create').' PenilaianPenyedia '.Yii::t('yii2-ajaxcrud', 'Success').'</span>',
    //                 'footer' =>  Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']).
    //                     Html::a(Yii::t('yii2-ajaxcrud', 'Create More'), ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
    //             ];
    //         }else{
    //             return [
    //                 'title' => Yii::t('yii2-ajaxcrud', 'Create New')." PenilaianPenyedia",
    //                 'content' => $this->renderAjax('create', [
    //                     'model' => $model,
    //                 ]),
    //                 'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']).
    //                     Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
    //             ];
    //         }
    //     }else{

    //         if ($model->load($request->post()) && $model->save()){
    //             return $this->redirect(['view', 'id' => $model->id]);
    //         }else{
    //             return $this->render('create', [
    //                 'model' => $model,
    //             ]);
    //         }
    //     }
    // }
    // public function actionUpdate($id)
    // {
    //     $request = Yii::$app->request;
    //     $model = $this->findModel($id);
    //     if($request->isAjax){
    //         Yii::$app->response->format = Response::FORMAT_JSON;
    //         if($model->load($request->post()) && $model->save()){
    //             return [
    //                 'forceReload' => '#crud-datatable-pjax',
    //                 'title' => "PenilaianPenyedia #".$id,
    //                 'content' => $this->renderAjax('view', [
    //                     'model' => $model,
    //                 ]),
    //                 'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']).
    //                     Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id],['class' => 'btn btn-primary', 'role' => 'modal-remote'])
    //             ];
    //         }else{
    //              return [
    //                 'title' => Yii::t('yii2-ajaxcrud', 'Update')." PenilaianPenyedia #".$id,
    //                 'content' => $this->renderAjax('update', [
    //                     'model' => $model,
    //                 ]),
    //                 'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']).
    //                     Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
    //             ];
    //         }
    //     }else{
    //         if ($model->load($request->post()) && $model->save()){
    //             return $this->redirect(['view', 'id' => $model->id]);
    //         }else{
    //             return $this->render('update', [
    //                 'model' => $model,
    //             ]);
    //         }
    //     }
    // }
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $this->findModel($id)->delete();
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            return $this->redirect(['index']);
        }
    }
    public function actionBulkdelete()
    {
        $request = Yii::$app->request;
        $pks = explode(',', $request->post( 'pks' ));
        foreach ( $pks as $pk ){
            $model = $this->findModel($pk);
            $model->delete();
        }
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            return $this->redirect(['index']);
        }
    }
    public function actionEvaluasi()
    {
        $request = Yii::$app->request;
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');
        $vendor_id = $request->get('vendor_id');

        $query = PenilaianPenyedia::find();
        
        if ($tahun && $tahun != 'all') {
            $query->andWhere(['strftime("%Y", tanggal_kontrak)' => (string)$tahun]);
        }
        if ($bulan && $bulan != 'all') {
            $query->andWhere(['strftime("%m", tanggal_kontrak)' => str_pad($bulan, 2, '0', STR_PAD_LEFT)]);
        }
        if ($vendor_id && $vendor_id != 'all') {
            $p = \app\models\Penyedia::findOne($vendor_id);
            if ($p) {
                $query->andWhere(['nama_perusahaan' => $p->nama_perusahaan]);
            }
        }

        $data = $query->all();
        
        $summary = [];
        foreach ($data as $item) {
            $details = json_decode($item->details, true);
            if (!$details) continue;
            
            $provider = $item->nama_perusahaan;
            
            if (!isset($summary[$provider])) {
                $summary[$provider] = [
                    'nama' => $provider,
                    'count' => 0,
                    'total_score' => 0,
                    'avg_score' => 0,
                    'total_nilai' => 0
                ];
            }
            
            $nilaiakhir_str = $details['nilaiakhir'] ?? 0;
            $nilaiakhir_num = 0;
            if (is_string($nilaiakhir_str) && strpos($nilaiakhir_str, '=') !== false) {
                $parts = explode('=', $nilaiakhir_str);
                $nilaiakhir_num = (float) trim(end($parts));
            } else {
                $nilaiakhir_num = (float) $nilaiakhir_str;
            }
            
            $summary[$provider]['count']++;
            $summary[$provider]['total_score'] += $nilaiakhir_num;
            $summary[$provider]['total_nilai'] += $item->nilai_kontrak;
        }
        
        $sort = $request->get('sort', 'rating_desc');
        
        foreach ($summary as &$s) {
            $s['avg_score'] = $s['count'] > 0 ? round($s['total_score'] / $s['count'], 2) : 0;
        }

        // Sorting logic
        uasort($summary, function($a, $b) use ($sort) {
            switch ($sort) {
                case 'rating_asc':
                    return $a['avg_score'] <=> $b['avg_score'];
                case 'rating_desc':
                    return $b['avg_score'] <=> $a['avg_score'];
                case 'count_asc':
                    return $a['count'] <=> $b['count'];
                case 'count_desc':
                    return $b['count'] <=> $a['count'];
                case 'nilai_asc':
                    return $a['total_nilai'] <=> $b['total_nilai'];
                case 'nilai_desc':
                    return $b['total_nilai'] <=> $a['total_nilai'];
                default:
                    return $b['avg_score'] <=> $a['avg_score'];
            }
        });

        if ($request->get('export') == 'excel') {
            return $this->exportExcel($data, $summary);
        }
        
        if ($request->get('export') == 'pdf') {
            return $this->exportPdf($data, $summary);
        }

        return $this->render('evaluasi', [
            'summary' => $summary,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'vendor_id' => $vendor_id,
            'sort' => $sort
        ]);
    }

    public function exportExcel($data, $summary)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Style array for borders
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        // Title
        $sheet->mergeCells('A1:T1');
        $sheet->setCellValue('A1', 'REKAPITULASI PENILAIAN PENYEDIA BARANG OLEH PEJABAT PEMBUAT KOMITMEN');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header Structure
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'R', 'S', 'T'];
        foreach ($columns as $col) {
            $sheet->mergeCells($col.'3:'.$col.'4');
        }

        $sheet->setCellValue('A3', 'No');
        $sheet->setCellValue('B3', 'Nama Penyedia');
        $sheet->setCellValue('C3', 'Alamat');
        $sheet->setCellValue('D3', 'Kategori');
        $sheet->setCellValue('E3', 'Nama Kegiatan');
        $sheet->setCellValue('F3', 'Bidang/Bagian');
        $sheet->setCellValue('G3', 'Metode Pemilihan');
        $sheet->setCellValue('H3', 'Tanggal Kontrak');
        $sheet->setCellValue('I3', 'Nilai Kontrak');

        $sheet->mergeCells('J3:M3');
        $sheet->setCellValue('J3', 'Skor Penilaian');
        $sheet->setCellValue('J4', '1');
        $sheet->setCellValue('K4', '2');
        $sheet->setCellValue('L4', '3');
        $sheet->setCellValue('M4', '4');

        $sheet->mergeCells('N3:Q3');
        $sheet->setCellValue('N3', 'Bobot Penilaian');
        // Retrieve weights from setting or default
        $setting = \app\models\Setting::findOne(['type' => 'evaluasi_suplier_ppk', 'active' => 1]);
        $weights = [20, 20, 30, 30];
        if ($setting) {
            $config = json_decode($setting->value, true);
            if (isset($config['kriteria']) && is_array($config['kriteria'])) {
                foreach ($config['kriteria'] as $idx => $crit) {
                    if (isset($crit['bobot']) && isset($weights[$idx])) {
                        $weights[$idx] = floatval($crit['bobot']);
                    }
                }
            }
        }
        
        $sheet->setCellValue('N4', $weights[0].'%');
        $sheet->setCellValue('O4', $weights[1].'%');
        $sheet->setCellValue('P4', $weights[2].'%');
        $sheet->setCellValue('Q4', $weights[3].'%');

        $sheet->setCellValue('R3', 'Nilai Kinerja');
        $sheet->setCellValue('S3', 'Hasil Evaluasi');
        $sheet->setCellValue('T3', 'Keterangan');

        // Apply bold and center to header
        $sheet->getStyle('A3:T4')->getFont()->setBold(true);
        $sheet->getStyle('A3:T4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:T4')->applyFromArray($styleArray);

        // adjust column widths
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('R')->setWidth(15);
        $sheet->getColumnDimension('S')->setWidth(15);
        $sheet->getColumnDimension('T')->setWidth(20);

        $rowNum = 5;
        $no = 1;

        foreach ($summary as $providerName => $s) {
            foreach ($data as $item) {
                if ($item->nama_perusahaan !== $providerName) continue;
                
                $details = json_decode($item->details, true);
                if (!$details) continue;

                $penyedia = \app\models\Penyedia::findOne(['nama_perusahaan' => $item->nama_perusahaan]);
                $kategori = $penyedia ? $penyedia->kategori_usaha : '';
                $alamat = $penyedia ? $penyedia->alamat_perusahaan : $item->alamat_perusahaan;

                $sheet->setCellValue('A'.$rowNum, $no++);
                $sheet->setCellValue('B'.$rowNum, $item->nama_perusahaan);
                $sheet->setCellValue('C'.$rowNum, $alamat);
                $sheet->setCellValue('D'.$rowNum, $kategori);
                $sheet->setCellValue('E'.$rowNum, $item->paket_pekerjaan);
                $sheet->setCellValue('F'.$rowNum, $item->unit_kerja);
                $sheet->setCellValue('G'.$rowNum, $item->metode_pemilihan);
                $sheet->setCellValue('H'.$rowNum, $item->tanggal_kontrak ? date('d-m-Y', strtotime($item->tanggal_kontrak)) : '');
                $sheet->setCellValue('I'.$rowNum, $item->nilai_kontrak);
                $sheet->getStyle('I'.$rowNum)->getNumberFormat()->setFormatCode('#,##0');

                $skor1 = isset($details['skor'][0]) ? floatval($details['skor'][0]) : 0;
                $skor2 = isset($details['skor'][1]) ? floatval($details['skor'][1]) : 0;
                $skor3 = isset($details['skor'][2]) ? floatval($details['skor'][2]) : 0;
                $skor4 = isset($details['skor'][3]) ? floatval($details['skor'][3]) : 0;
                
                $bobot1 = ($skor1 * $weights[0]) / 100;
                $bobot2 = ($skor2 * $weights[1]) / 100;
                $bobot3 = ($skor3 * $weights[2]) / 100;
                $bobot4 = ($skor4 * $weights[3]) / 100;
                
                $sheet->setCellValue('J'.$rowNum, $skor1);
                $sheet->setCellValue('K'.$rowNum, $skor2);
                $sheet->setCellValue('L'.$rowNum, $skor3);
                $sheet->setCellValue('M'.$rowNum, $skor4);
                
                $sheet->setCellValue('N'.$rowNum, $bobot1);
                $sheet->setCellValue('O'.$rowNum, $bobot2);
                $sheet->setCellValue('P'.$rowNum, $bobot3);
                $sheet->setCellValue('Q'.$rowNum, $bobot4);

                $nilaiakhir_str = $details['nilaiakhir'] ?? 0;
                if (is_string($nilaiakhir_str) && strpos($nilaiakhir_str, '=') !== false) {
                    $parts = explode('=', $nilaiakhir_str);
                    $nilai_kinerja = (float) trim(end($parts));
                } else {
                    $nilai_kinerja = (float) $nilaiakhir_str;
                }
                
                $sheet->setCellValue('R'.$rowNum, $nilai_kinerja);
                $sheet->setCellValue('S'.$rowNum, $details['hasil_evaluasi'] ?? '');
                $sheet->setCellValue('T'.$rowNum, $details['ulasan_pejabat_pengadaan'] ?? '');
                
                $sheet->getStyle('A'.$rowNum.':T'.$rowNum)->applyFromArray($styleArray);
                
                // align numbers
                $sheet->getStyle('J'.$rowNum.':R'.$rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $rowNum++;
            }
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekapitulasi_Penilaian_Penyedia_PPK_' . date('YmdHis') . '.xlsx';

        // Clear output buffer to prevent corrupting the file
        if (ob_get_length() > 0) {
            ob_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function exportPdf($data, $summary)
    {
        $setting = \app\models\Setting::findOne(['type' => 'evaluasi_suplier_ppk', 'active' => 1]);
        $weights = [20, 20, 30, 30];
        if ($setting) {
            $config = json_decode($setting->value, true);
            if (isset($config['kriteria']) && is_array($config['kriteria'])) {
                foreach ($config['kriteria'] as $idx => $crit) {
                    if (isset($crit['bobot']) && isset($weights[$idx])) {
                        $weights[$idx] = floatval($crit['bobot']);
                    }
                }
            }
        }

        $html = '<h4 style="text-align:center;">REKAPITULASI PENILAIAN PENYEDIA BARANG OLEH PEJABAT PEMBUAT KOMITMEN</h4>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%" style="font-size:10px;">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th rowspan="2">No</th>';
        $html .= '<th rowspan="2">Nama Penyedia</th>';
        $html .= '<th rowspan="2">Alamat</th>';
        $html .= '<th rowspan="2">Kategori</th>';
        $html .= '<th rowspan="2">Nama Kegiatan</th>';
        $html .= '<th rowspan="2">Bidang/Bagian</th>';
        $html .= '<th rowspan="2">Metode Pemilihan</th>';
        $html .= '<th rowspan="2">Tanggal Kontrak</th>';
        $html .= '<th rowspan="2">Nilai Kontrak</th>';
        $html .= '<th colspan="4" style="text-align:center;">Skor Penilaian</th>';
        $html .= '<th colspan="4" style="text-align:center;">Bobot Penilaian</th>';
        $html .= '<th rowspan="2">Nilai Kinerja</th>';
        $html .= '<th rowspan="2">Hasil Evaluasi</th>';
        $html .= '<th rowspan="2">Keterangan</th>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<th>1</th><th>2</th><th>3</th><th>4</th>';
        $html .= '<th>'.$weights[0].'%</th><th>'.$weights[1].'%</th><th>'.$weights[2].'%</th><th>'.$weights[3].'%</th>';
        $html .= '</tr>';
        $html .= '</thead><tbody>';

        $no = 1;
        foreach ($summary as $providerName => $s) {
            foreach ($data as $item) {
                if ($item->nama_perusahaan !== $providerName) continue;
                
                $details = json_decode($item->details, true);
                if (!$details) continue;

                $penyedia = \app\models\Penyedia::findOne(['nama_perusahaan' => $item->nama_perusahaan]);
                $kategori = $penyedia ? $penyedia->kategori_usaha : '';
                $alamat = $penyedia ? $penyedia->alamat_perusahaan : $item->alamat_perusahaan;

                $skor1 = isset($details['skor'][0]) ? floatval($details['skor'][0]) : 0;
                $skor2 = isset($details['skor'][1]) ? floatval($details['skor'][1]) : 0;
                $skor3 = isset($details['skor'][2]) ? floatval($details['skor'][2]) : 0;
                $skor4 = isset($details['skor'][3]) ? floatval($details['skor'][3]) : 0;
                
                $bobot1 = ($skor1 * $weights[0]) / 100;
                $bobot2 = ($skor2 * $weights[1]) / 100;
                $bobot3 = ($skor3 * $weights[2]) / 100;
                $bobot4 = ($skor4 * $weights[3]) / 100;

                $nilaiakhir_str = $details['nilaiakhir'] ?? 0;
                if (is_string($nilaiakhir_str) && strpos($nilaiakhir_str, '=') !== false) {
                    $parts = explode('=', $nilaiakhir_str);
                    $nilai_kinerja = (float) trim(end($parts));
                } else {
                    $nilai_kinerja = (float) $nilaiakhir_str;
                }

                $html .= '<tr>';
                $html .= '<td align="center">'.$no++.'</td>';
                $html .= '<td>'.Html::encode($item->nama_perusahaan).'</td>';
                $html .= '<td>'.Html::encode($alamat).'</td>';
                $html .= '<td>'.Html::encode($kategori).'</td>';
                $html .= '<td>'.Html::encode($item->paket_pekerjaan).'</td>';
                $html .= '<td>'.Html::encode($item->unit_kerja).'</td>';
                $html .= '<td>'.Html::encode($item->metode_pemilihan).'</td>';
                $html .= '<td>'.($item->tanggal_kontrak ? date('d-m-Y', strtotime($item->tanggal_kontrak)) : '').'</td>';
                $html .= '<td align="right">'.number_format($item->nilai_kontrak, 0, ',', '.').'</td>';
                $html .= '<td align="center">'.$skor1.'</td>';
                $html .= '<td align="center">'.$skor2.'</td>';
                $html .= '<td align="center">'.$skor3.'</td>';
                $html .= '<td align="center">'.$skor4.'</td>';
                $html .= '<td align="center">'.$bobot1.'</td>';
                $html .= '<td align="center">'.$bobot2.'</td>';
                $html .= '<td align="center">'.$bobot3.'</td>';
                $html .= '<td align="center">'.$bobot4.'</td>';
                $html .= '<td align="center">'.$nilai_kinerja.'</td>';
                $html .= '<td align="center">'.Html::encode($details['hasil_evaluasi'] ?? '').'</td>';
                $html .= '<td>'.Html::encode($details['ulasan_pejabat_pengadaan'] ?? '').'</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</tbody></table>';

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'destination' => Pdf::DEST_DOWNLOAD,
            'content' => $html,
            'cssInline' => 'table { font-size: 10px; border-collapse: collapse; } th, td { border: 1px solid black; padding: 5px; } th { background-color: #f2f2f2; text-align: center; font-weight: bold; }',
            'filename' => 'Rekapitulasi_Penilaian_Penyedia_PPK_' . date('YmdHis') . '.pdf',
            'options' => ['title' => 'Evaluasi Kinerja Penyedia'],
            'methods' => [
                'SetHeader' => ['|REKAPITULASI PENILAIAN PENYEDIA|'],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);

        return $pdf->render();
    }

    public function actionDrillDown($vendor_nama)
    {
        $request = Yii::$app->request;
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');

        $query = PenilaianPenyedia::find()->where(['nama_perusahaan' => $vendor_nama]);
        
        if ($tahun && $tahun != 'all') {
            $query->andWhere(['strftime("%Y", tanggal_kontrak)' => (string)$tahun]);
        }
        if ($bulan && $bulan != 'all') {
            $query->andWhere(['strftime("%m", tanggal_kontrak)' => str_pad($bulan, 2, '0', STR_PAD_LEFT)]);
        }

        $data = $query->all();
        
        return $this->renderAjax('drill_down', [
            'data' => $data,
            'vendor_nama' => $vendor_nama
        ]);
    }

    protected function findModel($id)
    {
        if (($model = PenilaianPenyedia::findOne($id)) !== null)
        {
            return $model;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}