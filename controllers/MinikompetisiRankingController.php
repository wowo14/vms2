<?php

namespace app\controllers;

use Yii;
use app\models\Minikompetisi;
use app\models\MinikompetisiSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use kartik\mpdf\Pdf;

class MinikompetisiRankingController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new MinikompetisiSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $items = $model->minikompetisiItems;
        $vendors = $model->minikompetisiVendors;
        $penawarans = $model->minikompetisiPenawarans;

        // Prepare ranking data per item
        $itemRankings = [];
        $maxRank = 0;

        foreach ($items as $item) {
            // Get all penawaran items for this item
            $itemPenawarans = [];
            foreach ($penawarans as $penawaran) {
                foreach ($penawaran->minikompetisiPenawaranItems as $pItem) {
                    if ($pItem->item_id == $item->id) {
                        // Only include if harga > 0
                        if ($pItem->harga_penawaran > 0) {
                            $itemPenawarans[] = [
                                'vendor' => $penawaran->vendor,
                                'harga' => $pItem->harga_penawaran,
                                'skor_kualitas' => $pItem->skor_kualitas,
                                'keterangan' => $pItem->keterangan,
                            ];
                        }
                    }
                }
            }

            // Sort by harga (ascending) for lowest price first
            usort($itemPenawarans, function ($a, $b) {
                return $a['harga'] <=> $b['harga'];
            });

            // Assign rankings
            $ranked = [];
            $rank = 1;
            foreach ($itemPenawarans as $p) {
                $ranked[$rank] = $p;
                if ($rank > $maxRank) {
                    $maxRank = $rank;
                }
                $rank++;
            }

            $itemRankings[$item->id] = [
                'item' => $item,
                'rankings' => $ranked,
            ];
        }

        return $this->render('view', [
            'model' => $model,
            'items' => $items,
            'vendors' => $vendors,
            'penawarans' => $penawarans,
            'itemRankings' => $itemRankings,
            'maxRank' => $maxRank,
        ]);
    }

    public function actionExportExcel($id)
    {
        $model = $this->findModel($id);
        $items = $model->minikompetisiItems;
        $penawarans = $model->minikompetisiPenawarans;

        // Prepare ranking data per item
        $itemRankings = [];
        $maxRank = 0;

        foreach ($items as $item) {
            $itemPenawarans = [];
            foreach ($penawarans as $penawaran) {
                foreach ($penawaran->minikompetisiPenawaranItems as $pItem) {
                    if ($pItem->item_id == $item->id && $pItem->harga_penawaran > 0) {
                        $itemPenawarans[] = [
                            'vendor' => $penawaran->vendor,
                            'harga' => $pItem->harga_penawaran,
                        ];
                    }
                }
            }

            usort($itemPenawarans, function ($a, $b) {
                return $a['harga'] <=> $b['harga'];
            });

            $ranked = [];
            $rank = 1;
            foreach ($itemPenawarans as $p) {
                $ranked[$rank] = $p;
                if ($rank > $maxRank) {
                    $maxRank = $rank;
                }
                $rank++;
            }

            $itemRankings[$item->id] = [
                'item' => $item,
                'rankings' => $ranked,
            ];
        }

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'Ranking Minikompetisi');
        $sheet->setCellValue('A2', 'Judul: ' . $model->judul);
        $sheet->setCellValue('A3', 'Tanggal: ' . Yii::$app->formatter->asDate($model->tanggal));
        $sheet->setCellValue('A4', 'Metode: ' . $model->getMetodeText());
        $sheet->setCellValue('A5', 'Status: ' . $model->getStatusText());

        // Set headers
        $row = 7;
        $col = 1;
        $sheet->setCellValueByColumnAndRow($col++, $row, 'No');
        $sheet->setCellValueByColumnAndRow($col++, $row, 'Nama Produk');
        
        // Store starting column for each rank
        $rankStartCols = [];
        for ($i = 1; $i <= $maxRank; $i++) {
            $rankStartCols[$i] = $col;
            $sheet->setCellValueByColumnAndRow($col++, $row, 'Terpilih ' . $i);
            $col++; // Skip next column for merge
        }

        // Style first row headers
        $headerRange = 'A' . $row . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col - 1) . $row;
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Merge cells for first row (Terpilih columns only, No and Nama Produk remain separate)
        for ($i = 1; $i <= $maxRank; $i++) {
            $startCol = $rankStartCols[$i];
            $endCol = $startCol + 1;
            $sheet->mergeCellsByColumnAndRow($startCol, $row, $endCol, $row);
        }

        // Second row headers
        $row++;
        $col = 1;
        $sheet->setCellValueByColumnAndRow($col++, $row, '');
        $sheet->setCellValueByColumnAndRow($col++, $row, '');
        
        for ($i = 1; $i <= $maxRank; $i++) {
            $sheet->setCellValueByColumnAndRow($col++, $row, 'Perusahaan');
            $sheet->setCellValueByColumnAndRow($col++, $row, 'Harga');
        }

        // Style second row headers
        $headerRange2 = 'A' . $row . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col - 1) . $row;
        $sheet->getStyle($headerRange2)->getFont()->setBold(true);
        $sheet->getStyle($headerRange2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFDDDDDD');
        $sheet->getStyle($headerRange2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange2)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Set data
        $row++;
        $no = 1;
        foreach ($itemRankings as $itemRanking) {
            $item = $itemRanking['item'];
            $rankings = $itemRanking['rankings'];
            
            $col = 1;
            $sheet->setCellValueByColumnAndRow($col, $row, $no++);
            $cell = $sheet->getCellByColumnAndRow($col, $row);
            $sheet->getStyle($cell->getCoordinate())->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle($cell->getCoordinate())->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $col++;
            
            $sheet->setCellValueByColumnAndRow($col, $row, $item->nama_produk);
            $cell = $sheet->getCellByColumnAndRow($col, $row);
            $sheet->getStyle($cell->getCoordinate())->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $col++;
            
            for ($i = 1; $i <= $maxRank; $i++) {
                // Define colors for each rank
                $colors = [
                    1 => 'FFD4EDDA', // Hijau
                    2 => 'FFFFF3CD', // Kuning
                    3 => 'FFF8D7DA', // Merah
                    4 => 'FFE2E3E5', // Abu-abu
                    5 => 'FFD1ECF1', // Biru muda
                    6 => 'FFFCE4EC', // Pink
                    7 => 'FFE0E0E0', // Abu-abu gelap
                    8 => 'FFD1C4E9', // Ungu muda
                    9 => 'FFC8E6C9', // Hijau muda
                    10 => 'FFFFCCBC', // Oranye
                ];
                $color = $colors[$i] ?? 'FFE0E0E0';
                
                if (isset($rankings[$i])) {
                    $rank = $rankings[$i];
                    
                    // Perusahaan cell
                    $sheet->setCellValueByColumnAndRow($col, $row, $rank['vendor']->nama_vendor ?? '-');
                    $cell = $sheet->getCellByColumnAndRow($col, $row);
                    $sheet->getStyle($cell->getCoordinate())->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($color);
                    $sheet->getStyle($cell->getCoordinate())->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $col++;
                    
                    // Harga cell
                    $sheet->setCellValueByColumnAndRow($col, $row, $rank['harga']);
                    $cell = $sheet->getCellByColumnAndRow($col, $row);
                    $sheet->getStyle($cell->getCoordinate())->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($color);
                    $sheet->getStyle($cell->getCoordinate())->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $sheet->getStyle($cell->getCoordinate())->getNumberFormat()->setFormatCode('#,##0');
                    $col++;
                } else {
                    // Empty cells with color
                    $sheet->setCellValueByColumnAndRow($col, $row, '-');
                    $cell = $sheet->getCellByColumnAndRow($col, $row);
                    $sheet->getStyle($cell->getCoordinate())->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($color);
                    $sheet->getStyle($cell->getCoordinate())->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $sheet->getStyle($cell->getCoordinate())->getFont()->getColor()->setARGB('FF999999');
                    $col++;
                    
                    $sheet->setCellValueByColumnAndRow($col, $row, '-');
                    $cell = $sheet->getCellByColumnAndRow($col, $row);
                    $sheet->getStyle($cell->getCoordinate())->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($color);
                    $sheet->getStyle($cell->getCoordinate())->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $sheet->getStyle($cell->getCoordinate())->getFont()->getColor()->setARGB('FF999999');
                    $col++;
                }
            }
            $row++;
        }

        // Auto size columns
        foreach (range(1, $col - 1) as $colIndex) {
            $sheet->getColumnDimensionByColumn($colIndex)->setAutoSize(true);
        }

        // Save file
        $filename = 'Ranking_' . $model->judul . '_' . date('Y-m-d') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    public function actionExportPdf($id)
    {
        $model = $this->findModel($id);
        $items = $model->minikompetisiItems;
        $vendors = $model->minikompetisiVendors;
        $penawarans = $model->minikompetisiPenawarans;

        // Prepare ranking data per item
        $itemRankings = [];
        $maxRank = 0;

        foreach ($items as $item) {
            $itemPenawarans = [];
            foreach ($penawarans as $penawaran) {
                foreach ($penawaran->minikompetisiPenawaranItems as $pItem) {
                    if ($pItem->item_id == $item->id && $pItem->harga_penawaran > 0) {
                        $itemPenawarans[] = [
                            'vendor' => $penawaran->vendor,
                            'harga' => $pItem->harga_penawaran,
                        ];
                    }
                }
            }

            usort($itemPenawarans, function ($a, $b) {
                return $a['harga'] <=> $b['harga'];
            });

            $ranked = [];
            $rank = 1;
            foreach ($itemPenawarans as $p) {
                $ranked[$rank] = $p;
                if ($rank > $maxRank) {
                    $maxRank = $rank;
                }
                $rank++;
            }

            $itemRankings[$item->id] = [
                'item' => $item,
                'rankings' => $ranked,
            ];
        }

        $content = $this->renderPartial('_pdf', [
            'model' => $model,
            'itemRankings' => $itemRankings,
            'maxRank' => $maxRank,
        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '
                .rank-1 { background-color: #d4edda !important; }
                .rank-2 { background-color: #fff3cd !important; }
                .rank-3 { background-color: #f8d7da !important; }
                .rank-4 { background-color: #e2e3e5 !important; }
                .rank-5 { background-color: #d1ecf1 !important; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #000; padding: 5px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .text-right { text-align: right; }
                .text-center { text-align: center; }
            ',
            'options' => [
                'title' => 'Ranking Minikompetisi - ' . $model->judul,
                'subject' => 'Ranking Minikompetisi',
            ],
            'methods' => [
                'SetHeader' => ['Ranking Minikompetisi - ' . $model->judul . '||Generated: ' . date('Y-m-d H:i')],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);

        return $pdf->render();
    }

    protected function findModel($id)
    {
        if (($model = Minikompetisi::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
