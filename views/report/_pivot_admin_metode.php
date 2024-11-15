<link href="https://cdn.webdatarocks.com/latest/webdatarocks.min.css" rel="stylesheet" />
<script src="https://cdn.webdatarocks.com/latest/webdatarocks.toolbar.min.js"></script>
<script src="https://cdn.webdatarocks.com/latest/webdatarocks.js"></script>
<script src="https://cdn.webdatarocks.com/latest/webdatarocks.highcharts.js"></script>
<div id="pivot-table-container"></div>
<div id="pivot-table-metode"></div>
<?php
$modeldata = json_encode($model, JSON_NUMERIC_CHECK);
$js = <<<JS
new WebDataRocks({
        container: "#pivot-table-container",
        toolbar: true,
        report: {
            dataSource: {
                data: $modeldata
            },
            "slice": {
                "rows": [
                    {
                        "uniqueName": "pejabat_pengadaan",
                        "caption": "Pejabat Pengadaan"
                    }
                ],
                "columns": [
                    {
                        "uniqueName": "month",
                        // "caption": "Month",
                        "showTotals": false
                    },
                    {
                        "uniqueName": "kategori_pengadaan",
                        // "caption": "Kategori Pengadaan"
                    }
                ],
                "measures": [
                    {
                        "uniqueName": "pagu",
                        "aggregation": "count",
                        "caption": "Total Budget (Pagu)"
                    }
                ]
            },
            options: {
                 grid: {
                    type: "compact",
                    showGrandTotals: "on", // Enable grand totals
                    showTotals: "off", // Disable sub-totals within rows
                    showEmptyValues: false
                }
            }
        },
        reportcomplete: function() {
        let pivot = this;
        pivot.off("reportcomplete"); // Ensure it only runs once
        document.querySelectorAll('.webdatarocks-table td:empty').forEach(cell => {
            cell.style.display = 'none';
        });
    }
});
new WebDataRocks({
        container: "#pivot-table-metode",
        toolbar: true,
        report: {
            dataSource: {
                data: $modeldata
            },
            "slice": {
                "rows": [
                    {
                        "uniqueName": "pejabat_pengadaan",
                        "caption": "Pejabat Pengadaan"
                    }
                ],
                "columns": [
                    {
                        "uniqueName": "month",
                        // "caption": "Month",
                        "showTotals": false
                    },
                    {
                        "uniqueName": "metode_pengadaan",
                        // "caption": "Kategori Pengadaan"
                    }
                ],
                "measures": [
                    {
                        "uniqueName": "pagu",
                        "aggregation": "count",
                        "caption": "Total"
                    }
                ]
            },
            options: {
                 grid: {
                    type: "compact",
                    showGrandTotals: "on", // Enable grand totals
                    showTotals: "off", // Disable sub-totals within rows
                    showEmptyValues: false
                }
            }
        },
        reportcomplete: function() {
        let pivot = this;
        pivot.off("reportcomplete"); // Ensure it only runs once
        document.querySelectorAll('.webdatarocks-table td:empty').forEach(cell => {
            cell.style.display = 'none';
        });
    }
});
new WebDataRocks({
        container: "#pivot-table-metode",
        toolbar: true,
        report: {
            dataSource: {
                data: $modeldata
            },
            "slice": {
                "rows": [
                    {
                        "uniqueName": "pejabat_pengadaan",
                        "caption": "Pejabat Pengadaan"
                    }
                ],
                "columns": [
                    {
                        "uniqueName": "month",
                        // "caption": "Month",
                        "showTotals": false
                    },
                    {
                        "uniqueName": "metode_pengadaan",
                        // "caption": "Kategori Pengadaan"
                    }
                ],
                "measures": [
                    {
                        "uniqueName": "pagu",
                        "aggregation": "count",
                        "caption": "Total"
                    }
                ]
            },
            options: {
                 grid: {
                    type: "compact",
                    showGrandTotals: "on", // Enable grand totals
                    showTotals: "off", // Disable sub-totals within rows
                    showEmptyValues: false
                }
            }
        },
        reportcomplete: function() {
        let pivot = this;
        pivot.off("reportcomplete"); // Ensure it only runs once
        document.querySelectorAll('.webdatarocks-table td:empty').forEach(cell => {
            cell.style.display = 'none';
        });
    }
});
JS;
$this->registerJs($js);
