<?php
echo '<table border="1">';
echo '<tr>';
echo '<th>Name</th>';
foreach ($months as $month) {
    foreach ($metodePengadaanTypes as $method) {
        echo "<th>$month ($method)</th>";
    }
}
echo '<th>Total</th>';
echo '</tr>';
foreach ($pivotTable as $admin => $row) {
    echo '<tr>';
    echo '<td>' . $admin . '</td>';
    foreach ($months as $month) {
        foreach ($metodePengadaanTypes as $method) {
            echo '<td>' . ($row[$month][$method] ?? 0) . '</td>';
        }
    }
    echo '<td>' . $row['total'] . '</td>';
    echo '</tr>';
}
echo '</table>';
