<?php

use yii\helpers\Html;
// print_r($keys);
?>
<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr class="table-primary">
            <th><?= Html::encode($rowLabel) ?></th>
            <?php foreach ($months as $month): ?>
                <th><?= Html::encode($month) ?></th>
            <?php endforeach; ?>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $dataRow = [];
        foreach ($report['pivotData'] as $rowName => $rowData): ?>
            <tr>
                <td><?= Html::encode($rowData[$keys['rowField']]) ?></td>
                <?php
                $total = $value = 0;
                foreach ($months as $monthIndex => $monthName) {
                    $dataRow = is_object($rowData) ? $rowData->allModels : $rowData;
                    // print_r($dataRow);
                    // foreach($dataRow as $data){
                    //     echo $data;
                    $value = isset($dataRow[$monthIndex + 1]) ? $dataRow[$monthIndex + 1] : 0;
                    // }
                    $total += $value;
                    echo '<td>' . number_format($value, 0, ',', '.') . '</td>';
                }
                ?>
                <td><strong><?= number_format($total, 0, ',', '.') ?></strong></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>