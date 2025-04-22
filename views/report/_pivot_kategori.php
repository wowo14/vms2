<link href="https://cdn.webdatarocks.com/latest/webdatarocks.min.css" rel="stylesheet" />
<script src="https://cdn.webdatarocks.com/latest/webdatarocks.toolbar.min.js"></script>
<script src="https://cdn.webdatarocks.com/latest/webdatarocks.js"></script>
<script src="https://cdn.webdatarocks.com/latest/webdatarocks.highcharts.js"></script>
<select id="yearFilter" style="margin: 10px; padding: 5px;">
    <option value="all">Semua Tahun</option>
</select>
<label for="startMonth">Bulan Awal:</label>
<select id="startMonth"></select>
<label for="endMonth">Bulan Akhir:</label>
<select id="endMonth"></select>
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
<?php
$modeldata = json_encode($model, JSON_NUMERIC_CHECK);
$js = <<<JS
const monthLabels = {
  1: "1.Januari", 2: "2.Februari", 3: "3.Maret", 4: "4.April",
  5: "5.Mei", 6: "6.Juni", 7: "7.Juli", 8: "8.Agustus",
  9: "9.September", 10: "10.Oktober", 11: "11.November", 12: "12.Desember"
};
let currentYear = "all";
const originalData = $modeldata.map(row => ({
  ...row,
  monthLabel: monthLabels[row.month] || row.month
}));
function filterDataByPeriod(data, year, startMonth, endMonth) {
  return data.filter(row => {
    const matchYear = year === "all" || String(row.year) === year;
    const matchMonth = startMonth && endMonth
      ? (startMonth <= endMonth
        ? row.month >= startMonth && row.month <= endMonth
        : row.month >= startMonth || row.month <= endMonth)
      : true;
    return matchYear && matchMonth;
  });
}
function initPivot({ container, title, rows, columns, measures }, data) {
  new WebDataRocks({
    container,
    toolbar: true,
    report: {
      dataSource: { data },
      slice: { rows, columns, measures },
      options: {
        grid: {
          title,
          type: "compact",
          showGrandTotals: "on",
          showTotals: "off",
          showEmptyValues: false
        }
      }
    },
  });
}
const pivotConfigs = [
  {
    container: "#pivot-kategoricount",
    title: "Jumlah Per Kategori Pengadaan",
    rows: [{ uniqueName: "kategori_pengadaan", caption: "Kategori Pengadaan" }],
    columns: [{ uniqueName: "month", showTotals: false }],
    measures: [{ uniqueName: "pagu", aggregation: "count", caption: "Jumlah" }]
  },
  {
    container: "#pivot-kategorisum",
    title: "Total Kontrak Per Kategori Pengadaan",
    rows: [{ uniqueName: "kategori_pengadaan", caption: "Kategori Pengadaan" }],
    columns: [{ uniqueName: "month", showTotals: false }],
    measures: [{ uniqueName: "hasilnego", aggregation: "sum", caption: "Total Kontrak" }]
  },
  {
    container: "#pivot-metodecount",
    title: "Jumlah Per Metode Pengadaan",
    rows: [{ uniqueName: "metode_pengadaan", caption: "Metode Pengadaan" }],
    columns: [{ uniqueName: "month", showTotals: false }],
    measures: [{ uniqueName: "pagu", aggregation: "count", caption: "Jumlah" }]
  },
  {
    container: "#pivot-metodesum",
    title: "Total Kontrak Per Metode Pengadaan",
    rows: [{ uniqueName: "metode_pengadaan", caption: "Metode Pengadaan" }],
    columns: [{ uniqueName: "month", showTotals: false }],
    measures: [{ uniqueName: "hasilnego", aggregation: "sum", caption: "Total Kontrak" }]
  },
  {
    container: "#pivot-pejabatkategoricount",
    title: "Jumlah Kegiatan Per Pejabat/Kategori",
    rows: [{ uniqueName: "pejabat_pengadaan", caption: "Pejabat Pengadaan" }],
    columns: [
      { uniqueName: "month", showTotals: false },
      { uniqueName: "kategori_pengadaan" }
    ],
    measures: [{ uniqueName: "pagu", aggregation: "count", caption: "Jumlah" }]
  },
  {
    container: "#pivot-pejabatkategorisum",
    title: "Total Kontrak Per Pejabat/Kategori",
    rows: [{ uniqueName: "pejabat_pengadaan", caption: "Pejabat Pengadaan" }],
    columns: [
      { uniqueName: "month", showTotals: false },
      { uniqueName: "kategori_pengadaan" }
    ],
    measures: [{ uniqueName: "hasilnego", aggregation: "sum", caption: "Total Kontrak" }]
  }
];
function renderAllPivots(year = "all", startMonth = null, endMonth = null) {
  const filteredData = filterDataByPeriod(originalData, year, startMonth, endMonth);
  document.querySelectorAll(".webdatarocks").forEach(el => el.innerHTML = "");
  pivotConfigs.forEach(config => initPivot(config, filteredData));
}
renderAllPivots();
// Isi dropdown tahun
const yearSet = new Set(originalData.map(row => row.year));
const yearFilter = document.getElementById("yearFilter");
Array.from(yearSet).sort().forEach(year => {
  const option = document.createElement("option");
  option.value = year;
  option.textContent = year;
  yearFilter.appendChild(option);
});
// Isi dropdown bulan
const startMonthSelect = document.getElementById("startMonth");
const endMonthSelect = document.getElementById("endMonth");
for (let i = 1; i <= 12; i++) {
  startMonthSelect.appendChild(new Option(monthLabels[i], i));
  endMonthSelect.appendChild(new Option(monthLabels[i], i));
}
// Event handler perubahan filter
function updatePivotsFromFilter() {
  const year = yearFilter.value;
  const startMonth = parseInt(startMonthSelect.value) || null;
  const endMonth = parseInt(endMonthSelect.value) || null;
  renderAllPivots(year, startMonth, endMonth);
}
yearFilter.addEventListener("change", updatePivotsFromFilter);
startMonthSelect.addEventListener("change", updatePivotsFromFilter);
endMonthSelect.addEventListener("change", updatePivotsFromFilter);
JS;
$this->registerJs($js);
?>