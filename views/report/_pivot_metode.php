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
const monthLabels = {
  1: "1.Januari", 2: "2.Februari", 3: "3.Maret", 4: "4.April",
  5: "5.Mei", 6: "6.Juni", 7: "7.Juli", 8: "8.Agustus",
  9: "9.September", 10: "10.Oktober", 11: "11.November", 12: "12.Desember"
};
let currentYear = "all";
function filterDataByYear(data, year) {
  return year === "all" ? data : data.filter(row => String(row.year) === year);
}
const originalData = $modeldata.map(row => ({
  ...row,
  monthLabel: monthLabels[row.month] || row.month
}));
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
    container: "#pivot-unitbidangcount",
    title: "Jumlah Kegiatan Pengadaan Per Unit/Bidang/Bagian",
    rows: [{ uniqueName: "bidang_bagian", caption: "Unit/Bidang/Bagian" }],
    columns: [{ uniqueName: "month", caption: "Month", showTotals: false }],
    measures: [{ uniqueName: "pagu", aggregation: "count", caption: "Total" }]
  },
  {
    container: "#pivot-unitbidangsum",
    title: "Total Kontrak Pengadaan Per Unit/Bidang/Bagian",
    rows: [{ uniqueName: "bidang_bagian", caption: "Unit/Bidang/Bagian" }],
    columns: [
      { uniqueName: "month", caption: "Month", showTotals: false },
      { uniqueName: "metode_pengadaan" }
    ],
    measures: [{ uniqueName: "hasilnego", aggregation: "sum", caption: "Total" }]
  },
  {
    container: "#pivot-metodecount",
    title: "Jumlah Kegiatan Pengadaan Per Metode Pengadaan",
    rows: [{ uniqueName: "metode_pengadaan", caption: "Metode Pengadaan" }],
    columns: [{ uniqueName: "month", showTotals: false }],
    measures: [{ uniqueName: "pagu", aggregation: "count", caption: "Total" }]
  },
  {
    container: "#pivot-metodesum",
    title: "Total Kontrak Pengadaan Per Metode Pengadaan",
    rows: [{ uniqueName: "metode_pengadaan", caption: "Metode Pengadaan" }],
    columns: [{ uniqueName: "month", showTotals: false }],
    measures: [{ uniqueName: "hasilnego", aggregation: "sum", caption: "Total" }]
  },
  {
    container: "#pivot-pejabatkategoricount",
    title: "Jumlah Kegiatan Pengadaan Per Unit/Bidang/Bagian",
    rows: [{ uniqueName: "pejabat_pengadaan", caption: "Pejabat Pengadaan" }],
    columns: [
      { uniqueName: "month", showTotals: false },
      { uniqueName: "kategori_pengadaan" }
    ],
    measures: [{ uniqueName: "pagu", aggregation: "count", caption: "Total Job" }]
  },
  {
    container: "#pivot-pejabatkategorisum",
    title: "Total Kontrak Pengadaan Per Unit/Bidang/Bagian",
    rows: [{ uniqueName: "pejabat_pengadaan", caption: "Pejabat Pengadaan" }],
    columns: [
      { uniqueName: "month", showTotals: false },
      { uniqueName: "kategori_pengadaan" }
    ],
    measures: [{ uniqueName: "hasilnego", aggregation: "sum", caption: "Total" }]
  },
  {
    container: "#pivot-pejabatmetodecount",
    title: "Jumlah Kegiatan Pengadaan Per Metode Pengadaan",
    rows: [{ uniqueName: "pejabat_pengadaan", caption: "Pejabat Pengadaan" }],
    columns: [
      { uniqueName: "month", showTotals: false },
      { uniqueName: "metode_pengadaan" }
    ],
    measures: [{ uniqueName: "pagu", aggregation: "count", caption: "Total" }]
  },
  {
    container: "#pivot-pejabatmetodesum",
    title: "Total Kontrak Pengadaan Per Metode Pengadaan",
    rows: [{ uniqueName: "pejabat_pengadaan", caption: "Pejabat Pengadaan" }],
    columns: [
      { uniqueName: "month", showTotals: false },
      { uniqueName: "metode_pengadaan" }
    ],
    measures: [{ uniqueName: "hasilnego", aggregation: "sum", caption: "Total" }]
  }
];
function renderAllPivots(year = "all") {
  const filteredData = filterDataByYear(originalData, year);
  pivotConfigs.forEach(config => initPivot(config, filteredData));
}
renderAllPivots();
// Populate tahun ke dropdown
const yearSet = new Set(originalData.map(row => row.year));
console.log(originalData);
console.log(yearSet);
const yearFilter = document.getElementById("yearFilter");
Array.from(yearSet).sort().forEach(year => {
  const option = document.createElement("option");
  option.value = year;
  option.textContent = year;
  yearFilter.appendChild(option);
});
// Event: Saat user ganti tahun
yearFilter.addEventListener("change", function () {
  currentYear = this.value;
  document.querySelectorAll(".webdatarocks").forEach(el => el.innerHTML = ""); // clear containers
  renderAllPivots(currentYear);
});
function filterDataByPeriod(data, year, startMonth, endMonth) {
  return data.filter(row => {
    const matchYear = year === "all" || String(row.tahun) === year;
    const matchMonth = startMonth && endMonth
      ? (startMonth <= endMonth
        ? row.month >= startMonth && row.month <= endMonth
        : row.month >= startMonth || row.month <= endMonth)
      : true;
    return matchYear && matchMonth;
  });
}
function renderAllPivots(year = "all", startMonth = null, endMonth = null) {
  const filteredData = filterDataByPeriod(originalData, year, startMonth, endMonth);
  document.querySelectorAll(".webdatarocks").forEach(el => el.innerHTML = ""); // clear
  pivotConfigs.forEach(config => initPivot(config, filteredData));
}
// Populate dropdown bulan
const startMonthSelect = document.getElementById("startMonth");
const endMonthSelect = document.getElementById("endMonth");
for (let i = 1; i <= 12; i++) {
  const opt1 = new Option(monthLabels[i], i);
  const opt2 = new Option(monthLabels[i], i);
  startMonthSelect.appendChild(opt1);
  endMonthSelect.appendChild(opt2);
}
// Event handler
document.getElementById("yearFilter").addEventListener("change", function () {
  const year = this.value;
  const startMonth = parseInt(startMonthSelect.value) || null;
  const endMonth = parseInt(endMonthSelect.value) || null;
  renderAllPivots(year, startMonth, endMonth);
});
[startMonthSelect, endMonthSelect].forEach(select => {
  select.addEventListener("change", () => {
    const year = document.getElementById("yearFilter").value;
    const startMonth = parseInt(startMonthSelect.value) || null;
    const endMonth = parseInt(endMonthSelect.value) || null;
    renderAllPivots(year, startMonth, endMonth);
  });
});
JS;
$this->registerJs($js);
