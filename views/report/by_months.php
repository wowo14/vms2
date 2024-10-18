<?php
use app\assets\AppAsset;
AppAsset::register($this);
$js = <<<JS
$(document).ready(function() {
    $('#report-table').DataTable({
        dom: 'Bfrtip', // Defines the layout with Buttons at the top
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        columnDefs: [
            {
                targets: 0,
                orderable: false
            }
        ]
    });
});
JS;
$this->registerJs($js);
$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Report', 'url' => ['/report/index']];
echo '<table id="report-table" class="display" style="width:100%">';
echo '<thead><tr>';
echo '<th class="text-center align-middle">Name</th>';
foreach ($months as $month) {
    echo "<th class='text-center align-middle'>" . DateTime::createFromFormat('n', $month)->format('M') . "</th>";
}
echo '<th>Total</th>';
echo '</tr>
</thead>';
foreach ($pivotTable as $admin => $row) {
    echo '<tr>';
    echo '<td>' . $admin . '</td>';
    foreach ($months as $month) {
        echo '<td class="text-center align-middle">' . ($row[$month] ?? 0) . '</td>';
    }
    echo '<td class="text-center align-middle font-weight-bold">' . $row['total'] . '</td>';
    echo '</tr>';
}
echo '</table>';
// echo json_encode($pivotTable);
