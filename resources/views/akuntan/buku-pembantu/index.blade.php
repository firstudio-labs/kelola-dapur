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
                <span class="text-dark small">Buku Pembantu</span>
            </nav>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fw-bold mb-1">Buku Pembantu</h4>
                    <p class="mb-0 text-muted small">Rincian mutasi per kelompok kategori laporan</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
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
                    <label class="form-label small fw-bold mb-1">Kelompok</label>
                    <select name="group" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Kelompok --</option>
                        @foreach($groups as $key => $label)
                            <option value="{{ $key }}" {{ $selectedGroup == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
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

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 50px;">No</th>
                            <th>Tanggal</th>
                            <th>No. Bukti</th>
                            <th>Uraian</th>
                            <th>Kategori</th>
                            <th>Kelompok</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Kredit</th>
                            <th class="text-end pe-3">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($activePeriod)
                        <tr class="table-secondary fw-semibold">
                            <td class="ps-3" colspan="6">Saldo Awal Periode</td>
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
                            <td>{{ $row->description }}</td>
                            <td>{{ $row->category->name ?? '-' }}</td>
                            <td><span class="badge bg-label-secondary small" style="font-size: 0.7rem;">{{ $row->category->group_label ?? '-' }}</span></td>
                            <td class="text-end text-success">{{ $row->debit > 0 ? 'Rp '.number_format($row->debit, 0, ',', '.') : '-' }}</td>
                            <td class="text-end text-danger">{{ $row->credit > 0 ? 'Rp '.number_format($row->credit, 0, ',', '.') : '-' }}</td>
                            <td class="text-end pe-3 fw-semibold {{ $row->saldo >= 0 ? '' : 'text-danger' }}">Rp {{ number_format($row->saldo, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center py-5 text-muted">
                            @if(!$activePeriod) Pilih periode terlebih dahulu. @else Tidak ada transaksi ditemukan. @endif
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
