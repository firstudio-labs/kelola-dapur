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
    <h2>BERITA ACARA PENGALIHAN SISA DANA</h2>
    <h3>{{ $setting->institution_name ?? '[Nama Lembaga]' }}</h3>
    <p class="subtitle">{{ $setting->address ?? '[Alamat]' }}</p>
</div>
<table class="kv" style="width:100%;margin-bottom:12px;">
    <tr><td class="k">Nama Lembaga</td><td class="sep">:</td><td>{{ $setting->institution_name ?? '-' }}</td></tr>
    <tr><td class="k">Yayasan / Mitra</td><td class="sep">:</td><td>{{ $setting->foundation_name ?? '-' }}</td></tr>
    <tr><td class="k">Periode</td><td class="sep">:</td><td>{{ $activePeriod->name ?? '-' }} ({{ $activePeriod->year ?? '' }})</td></tr>
    <tr><td class="k">Tanggal</td><td class="sep">:</td><td>{{ $setting->report_date ? $setting->report_date->format('d F Y') : date('d F Y') }}</td></tr>
    <tr><td class="k">Total Penerimaan</td><td class="sep">:</td><td>Rp {{ number_format($transactions->sum('debit'), 0, ',', '.') }}</td></tr>
    <tr><td class="k">Total Pengeluaran</td><td class="sep">:</td><td>Rp {{ number_format($transactions->sum('credit'), 0, ',', '.') }}</td></tr>
    <tr><td class="k">Sisa Dana yang Dialihkan</td><td class="sep">:</td><td><strong>Rp {{ number_format($openingBalance + $transactions->sum('debit') - $transactions->sum('credit'), 0, ',', '.') }}</strong></td></tr>
</table>
<p class="statement">
Pada hari ini, telah dilaksanakan pengalihan sisa dana oleh <strong>{{ $setting->treasurer_name ?? '[Bendahara]' }}</strong> atas nama <strong>{{ $setting->institution_name ?? '[Lembaga]' }}</strong> kepada <strong>{{ $setting->foundation_head ?? '[Ketua Yayasan]' }}</strong> selaku Ketua <strong>{{ $setting->foundation_name ?? '[Yayasan]' }}</strong>, dengan sisa dana sebesar <strong>Rp {{ number_format($openingBalance + $transactions->sum('debit') - $transactions->sum('credit'), 0, ',', '.') }}</strong> untuk periode <strong>{{ $activePeriod->name ?? '-' }}</strong>.
</p>
<div style="margin-top:50px; display:flex; justify-content:space-between;">
    <div style="text-align:center; width:45%">
        <p>Yang menyerahkan,</p>
        <div style="height:50px;"></div>
        <strong>{{ $setting->treasurer_name ?? '...................' }}</strong><br>
        <small>Bendahara</small>
    </div>
    <div style="text-align:center; width:45%">
        <p>Yang menerima,</p>
        <div style="height:50px;"></div>
        <strong>{{ $setting->foundation_head ?? '...................' }}</strong><br>
        <small>Ketua {{ $setting->foundation_name ?? '' }}</small>
    </div>
</div>
</body>
</html>
