<?php
namespace app\controllers;

use Yii;
use app\models\PaketPengadaan;
use app\models\Dpp;
use app\models\Penyedia;
use app\models\Pegawai;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\web\UploadedFile;

class ImportpaketController extends Controller {

    public function actionIndex() {
        if (Yii::$app->request->isPost) {
            $file = UploadedFile::getInstanceByName('file');
            if ($file) {
                try {
                    $spreadsheet = IOFactory::load($file->tempName);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $rows = $worksheet->toArray();
                    
                    $successCount = 0;
                    $errorCount = 0;
                    
                    // Skip header row (index 0)
                    for ($i = 1; $i < count($rows); $i++) {
                        $row = $rows[$i];
                        
                        if (empty($row[1])) continue; // Skip jika Nama Paket kosong
                        
                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            $paket = new PaketPengadaan();
                            $paket->nomor = !empty($row[0]) ? (string)$row[0] : 'IMP-'.time().'-'.$i;
                            $paket->nama_paket = (string)$row[1];
                            $paket->tanggal_paket = !empty($row[2]) ? (string)$row[2] : date('Y-m-d H:i:s');
                            $paket->pagu = (float)($row[3] ?? 0);
                            $paket->pemenang = !empty($row[4]) ? $row[4] : null;
                            $paket->ppkom = !empty($row[6]) ? $row[6] : null;
                            $paket->tahun_anggaran = !empty($row[7]) ? (int)$row[7] : date('Y');
                            
                            // Field Dummy
                            $paket->tanggal_dpp = $paket->tanggal_paket;
                            $paket->tanggal_persetujuan = $paket->tanggal_paket;
                            $paket->nomor_persetujuan = 'IMP-' . time();
                            $paket->kode_program = '-';
                            $paket->kode_kegiatan = '-';
                            $paket->kode_rekening = '-';
                            $paket->unit = 1; 
                            $paket->metode_pengadaan = 'PL'; 
                            $paket->kategori_pengadaan = 'barang/jasa';
                            $paket->is_import = 1; // FLAG IMPORT
                            
                            if ($paket->save(false)) {
                                $dpp = new Dpp();
                                $dpp->nomor_dpp = 'DPP-IMP-' . $paket->id;
                                $dpp->paket_id = $paket->id;
                                $dpp->pejabat_pengadaan = !empty($row[5]) ? $row[5] : null;
                                $dpp->tanggal_dpp = $paket->tanggal_paket;
                                $dpp->save(false);
                                
                                // Insert Dummy Detail
                                $detail = new \app\models\PaketPengadaanDetails();
                                $detail->paket_id = $paket->id;
                                $detail->nama_produk = $paket->nama_paket;
                                $detail->qty = 1;
                                $detail->volume = 1;
                                $detail->satuan = 'ls';
                                $detail->hps_satuan = $paket->pagu;
                                $detail->penawaran = $paket->pagu;
                                $detail->negosiasi = $paket->pagu;
                                $detail->save(false);
                                
                                $successCount++;
                                $transaction->commit();
                            } else {
                                $errorCount++;
                                $transaction->rollBack();
                            }
                        } catch (\Exception $e) {
                            $errorCount++;
                            $transaction->rollBack();
                            Yii::error("Import error row $i: " . $e->getMessage());
                        }
                    }
                    
                    Yii::$app->session->setFlash('success', "Import Selesai. Berhasil: $successCount, Gagal/Error: $errorCount");
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', "Gagal memproses file Excel: " . $e->getMessage());
                }
                
                return $this->redirect(['index']);
            }
        }
        return $this->render('index');
    }
    
    public function actionTemplate() {
        $spreadsheet = new Spreadsheet();
        
        // Sheet 1: Form Import
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Form Import');
        
        $headers = [
            'A1' => 'Nomor Paket',
            'B1' => 'Nama Paket',
            'C1' => 'Tanggal Paket (YYYY-MM-DD)',
            'D1' => 'Pagu (Angka)',
            'E1' => 'ID Vendor (Penyedia)',
            'F1' => 'ID Pejabat Pengadaan',
            'G1' => 'ID PPK (Pejabat Pembuat Komitmen)',
            'H1' => 'Tahun Anggaran'
        ];
        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }
        
        // Buat kolom agak lebar
        foreach(range('A','H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Sheet 2: Data Master
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Data Master');
        
        $sheet2->setCellValue('A1', 'DATA VENDOR (PENYEDIA)');
        $sheet2->setCellValue('A2', 'ID');
        $sheet2->setCellValue('B2', 'Nama Vendor');
        $sheet2->getStyle('A1:B2')->getFont()->setBold(true);
        
        $vendors = Penyedia::find()->where(['active' => 1])->all();
        $row = 3;
        foreach ($vendors as $v) {
            $sheet2->setCellValue('A'.$row, $v->id);
            $sheet2->setCellValue('B'.$row, $v->nama_perusahaan);
            $row++;
        }
        
        $sheet2->setCellValue('D1', 'DATA PEGAWAI (PEJABAT/PPK)');
        $sheet2->setCellValue('D2', 'ID');
        $sheet2->setCellValue('E2', 'Nama Pegawai');
        $sheet2->getStyle('D1:E2')->getFont()->setBold(true);
        
        $pegawai = Pegawai::find()->where(['status' => '1'])->all();
        $row = 3;
        foreach ($pegawai as $p) {
            $sheet2->setCellValue('D'.$row, $p->id);
            $sheet2->setCellValue('E'.$row, $p->nama);
            $row++;
        }
        
        foreach(range('A','E') as $columnID) {
            $sheet2->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        $spreadsheet->setActiveSheetIndex(0);
        
        // Clean output buffer before sending binary data
        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $content = ob_get_clean();
        
        return Yii::$app->response->sendContentAsFile($content, 'Template_Import_Paket.xlsx', [
            'mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'inline' => false
        ]);
    }
}
