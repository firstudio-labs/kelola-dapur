@extends('template_produksi.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
<div class="row">
    <div class="col-12 mb-4">
        <div class="card bg-label-primary border-0 shadow-none">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1 fw-bold">Selamat Datang, {{ $user->nama }}! 🎉</h4>
                        <p class="mb-0">
                            Produksi di <span class="fw-bold text-primary">{{ $dapur->nama_dapur }}</span>
                        </p>
                         <div class="mt-2 text-dark small">
                              @if($user->isKepalaProduksi())
                                 <span class="badge bg-success text-white fw-bold shadow-sm"><i class="bx bx-shield me-1 text-white"></i>Kepala Produksi</span>
                             @else
                                 <span class="badge bg-info text-white fw-bold shadow-sm"><i class="bx bx-user me-1 text-white"></i>Anggota</span>
                             @endif
                         </div>
                    </div>
                    <div class="d-none d-md-block text-center ms-3">
                        <img src="{{ auth()->user()->produksi->foto_diri ? Storage::url(auth()->user()->produksi->foto_diri) : asset('admin/assets/img/illustrations/man-with-laptop-light.png') }}" 
                             height="100" 
                             alt="User Profile" 
                             class="rounded-circle border border-4 border-white shadow-sm" 
                             style="width: 100px; height: 100px; object-fit: cover;" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12">
        <h6 class="text-muted fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Menu Utama</h6>
    </div>

    <!-- Main Action Card -->
    <div class="col-12 col-md-6 mb-3">
        <a href="{{ route('produksi.order.index') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm border-start border-4 border-primary hover-elevate">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3">
                            <span class="avatar-initial rounded bg-label-primary shadow-sm">
                                <i class="bx bx-receipt fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Order Produksi</h5>
                            <p class="mb-0 text-muted small">Kelola produksi & stok harian</p>
                        </div>
                    </div>
                    <div class="mt-3 text-primary fw-semibold small">
                        Lihat Daftar Order <i class="bx bx-right-arrow-alt align-middle ms-1"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-12 col-md-6 mb-3">
        <a href="{{ route('produksi.profile.edit') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm border-start border-4 border-info hover-elevate">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3">
                            <span class="avatar-initial rounded bg-label-info shadow-sm">
                                <i class="bx bx-user-circle fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Profil Saya</h5>
                            <p class="mb-0 text-muted small">Update data & foto profil</p>
                        </div>
                    </div>
                    <div class="mt-3 text-info fw-semibold small">
                        Kelola Profil <i class="bx bx-right-arrow-alt align-middle ms-1"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-12 mt-2">
        <h6 class="text-muted fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Ikhtisar Hari Ini</h6>
    </div>

    <div class="col-6 col-md-4 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3 text-center">
                <div class="avatar mx-auto mb-2">
                    <span class="avatar-initial rounded-circle bg-label-warning fw-bold" style="color: #ffab00 !important;">
                        <i class="bx bx-restaurant"></i>
                    </span>
                </div>
                <h3 class="fw-bold mb-0 text-dark">-</h3>
                <span class="text-muted small">Menu Aktif</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3 text-center">
                <div class="avatar mx-auto mb-2">
                    <span class="avatar-initial rounded-circle bg-label-success fw-bold" style="color: #71dd37 !important;">
                        <i class="bx bx-check-circle"></i>
                    </span>
                </div>
                <h3 class="fw-bold mb-0 text-dark">0</h3>
                <span class="text-muted small">Porsi Selesai</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3 text-center">
                <div class="avatar mx-auto mb-2">
                    <span class="avatar-initial rounded-circle bg-label-danger fw-bold" style="color: #ff3e1d !important;">
                        <i class="bx bx-error-circle"></i>
                    </span>
                </div>
                <h3 class="fw-bold mb-0 text-dark">0</h3>
                <span class="text-muted small">Stok Kurang</span>
            </div>
        </div>
    </div>
</div>

<style>
.hover-elevate {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-elevate:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
}
</style>

</div>
@endsection
