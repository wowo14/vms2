<link href="https://cdn.webdatarocks.com/latest/webdatarocks.min.css" rel="stylesheet" />
<script src="https://cdn.webdatarocks.com/latest/webdatarocks.toolbar.min.js"></script>
<script src="https://cdn.webdatarocks.com/latest/webdatarocks.js"></script>
<script src="https://cdn.webdatarocks.com/latest/webdatarocks.highcharts.js"></script>
<script src="https://code.highcharts.com/4.2.2/highcharts.js"></script>
<script src="https://code.highcharts.com/4.2.2/highcharts-more.js"></script>
<div class="row">
    <div class="col-md-6">
        A. Jumlah Per Kategori Pengadaan Barang/Jasa
        <div id="pivot-kategoricount"></div>
    </div>
    <div class="col-md-6">
        Total Kontrak Pengadaan Barang/Jasa
        <div id="pivot-kategorisum"></div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        B. Jumlah Metode Pengadaan Barang/Jasa
        <div id="pivot-metodecount"></div>
    </div>
    <div class="col-md-6">
        Total Kontrak Per Metode Pengadaan Barang/Jasa
        <div id="pivot-metodesum"></div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        C. Jumlah Kegiatan Pengadaan Per Pejabat Pengadaan / kategori
        <div id="pivot-pejabatkategoricount"></div>
    </div>
    <div class="col-md-6">
        Total Kontrak Pengadaan Per Pejabat Pengadaan
        <div id="pivot-pejabatkategorisum"></div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        D. Jumlah Kegiatan Pengadaan Per Pejabat Pengadaan / Metode
        <div id="pivot-pejabatmetodecount"></div>
    </div>
    <div class="col-md-6">
        Total Kontrak Pengadaan Per Pejabat Pengadaan
        <div id="pivot-pejabatmetodesum"></div>
    </div>
    <div id="highcharts-container"></div>
