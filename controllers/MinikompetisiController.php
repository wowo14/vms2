<?php

namespace app\controllers;

use Yii;
use app\models\Minikompetisi;
use app\models\MinikompetisiSearch;
use app\models\MinikompetisiItem;
use app\models\MinikompetisiVendor;
use app\models\MinikompetisiPenawaran;
use app\models\MinikompetisiPenawaranItem;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MinikompetisiController extends Controller
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

        // Ranking
        if (!empty($penawarans)) {
            usort($penawarans, function ($a, $b) {
                return $b->total_skor_akhir <=> $a->total_skor_akhir;
            });
        }

        return $this->render('view', [
            'model' => $model,
            'items' => $items,
            'vendors' => $vendors,
            'penawarans' => $penawarans,
        ]);
    }

    public function actionCreate()
    {
        $model = new Minikompetisi();

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = date('Y-m-d H:i:s');
            // user id might not be available if not logged in but assumed logged in
            $model->created_by = Yii::$app->user->id ?? 1;

            if ($model->save()) {
                // handle items
                $items = Yii::$app->request->post('MinikompetisiItem', []);
                foreach ($items as $itemData) {
                    if (!empty($itemData['nama_produk'])) {
                        $item = new MinikompetisiItem();
                        $item->minikompetisi_id = $model->id;
                        $item->nama_produk = $itemData['nama_produk'];
                        $item->qty = $itemData['qty'];
                        $item->satuan = $itemData['satuan'];
                        $item->harga_hps = str_replace(',', '', $itemData['harga_hps']);
                        $item->harga_existing = str_replace(',', '', $itemData['harga_existing']);
                        $item->save();
                    }
                }

                // handle vendors
                $vendors = Yii::$app->request->post('MinikompetisiVendor', []);
                foreach ($vendors as $vendorData) {
                    if (!empty($vendorData['nama_vendor'])) {
                        $vendor = new MinikompetisiVendor();
                        $vendor->minikompetisi_id = $model->id;
                        $vendor->nama_vendor = $vendorData['nama_vendor'];
                        $vendor->email_vendor = $vendorData['email_vendor'];
                        $vendor->save();
                    }
                }

                Yii::$app->session->setFlash('success', 'Minikompetisi berhasil dibuat.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->updated_at = date('Y-m-d H:i:s');
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Minikompetisi berhasil diupdate.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Data berhasil dihapus.');
        return $this->redirect(['index']);
    }

    public function actionTemplate($id)
    {
        $model = $this->findModel($id);
        $items = $model->minikompetisiItems;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Format Penawaran Minikompetisi');
        $sheet->setCellValue('A2', 'Paket: ' . $model->judul);
        $sheet->setCellValue('A3', 'ID Paket: ' . $model->id); // For parsing purpose

        $sheet->setCellValue('A5', 'ID Item (JANGAN DIUBAH)');
        $sheet->setCellValue('B5', 'Nama Produk');
        $sheet->setCellValue('C5', 'Kuantitas');
        $sheet->setCellValue('D5', 'Satuan');
        $sheet->setCellValue('E5', 'Harga Penawaran Satuan');

        if ($model->metode == 2) {
            $sheet->setCellValue('F5', 'Skor Kualitas (1-100)');
            $sheet->setCellValue('G5', 'Keterangan Kualitas');
        }

        $row = 6;
        foreach ($items as $item) {
            $sheet->setCellValue('A' . $row, $item->id);
            $sheet->setCellValue('B' . $row, $item->nama_produk);
            $sheet->setCellValue('C' . $row, $item->qty);
            $sheet->setCellValue('D' . $row, $item->satuan);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Penawaran_' . $model->id . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function actionImport($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            $file = UploadedFile::getInstanceByName('file_excel');
            $vendor_id = Yii::$app->request->post('vendor_id');

            if ($file && $vendor_id) {
                // Delete previous offer from this vendor if exist
                MinikompetisiPenawaran::deleteAll(['minikompetisi_id' => $id, 'vendor_id' => $vendor_id]);

                $spreadsheet = IOFactory::load($file->tempName);
                $sheet = $spreadsheet->getActiveSheet();

                $highestRow = $sheet->getHighestRow();

                $penawaran = new MinikompetisiPenawaran();
                $penawaran->minikompetisi_id = $id;
                $penawaran->vendor_id = $vendor_id;
                $penawaran->created_at = date('Y-m-d H:i:s');

                $total_harga = 0;
                $total_skor_kualitas = 0;
                $jumlah_item = 0;

                if ($penawaran->save()) {
                    for ($row = 6; $row <= $highestRow; $row++) {
                        $item_id = $sheet->getCell('A' . $row)->getValue();
                        if (!$item_id)
                            continue;

                        $harga = (float) $sheet->getCell('E' . $row)->getValue();
                        $skor_kualitas = 0;
                        $keterangan = '';

                        if ($model->metode == 2) {
                            $skor_kualitas = (float) $sheet->getCell('F' . $row)->getValue();
                            $keterangan = $sheet->getCell('G' . $row)->getValue();
                        }

                        // find item
                        $mItem = MinikompetisiItem::findOne($item_id);
                        if ($mItem) {
                            $pItem = new MinikompetisiPenawaranItem();
                            $pItem->penawaran_id = $penawaran->id;
                            $pItem->item_id = $item_id;
                            $pItem->harga_penawaran = $harga;
                            $pItem->skor_kualitas = $skor_kualitas;
                            $pItem->keterangan = $keterangan;
                            $pItem->save();

                            $total_harga += ($harga * $mItem->qty);
                            $total_skor_kualitas += $skor_kualitas;
                            $jumlah_item++;
                        }
                    }

                    $penawaran->total_harga = $total_harga;
                    if ($jumlah_item > 0 && $model->metode == 2) {
                        $penawaran->total_skor_kualitas = $total_skor_kualitas / $jumlah_item;
                    }
                    $penawaran->save();

                    // Trigger Calculation
                    $this->calculateRanking($model);

                    Yii::$app->session->setFlash('success', 'Penawaran berhasil diimport dan dihitung ulang.');
                    return $this->redirect(['view', 'id' => $id]);
                }
            } else {
                Yii::$app->session->setFlash('error', 'Vendor atau File Excel belum dipilih.');
            }
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    protected function calculateRanking($model)
    {
        $penawarans = $model->minikompetisiPenawarans;
        if (empty($penawarans))
            return;

        // Method 1 & 3: Harga Terendah (Lowest Price takes 100 skor for price)
        // Method 2: Mix quality and price

        $lowest_price = null;
        foreach ($penawarans as $p) {
            if ($lowest_price === null || $p->total_harga < $lowest_price) {
                if ($p->total_harga > 0) { // Avoid zero price
                    $lowest_price = $p->total_harga;
                }
            }
        }

        foreach ($penawarans as $p) {
            $skor_harga = 0;
            if ($p->total_harga > 0 && $lowest_price > 0) {
                $skor_harga = ($lowest_price / $p->total_harga) * 100; // standard procurement relative formula
            }
            $p->total_skor_harga = $skor_harga;

            if ($model->metode == 1 || $model->metode == 3) {
                // Skoring harga is final
                $p->total_skor_akhir = $skor_harga;
            } elseif ($model->metode == 2) {
                $bobot_h = $model->bobot_harga / 100;
                $bobot_k = $model->bobot_kualitas / 100;
                $p->total_skor_akhir = ($p->total_skor_harga * $bobot_h) + ($p->total_skor_kualitas * $bobot_k);
            }
            $p->save(false);
        }

        // Rank them
        $penawarans = MinikompetisiPenawaran::find()
            ->where(['minikompetisi_id' => $model->id])
            ->orderBy(['total_skor_akhir' => SORT_DESC])
            ->all();

        $rank = 1;
        foreach ($penawarans as $p) {
            $p->ranking = $rank++;
            $p->is_winner = ($p->ranking === 1) ? 1 : 0;
            $p->save(false);
        }
    }

    protected function findModel($id)
    {
        if (($model = Minikompetisi::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
