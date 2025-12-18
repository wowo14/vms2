# Report Statistik Penyedia

## Deskripsi
Report ini menampilkan statistik penyedia berdasarkan kontrak yang pernah dilakukan, dengan nilai evaluasi dari tabel `penilaian_penyedia`.

## Fitur

### 1. GridView
Menampilkan data penyedia dengan kolom:
- **No**: Nomor urut
- **Nama Penyedia**: Nama perusahaan penyedia
- **Alamat**: Alamat perusahaan
- **Unit/Bidang Pemesan**: Unit kerja yang memesan
- **Metode Pengadaan**: Metode pemilihan penyedia
- **Jumlah Kontrak**: Total kontrak yang pernah dilakukan
- **Total Nilai Kontrak**: Akumulasi nilai semua kontrak
- **Nilai Evaluasi**: Rata-rata nilai evaluasi (dari tabel penilaian_penyedia)
- **PPK**: Nama Pejabat Pembuat Komitmen

### 2. Grafik Statistik
Report dilengkapi dengan 3 grafik:

#### a. Top 10 Penyedia Berdasarkan Jumlah Kontrak
- Tipe: Column Chart
- Menampilkan 10 penyedia dengan kontrak terbanyak

#### b. Top 10 Penyedia Berdasarkan Total Nilai Kontrak
- Tipe: Bar Chart
- Menampilkan 10 penyedia dengan total nilai kontrak terbesar

#### c. Distribusi Nilai Evaluasi Penyedia
- Tipe: Pie Chart
- Kategori:
  - Sangat Baik (85-100)
  - Baik (70-84)
  - Cukup (55-69)
  - Kurang (<55)

### 3. Export
- **HTML**: Tampilan web dengan GridView dan grafik interaktif
- **PDF**: Dokumen PDF landscape dengan tabel dan ringkasan statistik

## Filter
Report dapat difilter berdasarkan:
- Tahun Anggaran
- Bulan
- Kategori Pengadaan
- Metode Pengadaan
- Pejabat Pengadaan
- PPK (Pejabat Pembuat Komitmen)
- Admin Pengadaan
- Bidang/Bagian

## Akses
URL: `/report/statistik-penyedia`

## Sumber Data
- **Tabel Utama**: `penilaian_penyedia`
- **Relasi**: 
  - `penilaian_penyedia` -> `dpp` -> `paket_pengadaan`
  - `paket_pengadaan` -> `pegawai` (untuk PPK)

## Perhitungan
- **Jumlah Kontrak**: COUNT(DISTINCT penilaian_penyedia.id) per penyedia
- **Total Nilai Kontrak**: SUM(nilai_kontrak) per penyedia
- **Rata-rata Nilai Evaluasi**: AVG(nilaiakhir dari JSON details) per penyedia

## File Terkait
- Controller: `controllers/ReportController.php` -> `actionStatistikPenyedia()`
- View HTML: `views/report/_statistik_penyedia.php`
- View PDF: `views/report/_pdf_statistik_penyedia.php`
- Form Filter: `views/report/index.php` (reuse existing form)

## Dependencies
- kartik\grid\GridView
- **Highcharts JS** (loaded via CDN: https://code.highcharts.com/highcharts.js)
- kartik\mpdf\Pdf
- Laravel Collections (untuk data processing)

## Catatan Teknis
1. Query menggunakan GROUP BY untuk agregasi data per penyedia
2. PPK diambil dari relasi paket_pengadaan menggunakan GROUP_CONCAT
3. Nilai evaluasi diambil dari JSON field 'details' dengan json_extract
4. **Grafik menggunakan Highcharts JS** (seperti di dashboard) untuk performa lebih baik
5. Data grafik di-encode ke JSON dan di-render via `registerJs()`
6. PDF menggunakan orientasi landscape untuk menampung semua kolom

## Implementasi Highcharts
Menggunakan pendekatan yang sama dengan dashboard:
```php
// Register Highcharts JS
$this->registerJsFile('https://code.highcharts.com/highcharts.js', ['depends' => ['app\assets\AppAsset']]);

// Prepare and encode data
$categoriesJson = json_encode($categories, JSON_NUMERIC_CHECK);
$dataJson = json_encode($data, JSON_NUMERIC_CHECK);

// Register JS
$js = <<<JS
Highcharts.chart('chartId', {
    // chart configuration
});
JS;
$this->registerJs($js);
```