</div>
<?php
$modeldata = json_encode($model, JSON_NUMERIC_CHECK);
// print_r($modeldata);
$js = <<<JS
const pivotkategoricount =new WebDataRocks({
        container: "#pivot-kategoricount",
        toolbar: true,
        report: {
            dataSource: {
                data: $modeldata
            },
            "slice": {
                "rows": [
                    {
                        "uniqueName": "kategori_pengadaan",
                        "caption": "Kategori Pengadaan"
                    }
                ],
                "columns": [
                    {
                        "uniqueName": "bulan",
                        // "caption": "Month",
                        "showTotals": false
                    },
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
                },
                columnWidth: "auto"
            }
        },
        reportcomplete: function() {
        // let pivot = this;
        pivotkategoricount.off("reportcomplete"); // Ensure it only runs once
        document.querySelectorAll('.webdatarocks-table td:empty').forEach(cell => {
            cell.style.display = 'none';
        });
    }
});
const pivotkategorisum=new WebDataRocks({
        container: "#pivot-kategorisum",
        toolbar: true,
        report: {
            dataSource: {
                data: $modeldata
            },
            "slice": {
                "rows": [
                    {
                        "uniqueName": "kategori_pengadaan",
                        "caption": "Kategori Pengadaan"
                    }
                ],
                "columns": [
                    {
                        "uniqueName": "bulan",
                        // "caption": "Month",
                        "showTotals": false
                    }
                ],
                "measures": [
                    {
                        "uniqueName": "hasilnego",
                        "aggregation": "sum",
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
                },
                columnWidth: "auto"
            }
        },
        reportcomplete: function() {
        // let pivot = this;
        pivotkategorisum.off("reportcomplete"); // Ensure it only runs once
        document.querySelectorAll('.webdatarocks-table td:empty').forEach(cell => {
            cell.style.display = 'none';
        });
    }
});
const pivotmetodecount=new WebDataRocks({
        container: "#pivot-metodecount",
        toolbar: true,
        report: {
            dataSource: {
                data: $modeldata
            },
            "slice": {
                "rows": [
                    {
                        "uniqueName": "metode_pengadaan",
                        "caption": "Metode Pengadaan"
                    }
                ],
                "columns": [
                    {
                        "uniqueName": "bulan",
                        // "caption": "Month",
                        "showTotals": false
                    },
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
                },
                columnWidth: "auto"
            }
        },
        reportcomplete: function() {
        // let pivot = this;
        pivotmetodecount.off("reportcomplete"); // Ensure it only runs once
        document.querySelectorAll('.webdatarocks-table td:empty').forEach(cell => {
            cell.style.display = 'none';
        });
    }
});
const pivotmetodesum=new WebDataRocks({
        container: "#pivot-metodesum",
        toolbar: true,
        report: {
            dataSource: {
                data: $modeldata
            },
            "slice": {
                "rows": [
                    {
                        "uniqueName": "metode_pengadaan",
                        "caption": "Metode Pengadaan"
                    }
                ],
                "columns": [
                    {
                        "uniqueName": "bulan",
                        // "caption": "Month",
                        "showTotals": false
                    }
                ],
                "measures": [
                    {
                        "uniqueName": "hasilnego",
                        "aggregation": "sum",
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
                },
                columnWidth: "auto"
            }
        },
        reportcomplete: function() {
        // let pivot = this;
        pivotmetodesum.off("reportcomplete"); // Ensure it only runs once
        document.querySelectorAll('.webdatarocks-table td:empty').forEach(cell => {
            cell.style.display = 'none';
        });
    }
});
const pivotpejabatkategoricount=new WebDataRocks({
        container: "#pivot-pejabatkategoricount",
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
                        "uniqueName": "bulan",
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
                        "caption": "Total Job"
                    }
                ]
            },
            options: {
                 grid: {
                    type: "compact",
                    showGrandTotals: "on", // Enable grand totals
                    showTotals: "off", // Disable sub-totals within rows
                    showEmptyValues: false
                },
                columnWidth: "auto"
            }
        },
        reportcomplete: function() {
        // let pivot = this;
        pivotpejabatkategoricount.off("reportcomplete"); // Ensure it only runs once
        document.querySelectorAll('.webdatarocks-table td:empty').forEach(cell => {
            cell.style.display = 'none';
        });
    }
});
const pivotpejabatkategorisum=new WebDataRocks({
        container: "#pivot-pejabatkategorisum",
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
                        "uniqueName": "bulan",
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
                        "uniqueName": "hasilnego",
                        "aggregation": "sum",
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
                },
                columnWidth: "auto"
            }
        },
        reportcomplete: function() {
        // let pivot = this;
        pivotpejabatkategorisum.off("reportcomplete"); // Ensure it only runs once
        document.querySelectorAll('.webdatarocks-table td:empty').forEach(cell => {
            cell.style.display = 'none';
        });
    }
});
const pivotpejabatmetodecount=new WebDataRocks({
        container: "#pivot-pejabatmetodecount",
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
                        "uniqueName": "bulan",
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
                },
                columnWidth: "auto"
            }
        },
        reportcomplete: function() {
        // let pivot = this;
        pivotpejabatmetodecount.off("reportcomplete"); // Ensure it only runs once
        document.querySelectorAll('.webdatarocks-table td:empty').forEach(cell => {
            cell.style.display = 'none';
        });
    }
});
const pivotpejabatmetodesum=new WebDataRocks({
        container: "#pivot-pejabatmetodesum",
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
                        "uniqueName": "bulan",
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
                        "uniqueName": "hasilnego",
                        "aggregation": "sum",
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
                },
                columnWidth: "auto"
            }
        },
        reportcomplete: function() {
            // let pivot = this;
            pivotpejabatmetodesum.off("reportcomplete"); // Ensure it only runs once
            document.querySelectorAll('.webdatarocks-table td:empty').forEach(cell => {
                cell.style.display = 'none';
            });
            createChartpejabatmetodesum();
        }
});
function createChartpejabatmetodesum() {
  pivotpejabatmetodecount.highcharts.getData({
    type: "spline"
  },
  // Function called when data for the chart is ready
  (data) => {
    Highcharts.chart("highcharts-container", data);
  },
  // Function called on report changes (filtering, sorting, etc.)
  (data) => {
    Highcharts.chart("highcharts-container", data);
  });
}
JS;
$this->registerJs($js);
