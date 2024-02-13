<?php
echo $model[0]->tahun_anggaran.' ';
echo '<br>';
echo '<b>'.$model[0]->kode_program.' ';
echo $model[0]->nama_program.' ';
echo '</b><br>';
foreach($model as $r){
    echo $r->kode_kegiatan.' '.$r->nama_kegiatan."<br>";
    echo $r->kode_rekening.' '.$r->uraian_anggaran.' '.$r->jumlah_anggaran."<br>";
    foreach($r->details as $i=>$d){
        echo ($i+1).' '.$d->produk->nama_produk.' '.$d->volume.' '.$d->satuan.' '.$d->harga_satuan.' '.$d->subtotal."<br>";
    }
    echo'<br>';
}