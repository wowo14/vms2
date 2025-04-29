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
     * @return array An array containing 'dataProvider' and 'columnDefinitions'
     */
    public static function generatePivotReport($data, $rowField, $rowLabel, $colField = 'month', $monthLabels = [], $countField = null, $sumField = null) {
        // Initialize variables
        $pivot = [];
        $columns = [];
        $totalField = $sumField ? 'total_sum' : 'total_count';
        foreach ($data as $row) {
            $rowValue = $row[$rowField];
            $colValue = $row[$colField];
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
        // Sort columns (e.g., months)
        ksort($columns);
        // Create pivot rows
        $pivotRows = [];
        foreach ($pivot as $rowValue => $rowData) {
            $entry = [$rowField => $rowValue];
            foreach ($columns as $colValue) {
                if ($sumField) {
                    $entry[$colValue] = $rowData[$colValue]['sum'] ?? 0;
                } else {
                    $entry[$colValue] = $rowData[$colValue]['count'] ?? 0;
                }
            }
            $pivotRows[] = $entry;
        }
        // Calculate row totals
        foreach ($pivotRows as &$row) {
            $row[$totalField] = 0;
            foreach ($columns as $colValue) {
                $row[$totalField] += $row[$colValue] ?? 0;
            }
        }
        unset($row);
        // Create data provider
        $dataProvider = new ArrayDataProvider([
            'allModels' => $pivotRows,
            'pagination' => false,
            'sort' => [
                'attributes' => [$rowField, $totalField],
                'defaultOrder' => [
                    $totalField => SORT_DESC,
                ],
            ],
        ]);
        // Calculate column totals
        $totals = [];
        foreach ($columns as $colValue) {
            $totals[$colValue] = array_sum(array_column($dataProvider->allModels, $colValue));
        }
        // Build column definitions
        $columnDefinitions = array_merge(
            [
                [
                    'attribute' => $rowField,
                    'label' => $rowLabel,
                    'footer' => 'Total',
                ]
            ],
            array_map(function ($colValue) use ($dataProvider, $monthLabels, $sumField) {
                $total = array_sum(ArrayHelper::getColumn($dataProvider->allModels, $colValue));
                $colLabel = isset($monthLabels[$colValue]) ? $monthLabels[$colValue] : $colValue;
                return [
                    'attribute' => $colValue,
                    'label' => $colLabel,
                    'format' => $sumField ? 'currency' : 'raw',
                    'contentOptions' => $sumField ? ['class' => 'text-right'] : [],
                    'headerOptions' => $sumField ? ['class' => 'text-right'] : [],
                    'footerOptions' => $sumField ? ['class' => 'text-right'] : [],
                    'value' => function ($model) use ($colValue) {
                        return $model[$colValue] ?? 0;
                    },
                    'footer' => $sumField ? Yii::$app->tools->asCurrency($total) : $total,
                ];
            }, array_keys($columns)),
            [
                [
                    'attribute' => $totalField,
                    'label' => 'Jumlah',
                    'format' => $sumField ? 'currency' : 'raw',
                    'contentOptions' => $sumField ? ['class' => 'text-right'] : [],
                    'headerOptions' => $sumField ? ['class' => 'text-right'] : [],
                    'footerOptions' => $sumField ? ['class' => 'text-right'] : [],
                    'value' => function ($model) use ($totalField) {
                        return $model[$totalField];
                    },
                    'footer' => $sumField? Yii::$app->tools->asCurrency(array_sum(ArrayHelper::getColumn($dataProvider->allModels, $totalField))):array_sum(ArrayHelper::getColumn($dataProvider->allModels, $totalField)),
                ]
            ]
        );
        return [
            'dataProvider' => $dataProvider,
            'columnDefinitions' => $columnDefinitions,
            'pivotData' => $pivotRows,
            'columns' => $columns,
            'totals' => $totals
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
                $config['sumField'] ?? null
            );
        }
        return $reports;
    }
}
