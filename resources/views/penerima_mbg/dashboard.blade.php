@extends('template_penerima_mbg.layout')
@section('title', 'Dashboard')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Header Section as a Block --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <nav class="d-flex align-items-center mb-1">
                        <a href="{{ route('penerima-mbg.dashboard') }}" class="text-muted me-2 small">
                            <i class="bx bx-home-alt me-1"></i> Dashboard
                        </a>
                    </nav>
                    <h4 class="mb-0 fw-bold text-dark">Dashboard</h4>
                    <p class="text-muted small mb-0">{{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
                <div class="d-none d-md-block">
                    <div class="d-flex align-items-center bg-label-primary px-3 py-2 rounded">
                        <div class="avatar avatar-xs me-2">
                             <span class="avatar-initial rounded bg-primary"><i class="bx bx-user fs-6"></i></span>
                        </div>
                        <span class="fw-semibold small text-primary">{{ $user->nama }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($penerima)
        <div class="row g-4">
            {{-- Left Column: Transaksi Hari Ini --}}
            <div class="col-12 col-lg-8">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header border-bottom d-flex align-items-center justify-content-between bg-transparent py-3">
                        <h5 class="mb-0 fw-bold"><i class="bx bx-time-five me-2 text-primary"></i>Transaksi Hari Ini</h5>
                        <a href="{{ route('penerima-mbg.history.index') }}" class="btn btn-sm btn-outline-primary px-3">Riwayat</a>
                    </div>
                    <div class="card-body p-4">
                        @forelse($todayDeliveries as $delivery)
                            @php
                                $transaksi = $delivery->orderDistribusi?->orderProduksi?->transaksiDapur;
                                $menus = $transaksi?->detailTransaksiDapur ?? collect();
                                $statusPenerimaan = $delivery->status_penerimaan;
                                $badgeColor = match($statusPenerimaan) {
                                    'diterima' => 'success',
                                    'ditolak'  => 'danger',
                                    default    => 'warning',
                                };
                                $badgeLabel = match($statusPenerimaan) {
                                    'diterima' => 'Dikonfirmasi',
                                    'ditolak'  => 'Ditolak',
                                    default    => 'Menunggu',
                                };
                            @endphp
                            <div class="card mb-3 border shadow-none bg-light bg-opacity-10">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md bg-white shadow-sm rounded-circle me-3 d-flex align-items-center justify-content-center">
                                                <i class="bx bx-dish fs-3 text-info"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-bold text-dark">
                                                    {{ $menus->map(fn($d) => $d->menuMakanan?->nama_menu)->filter()->implode(', ') ?: 'Menu Hari Ini' }}
                                                </h6>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-label-info me-2 py-0 px-2 small">
                                                        <i class="bx bx-bowl-rice me-1"></i> {{ $delivery->porsi_besar + $delivery->porsi_kecil }} Porsi
                                                    </span>
                                                    <small class="text-muted">({{ $delivery->porsi_besar }}B / {{ $delivery->porsi_kecil }}K)</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end d-flex flex-column align-items-end">
                                            <span class="badge bg-label-{{ $badgeColor }} mb-2">{{ $badgeLabel }}</span>
                                            <a href="{{ route('penerima-mbg.history.show', $delivery->id_detail) }}" class="btn btn-sm btn-primary px-3">
                                                Detail <i class="bx bx-chevron-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="bx bx-info-circle fs-1 text-muted opacity-25 mb-3"></i>
                                <h6 class="text-muted fw-normal mb-0">Belum ada transaksi diantarkan hari ini.</h6>
                                <p class="text-muted small mt-1">Gunakan menu Riwayat untuk melihat transaksi sebelumnya.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Right Column: Status & Portion --}}
            <div class="col-12 col-lg-4">
                <div class="row g-4">
                    {{-- Allocation Card --}}
                    <div class="col-12">
                        <div class="card shadow-sm border-0 bg-primary text-white overflow-hidden" style="height: 180px;">
                            <div class="card-body d-flex flex-column justify-content-between p-4 position-relative">
                                <div class="position-absolute top-0 end-0 p-3 opacity-25">
                                    <i class="bx bx-bowl-rice" style="font-size: 80px;"></i>
                                </div>
                                <div class="z-index-1">
                                    <h6 class="text-white opacity-75 mb-1">Alokasi Harian</h6>
                                    <h2 class="text-white mb-0 fw-bold">{{ $penerima->jumlah_porsi }} <small class="fs-6 fw-normal">Porsi</small></h2>
                                </div>
                                @if($penerima->isApproved())
                                    <button class="btn btn-white btn-sm text-primary fw-bold w-100 mt-3" data-bs-toggle="modal" data-bs-target="#modalUpdatePorsi">
                                        Update Porsi
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Account Info Card --}}
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Informasi Akun</h6>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-sm bg-label-secondary rounded me-2 d-flex align-items-center justify-content-center">
                                        <i class="bx bx-building fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block small" style="font-size: 0.7rem;">Dapur SPPG</small>
                                        <span class="fw-semibold text-dark small" style="font-size: 0.85rem;">{{ $penerima->dapur?->nama_dapur ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    @php
                                        $statusColor = match($penerima->status_approval) {
                                            'approved' => 'success',
                                            'rejected'  => 'danger',
                                            default     => 'warning',
                                        };
                                        $statusLabel = match($penerima->status_approval) {
                                            'approved' => 'Terverifikasi',
                                            'rejected'  => 'Ditolak',
                                            default     => 'Pending',
                                        };
                                    @endphp
                                    <div class="avatar avatar-sm bg-label-{{ $statusColor }} rounded me-2 d-flex align-items-center justify-content-center">
                                        <i class="bx bx-check-shield fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block small" style="font-size: 0.7rem;">Status Akun</small>
                                        <span class="badge bg-label-{{ $statusColor }} py-0 px-2 small" style="font-size: 0.75rem;">{{ $statusLabel }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Quick Actions</h6>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('penerima-mbg.profile.edit') }}" class="btn btn-label-secondary btn-sm text-start">
                                        <i class="bx bx-user-circle me-2"></i> Pengaturan Profil
                                    </a>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-label-danger btn-sm text-start w-100">
                                            <i class="bx bx-log-out me-2"></i> Keluar Aplikasi
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning shadow-sm border-0">
            <i class="bx bx-error me-2"></i> Data profil tidak ditemukan. Hubungi administrator.
        </div>
    @endif
</div>

{{-- MODAL UPDATE PORSI --}}
@if($penerima && $penerima->isApproved())
<div class="modal fade" id="modalUpdatePorsi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Update Porsi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('penerima-mbg.porsi.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body text-center">
                    <p class="text-muted small mb-4">Ubah jumlah porsi kebutuhan harian Anda.</p>
                    <div class="input-group input-group-lg justify-content-center border rounded overflow-hidden">
                        <input type="number" name="jumlah_porsi" class="form-control text-center border-0"
                               value="{{ old('jumlah_porsi', $penerima->jumlah_porsi) }}" min="1" max="1000000" required>
                        <span class="input-group-text border-0 bg-light">Porsi</span>
                    </div>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                    <button type="submit" class="btn btn-primary px-5">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<style>
    .btn-white {
        background-color: #fff;
        color: #696cff;
    }
    .btn-white:hover {
        background-color: #f7f7ff;
        color: #5f61e6;
    }
    .z-index-1 { z-index: 1; }
    .bg-label-info { background-color: #e7f7ff !important; color: #03c3ec !important; }
    .btn-label-secondary {
        background-color: #f5f5f9;
        color: #8592a3;
        border: none;
    }
    .btn-label-secondary:hover {
        background-color: #ebebef;
        color: #717e8f;
    }
    .btn-label-danger {
        background-color: #ffeef3;
        color: #ff3e1d;
        border: none;
    }
    .btn-label-danger:hover {
        background-color: #ffdce5;
        color: #e6381a;
    }
</style>
@endsection


