<link href="https://cdn.webdatarocks.com/latest/webdatarocks.min.css" rel="stylesheet" />
<script src="https://cdn.webdatarocks.com/latest/webdatarocks.toolbar.min.js"></script>
<script src="https://cdn.webdatarocks.com/latest/webdatarocks.js"></script>
<script src="https://cdn.webdatarocks.com/latest/webdatarocks.highcharts.js"></script>
<script src="https://code.highcharts.com/4.2.2/highcharts.js"></script>
<script src="https://code.highcharts.com/4.2.2/highcharts-more.js"></script>
<div class="row">
    <div class="col-md-6">A. Jumlah Per Kategori Pengadaan Barang/Jasa
        <div id="pivot-kategoricount"></div>
    </div>
    <div class="col-md-6">Total Kontrak Pengadaan Barang/Jasa
        <div id="pivot-kategorisum"></div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">B. Jumlah Metode Pengadaan Barang/Jasa
        <div id="pivot-metodecount"></div>
    </div>
    <div class="col-md-6">Total Kontrak Per Metode Pengadaan Barang/Jasa
        <div id="pivot-metodesum"></div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">C. Jumlah Kegiatan Pengadaan Per Pejabat Pengadaan / kategori
        <div id="pivot-pejabatkategoricount"></div>
    </div>
    <div class="col-md-6">Total Kontrak Pengadaan Per Pejabat Pengadaan
        <div id="pivot-pejabatkategorisum"></div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">D. Jumlah Kegiatan Pengadaan Per Pejabat Pengadaan / Metode
        <div id="pivot-pejabatmetodecount"></div>
    </div>
    <div class="col-md-6">Total Kontrak Pengadaan Per Pejabat Pengadaan
        <div id="pivot-pejabatmetodesum"></div>
    </div>
    <!-- <div id="highcharts-container"></div> -->
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
//var pdfFile{$containerId} = "{$pdfFile}";
// var pdfViewer{$containerId} = document.getElementById("$containerId");
$js = <<<JS
function createPivot(container, rows, columns, measures, aggregation, caption) {
    return new WebDataRocks({
        container: container,
        toolbar: true,
        report: {
            dataSource: {
                data: $modeldata
            },
            slice: {
                rows: rows,
                columns: columns,
                measures: measures
            },
            options: {
                grid: {
                    type: "compact",
                    showGrandTotals: "on",
                    showTotals: "off",
                    showEmptyValues: false
                },
                columnWidth: "auto"
            }
        },
        // reportcomplete: function() {
        //     variableid.off("reportcomplete");
        //     document.querySelectorAll('.webdatarocks-table td:empty').forEach(cell => {
        //         cell.style.display = 'none';
        //     });
        // }
    });
}
const unitbidangcount = createPivot(
    "#pivot-unitbidangcount",
    [{ uniqueName: "bidang_bagian", caption: "Unit/Bidang/Bagian" }],
    [{ uniqueName: "month", showTotals: false }],
    [{ uniqueName: "pagu", aggregation: "count", caption: "Total" }]
);
const unitbidangsum = createPivot(
    "#pivot-unitbidangsum",
    [{ uniqueName: "bidang_bagian", caption: "Unit/Bidang/Bagian" }],
    [{ uniqueName: "month", showTotals: false }],
    [{ uniqueName: "hasilnego", aggregation: "sum", caption: "Total" }]
);
const pivotkategoricount = createPivot(
    "#pivot-kategoricount",
    [{ uniqueName: "kategori_pengadaan", caption: "Kategori Pengadaan" }],
    [{ uniqueName: "month", showTotals: false }],
    [{ uniqueName: "pagu", aggregation: "count", caption: "Total" }]
);
const pivotkategorisum = createPivot(
    "#pivot-kategorisum",
    [{ uniqueName: "kategori_pengadaan", caption: "Kategori Pengadaan" }],
    [{ uniqueName: "month", showTotals: false }],
    [{ uniqueName: "hasilnego", aggregation: "sum", caption: "Total" }]
);
const pivotmetodecount = createPivot(
    "#pivot-metodecount",
    [{ uniqueName: "metode_pengadaan", caption: "Metode Pengadaan" }],
    [{ uniqueName: "month", showTotals: false }],
    [{ uniqueName: "pagu", aggregation: "count", caption: "Total" }]
);
const pivotmetodesum = createPivot(
    "#pivot-metodesum",
    [{ uniqueName: "metode_pengadaan", caption: "Metode Pengadaan" }],
    [{ uniqueName: "month", showTotals: false }],
    [{ uniqueName: "hasilnego", aggregation: "sum", caption: "Total" }]
);
const pivotpejabatkategoricount = createPivot(
    "#pivot-pejabatkategoricount",
    [
        { uniqueName: "pejabat_pengadaan", caption: "Pejabat Pengadaan" },
        { uniqueName: "kategori_pengadaan" }
    ],
    [{ uniqueName: "month", showTotals: false }],
    [{ uniqueName: "pagu", aggregation: "count", caption: "Total Job" }]
);
const pivotpejabatkategorisum = createPivot(
    "#pivot-pejabatkategorisum",
    [
        { uniqueName: "pejabat_pengadaan", caption: "Pejabat Pengadaan" },
        { uniqueName: "kategori_pengadaan" }
    ],
    [{ uniqueName: "month", showTotals: false }],
    [{ uniqueName: "hasilnego", aggregation: "sum", caption: "Total" }]
);
const pivotpejabatmetodecount = createPivot(
    "#pivot-pejabatmetodecount",
    [{ uniqueName: "pejabat_pengadaan", caption: "Pejabat Pengadaan" }],
    [
        { uniqueName: "month", showTotals: false },
        { uniqueName: "metode_pengadaan" }
    ],
    [{ uniqueName: "pagu", aggregation: "count", caption: "Total" }]
);
const pivotpejabatmetodesum = createPivot(
    "#pivot-pejabatmetodesum",
    [{ uniqueName: "pejabat_pengadaan", caption: "Pejabat Pengadaan" }],
    [
        { uniqueName: "month", showTotals: false },
        { uniqueName: "metode_pengadaan" }
    ],
    [{ uniqueName: "hasilnego", aggregation: "sum", caption: "Total" }]
);
function createChartpejabatmetodesum() {
    pivotpejabatmetodecount.on("reportcomplete", function() {
        pivotpejabatmetodecount.highcharts.getData({
            type: "spline"
        }, (data) => {
            Highcharts.chart("highcharts-container", data);
        }, (data) => {
            Highcharts.chart("highcharts-container", data);
        });
    });
}
createChartpejabatmetodesum();
JS;
$this->registerJs($js);
