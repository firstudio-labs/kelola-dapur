<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
    h2 { text-align: center; margin-bottom: 4px; }
    .sub { text-align: center; color: #555; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f3f4f6; border: 1px solid #ccc; padding: 5px; text-align: left; }
    td { border: 1px solid #ddd; padding: 5px; }
    .num { text-align: right; }
    .total { font-weight: bold; background: #f3f4f6; }
</style>
</head>
<body>
<h2>BUKU KAS UMUM</h2>
<p class="sub">{{ $dapur->nama_dapur ?? '' }} &mdash; Periode: {{ $activePeriod->name ?? '-' }} ({{ $activePeriod->year ?? '' }})</p>
<table>
    <thead>
        <tr>
            <th>Tanggal</th><th>No. Bukti</th><th>Uraian</th>
            <th class="num">Debit (Rp)</th><th class="num">Kredit (Rp)</th><th class="num">Saldo (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <tr><td>{{ $activePeriod?->start_date?->format('d/m/Y') }}</td><td>-</td><td><b>Saldo Awal Periode</b></td><td class="num">-</td><td class="num">-</td><td class="num"><b>{{ number_format($openingBalance, 0, ',', '.') }}</b></td></tr>
        @foreach($rows as $row)
        <tr>
            <td>{{ $row->date->format('d/m/Y') }}</td>
            <td>{{ $row->no_bukti ?? '-' }}</td>
            <td>{{ $row->description }}</td>
            <td class="num">{{ $row->debit > 0 ? number_format($row->debit, 0, ',', '.') : '-' }}</td>
            <td class="num">{{ $row->credit > 0 ? number_format($row->credit, 0, ',', '.') : '-' }}</td>
            <td class="num">{{ number_format($row->saldo, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr class="total">
            <td colspan="3">TOTAL</td>
            <td class="num">{{ number_format($rows->sum('debit'), 0, ',', '.') }}</td>
            <td class="num">{{ number_format($rows->sum('credit'), 0, ',', '.') }}</td>
            <td class="num">{{ number_format($rows->last()?->saldo ?? $openingBalance, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>
</body>
</html>
