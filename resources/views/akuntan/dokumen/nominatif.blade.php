<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8">
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 9px; margin: 20px; }
.header { text-align: center; margin-bottom: 16px; }
h2 { margin: 0; font-size: 13px; text-transform: uppercase; }
h3 { margin: 4px 0; font-size: 10px; }
.subtitle { font-size: 9px; color: #555; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
th { background: #f3f4f6; border: 1px solid #aaa; padding: 4px 6px; text-align: left; font-size: 9px; }
td { border: 1px solid #ccc; padding: 4px 6px; font-size: 9px; }
.num { text-align: right; }
.total-row td { font-weight: bold; background: #f3f4f6; }
</style>
</head>
<body>
<div class="header">
    <h2>DAFTAR NOMINATIF</h2>
    <h3>{{ $setting->institution_name ?? '[Nama Lembaga]' }}</h3>
    <p class="subtitle">Periode: {{ $activePeriod->name ?? '-' }} ({{ $activePeriod->year ?? '' }}) | {{ $activePeriod->start_date?->format('d M Y') ?? '' }} – {{ $activePeriod->end_date?->format('d M Y') ?? '' }}</p>
</div>
<table>
    <thead>
        <tr>
            <th style="width:3%">No</th>
            <th style="width:9%">Tanggal</th>
            <th style="width:10%">No. Bukti</th>
            <th>Uraian</th>
            <th style="width:12%">Kategori</th>
            <th style="width:12%" class="num">Debit (Rp)</th>
            <th style="width:12%" class="num">Kredit (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="5"><strong>Saldo Awal Periode</strong></td>
            <td class="num">-</td>
            <td class="num">-</td>
        </tr>
        @foreach($transactions as $i => $t)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $t->date->format('d/m/Y') }}</td>
            <td>{{ $t->no_bukti ?? '-' }}</td>
            <td>{{ $t->description }}</td>
            <td>{{ $t->category->name ?? '-' }}</td>
            <td class="num">{{ $t->debit > 0 ? number_format($t->debit, 0, ',', '.') : '-' }}</td>
            <td class="num">{{ $t->credit > 0 ? number_format($t->credit, 0, ',', '.') : '-' }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="5">TOTAL</td>
            <td class="num">{{ number_format($transactions->sum('debit'), 0, ',', '.') }}</td>
            <td class="num">{{ number_format($transactions->sum('credit'), 0, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <td colspan="5">SALDO AKHIR (Saldo Awal + Debit - Kredit)</td>
            <td colspan="2" class="num">Rp {{ number_format($openingBalance + $transactions->sum('debit') - $transactions->sum('credit'), 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>
<div style="margin-top:30px; display:flex; justify-content:space-between;">
    <div style="text-align:center; width:45%">
        <p>{{ $setting->report_location ?? '...' }}, {{ $setting->report_date ? $setting->report_date->format('d F Y') : date('d F Y') }}</p>
        <p style="font-weight:bold;">Kepala Lembaga,</p>
        <div style="height:40px;"></div>
        <strong>{{ $setting->head_name ?? '...................' }}</strong>
    </div>
    <div style="text-align:center; width:45%">
        <p>&nbsp;</p>
        <p style="font-weight:bold;">Bendahara,</p>
        <div style="height:40px;"></div>
        <strong>{{ $setting->treasurer_name ?? '...................' }}</strong>
    </div>
</div>
</body>
</html>
