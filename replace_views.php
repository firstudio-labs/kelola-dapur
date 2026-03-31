<?php

$files = [
    __DIR__ . '/resources/views/mitra/laporan-transaksi/index.blade.php',
    __DIR__ . '/resources/views/mitra/laporan-transaksi/show.blade.php',
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // String replacements
    $content = str_replace([
        'template_kepala_dapur.layout',
        'kepala-dapur.approval-transaksi',
        'kepala-dapur.dashboard',
        'Persetujuan Transaksi',
        'Menunggu Persetujuan',
        'Kelola persetujuan transaksi paket menu untuk dapur Anda',
        'Pilih Semua Pending',
        'Aksi Massal',
    ], [
        'template_mitra.layout',
        'mitra.laporan-transaksi',
        'mitra.dashboard',
        'Laporan Transaksi',
        'Menunggu',
        'Lihat laporan transaksi untuk dapur Anda',
        '',
        '',
    ], $content);
    
    file_put_contents($file, $content);
    echo "Updated $file\n";
}
