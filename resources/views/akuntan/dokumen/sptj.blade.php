<?php
// SPTJ, BAPSD, Nominatif all use the same view but with different layout
// This file intentionally renders the raw document HTML for PDF export
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8">
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 10px; margin: 30px; }
.header { text-align: center; margin-bottom: 20px; }
h2 { margin: 0; font-size: 14px; text-transform: uppercase; }
h3 { margin: 4px 0; font-size: 11px; }
.subtitle { font-size: 10px; color: #555; }
p.statement { margin-top: 16px; text-align: justify; line-height: 1.6; }
table.kv td { padding: 2px 4px; vertical-align: top; }
table.kv .k { width: 35%; font-weight: bold; }
table.kv .sep { width: 5%; }
</style>
</head>
<body>
<div class="header">
    <h2>SURAT PERNYATAAN TANGGUNG JAWAB</h2>
    <h3>{{ $setting->institution_name ?? '[Nama Lembaga]' }}</h3>
    <p class="subtitle">{{ $setting->address ?? '[Alamat]' }}</p>
</div>
<table class="kv" style="width:100%;margin-bottom:12px;">
    <tr><td class="k">Nama Lembaga</td><td class="sep">:</td><td>{{ $setting->institution_name ?? '-' }}</td></tr>
    <tr><td class="k">Periode</td><td class="sep">:</td><td>{{ $activePeriod->name ?? '-' }} ({{ $activePeriod->year ?? '' }})</td></tr>
    <tr><td class="k">Nama Kepala</td><td class="sep">:</td><td>{{ $setting->head_name ?? '-' }}</td></tr>
    <tr><td class="k">Nama Bendahara</td><td class="sep">:</td><td>{{ $setting->treasurer_name ?? '-' }}</td></tr>
</table>
<p class="statement">
Yang bertanda tangan di bawah ini, atas nama <strong>{{ $setting->institution_name ?? '[Nama Lembaga]' }}</strong>, menyatakan dengan sesungguhnya bahwa kami bertanggung jawab penuh atas kebenaran dan keabsahan seluruh bukti transaksi pada periode <strong>{{ $activePeriod->name ?? '-' }}</strong>, dengan rincian: total penerimaan sebesar <strong>Rp {{ number_format($transactions->sum('debit'), 0, ',', '.') }}</strong>, total pengeluaran sebesar <strong>Rp {{ number_format($transactions->sum('credit'), 0, ',', '.') }}</strong>, dan saldo akhir sebesar <strong>Rp {{ number_format($openingBalance + $transactions->sum('debit') - $transactions->sum('credit'), 0, ',', '.') }}</strong>. Apabila di kemudian hari terdapat kerugian negara, kami siap bertanggung jawab penuh sesuai ketentuan yang berlaku.
</p>
<div style="margin-top:50px; display:flex; justify-content:space-between;">
    <div style="text-align:center; width:45%">
        <p>{{ $setting->report_location ?? '...' }}, {{ $setting->report_date ? $setting->report_date->format('d F Y') : date('d F Y') }}</p>
        <p style="font-weight:bold;">Kepala Lembaga,</p>
        <div style="height:50px;"></div>
        <strong>{{ $setting->head_name ?? '...................' }}</strong>
    </div>
    <div style="text-align:center; width:45%">
        <p>&nbsp;</p>
        <p style="font-weight:bold;">Bendahara,</p>
        <div style="height:50px;"></div>
        <strong>{{ $setting->treasurer_name ?? '...................' }}</strong>
    </div>
</div>
</body>
</html>
