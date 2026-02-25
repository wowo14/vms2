task baru, untuk rangking calon penyedia:

A. Latar belakang
    proses pemilihan calon penyedia dengan cara minikompetisi kesulitan membandingkan produk
    dengan beberapa parameter, misal kualitas dan harga 

    proses hitung secara manual membutuhkan waktu lama dan kurang tepat

B. Tujuan
    user menyediakan template untuk di isi oleh penyedia 
    hasil inputan dari penyedia / penawaran penyedia di import ke system
    dan melakukan auto perhitungan , auto skoring, auto rangking 

c. Teknis
    saya ingin anda buatkan fitur / modul baru untuk kegiatan minikompetisi
    yang bisa di seting dengan beberapa metode:
    1. Hanya membandingkan harga penawaran terendah
    2. membandingkan harga penawaran dan kualitas produk ( 60% dari kualitas dan 40% dari harga)
    3. membandingkan secara lumpsum dari total penawaran masing-masing penyedia

    untuk metode, parameter, seting lain diharapkan tidak hardcode, dan bisa fleksibel bisa diubah 
    misal saat paket A: menggunakan metode 2 dengan seting parameter kualitas 55% dan harga 45%

    user flow:
    PPK membuat paket, input detail produk, qty, volume, hps, hargabeli existing
    system auto menggenerate template untuk paket tersebut dan dikirimkan ke calon penyedia
    penyedia mengirim file excel ke system
    system import file excel penyedia dan auto membuat konsolidasi harga, skor, rangking
    system membuat grafik / reporting dari hasil konsolidasi dan membandingkan dengan harga beli existing
    ppk mengambil keputusan menenetapkan calon penyedia 1,2,3 sesuai ranking 