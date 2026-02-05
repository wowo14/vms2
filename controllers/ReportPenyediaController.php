<?php

namespace app\controllers;

use Yii;
use app\models\ReportPenyedia;
use app\models\ReportPenyediaSearch;
use app\models\PenilaianPenyedia;
use app\models\Penyedia;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\helpers\Html;
use yii\web\Response;

/**
 * ReportPenyediaController implements the CRUD actions for ReportPenyedia model.
 */
class ReportPenyediaController extends Controller
{
    /**
     * Lists all ReportPenyedia models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReportPenyediaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ReportPenyedia model.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "Detail Report Penyedia",
                'content' => $this->renderAjax('view', [
                    'model' => $this->findModel($id),
                ]),
                'footer' => Html::button('Tutup', ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal'])
            ];
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Sync data from existing PenilaianPenyedia
     */
    public function actionSync()
    {
        $penilaians = PenilaianPenyedia::find()->all();
        $count = 0;
        foreach ($penilaians as $penilaian) {
            // Check if already synced
            $exists = ReportPenyedia::find()->where(['penilaian_id' => $penilaian->id, 'source' => 'system'])->exists();
            if ($exists)
                continue;

            $report = new ReportPenyedia();
            $report->penilaian_id = $penilaian->id;
            $report->nama_penyedia = $penilaian->nama_perusahaan;
            $report->alamat = $penilaian->alamat_perusahaan;
            $report->nama_paket = $penilaian->paket_pekerjaan;

            $details = json_decode($penilaian->details, true);
            $report->nilai_evaluasi = isset($details['nilaiakhir']) ? (string) $details['nilaiakhir'] : '';
            $report->source = 'system';

            // Try to find Penyedia details
            $dpp = $penilaian->dpp;
            if ($dpp) {
                $report->bidang = $dpp->unit ? $dpp->unit->unit : $dpp->bidang_bagian;
                $paket = $dpp->paketpengadaan;
                if ($paket) {
                    $report->jenis_pekerjaan = $paket->kategori_pengadaan;
                    $report->penyedia_id = $paket->pemenang;

                    // Get products from details
                    $produkList = [];
                    foreach ($paket->details as $pd) {
                        $produkList[] = $pd->nama_produk;
                    }
                    $report->produk_ditawarkan = implode(', ', $produkList);
                }
            }

            if ($report->penyedia_id) {
                $penyedia = Penyedia::findOne($report->penyedia_id);
                if ($penyedia) {
                    $report->nama_penyedia = $penyedia->nama_perusahaan; // Prefer full name
                    $report->kota = $penyedia->kota;
                    $report->telepon = $penyedia->nomor_telepon;
                    if (empty($report->alamat)) {
                        $report->alamat = $penyedia->alamat_perusahaan;
                    }
                }
            }

            if ($report->save()) {
                $count++;
            } else {
                Yii::error("Failed to sync Penilaian ID {$penilaian->id}: " . json_encode($report->getErrors()));
            }
        }

        if (Yii::$app instanceof \yii\web\Application) {
            Yii::$app->session->setFlash('success', "Successfully synced $count records from system.");
            return $this->redirect(['index']);
        }
        return "Synced $count records.";
    }

    /**
     * Import data from Excel
     */
    public function actionImport()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $file = UploadedFile::getInstanceByName('file');
            if ($file) {
                try {
                    // Check if zip extension is loaded if the file is xlsx
                    $ext = strtolower($file->extension);
                    if (($ext == 'xlsx' || $ext == 'xls') && !class_exists('ZipArchive')) {
                        throw new \Exception("PHP Extension 'zip' tidak aktif di server ini. Silakan aktifkan extension=zip di php.ini atau gunakan file format .csv");
                    }

                    $spreadsheet = IOFactory::load($file->tempName);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $rows = $worksheet->toArray();

                    // Skip header row
                    $count = 0;
                    foreach ($rows as $index => $row) {
                        if ($index == 0)
                            continue;
                        if (empty($row[0]))
                            continue; // Skip empty rows

                        $report = new ReportPenyedia();
                        $report->nama_penyedia = $row[0];
                        $report->alamat = $row[1] ?? '';
                        $report->kota = $row[2] ?? '';
                        $report->telepon = $row[3] ?? '';
                        $report->produk_ditawarkan = $row[4] ?? '';
                        $report->jenis_pekerjaan = $row[5] ?? '';
                        $report->nama_paket = $row[6] ?? '';
                        $report->bidang = $row[7] ?? '';
                        $report->nilai_evaluasi = $row[8] ?? ''; // Take as string
                        $report->source = 'excel';

                        if ($report->save()) {
                            $count++;
                        }
                    }

                    Yii::$app->session->setFlash('success', "Berhasil mengimport $count data dari file.");
                    return $this->redirect(['index']);
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', "Gagal mengimport file: " . $e->getMessage());
                    return $this->redirect(['index']);
                }
            }
        }

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "Import Excel",
                'content' => $this->renderAjax('import'),
                'footer' => Html::button('Tutup', ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                    Html::button('Import', ['class' => 'btn btn-primary', 'type' => 'submit'])
            ];
        }

        return $this->render('import');
    }

    /**
     * Finds the ReportPenyedia model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return ReportPenyedia the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReportPenyedia::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
