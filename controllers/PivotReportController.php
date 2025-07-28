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
            $all = $this->getAllReportConfigs();
            // Kumpulkan keyword filter yang aktif
            $filters = [];
            $filterLabels = [];
            if ($model->kategori) {
                $filters[] = 'kategori';
                if ($model->kategori !== 'all') {
                    $ar = array_filter($model::optionsSettingtype('kategori_pengadaan', ['value', 'id']), function ($key) use ($model) {
                        return strpos($key, $model->kategori) !== false;
                    }, ARRAY_FILTER_USE_KEY);
                    $filterLabels[] = 'Kategori: ' . reset($ar);
                }
            }
            if ($model->metode) {
                $filters[] = 'metode';
                if ($model->metode !== 'all') {
                    $ar = array_filter($model::optionsSettingtype('metode_pengadaan', ['value', 'id']), function ($key) use ($model) {
                        return strpos($key, $model->metode) !== false;
                    }, ARRAY_FILTER_USE_KEY);
                    $filterLabels[] = 'Metode: ' . reset($ar);
                }
            }
            if ($model->pejabat) {
                $filters[] = 'pejabat';
                if ($model->pejabat !== 'all') {
                    $ar = array_filter($model::getAllpetugas(), function ($key) use ($model) {
                        return strpos($key, $model->pejabat) !== false;
                    }, ARRAY_FILTER_USE_KEY);
                    $filterLabels[] = 'Pejabat: ' . reset($ar);
                }
            }
            if ($model->admin) {
                $filters[] = 'admin';
                if ($model->admin !== 'all') {
                    $ar = array_filter($model::getAlladmin(), function ($key) use ($model) {
                        return strpos($key, $model->admin) !== false;
                    }, ARRAY_FILTER_USE_KEY);
                    $filterLabels[] = 'Admin: ' . reset($ar);
                }
            }
            if ($model->bidang) {
                $filters[] = 'bidang';
                if ($model->bidang !== 'all') {
                    $ar = array_filter(\app\models\Unit::collectAll()->pluck('unit', 'id')->toArray(), function ($key) use ($model) {
                        return strpos($key, $model->bidang) !== false;
                    }, ARRAY_FILTER_USE_KEY);
                    $filterLabels[] = 'Bidang: ' . reset($ar);
                }
            }
            if ($model->ppkom) {
                $filters[] = 'ppkom';
                if ($model->ppkom !== 'all') {
                    $ar = array_filter($model::optionppkom(), function ($key) use ($model) {
                        return strpos($key, $model->ppkom) !== false;
                    }, ARRAY_FILTER_USE_KEY);
                    $filterLabels[] = 'PPKOM: ' . reset($ar);
                }
            }
            // Yii::error($filterLabels);
            if (empty($filters)) {
                $allConfigs = $all;
            } else {
                $allConfigs = array_filter($all, function ($_, $key) use ($filters) {
                    foreach ($filters as $filter) {
                        if (strpos($key, $filter) !== false) {
                            return true;
                        }
                    }
                    return false;
                }, ARRAY_FILTER_USE_BOTH);
            }
            // Yii::error($allConfigs);
            $types = array_keys($allConfigs);
            $configs = [];
            $reports = [];
            foreach ($types as $type) {
                $config = $this->getReportConfig($type);
                $configs[$type] = $config;
                // Jika config punya sumField, dan multi gunakan multiSumFields
                if (isset($config['multi'])) {
                    $multipleSumFields = ['hps', 'hasilnego', 'efisien'];
                    $reports[$type] = PivotReportHelper::generatePivotReport(
                        $data,
                        $config['rowField'],
                        $config['rowLabel'],
                        'month',
                        (new PaketPengadaan())->months,
                        null,
                        null,
                        $multipleSumFields
                    );
                } else {
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
            }
            // Yii::error($reports);
            $viewData = [
                'reports' => $reports,
                'configs' => $configs,
                'months' => (new PaketPengadaan())->months,
                'year' => $model->tahun,
                'model' => $model,
                'filters' => $filters,
                'filterLabels' => $filterLabels,
            ];
            if (Yii::$app->request->post('type') === 'pdf') {
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
            return $this->render('report_all', $viewData);
        }
        return $this->redirect(['index']);
    }
    private function getYearList() {
        $years = PaketPengadaan::find()
            ->select(
                new Expression("strftime('%Y', tanggal_paket)  AS year")
            )
            ->distinct()
            ->asArray()
            ->all();
        if (empty($years)) {
            $years['year'] = [date('Y')];
        }
        // Sort years descending
        rsort($years);
        $years = ArrayHelper::map($years, 'year', 'year');
        return $years;
    }
    private function getRawData(ReportModel $model = null) {
        $query = collect((new PaketPengadaan)->rawData);
        if ($model) {
            if ($model->tahun) {
                $query = $query->filter(fn($item) => $item['year'] == $model->tahun);
            }
            // Tambahan filter periode
            if ($model->bulan_awal && $model->bulan_akhir && $model->bulan_awal != 0 && $model->bulan_akhir != 0) {
                $awal = (int)$model->bulan_awal;
                $akhir = (int)$model->bulan_akhir;
                if ($awal > $akhir) {
                    // swap jika user input terbalik
                    [$awal, $akhir] = [$akhir, $awal];
                }
                $query = $query->filter(fn($item) => (int)$item['month'] >= $awal && (int)$item['month'] <= $akhir);
            } else if ($model->bulan && $model->bulan != 0) {
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
            if ($model->ppkom && $model->ppkom !== 'all') {
                $query = $query->filter(fn($item) => $item['pejabat_ppkom_id'] == $model->ppkom);
            }
        }
        return $query->toArray();
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
            'metode_total_multiple' => [
                'rowField' => 'metode_pengadaan',
                'rowLabel' => 'Metode Pengadaan',
                'title' => 'Total Kontrak per Metode',
                'multi' => 'hasilnego',
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
            'kategori_total_multiple' => [
                'rowField' => 'kategori_pengadaan',
                'rowLabel' => 'Kategori Pengadaan',
                'title' => 'Total Kontrak per Kategori',
                'multi' => 'hasilnego',
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
            'pejabat_total_multiple' => [
                'rowField' => 'pejabat_pengadaan',
                'rowLabel' => 'Pejabat Pengadaan',
                'title' => 'Total Kontrak per Pejabat',
                'multi' => 'hasilnego',
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
            'admin_total_multiple' => [
                'rowField' => 'admin_pengadaan',
                'rowLabel' => 'Admin Pengadaan',
                'title' => '∑ Kontrak per Admin Pengadaan',
                'multi' => 'hasilnego',
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
            'bidang_total_multiple' => [
                'rowField' => 'bidang_bagian',
                'rowLabel' => 'Bidang Bagian',
                'title' => '∑ Kontrak per Bidang Bagian',
                'multi' => 'hasilnego',
                'subTitle' => 'Jumlah Kontrak Per Bidang Bagian'
            ],
            'ppkom' => [
                'rowField' => 'pejabat_ppkom',
                'rowLabel' => 'PPKOM',
                'title' => '∑ Pengadaan per PPKOM',
                'subTitle' => 'Jumlah Paket Pengadaan Per PPKOM'
            ],
            'ppkom_total' => [
                'rowField' => 'pejabat_ppkom',
                'rowLabel' => 'PPKOM',
                'title' => '∑ Kontrak per PPKOM',
                'sumField' => 'hasilnego',
                'subTitle' => 'Jumlah Kontrak Per PPKOM'
            ],
            'ppkom_total_multiple' => [
                'rowField' => 'pejabat_ppkom',
                'rowLabel' => 'PPKOM',
                'title' => '∑ Kontrak per PPKOM',
                'multi' => 'hasilnego',
                'subTitle' => 'Jumlah Kontrak Per PPKOM'
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
