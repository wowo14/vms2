<?php
$this->title = '';
$this->params['breadcrumbs'][] = ['label' => 'Report', 'url' => ['/report/index']];
echo '<table border="1">';
echo '<tr>';
echo '<th class="text-center align-middle">Name</th>';
foreach ($months as $month) {
    echo "<th class='text-center align-middle'>" . DateTime::createFromFormat('n', $month)->format('M') . "</th>";
}
echo '<th>Total</th>';
echo '</tr>';
foreach ($pivotTable as $admin => $row) {
    echo '<tr>';
    echo '<td>' . $admin . '</td>';
    foreach ($months as $month) {
        echo '<td class="text-center align-middle">' . ($row[$month] ?? 0) . '</td>';
    }
    echo '<td class="text-center align-middle">' . $row['total'] . '</td>';
    echo '</tr>';
}
echo '</table>';
