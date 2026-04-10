@extends('template_akuntan.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <nav class="d-flex align-items-center mb-2">
                <a href="{{ route('akuntan.dashboard') }}" class="text-muted me-2 small">
                    <i class="bx bx-home-alt me-1"></i>Dashboard
                </a>
                <i class="bx bx-chevron-right me-2 text-muted small"></i>
                <span class="text-dark small">Buku Kas Umum</span>
            </nav>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fw-bold mb-1">Buku Kas Umum</h4>
                    <p class="mb-0 text-muted small">Laporan kronologis seluruh arus kas masuk dan keluar</p>
                </div>
                @if($activePeriod)
                <a href="{{ route('akuntan.buku-kas.export-pdf', ['period_id' => $activePeriod->id, 'cash_account_id' => $selectedAccountId]) }}" class="btn btn-danger" target="_blank">
                    <i class="bx bx-file-pdf me-1"></i> Export PDF
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-12 col-md-auto">
                    <label class="form-label small fw-bold mb-1">Tahun</label>
                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-auto">
                    <label class="form-label small fw-bold mb-1">Periode</label>
                    <select name="period_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Pilih Periode --</option>
                        @foreach($periods as $p)
                            <option value="{{ $p->id }}" {{ $activePeriod && $activePeriod->id == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-auto">
                    <label class="form-label small fw-bold mb-1">Akun Kas</label>
                    <select name="cash_account_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Akun</option>
                        @foreach($cashAccounts as $ca)
                            <option value="{{ $ca->id }}" {{ $selectedAccountId == $ca->id ? 'selected' : '' }}>
                                {{ $ca->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if($activePeriod)
                <div class="col-12 col-md-auto align-self-end pb-1">
                    <small class="text-muted">
                        <i class="bx bx-calendar-event me-1"></i>{{ $activePeriod->start_date->format('d M Y') }} – {{ $activePeriod->end_date->format('d M Y') }}
                    </small>
                </div>
                @endif
                <div class="col-12 col-md-auto ms-md-auto">
                    <label class="form-label small fw-bold mb-1">Cari</label>
                    <div class="input-group input-group-merge input-group-sm">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Uraian / No. Bukti..." value="{{ request('search') }}">
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Buku Kas Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 50px;">No</th>
                            <th>Tanggal</th>
                            <th>No. Bukti</th>
                            <th>Akun</th>
                            <th>Uraian</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Kredit</th>
                            <th class="text-end pe-3">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Opening balance row --}}
                        @if($activePeriod)
                        <tr class="table-secondary fw-semibold">
                            <td class="ps-3">-</td>
                            <td>{{ $activePeriod->start_date->format('d/m/Y') }}</td>
                            <td>-</td>
                            <td>-</td>
                            <td>Saldo Awal Periode</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end pe-3 fw-bold">Rp {{ number_format($openingBalance, 0, ',', '.') }}</td>
                        </tr>
                        @endif

                        @forelse($rows as $index => $row)
                        <tr>
                            <td class="ps-3 text-muted">{{ $index + 1 }}</td>
                            <td>{{ $row->date->format('d/m/Y') }}</td>
                            <td><small class="text-muted">{{ $row->no_bukti ?? '-' }}</small></td>
                            <td class="small">{{ $row->cashAccount->name ?? '-' }}</td>
                            <td>{{ $row->description }}</td>
                            <td class="text-end text-success">
                                {{ $row->debit > 0 ? 'Rp '.number_format($row->debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end text-danger">
                                {{ $row->credit > 0 ? 'Rp '.number_format($row->credit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end pe-3 fw-semibold {{ $row->saldo >= 0 ? 'text-dark' : 'text-danger' }}">
                                Rp {{ number_format($row->saldo, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-5 text-muted">
                            @if(!$activePeriod) Pilih periode terlebih dahulu. @else Belum ada transaksi pada periode ini. @endif
                        </td></tr>
                        @endforelse

                        {{-- Totals footer --}}
                        @if($rows->count() > 0)
                        <tr class="table-light fw-bold border-top border-2">
                            <td class="ps-3" colspan="5">Total Transaksi Saja</td>
                            <td class="text-end text-success">Rp {{ number_format($rows->sum('debit'), 0, ',', '.') }}</td>
                            <td class="text-end text-danger">Rp {{ number_format($rows->sum('credit'), 0, ',', '.') }}</td>
                            <td class="text-end pe-3">Rp {{ number_format($rows->last()->saldo, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
