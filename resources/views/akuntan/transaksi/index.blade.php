@extends('template_akuntan.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Header --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <nav class="d-flex align-items-center mb-2">
                <a href="{{ route('akuntan.dashboard') }}" class="text-muted me-2 small">
                    <i class="bx bx-home-alt me-1"></i>Dashboard
                </a>
                <i class="bx bx-chevron-right me-2 text-muted small"></i>
                <span class="text-dark small">Transaksi</span>
            </nav>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fw-bold mb-1">Daftar Transaksi</h4>
                    <p class="mb-0 text-muted small">Kelola dan pantau seluruh riwayat transaksi keuangan</p>
                </div>
                <a href="{{ route('akuntan.transaksi.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah Transaksi
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bx bx-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter --}}
    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('akuntan.transaksi.index') }}" class="row g-3 align-items-center">
                <div class="col-12 col-md-auto">
                    <label class="form-label small fw-bold mb-1">Tahun</label>
                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach($years as $yr)
                            <option value="{{ $yr }}" {{ (request('year', $selectedYear) == $yr) ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-auto">
                    <label class="form-label small fw-bold mb-1">Periode</label>
                    <select name="period_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Periode --</option>
                        @foreach($periods as $p)
                            <option value="{{ $p->id }}" {{ ($activePeriod && $activePeriod->id == $p->id) ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-auto">
                    <label class="form-label small fw-bold mb-1">Kategori</label>
                    <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua --</option>
                        @foreach($categories->groupBy('group_label') as $groupName => $cats)
                            <optgroup label="{{ $groupName }}">
                                @foreach($cats as $c)
                                    <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }} ({{ $c->type === 'income' ? 'Penerimaan' : 'Pengeluaran' }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-auto">
                    <label class="form-label small fw-bold mb-1">Dari</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-6 col-md-auto">
                    <label class="form-label small fw-bold mb-1">Sampai</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-12 col-md-auto ms-md-auto">
                    <label class="form-label small fw-bold mb-1">Cari</label>
                    <div class="input-group input-group-merge input-group-sm">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Uraian / No. Bukti..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-auto align-self-end">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-filter-alt"></i></button>
                        <a href="{{ route('akuntan.transaksi.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bx bx-reset"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($activePeriod)
    <div class="mb-3">
        <span class="badge {{ $activePeriod->status === 'open' ? 'bg-success' : 'bg-secondary' }} me-2">
            {{ $activePeriod->status === 'open' ? 'Periode Aktif' : 'Periode Ditutup' }}
        </span>
        <small class="text-muted">{{ $activePeriod->name }} | {{ $activePeriod->start_date->format('d M Y') }} – {{ $activePeriod->end_date->format('d M Y') }}</small>
    </div>
    @endif

    {{-- Table --}}
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
                            <th>Jenis BP</th>
                            <th>Alur Dana</th>
                            <th class="text-end">Debet</th>
                            <th class="text-end">Kredit</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $index => $t)
                        <tr>
                            <td class="ps-3 text-muted small">{{ $transactions->firstItem() + $index }}</td>
                            <td>{{ $t->date->format('d/m/Y') }}</td>
                            <td><small class="text-muted">{{ $t->no_bukti ?? '-' }}</small></td>
                            <td>{{ $t->description }}</td>
                            <td>
                                <span class="badge bg-label-info">
                                    {{ $t->category->group_label ?? '-' }}
                                </span>
                                <div class="small text-muted">{{ $t->category->name }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold small">{{ $t->cashAccount->name ?? 'Internal' }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $t->cashAccount ? $t->cashAccount->type_label : '-' }}</div>
                            </td>
                            <td class="text-end text-success fw-semibold">
                                {{ $t->debit > 0 ? 'Rp '.number_format($t->debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end text-danger fw-semibold">
                                {{ $t->credit > 0 ? 'Rp '.number_format($t->credit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-center">
                                @if($t->period && $t->period->isOpen())
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('akuntan.transaksi.edit', $t->id) }}" class="btn btn-icon btn-sm btn-outline-primary">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    <form action="{{ route('akuntan.transaksi.destroy', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus transaksi ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-icon btn-sm btn-outline-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </div>
                                @else
                                <span class="text-muted small">Ditutup</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center py-5 text-muted">Belum ada transaksi pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transactions->hasPages())
        <div class="card-footer bg-transparent">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
