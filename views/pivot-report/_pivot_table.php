<?php
use kartik\grid\GridView;
$bulan = '';
if($model->bulan_awal==$model->bulan_akhir){
    $model->bulan=$model->bulan_awal;
}
if ($model->bulan && $model->bulan != 0) {
    if (is_array($report['columnDefinitions'])) {
        $ar = $report['columnDefinitions'];
        $bulan = ($ar[1]['label']);
    }
}
if($model->bulan_awal!==$model->bulan_akhir){
    $bulan=$months[$model->bulan_awal].' s/d '.$months[$model->bulan_akhir];
}
// echo '<h3 style="text-align:center">Dokumen Persiapan Pengadaan</h3>';
echo '<h3 style="text-align:center">' . $keys['subTitle'] . ' Periode ' . $bulan . ' Tahun ' . $year . '</h3>';
echo GridView::widget([
    'dataProvider' => $report['dataProvider'],
    'columns' => $report['columnDefinitions'],
    'summary' => false,
    'showFooter' => true
]);
?>