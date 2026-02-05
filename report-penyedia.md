## Report penyedia detail

Anda seorang senior developer yang sedang bekerja di sebuah perusahaan IT.
 Anda diberikan tugas untuk membuat laporan detail tentang penyedia yang terdaftar di sistem.
  Laporan ini harus mencakup informasi tentang nama, alamat (kota), dan nomor telepon penyedia, 
  serta daftar produk yang mereka tawarkan,Pekerjaan yang di tawarkan(konstruksi,alat kesehatan,alat medis,jasa lainya), nama paket pengadaan, Bidang yang melakukan pekerjaan,
  nilai dari evaluasi penilaian penyedia.

  tampilan table/grid wajib ada filter,pencarian, sorting untuk semua kolom, bisa di klik untuk melihat secara detail.

kondisi sekarang ada beberapa dpp / paket pengadaan yang sudah terisi di fitur penilaian penyedia.,
tapi masih minim data karena banyak kegiatan yang tidak masuk di sistem.

saya ingin membuat fitur untuk membuat Report penyedia seperti diatas, bersumber dari hasil penilaian
juga dari penilaian yang belom masuk di sistem. agar mudah mungkin dilakukan dengan cara import file excel 
jadi raw data mentah adalah gabungan dari import excel dan yang sudah ada di sistem.

buatkan fitur ini dengan menggunakan yii2, sqlite, dan bootstrap sesuai design patern yang sudah ada di sistem ini,
buatkan unit test nya juga agar saya bisa memastikan fitur ini bekerja dengan benar.


### 

raw rumus penilaian :
evaluasi_suplier_ppk		{ "kriteria": [ { "id": 1, "name": "Dokumentasi dan Administrasi", "description": { "5": "Semua dokumen lengkap, sesuai ketentuan, dan tidak ada kesalahan.", "3": "Dokumen cukup lengkap, tetapi terkadang ada kesalahan kecil. Ada tidak tepat waktu.", "1": "Dokumen sering tidak lengkap atau tidak sesuai ketentuan." } }, { "id": 2, "name": "Mutu Produk / Jasa", "description": { "5": "Produk/jasa selalu sesuai spesifikasi, berkualitas baik, dan layak digunakan.", "3": "Produk/jasa cukup sesuai dengan spesifikasi, namun terkadang ada masalah kecil.", "1": "Produk/jasa sering tidak sesuai spesifikasi, cacat, atau tidak layak digunakan." } }, { "id": 3, "name": "Harga dan Kompetitif", "description": { "5": "Harga kompetitif dan memberikan nilai terbaik bagi anggaran.", "3": "Harga cukup bersaing, tetapi ada opsi lebih baik di pasar.", "1": "Harga tidak sesuai standar pasar dan kurang kompetitif." } }, { "id": 4, "name": "Waktu Ketepatan Pengiriman / Pelaksanaan", "description": { "5": "Selalu tepat waktu sesuai jadwal yang disepakati.", "3": "Kadang terlambat, tetapi masih dalam batas yang dapat diterima.", "1": "Sering terlambat lebih dari batas toleransi tanpa alasan yang jelas." } }, { "id": 5, "name": "Jumlah Kirim / Kesesuaian Progress", "description": { "5": "Dikirim / dikerjakan sesuai permintaan.", "3": "Hanya terjadi 1 (satu) kali perubahan jumlah.", "1": "Terjadi lebih dari 1 (satu) kali perubahan jumlah." } }, { "id": 6, "name": "Pelayanan dan Responsivitas", "description": { "5": "Cepat tanggap, memberikan solusi yang memadai, dan mudah dihubungi.", "3": "Respons cukup baik, tetapi terkadang lambat dalam menanggapi keluhan.", "1": "Sulit dihubungi, lambat menanggapi keluhan, atau tidak memberikan solusi." } }, { "id": 7, "name": "Kepatuhan terhadap Kontrak", "description": { "5": "Selalu mematuhi semua ketentuan kontrak tanpa masalah.", "3": "Kadang terjadi penyimpangan, tetapi tidak signifikan.", "1": "Sering tidak mematuhi kontrak, termasuk kuantitas, harga, dan syarat lainnya." } }, { "id": 8, "name": "Kepatuhan terhadap Sistem Manajemen Mutu", "description": { "5": "Menerapkan sistem manajemen mutu yang terstandarisasi (misalnya ISO 9001) dan menjalankan prosedur dengan baik.", "3": "Memiliki sistem manajemen mutu tetapi belum sepenuhnya diterapkan secara konsisten.", "1": "Tidak memiliki sistem manajemen mutu atau tidak menerapkan standar mutu yang jelas." } } ] }	1	
	2	evaluasi_suplier_pejabat		{ "kriteria": [ { "id": 1, "name": "Administrasi dan Kualifikasi (Surat penawaran, Akta, TDP, SIUP, Pajak, dll)", "description": { "5": "Memenuhi persyaratan administrasi, perijinan dan sesuai dengan pekerjaan yang akan dilaksanakan", "3": "Memenuhi sebagian persyaratan administrasi, perijinan dan sesuai dengan pekerjaan yang akan dilaksanakan", "1": "Tidak memenuhi persyaratan administrasi, perijinan dan sesuai dengan pekerjaan yang akan dilaksanakan" } }, { "id": 2, "name": "Harga", "description": { "5": "Harga dapat dilakukan Negosisasi", "3": "Sesuai harga pasar", "1": "Diatas harga pasar" } }, { "id": 3, "name": "Respon terhadap proses pengadaan", "description": { "5": "Respon cepat", "3": "Respon lambat", "1": "Tidak ada respon" } }, { "id": 4, "name": "Penguasaan terhadap Sistem Pengadaan Elektronik (diisi jika menggunakan pengadaan elektronik)", "description": { "5": "Sudah terbiasa menggunakan sistem pengadaan secara elektronik", "3": "Jarang menggunakan sistem pengadaan secara elektronik", "1": "Tidak pernah menggunakan sistem pengadaan secara elektronik" } }, { "id": 5, "name": "Dokumentasi", "description": { "5": "Dokumen pendukung terkait pengadaan lengkap", "3": "Terdapat 1 (satu) dokumen pendukung terkait pengadaan tidak lengkap dan tidak jelas", "1": "Terdapat lebih dari 1 (satu) dokumen pendukung terkait pengadaan tidak lengkap dan tidak jelas" } } ] }

##
Sistem ini adalah aplikasi Yii2 dengan pola MVC standar.
Sudah ada modul Penilaian Penyedia dengan tabel:
- Model Penyedia
- Model Paket Pengadaan
- Model PenilaianPenedia
- Model DPP

Relasi antar model:
- Penyedia memiliki relasi dengan Paket Pengadaan (one-to-many) kolom pemenang di paket pengadaan
- Penyedia memiliki relasi dengan PenilaianPenedia via dpp via paketpengadaan
- PenilaianPenedia memiliki relasi dengan DPP (one-to-many) dpp_id

raw rumus diatas sudah ada di table setting type=evaluasi_suplier_ppk dan type=evaluasi_suplier_pejabat
