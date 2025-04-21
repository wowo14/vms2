<?php
$rows = collect($rawData)->map(function ($item, $index) {
    return [
        'No'         => $index + 1,
        'Nama Paket'         => $item['nama_paket'],
        'Metode Pengadaan'   => $item['metode_pengadaan'],
        'Kategori'           => $item['kategori_pengadaan'],
        'Pagu'               => number_format($item['pagu'], 0, ',', '.'),
        'Hasil Nego'         => number_format($item['hasilnego'], 0, ',', '.'),
        'HPS'                => number_format($item['hps'], 0, ',', '.'),
        // 'Pemenang'           => $item['pemenang'],
        'Admin Pengadaan'    => $item['admin_pengadaan'],
        'Pejabat Pengadaan'  => $item['pejabat_pengadaan'],
        'Pejabat PPKOM'      => $item['pejabat_ppkom'],
        'Bidang'             => $item['bidang_bagian'],
        'Tahun'              => $item['year'],
        'Bulan'              => $item['month'],
    ];
});

?>
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <?php foreach (array_keys($rows[0]) as $col): ?>
                <th><?= htmlspecialchars($col) ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <?php foreach ($row as $cell): ?>
                    <td><?= htmlspecialchars($cell) ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>