@extends('template_akuntan.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

{{-- Welcome Banner --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-label-primary border-0 shadow-none">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1 fw-bold">Selamat Datang, {{ $user->nama }}! 🎉</h4>
                        <p class="mb-0">Akuntan di <span class="fw-bold text-primary">{{ $dapur->nama_dapur }}</span></p>
                        <div class="mt-2">
                            @if($user->isKepalaAkuntan())
                                <span class="badge bg-success text-white fw-bold shadow-sm"><i class="bx bx-shield me-1"></i>Kepala Akuntan</span>
                            @else
                                <span class="badge bg-info text-white fw-bold shadow-sm"><i class="bx bx-user me-1"></i>Anggota</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-none d-md-block text-center ms-3">
                        <img src="{{ auth()->user()->akuntan->foto_diri ? Storage::url(auth()->user()->akuntan->foto_diri) : asset('admin/assets/img/illustrations/man-with-laptop-light.png') }}"
                             height="100" alt="User Profile"
                             class="rounded-circle border border-4 border-white shadow-sm"
                             style="width: 100px; height: 100px; object-fit: cover;" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Period Filter --}}
<div class="row mb-4">
    <div class="col-12 col-md-5">
        <form method="GET" action="{{ route('akuntan.dashboard') }}" class="d-flex gap-2 align-items-center">
            <label class="fw-semibold text-nowrap mb-0">Periode:</label>
            <select name="period_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Semua --</option>
                @foreach($periods as $p)
                    <option value="{{ $p->id }}" {{ $activePeriod && $activePeriod->id == $p->id ? 'selected' : '' }}>
                        {{ $p->name }} ({{ $p->year }}) - {{ $p->status === 'open' ? 'Aktif' : 'Ditutup' }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    @if($activePeriod)
    <div class="col-12 col-md-7 mt-2 mt-md-0 d-flex align-items-center">
        <small class="text-muted">
            <i class="bx bx-calendar me-1"></i>
            {{ $activePeriod->start_date->format('d M Y') }} – {{ $activePeriod->end_date->format('d M Y') }}
            &nbsp;
            @if($activePeriod->status === 'open')
                <span class="badge bg-success">Aktif</span>
            @else
                <span class="badge bg-secondary">Ditutup</span>
            @endif
        </small>
    </div>
    @endif
</div>

{{-- Stats Cards --}}
<div class="row g-4 mb-4">
    <div class="col-6 col-md-4 col-xl">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
                <div class="avatar mx-auto mb-2">
                    <span class="avatar-initial rounded-circle bg-label-info"><i class="bx bx-trending-up fs-4"></i></span>
                </div>
                <h5 class="fw-bold mb-0 text-dark">Rp {{ number_format($stats['opening_balance'], 0, ',', '.') }}</h5>
                <small class="text-muted">Saldo Awal</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
                <div class="avatar mx-auto mb-2">
                    <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-plus-circle fs-4"></i></span>
                </div>
                <h5 class="fw-bold mb-0 text-success">Rp {{ number_format($stats['total_debit'], 0, ',', '.') }}</h5>
                <small class="text-muted">Total Pemasukan</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
                <div class="avatar mx-auto mb-2">
                    <span class="avatar-initial rounded-circle bg-label-danger"><i class="bx bx-minus-circle fs-4"></i></span>
                </div>
                <h5 class="fw-bold mb-0 text-danger">Rp {{ number_format($stats['total_credit'], 0, ',', '.') }}</h5>
                <small class="text-muted">Total Pengeluaran</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
                <div class="avatar mx-auto mb-2">
                    <span class="avatar-initial rounded-circle bg-label-primary"><i class="bx bx-wallet fs-4"></i></span>
                </div>
                <h5 class="fw-bold mb-0 {{ $stats['closing_balance'] >= 0 ? 'text-primary' : 'text-danger' }}">
                    Rp {{ number_format($stats['closing_balance'], 0, ',', '.') }}
                </h5>
                <small class="text-muted">Saldo Akhir</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
                <div class="avatar mx-auto mb-2">
                    <span class="avatar-initial rounded-circle bg-label-warning"><i class="bx bx-list-ul fs-4"></i></span>
                </div>
                <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_transaksi']) }}</h3>
                <small class="text-muted">Jumlah Transaksi</small>
            </div>
        </div>
    </div>
</div>

@if(count($cashAccountStats) > 0)
{{-- Cash Account Breakdown --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <h6 class="text-muted fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Rincian Saldo Akun</h6>
    </div>
    @foreach($cashAccountStats as $cas)
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm overflow-hidden">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar flex-shrink-0 me-2">
                        <span class="avatar-initial rounded bg-label-{{ $cas['type'] === 'tunai' ? 'success' : 'info' }}">
                            <i class="bx {{ $cas['type'] === 'tunai' ? 'bx-money' : 'bx-credit-card' }}"></i>
                        </span>
                    </div>
                    <div>
                        <h6 class="mb-0 text-dark fw-semibold small">{{ $cas['name'] }}</h6>
                        <small class="text-muted" style="font-size: 0.7rem;">{{ ucfirst($cas['type']) }}</small>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-1 mt-1">
                    <h5 class="fw-bold mb-0 text-dark">Rp {{ number_format($cas['balance'], 0, ',', '.') }}</h5>
                </div>
                {{-- Dynamic Progress Bar --}}
                @php 
                    $percent = $stats['closing_balance'] > 0 ? ($cas['balance'] / $stats['closing_balance']) * 100 : 0;
                    $percent = max(0, min(100, $percent));
                @endphp
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-{{ $cas['type'] === 'tunai' ? 'success' : 'info' }}" 
                         role="progressbar" style="width: {{ $percent }}%" 
                         aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <small class="text-muted" style="font-size: 0.65rem;">Kontribusi terhadap total</small>
                    <small class="fw-semibold text-{{ $cas['type'] === 'tunai' ? 'success' : 'info' }}" style="font-size: 0.65rem;">{{ number_format($percent, 1) }}%</small>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Quick Actions --}}
<div class="row g-3">
    <div class="col-12">
        <h6 class="text-muted fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Akses Cepat</h6>
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="{{ route('akuntan.transaksi.create') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm border-start border-4 border-success hover-elevate">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <span class="avatar-initial rounded bg-label-success shadow-sm me-3 p-2"><i class="bx bx-plus-circle fs-4"></i></span>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Transaksi Baru</h6>
                            <small class="text-muted">Tambah transaksi</small>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="{{ route('akuntan.buku-kas.index') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm border-start border-4 border-primary hover-elevate">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <span class="avatar-initial rounded bg-label-primary shadow-sm me-3 p-2"><i class="bx bx-book-open fs-4"></i></span>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Buku Kas</h6>
                            <small class="text-muted">Lihat kas umum</small>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="{{ route('akuntan.laporan.resume') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm border-start border-4 border-info hover-elevate">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <span class="avatar-initial rounded bg-label-info shadow-sm me-3 p-2"><i class="bx bx-bar-chart-alt-2 fs-4"></i></span>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Laporan</h6>
                            <small class="text-muted">Resume keuangan</small>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="{{ route('akuntan.pengaturan.index') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm border-start border-4 border-warning hover-elevate">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <span class="avatar-initial rounded bg-label-warning shadow-sm me-3 p-2"><i class="bx bx-cog fs-4"></i></span>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Pengaturan</h6>
                            <small class="text-muted">Kelola periode & kategori</small>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

</div>

<style>
.hover-elevate { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.hover-elevate:hover { transform: translateY(-4px); box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important; }
</style>
@endsection
