<?php
use app\assets\AppAsset;
AppAsset::register($this);
$this->title = $title;
$counttype= count($types);
$this->params['breadcrumbs'][] = ['label' => 'Report', 'url' => ['/report/index']];
$js = <<<JS
$(document).ready(function() {
    $('#report-table').DataTable({
        dom: 'Bfrtip', // Defines the layout with Buttons at the top
        buttons: [
            'copy', 'csv', 'excel',
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':visible'
                },
                customize: function(doc) {
                    if (doc.content.length > 1 && doc.content[1].table) {
                        const numberOfColumns = doc.content[1].table.body[0].length;
                        const columnWidth = (100 / numberOfColumns).toFixed(2) + '%';
                        doc.content[1].table.widths = Array(numberOfColumns).fill(columnWidth);
                        doc.content[1].table.body[0].forEach((header, index) => {
                            header.style = 'header'; // You can define styles if needed
                        });
                    }
                }
            },
            'print'
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
echo '<table id="report-table" class="display" style="width:100%;border:solid 1px;" border="1">';
echo '<thead><tr>';
echo '<th rowspan="2" class="text-center align-middle">Name</th>';
foreach ($months as $month) {
    echo "<th class='text-center align-middle' colspan='" . $counttype . "'>" . DateTime::createFromFormat('n', $month)->format('M') . "</th>";
}
echo '<th rowspan="2" class="text-center align-middle">Total</th>';
echo '</tr>';
echo '<tr>';
foreach ($months as $month) {
    foreach ($types as $method) {
        echo "<th class='text-center align-middle'>$method</th>";
    }
}
echo '</tr></thead>';
foreach ($pivotTable as $admin => $row) {
    echo '<tr>';
    echo '<td>' . $admin . '</td>';
    foreach ($months as $month) {
        foreach ($types as $method) {
            echo '<td class="text-center align-middle">' . ($row[$month][$method] ?? 0) . '</td>';
        }
    }
    echo '<td class="text-center align-middle">' . $row['total'] . '</td>';
    echo '</tr>';
}
echo '</table>';