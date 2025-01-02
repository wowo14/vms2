<link href="https://cdn.webdatarocks.com/latest/webdatarocks.min.css" rel="stylesheet" />
<script src="https://cdn.webdatarocks.com/latest/webdatarocks.toolbar.min.js"></script>
<script src="https://cdn.webdatarocks.com/latest/webdatarocks.js"></script>
<script src="https://cdn.webdatarocks.com/latest/webdatarocks.highcharts.js"></script>
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
</div>
<div class="row">
    <div class="col-md-6">E. Jumlah Kegiatan Pengadaan Per Unit/Bidang/Bagian
        <div id="pivot-unitbidangcount"></div>
    </div>
    <div class="col-md-6">Total Kontrak Pengadaan Per Unit/Bidang/Bagian
        <div id="pivot-unitbidangsum"></div>
    </div>
    <!-- <div id="highcharts-container"></div> -->
</div>
<?php
$modeldata = json_encode($model, JSON_NUMERIC_CHECK);
$js = <<<JS
new WebDataRocks({
        container: "#pivot-unitbidangcount",
        toolbar: true,
        report: {
            dataSource: {
                data: $modeldata
            },
            "slice": {
                "rows": [
                    {
                        "uniqueName": "bidang_bagian",
                        "caption": "Unit/Bidang/Bagian"
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
        container: "#pivot-unitbidangsum",
        toolbar: true,
        report: {
            dataSource: {
                data: $modeldata
            },
            "slice": {
                "rows": [
                    {
                        "uniqueName": "bidang_bagian",
                        "caption": "Unit/Bidang/Bagian"
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
