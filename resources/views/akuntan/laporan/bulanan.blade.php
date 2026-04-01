@extends('template_akuntan.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Catatan Pengeluaran Bulanan</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('akuntan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Laporan</li>
            <li class="breadcrumb-item active">Pengeluaran Bulanan</li>
        </ol></nav>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" class="d-flex gap-3 align-items-center flex-wrap">
                <label class="fw-semibold mb-0">Periode:</label>
                <select name="period_id" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                    <option value="">-- Pilih Periode --</option>
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ $activePeriod && $activePeriod->id == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->year }})</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Bulan</th>
                            <th class="text-center">Jumlah Transaksi</th>
                            <th class="text-end pe-3">Total Pengeluaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                        <tr>
                            <td class="ps-3 fw-semibold">{{ $row->month_name }}</td>
                            <td class="text-center">{{ number_format($row->jumlah_transaksi) }}</td>
                            <td class="text-end pe-3 text-danger fw-bold">Rp {{ number_format($row->total_pengeluaran, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-5 text-muted">@if(!$activePeriod) Pilih periode terlebih dahulu. @else Belum ada data pengeluaran. @endif</td></tr>
                        @endforelse
                        @if($rows->count() > 0)
                        <tr class="table-light fw-bold border-top border-2">
                            <td class="ps-3">TOTAL</td>
                            <td class="text-center">{{ number_format($rows->sum('jumlah_transaksi')) }}</td>
                            <td class="text-end pe-3 text-danger">Rp {{ number_format($rows->sum('total_pengeluaran'), 0, ',', '.') }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
