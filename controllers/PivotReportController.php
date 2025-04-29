<?php

namespace app\controllers;

use Yii;
use kartik\mpdf\Pdf;
use yii\db\Expression;
use yii\web\Controller;
use app\models\ReportModel;
use app\models\PaketPengadaan;
use app\helpers\PivotReportHelper;
use yii\helpers\ArrayHelper;

class PivotReportController extends Controller {
    public function actionIndex() {
        $model = new ReportModel();
        $request = \Yii::$app->request;
        if ($request->isGet) {
            return $this->render('index', [
                'action' => \yii\helpers\Url::to(['report-all']),
                'months' => (new PaketPengadaan())->months,
                'years' => $this->getYearList(),
                'model' => $model
            ]);
        } else if ($model->load($request->post())) {
        }
    }
    public function actionReportAll() {
        $model = new ReportModel();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $data = $this->getRawData($model);
            $types = ['metode', 'metode_total', 'kategori', 'kategori_total', 'pejabat', 'pejabat_total'];
            $configs = [];
            $reports = [];
            foreach ($types as $type) {
                $config = $this->getReportConfig($type);
                $configs[$type] = $config;
                // Yii::error('field: ' . $config['rowField']);
                // Yii::error('label: ' . $config['rowLabel']);
                $reports[$type] = PivotReportHelper::generatePivotReport(
                    $data,
                    $config['rowField'],
                    $config['rowLabel'],
                    'month',
                    (new PaketPengadaan())->months,
                    null,
                    $config['sumField'] ?? null
                );
            }
            // Yii::error($reports);
            if (Yii::$app->request->post('type') === 'pdf') {
                $viewData = [
                    'reports' => $reports,
                    'configs' => $configs,
                    'months' => (new PaketPengadaan())->months,
                    'year' => $model->tahun,
                ];
                // Generate PDF
                $pdf = new Pdf([
                    'mode' => Pdf::MODE_UTF8,
                    'format' => Pdf::FORMAT_A4,
                    'orientation' => Pdf::ORIENT_LANDSCAPE,
                    'destination' => Pdf::DEST_BROWSER,
                    'content' => $this->renderPartial('report_all', $viewData),
                    'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
                    'cssInline' => $this->getPdfStyles(),
                    'options' => [
                        'title' => 'Laporan Gabungan ' . $model->tahun,
                    ],
                    'methods' => [
                        'SetHeader' => ['Laporan Gabungan ' . $model->tahun . ' - ' . date('d/m/Y')],
                        'SetFooter' => ['{PAGENO}'],
                    ]
                ]);
                return $pdf->render();
            }
            return $this->render('report_all', [
                'reports' => $reports,
                'configs' => $configs,
                'months' => (new PaketPengadaan())->months,
                'year' => $model->tahun,
            ]);
        }
        return $this->redirect(['index']);
    }
    public function actionReport($type = 'all') {
        $model = new ReportModel();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $data = $this->getRawData($model);
            // Ambil semua konfigurasi
            $allConfigs = $this->getAllReportConfigs();
            Yii::error($allConfigs);
            // Jika type == all, proses semua jenis
            if ($type === 'all') {
                $reports = [];
                foreach ($allConfigs as $key => $config) {
                    $reports[$key] = PivotReportHelper::generatePivotReport(
                        $data,
                        $config['rowField'],
                        $config['rowLabel'],
                        'month',
                        (new PaketPengadaan())->months,
                        null,
                        $config['sumField'] ?? null
                    );
                }
                // Jika PDF semua
                if (Yii::$app->request->post('type') == 'pdf') {
                    return $this->renderPartial('_report_all_pdf', [
                        'reports' => $reports,
                        'configs' => $allConfigs,
                        'year' => $model->tahun,
                        'months' => (new PaketPengadaan())->months,
                    ]);
                }
                return $this->render('_report_all', [
                    'reports' => $reports,
                    'configs' => $allConfigs,
                    'model' => $model,
                    'months' => (new PaketPengadaan())->months,
                ]);
            }
            // Jika hanya satu type
            $reportConfig = $this->getReportConfig($type);
            $report = PivotReportHelper::generatePivotReport(
                $data,
                $reportConfig['rowField'],
                $reportConfig['rowLabel'],
                'month',
                (new PaketPengadaan())->months,
                null,
                $reportConfig['sumField'] ?? null
            );
            // Jika PDF tunggal
            if (Yii::$app->request->post('type') == 'pdf') {
                $pdf = new Pdf([
                    'mode' => Pdf::MODE_UTF8,
                    'format' => Pdf::FORMAT_A4,
                    'orientation' => Pdf::ORIENT_LANDSCAPE,
                    'destination' => Pdf::DEST_BROWSER,
                    'content' => $this->renderPartial('_report_pdf', [
                        'report' => $report,
                        'reportConfig' => $reportConfig,
                        'type' => $type,
                        'year' => $model->tahun,
                        'months' => (new PaketPengadaan())->months,
                    ]),
                    'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
                    'cssInline' => $this->getPdfStyles(),
                    'options' => [
                        'title' => 'Laporan ' . $reportConfig['title'],
                    ],
                    'methods' => [
                        'SetHeader' => ['Laporan ' . $reportConfig['title'] . ' - ' . date('d/m/Y')],
                        'SetFooter' => ['{PAGENO}'],
                    ]
                ]);
                return $pdf->render();
            }
            return $this->render('report', [
                'report' => $report,
                'reportConfig' => $reportConfig,
                'type' => $type,
                'model' => $model,
                'year' => $model->tahun,
                'months' => (new PaketPengadaan())->months,
            ]);
        }
        return $this->redirect(['index']);
    }
    /**
     * Generate multiple reports for dashboard view
     */
    public function actionDashboard($year = null, $monthStart = 1, $monthEnd = 12) {
        // Get raw data
        $data = $this->getRawData($year, $monthStart, $monthEnd);
        // Define report configurations
        $pivotConfigs = [
            'metodeCount' => [
                'rowField' => 'metode_pengadaan',
                'rowLabel' => 'Metode Pengadaan',
                'subTitle' => 'Jumlah Paket Pengadaan Per Metode Pengadaan',
                'colField' => 'month',
                'monthLabels' => (new PaketPengadaan())->months,
            ],
            'metodeTotal' => [
                'rowField' => 'metode_pengadaan',
                'rowLabel' => 'Metode Pengadaan',
                'subTitle' => 'Total Kontrak Per Metode Pengadaan',
                'colField' => 'month',
                'monthLabels' => (new PaketPengadaan())->months,
                'sumField' => 'hasilnego'
            ],
            'kategoriCount' => [
                'rowField' => 'kategori_pengadaan',
                'rowLabel' => 'Kategori Pengadaan',
                'subTitle' => 'Jumlah Paket Pengadaan Per Kategori Pengadaan',
                'colField' => 'month',
                'monthLabels' => (new PaketPengadaan())->months,
            ],
            'kategoriTotal' => [
                'rowField' => 'kategori_pengadaan',
                'rowLabel' => 'Kategori Pengadaan',
                'subTitle' => 'Total Kontrak Per Kategori Pengadaan',
                'colField' => 'month',
                'monthLabels' => (new PaketPengadaan())->months,
                'sumField' => 'hasilnego'
            ],
            'pejabatCount' => [
                'rowField' => 'pejabat_pengadaan',
                'rowLabel' => 'Pejabat Pengadaan',
                'subTitle' => 'Jumlah Paket Pengadaan Per Pejabat Pengadaan',
                'colField' => 'month',
                'monthLabels' => (new PaketPengadaan())->months,
            ],
        ];
        // Generate all reports at once
        $reports = PivotReportHelper::generateMultiplePivotReports($data, $pivotConfigs);
        return $this->render('dashboard', [
            'reports' => $reports,
            'year' => $year,
            'monthStart' => $monthStart,
            'monthEnd' => $monthEnd,
            'months' => (new PaketPengadaan())->months,
        ]);
    }
    /**
     * Export pivot report to PDF
     */
    public function actionExportPdf($raw, $type) {
        if (isset(Yii::$app->modules['debug'])) {
            Yii::$app->modules['debug']->enabled = false;
        }
        $reportConfig = $this->getReportConfig($type);
        $data = $raw;
        // Generate pivot report
        $report = PivotReportHelper::generatePivotReport(
            $data,
            $reportConfig['rowField'],
            $reportConfig['rowLabel'],
            'month',
            (new PaketPengadaan())->months,
            null,
            $reportConfig['sumField'] ?? null
        );
        // Prepare view data
        $viewData = [
            'report' => $report,
            'reportConfig' => $reportConfig,
            'type' => $type,
            'year' => "year",
            'months' => (new PaketPengadaan())->months,
            // 'monthStart' => $monthStart,
            // 'monthEnd' => $monthEnd,
        ];
        // Generate PDF
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $this->renderPartial('_report_pdf', $viewData),
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => $this->getPdfStyles(),
            'options' => [
                'title' => 'Laporan ' . $reportConfig['title'],
            ],
            'methods' => [
                'SetHeader' => ['Laporan ' . $reportConfig['title'] . ' - ' . date('d/m/Y')],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);
        return $pdf->render();
    }
    /**
     * Export multiple reports to PDF (dashboard)
     */
    public function actionExportDashboardPdf($year = null, $monthStart = 1, $monthEnd = 12) {
        // Disable debug module in PDF
        if (isset(Yii::$app->modules['debug'])) {
            Yii::$app->modules['debug']->enabled = false;
        }
        // Get raw data
        $data = $this->getRawData($year, $monthStart, $monthEnd);
        // Define report configurations
        $pivotConfigs = [
            'metodeCount' => [
                'rowField' => 'metode_pengadaan',
                'rowLabel' => 'Metode Pengadaan',
                'title' => 'Jumlah Per Metode Pengadaan',
                'colField' => 'month',
                'monthLabels' => (new PaketPengadaan())->months,
            ],
            'metodeTotal' => [
                'rowField' => 'metode_pengadaan',
                'rowLabel' => 'Metode Pengadaan',
                'title' => 'Total Kontrak Per Metode Pengadaan',
                'colField' => 'month',
                'monthLabels' => (new PaketPengadaan())->months,
                'sumField' => 'hasilnego'
            ],
            'kategoriCount' => [
                'rowField' => 'kategori_pengadaan',
                'rowLabel' => 'Kategori Pengadaan',
                'title' => 'Jumlah Per Kategori Pengadaan',
                'colField' => 'month',
                'monthLabels' => (new PaketPengadaan())->months,
            ],
            'kategoriTotal' => [
                'rowField' => 'kategori_pengadaan',
                'rowLabel' => 'Kategori Pengadaan',
                'title' => 'Total Kontrak Per Kategori Pengadaan',
                'colField' => 'month',
                'monthLabels' => (new PaketPengadaan())->months,
                'sumField' => 'hasilnego'
            ],
            'pejabatCount' => [
                'rowField' => 'pejabat_pengadaan',
                'rowLabel' => 'Pejabat Pengadaan',
                'title' => 'Jumlah Per Pejabat Pengadaan',
                'colField' => 'month',
                'monthLabels' => (new PaketPengadaan())->months,
            ],
        ];
        // Generate all reports at once
        $reports = PivotReportHelper::generateMultiplePivotReports($data, $pivotConfigs);
        // Prepare view data
        $viewData = [
            'reports' => $reports,
            'pivotConfigs' => $pivotConfigs,
            'year' => $year,
            'monthStart' => $monthStart,
            'monthEnd' => $monthEnd,
            'months' => (new PaketPengadaan())->months,
        ];
        // Generate PDF
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $this->renderPartial('_dashboard_pdf', $viewData),
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => $this->getPdfStyles(),
            'options' => [
                'title' => 'Dashboard Laporan Pengadaan',
            ],
            'methods' => [
                'SetHeader' => ['Dashboard Laporan Pengadaan - ' . date('d/m/Y')],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);
        return $pdf->render();
    }
    /**
     * AJAX endpoint to get raw data for WebDataRocks
     */
    public function actionGetData($year = null, $monthStart = 1, $monthEnd = 12) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // Get raw data
        $data = $this->getRawData($year, $monthStart, $monthEnd);
        // Define report configurations
        $pivotConfigs = [
            'kategoriJumlah' => [
                'rowField' => 'kategori_pengadaan',
                'rowLabel' => 'Kategori Pengadaan',
                'colField' => 'month',
            ],
            'kategoriTotal' => [
                'rowField' => 'kategori_pengadaan',
                'rowLabel' => 'Kategori Pengadaan',
                'colField' => 'month',
                'sumField' => 'hasilnego'
            ],
            'metodeJumlah' => [
                'rowField' => 'metode_pengadaan',
                'rowLabel' => 'Metode Pengadaan',
                'colField' => 'month',
            ],
            'metodeTotal' => [
                'rowField' => 'metode_pengadaan',
                'rowLabel' => 'Metode Pengadaan',
                'colField' => 'month',
                'sumField' => 'hasilnego'
            ],
        ];
        // Generate all reports at once
        $reports = PivotReportHelper::generateMultiplePivotReports($data, $pivotConfigs);
        // Format data for WebDataRocks
        $webDataRocksData = [];
        foreach ($reports as $key => $report) {
            $webDataRocksData[$key] = $this->formatDataForWebDataRocks(
                $report['pivotData'],
                $pivotConfigs[$key]['rowField'],
                $pivotConfigs[$key]['sumField'] ?? null
            );
        }
        return [
            'success' => true,
            'data' => $webDataRocksData,
        ];
    }
    /**
     * Get the list of available years for the filter
     */
    private function getYearList() {
        $years = PaketPengadaan::find()
            ->select(
                new Expression("strftime('%Y', tanggal_paket)  AS year")
            )
            ->distinct()
            ->asArray()
            ->all();
        // If no years found, include current year
        if (empty($years)) {
            $years['year'] = [date('Y')];
        }
        // Sort years descending
        rsort($years);
        $years = ArrayHelper::map($years, 'year', 'year');
        Yii::error($years);
        return $years;
    }
    /**
     * Get the raw data for reports
     */
    private function getRawData(ReportModel $model = null) {
        $query = collect((new PaketPengadaan)->rawData);
        if ($model) {
            if ($model->tahun && $model->tahun !== 'all') {
                $query = $query->filter(fn($item) => $item['year'] == $model->tahun);
            }
            if ($model->bulan && $model->bulan != 0) {
                $query = $query->filter(fn($item) => $item['month'] == $model->bulan);
            }
            if ($model->kategori && $model->kategori !== 'all') {
                $query = $query->filter(fn($item) => $item['kategori_pengadaan_id'] == $model->kategori);
            }
            if ($model->metode && $model->metode !== 'all') {
                $query = $query->filter(fn($item) => $item['metode_pengadaan_id'] == $model->metode);
            }
            if ($model->pejabat && $model->pejabat !== 'all') {
                $query = $query->filter(fn($item) => $item['pejabat_pengadaan_id'] == $model->pejabat);
            }
            if ($model->admin && $model->admin !== 'all') {
                $query = $query->filter(fn($item) => $item['admin_pengadaan_id'] == $model->admin);
            }
            if ($model->bidang && $model->bidang !== 'all') {
                $query = $query->filter(fn($item) => $item['bidang_bagian_id'] == $model->bidang);
            }
        }
        return $query->toArray();
    }
    private function formatDataForWebDataRocks($pivotData, $rowField, $sumField = null) {
        $result = [];
        foreach ($pivotData as $row) {
            $rowFieldValue = $row[$rowField];
            foreach ($row as $key => $value) {
                if ($key != $rowField && is_numeric($key)) {
                    $item = [
                        $rowField => $rowFieldValue,
                        'bulan' => (int)$key,
                    ];
                    if ($sumField) {
                        $item['total'] = $value;
                    } else {
                        $item['jumlah'] = $value;
                    }
                    $result[] = $item;
                }
            }
        }
        return $result;
    }
    private function getAllReportConfigs() {
        return [
            'metode' => [
                'rowField' => 'metode_pengadaan',
                'rowLabel' => 'Metode Pengadaan',
                'title' => '∑ Pengadaan per Metode',
                'subTitle' => 'Jumlah Paket Pengadaan Per Metode'
            ],
            'metode_total' => [
                'rowField' => 'metode_pengadaan',
                'rowLabel' => 'Metode Pengadaan',
                'title' => 'Total Kontrak per Metode',
                'sumField' => 'hasilnego',
                'subTitle' => 'Jumlah Kontrak Per Metode'
            ],
            'kategori' => [
                'rowField' => 'kategori_pengadaan',
                'rowLabel' => 'Kategori Pengadaan',
                'title' => '∑ Pengadaan per Kategori',
                'subTitle' => 'Jumlah Paket Pengadaan Per Kategori'
            ],
            'kategori_total' => [
                'rowField' => 'kategori_pengadaan',
                'rowLabel' => 'Kategori Pengadaan',
                'title' => 'Total Kontrak per Kategori',
                'sumField' => 'hasilnego',
                'subTitle' => 'Jumlah Kontrak Per Kategori'
            ],
            'pejabat' => [
                'rowField' => 'pejabat_pengadaan',
                'rowLabel' => 'Pejabat Pengadaan',
                'title' => '∑ Pengadaan per Pejabat',
                'subTitle' => 'Jumlah Paket Pengadaan Per Pejabat'
            ],
            'pejabat_total' => [
                'rowField' => 'pejabat_pengadaan',
                'rowLabel' => 'Pejabat Pengadaan',
                'title' => 'Total Kontrak per Pejabat',
                'sumField' => 'hasilnego',
                'subTitle' => 'Jumlah Kontrak Per Pejabat'
            ],
            'admin' => [
                'rowField' => 'admin_pengadaan',
                'rowLabel' => 'Admin Pengadaan',
                'title' => '∑ Pengadaan per Admin Pengadaan',
                'subTitle' => 'Jumlah Paket Pengadaan Per Admin Pengadaan'
            ],
            'admin_total' => [
                'rowField' => 'admin_pengadaan',
                'rowLabel' => 'Admin Pengadaan',
                'title' => '∑ Kontrak per Admin Pengadaan',
                'sumField' => 'hasilnego',
                'subTitle' => 'Jumlah Kontrak Per Admin Pengadaan'
            ],
            'bidang' => [
                'rowField' => 'bidang_bagian',
                'rowLabel' => 'Bidang Bagian',
                'title' => '∑ Pengadaan per Bidang Bagian',
                'subTitle' => 'Jumlah Paket Pengadaan Per Bidang Bagian'
            ],
            'bidang_total' => [
                'rowField' => 'bidang_bagian',
                'rowLabel' => 'Bidang Bagian',
                'title' => '∑ Kontrak per Bidang Bagian',
                'sumField' => 'hasilnego',
                'subTitle' => 'Jumlah Kontrak Per Bidang Bagian'
            ],
        ];
    }
    private function getReportConfig($type) {
        $configs = $this->getAllReportConfigs();
        return $configs[$type] ?? $configs['metode'];
    }
    private function getPdfStyles() {
        return '
            body {
                font-family: "DejaVu Sans", Arial, Helvetica, sans-serif;
                font-size: 10pt;
            }
            .report-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            .report-table th, .report-table td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            .report-table th {
                background-color: #f2f2f2;
                font-weight: bold;
            }
            .report-title {
                text-align: center;
                font-size: 16pt;
                font-weight: bold;
                margin-bottom: 20px;
            }
            .report-subtitle {
                text-align: center;
                font-size: 12pt;
                margin-bottom: 20px;
            }
            .report-date {
                text-align: right;
                margin-bottom: 20px;
            }
            .grand-total {
                font-weight: bold;
                background-color: #f9f9f9;
            }
            .section-header {
                font-size: 14pt;
                font-weight: bold;
                margin-top: 30px;
                margin-bottom: 10px;
            }
            .text-right {
                text-align: right;
            }
            .page-break {
                page-break-before: always;
            }
        ';
    }
}
