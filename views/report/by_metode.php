<?php
$this->title = '';
$this->params['breadcrumbs'][] =['label' => 'Report', 'url' => ['/report/index']];
echo '<table border="1">';
echo '<tr>';
echo '<th rowspan="2" class="text-center align-middle">Name</th>';
foreach ($months as $month) {
    echo "<th class='text-center align-middle' colspan='" . count($metodePengadaanTypes) . "'>". DateTime::createFromFormat('n', $month)->format('M')."</th>";
}
echo '<th rowspan="2" class="text-center align-middle">Total</th>';
echo '</tr>';
echo '<tr>';
foreach ($months as $month) {
    foreach ($metodePengadaanTypes as $method) {
        echo "<th class='text-center align-middle'>$method</th>";
    }
}
echo '</tr>';
foreach ($pivotTable as $admin => $row) {
    echo '<tr>';
    echo '<td>' . $admin . '</td>';
    foreach ($months as $month) {
        foreach ($metodePengadaanTypes as $method) {
            echo '<td class="text-center align-middle">' . ($row[$month][$method] ?? 0) . '</td>';
        }
    }
    echo '<td class="text-center align-middle">' . $row['total'] . '</td>';
    echo '</tr>';
}
echo '</table>';
