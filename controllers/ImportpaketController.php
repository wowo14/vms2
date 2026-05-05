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
                    // Coba ambil sheet berdasarkan nama, atau default ke indeks 0
                    $worksheetPaket = $spreadsheet->getSheetByName('Form Import Paket');
                    if (!$worksheetPaket) $worksheetPaket = $spreadsheet->getSheet(0);
                    $rows = $worksheetPaket->toArray();
                    
                    // Coba ambil sheet detail
                    $worksheetDetail = $spreadsheet->getSheetByName('Form Detail Paket');
                    $rowsDetail = $worksheetDetail ? $worksheetDetail->toArray() : [];
                    
                    $detailMap = []; // Grouping by Nomor Paket
                    if (!empty($rowsDetail)) {
                        for ($j = 1; $j < count($rowsDetail); $j++) {
                            $rowD = $rowsDetail[$j];
                            // Skip jika Nomor Paket atau Nama Produk kosong
                            if (empty($rowD[0]) || empty($rowD[1])) continue; 
                            
                            $nomorPaket = trim((string)$rowD[0]);
                            $detailMap[$nomorPaket][] = $rowD;
                        }
                    }
                    
                    $successCount = 0;
                    $errorCount = 0;
                    
                    // Skip header row (index 0)
                    for ($i = 1; $i < count($rows); $i++) {
                        $row = $rows[$i];
                        
                        if (empty($row[4])) continue; // Skip jika Nama Paket kosong
                        
                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            $paket = new PaketPengadaan();
                            $nomorPaketImport = !empty($row[0]) ? trim((string)$row[0]) : 'IMP-'.time().'-'.$i; // Nomor DPP
                            $paket->nomor = $nomorPaketImport;
                            $paket->tanggal_dpp = !empty($row[1]) ? (string)$row[1] : date('Y-m-d H:i:s');
                            $paket->nomor_persetujuan = !empty($row[2]) ? (string)$row[2] : 'IMP-' . time() . '-' . $i;
                            $paket->tanggal_persetujuan = !empty($row[3]) ? (string)$row[3] : date('Y-m-d H:i:s');
                            $paket->nama_paket = (string)$row[4];
                            $paket->tanggal_paket = !empty($row[5]) ? (string)$row[5] : date('Y-m-d H:i:s');
                            $paket->kode_program = !empty($row[6]) ? (string)$row[6] : '-';
                            $paket->kode_kegiatan = !empty($row[7]) ? (string)$row[7] : '-';
                            $paket->kode_rekening = !empty($row[8]) ? (string)$row[8] : '-';
                            $paket->ppkom = !empty($row[9]) ? $row[9] : null;
                            $paket->pagu = (float)($row[10] ?? 0);
                            $paket->metode_pengadaan = !empty($row[11]) ? (string)$row[11] : 'PL'; 
                            $paket->kategori_pengadaan = !empty($row[12]) ? (string)$row[12] : 'barang/jasa';
                            $paket->tahun_anggaran = !empty($row[13]) ? (int)$row[13] : date('Y');
                            $paket->unit = !empty($row[14]) ? $row[14] : 1; 
                            $paket->pemenang = !empty($row[15]) ? $row[15] : null;
                            
                            $paket->is_import = 1; // FLAG IMPORT
                            
                            if ($paket->save(false)) {
                                $dpp = new Dpp();
                                $dpp->nomor_dpp = $paket->nomor;
                                $dpp->tanggal_dpp = $paket->tanggal_dpp;
                                $dpp->nomor_persetujuan = $paket->nomor_persetujuan;
                                $dpp->bidang_bagian = $paket->unit;
                                $dpp->paket_id = $paket->id;
                                $dpp->pejabat_pengadaan = !empty($row[16]) ? $row[16] : null;
                                $dpp->admin_pengadaan = !empty($row[17]) ? $row[17] : null;
                                $dpp->kode = !empty($row[18]) ? (string)$row[18] : null;
                                $dpp->save(false);
                                
                                // Insert Detail (Multi-produk support)
                                if (isset($detailMap[$nomorPaketImport])) {
                                    foreach ($detailMap[$nomorPaketImport] as $rowD) {
                                        $detail = new \app\models\PaketPengadaanDetails();
                                        $detail->paket_id = $paket->id;
                                        $detail->nama_produk = (string)$rowD[1];
                                        $detail->qty = !empty($rowD[2]) ? (float)$rowD[2] : 1;
                                        $detail->volume = !empty($rowD[3]) ? (float)$rowD[3] : 1;
                                        $detail->satuan = !empty($rowD[4]) ? (string)$rowD[4] : 'ls';
                                        $detail->hps_satuan = !empty($rowD[5]) ? (float)$rowD[5] : 0;
                                        $detail->penawaran = !empty($rowD[6]) ? (float)$rowD[6] : $detail->hps_satuan;
                                        $detail->negosiasi = !empty($rowD[7]) ? (float)$rowD[7] : $detail->penawaran;
                                        $detail->save(false);
                                    }
                                } else {
                                    // Fallback: Jika tidak mengisi sheet detail, coba baca dari kolom T-Z (Sheet 1) atau buat dummy
                                    $detail = new \app\models\PaketPengadaanDetails();
                                    $detail->paket_id = $paket->id;
                                    $detail->nama_produk = !empty($row[19]) ? (string)$row[19] : $paket->nama_paket;
                                    $detail->qty = !empty($row[20]) ? (float)$row[20] : 1;
                                    $detail->volume = !empty($row[21]) ? (float)$row[21] : 1;
                                    $detail->satuan = !empty($row[22]) ? (string)$row[22] : 'ls';
                                    $detail->hps_satuan = !empty($row[23]) ? (float)$row[23] : $paket->pagu;
                                    $detail->penawaran = !empty($row[24]) ? (float)$row[24] : $detail->hps_satuan;
                                    $detail->negosiasi = !empty($row[25]) ? (float)$row[25] : $detail->penawaran;
                                    $detail->save(false);
                                }
                                
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
        $sheet->setTitle('Form Import Paket');
        
        $headers = [
            'A1' => 'Nomor DPP / Paket (Wajib sbg Relasi Detail)',
            'B1' => 'Tanggal DPP (YYYY-MM-DD)',
            'C1' => 'Nomor Persetujuan',
            'D1' => 'Tanggal Persetujuan (YYYY-MM-DD)',
            'E1' => 'Nama Paket',
            'F1' => 'Tanggal Paket (YYYY-MM-DD)',
            'G1' => 'Kode Program',
            'H1' => 'Kode Kegiatan',
            'I1' => 'Kode Rekening',
            'J1' => 'ID PPK',
            'K1' => 'Pagu Paket',
            'L1' => 'Metode Pengadaan (PL/EPL/E-Purchasing/dll)',
            'M1' => 'Kategori Pengadaan (barang/jasa/konstruksi/konsultansi)',
            'N1' => 'Tahun Anggaran',
            'O1' => 'ID Unit / Bidang Bagian',
            'P1' => 'ID Vendor (Pemenang)',
            'Q1' => 'ID Pejabat Pengadaan',
            'R1' => 'ID Admin Pengadaan',
            'S1' => 'Kode Paket / Kode Pemesanan',
        ];
        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }
        
        // Buat kolom agak lebar
        foreach(range('A','S') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Sheet 2: Form Detail Paket
        $sheetDetail = $spreadsheet->createSheet();
        $sheetDetail->setTitle('Form Detail Paket');
        
        $headersDetail = [
            'A1' => 'Nomor DPP / Paket (Sama dengan Sheet 1)',
            'B1' => 'Nama Produk / Jasa',
            'C1' => 'Qty',
            'D1' => 'Volume',
            'E1' => 'Satuan',
            'F1' => 'HPS Satuan',
            'G1' => 'Penawaran',
            'H1' => 'Negosiasi',
        ];
        
        foreach ($headersDetail as $cell => $val) {
            $sheetDetail->setCellValue($cell, $val);
            $sheetDetail->getStyle($cell)->getFont()->setBold(true);
        }
        
        foreach(range('A','H') as $columnID) {
            $sheetDetail->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Sheet 3: Data Master
        $sheetMaster = $spreadsheet->createSheet();
        $sheetMaster->setTitle('Data Master');
        
        $sheetMaster->setCellValue('A1', 'DATA VENDOR (PENYEDIA)');
        $sheetMaster->setCellValue('A2', 'ID');
        $sheetMaster->setCellValue('B2', 'Nama Vendor');
        $sheetMaster->getStyle('A1:B2')->getFont()->setBold(true);
        
        $vendors = Penyedia::find()->where(['active' => 1])->all();
        $row = 3;
        foreach ($vendors as $v) {
            $sheetMaster->setCellValue('A'.$row, $v->id);
            $sheetMaster->setCellValue('B'.$row, $v->nama_perusahaan);
            $row++;
        }
        
        $sheetMaster->setCellValue('D1', 'DATA PEGAWAI (PEJABAT/PPK)');
        $sheetMaster->setCellValue('D2', 'ID');
        $sheetMaster->setCellValue('E2', 'Nama Pegawai');
        $sheetMaster->getStyle('D1:E2')->getFont()->setBold(true);

        $pegawai = Pegawai::find()->where(['status' => '1'])->all();
        $row = 3;
        foreach ($pegawai as $p) {
            $sheetMaster->setCellValue('D'.$row, $p->id);
            $sheetMaster->setCellValue('E'.$row, $p->nama);
            $row++;
        }

        $sheetMaster->setCellValue('G1', 'DATA PROGRAM');
        $sheetMaster->setCellValue('G2', 'Kode');
        $sheetMaster->setCellValue('H2', 'Nama Program');
        $sheetMaster->getStyle('G1:H2')->getFont()->setBold(true);

        $programs = \app\models\ProgramKegiatan::find()->where(['type' => 'program', 'is_active' => 1])->all();
        $row = 3;
        foreach ($programs as $p) {
            $sheetMaster->setCellValue('G'.$row, $p->code);
            $sheetMaster->setCellValue('H'.$row, $p->desc);
            $row++;
        }

        $sheetMaster->setCellValue('J1', 'DATA KEGIATAN');
        $sheetMaster->setCellValue('J2', 'Kode');
        $sheetMaster->setCellValue('K2', 'Nama Kegiatan');
        $sheetMaster->getStyle('J1:K2')->getFont()->setBold(true);

        $kegiatans = \app\models\ProgramKegiatan::find()->where(['type' => 'kegiatan', 'is_active' => 1])->all();
        $row = 3;
        foreach ($kegiatans as $k) {
            $sheetMaster->setCellValue('J'.$row, $k->code);
            $sheetMaster->setCellValue('K'.$row, $k->desc);
            $row++;
        }

        $sheetMaster->setCellValue('M1', 'DATA KODE REKENING');
        $sheetMaster->setCellValue('M2', 'Kode');
        $sheetMaster->setCellValue('N2', 'Uraian Rekening');
        $sheetMaster->getStyle('M1:N2')->getFont()->setBold(true);

        $rekenings = \app\models\KodeRekening::find()->where(['is_active' => 1])->all();
        $row = 3;
        foreach ($rekenings as $r) {
            $sheetMaster->setCellValue('M'.$row, $r->kode);
            $sheetMaster->setCellValue('N'.$row, $r->rekening);
            $row++;
        }

        $sheetMaster->setCellValue('P1', 'DATA RAB (ANGGARAN)');
        $sheetMaster->setCellValue('P2', 'Program');
        $sheetMaster->setCellValue('Q2', 'Kegiatan');
        $sheetMaster->setCellValue('R2', 'Rekening');
        $sheetMaster->setCellValue('S2', 'Uraian Anggaran');
        $sheetMaster->setCellValue('T2', 'Pagu');
        $sheetMaster->getStyle('P1:T2')->getFont()->setBold(true);

        $rabs = \app\models\Rab::find()->all();
        $row = 3;
        foreach ($rabs as $rab) {
            $sheetMaster->setCellValue('P'.$row, $rab->kode_program);
            $sheetMaster->setCellValue('Q'.$row, $rab->kode_kegiatan);
            $sheetMaster->setCellValue('R'.$row, $rab->kode_rekening);
            $sheetMaster->setCellValue('S'.$row, $rab->uraian_anggaran);
            $sheetMaster->setCellValue('T'.$row, $rab->jumlah_anggaran);
            $row++;
        }

        foreach(range('A','T') as $columnID) {
            $sheetMaster->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheetMaster->setCellValue('V1', 'DATA UNIT (BIDANG/BAGIAN)');
        $sheetMaster->setCellValue('V2', 'ID');
        $sheetMaster->setCellValue('W2', 'Nama Unit');
        $sheetMaster->getStyle('V1:W2')->getFont()->setBold(true);

        $units = \app\models\Unit::find()->where(['aktif' => 1])->all();
        $row = 3;
        foreach ($units as $u) {
            $sheetMaster->setCellValue('V'.$row, $u->id);
            $sheetMaster->setCellValue('W'.$row, $u->unit);
            $row++;
        }

        foreach(range('V','W') as $columnID) {
            $sheetMaster->getColumnDimension($columnID)->setAutoSize(true);
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
