<?php
$this->registerCssFile('https://cdn.webdatarocks.com/latest/webdatarocks.min.css');
$this->registerJsFile('https://cdn.webdatarocks.com/latest/webdatarocks.toolbar.min.js');
$this->registerJsFile('https://cdn.webdatarocks.com/latest/webdatarocks.js');
$json = \yii\helpers\Json::encode($data);   // $data = array rows dari PHP
$js = <<<JS
new WebDataRocks({
    container: "#pivot",
    // toolbar: true,
    headers: false,
    report: {
        dataSource: { data: $json },
        slice: {
            rows:    [{ uniqueName: "metode_pengadaan" }],
            columns: [{ uniqueName: "bulan", showTotals: false }],
            measures: [{ uniqueName: "pagu", aggregation: "count", caption: "Total" }]
        },
        options: {
            grid:{
                showHeaders:false
            },
            sorting:"off"
        }
    }
});
JS;
$this->registerJs($js);
?>
<div id="pivot" style="height:600px;"></div>