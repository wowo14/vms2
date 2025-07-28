<?php
namespace app\helpers;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
class PivotReportHelper {
    /**
     * Generate pivot data and column definitions for reports
     *
     * @param array $data Raw data from the database
     * @param string $rowField The field name to use for rows (e.g., 'metode_pengadaan', 'kategori', 'pejabat_pengadaan')
     * @param string $rowLabel The label for the row field
     * @param string $colField The field name to use for columns (typically 'month')
     * @param array $monthLabels Associative array of month numbers to month names
     * @param string $countField Field to count (or null to count occurrences)
     * @param string $sumField Field to sum (or null for count only reports)
     * @param array|null $multiSumFields Array of field names to sum for multi-column reports (e.g., ['hps', 'hasilnego'])
     * @return array An array containing 'dataProvider' and 'columnDefinitions'
     */
    public static function generatePivotReport($data, $rowField, $rowLabel, $colField = 'month', $monthLabels = [], $countField = null, $sumField = null, $multiSumFields = null) {
        // Initialize variables
        $pivot = [];
        $columns = [];
        $allMonths = [];
        foreach ($data as $row) {
            $rowValue = $row[$rowField];
            $colValue = $row[$colField];
            $allMonths[$colValue] = $colValue;
            if ($multiSumFields && is_array($multiSumFields)) {
                foreach ($multiSumFields as $field) {
                    if (!isset($pivot[$rowValue][$colValue][$field])) {
                        $pivot[$rowValue][$colValue][$field] = 0;
                    }
                    $pivot[$rowValue][$colValue][$field] += isset($row[$field]) ? $row[$field] : 0;
                }
            } else {
                // Track unique column values
                $columns[$colValue] = $colValue;
                // Initialize if not exists
                if (!isset($pivot[$rowValue][$colValue])) {
                    $pivot[$rowValue][$colValue] = [
                        'count' => 0,
                        'sum' => 0
                    ];
                }
                // Count occurrences
                $pivot[$rowValue][$colValue]['count']++;
                // Sum values if needed
                if ($sumField && isset($row[$sumField])) {
                    $pivot[$rowValue][$colValue]['sum'] += $row[$sumField];
                }
            }
        }
        ksort($allMonths);
        // Create pivot rows
        $pivotRows = [];
        $columnDefinitions = [];
        // Jika multiSumFields diisi (hanya config dengan sumField), split kolom bulan menjadi multi kolom per bulan
        if ($multiSumFields && is_array($multiSumFields)) {
            foreach ($pivot as $rowValue => $rowData) {
                $entry = [$rowField => $rowValue];
                foreach ($allMonths as $colValue) {
                    foreach ($multiSumFields as $field) {
                        $entry[$field . '_' . $colValue] = isset($rowData[$colValue][$field]) ? $rowData[$colValue][$field] : 0;
                    }
                    // Efisien khusus: jika ada hps dan hasilnego
                    if (in_array('hps', $multiSumFields) && in_array('hasilnego', $multiSumFields)) {
                        $hasilnego = ($entry['hasilnego_' . $colValue] ?? 0);
                        if ($hasilnego > 0) {
                            $entry['efisien_' . $colValue] = ($entry['hps_' . $colValue] ?? 0) - $hasilnego;
                        } else {
                            $entry['efisien_' . $colValue] = 0;
                        }
                    }
                }
                $pivotRows[] = $entry;
            }
            // Build column definitions
            $columnDefinitions = [
                [
                    'attribute' => $rowField,
                    'label' => $rowLabel,
                    'footer' => 'Total',
                ]
            ];
            foreach ($allMonths as $colValue) {
                foreach ($multiSumFields as $field) {
                    $colLabel = isset($monthLabels[$colValue]) ? $monthLabels[$colValue] : $colValue;
                    $label = strtoupper($field);
                    if ($field === 'efisien') $label = 'Efisien';
                    $columnDefinitions[] = [
                        'attribute' => $field . '_' . $colValue,
                        'label' => $colLabel . ' ' . $label,
                        'format' => 'currency',
                        'contentOptions' => ['class' => 'text-right'],
                        'headerOptions' => ['class' => 'text-right'],
                        'footerOptions' => ['class' => 'text-right'],
                        'value' => function ($model) use ($field, $colValue) {
                            return $model[$field . '_' . $colValue] ?? 0;
                        },
                        'footer' => function ($models) use ($field, $colValue) {
                            return array_sum(array_column($models, $field . '_' . $colValue));
                        }
                    ];
                }
                // Efisien kolom
                if (in_array('hps', $multiSumFields) && in_array('hasilnego', $multiSumFields)) {
                    $columnDefinitions[] = [
                        'attribute' => 'efisien_' . $colValue,
                        'label' => (isset($monthLabels[$colValue]) ? $monthLabels[$colValue] : $colValue) . ' Efisien',
                        'format' => 'currency',
                        'contentOptions' => ['class' => 'text-right'],
                        'headerOptions' => ['class' => 'text-right'],
                        'footerOptions' => ['class' => 'text-right'],
                        'value' => function ($model) use ($colValue) {
                            return $model['efisien_' . $colValue] ?? 0;
                        },
                        'footer' => function ($models) use ($colValue) {
                            return array_sum(array_column($models, 'efisien_' . $colValue));
                        }
                    ];
                }
            }
        } else {
            // Ini adalah bagian yang dikoreksi
            // Jika multiSumFields null atau tidak array, maka buat pivot dengan 1 kolom data (sum atau count)
            foreach ($pivot as $rowValue => $rowData) {
                $entry = [$rowField => $rowValue];
                // Inisialisasi total untuk baris ini
                $rowTotal = 0;
                foreach ($allMonths as $colValue) { // Menggunakan $allMonths untuk memastikan semua bulan ada
                    if ($sumField) {
                        $value = $rowData[$colValue]['sum'] ?? 0;
                    } else {
                        $value = $rowData[$colValue]['count'] ?? 0;
                    }
                    $entry[$colValue] = $value; // Tetap simpan per bulan untuk perhitungan total baris
                    $rowTotal += $value;
                }
                $entry['total_data'] = $rowTotal; // Menambahkan kolom total data untuk baris ini
                $pivotRows[] = $entry;
            }
            // Build column definitions
            $columnDefinitions = [
                [
                    'attribute' => $rowField,
                    'label' => $rowLabel,
                    'footer' => 'Total',
                ]
            ];
            // Tambahkan kolom untuk setiap bulan yang ada
            foreach ($allMonths as $colValue) {
                $colLabel = isset($monthLabels[$colValue]) ? $monthLabels[$colValue] : $colValue;
                $columnDefinitions[] = [
                    'attribute' => $colValue,
                    'label' => $colLabel,
                    'format' => $sumField ? 'currency' : 'raw',
                    'contentOptions' => ['class' => 'text-right'],
                    'headerOptions' => ['class' => 'text-right'],
                    'footerOptions' => ['class' => 'text-right'],
                    'footer' => function ($models) use ($colValue) {
                        return array_sum(array_column($models, $colValue));
                    }
                ];
            }
            // Tambahkan kolom total di akhir
            $columnDefinitions[] = [
                'attribute' => 'total_data',
                'label' => 'Total',
                'format' => $sumField ? 'currency' : 'raw',
                'contentOptions' => ['class' => 'text-right'],
                'headerOptions' => ['class' => 'text-right'],
                'footerOptions' => ['class' => 'text-right'],
                'footer' => function ($models) {
                    return array_sum(array_column($models, 'total_data'));
                }
            ];
        }
        return [
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $pivotRows,
                'pagination' => false,
            ]),
            'columnDefinitions' => $columnDefinitions,
            'pivotData' => $pivotRows,
            'columns' => $allMonths,
        ];
    }
    /**
     * Generate multiple pivot reports based on the same data but different dimensions
     *
     * @param array $data Raw data
     * @param array $pivotConfigs Array of pivot configurations, each containing rowField, rowLabel, etc.
     * @return array Reports with the same keys as $pivotConfigs
     */
    public static function generateMultiplePivotReports($data, $pivotConfigs) {
        $reports = [];
        foreach ($pivotConfigs as $key => $config) {
            $reports[$key] = self::generatePivotReport(
                $data,
                $config['rowField'],
                $config['rowLabel'],
                $config['colField'] ?? 'month',
                $config['monthLabels'] ?? [],
                $config['countField'] ?? null,
                $config['sumField'] ?? null,
                $config['multiSumFields'] ?? null // Pastikan multiSumFields juga diteruskan
            );
        }
        return $reports;
    }
}
