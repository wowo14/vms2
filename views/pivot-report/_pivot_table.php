<?php
use kartik\grid\GridView;
echo '<h3 style="text-align:center">Dokumen Persiapan Pengadaan</h3>';
echo '<h3 style="text-align:center">'.$keys['subTitle'].' Periode Tahun '.$year.'</h3>';
echo GridView::widget([
    'dataProvider' => $report['dataProvider'],
    'columns' => $report['columnDefinitions'],
    'summary' => false,
    'showFooter' => true
]);