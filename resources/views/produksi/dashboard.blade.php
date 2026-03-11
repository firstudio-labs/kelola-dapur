@extends('template_produksi.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
<div class="row">
    <div class="col-lg-12 mb-4 order-0">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Selamat Datang, {{ $user->nama }}! 🎉</h5>
                        <p class="mb-4">
                            Anda masuk sebagai role <span class="fw-bold">Produksi</span> di <span class="fw-bold">{{ $dapur->nama_dapur }}</span>.
                            @if($user->isKepalaProduksi())
                                Anda memiliki akses sebagai <span class="badge bg-label-success">Penanggung Jawab (Kepala Produksi)</span>.
                            @else
                                Jabatan Anda saat ini adalah <span class="badge bg-label-info">Anggota</span>.
                            @endif
                        </p>
                        <a href="{{ route('produksi.profile.edit') }}" class="btn btn-sm btn-outline-primary">Lihat Profil</a>
                    </div>
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-4 text-center">
                        <img src="{{ auth()->user()->produksi->foto_diri ? Storage::url(auth()->user()->produksi->foto_diri) : asset('admin/assets/img/illustrations/man-with-laptop-light.png') }}" 
                             height="140" 
                             alt="View Badge User" 
                             class="rounded-circle border mb-3" 
                             style="width: 140px; object-fit: cover;" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Links -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-lg me-3">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="bx bx-list-check"></i>
                        </span>
                    </div>
                    <div>
                        <h6 class="mb-1">Order Produksi</h6>
                        <p class="mb-0 text-muted small">Daftar transaksi yang perlu diproduksi</p>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('produksi.order.index') }}" class="btn btn-primary btn-sm w-100">
                    <i class="bx bx-right-arrow-alt me-1"></i> Lihat Order
                </a>
            </div>
        </div>
    </div>
    <!-- Quick Stats -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <i class="bx bx-dish p-2 bg-label-primary rounded"></i>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Menu Hari Ini</span>
                <h3 class="card-title mb-2">-</h3>
                <small class="text-muted"><i class="bx bx-time"></i> Belum ada data</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <i class="bx bx-check-circle p-2 bg-label-success rounded"></i>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Porsi Selesai</span>
                <h3 class="card-title mb-2">0</h3>
                <small class="text-success fw-semibold"><i class='bx bx-up-arrow-alt'></i> 0% dari target</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <i class="bx bx-error p-2 bg-label-warning rounded"></i>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Perlu Approval</span>
                <h3 class="card-title mb-2">{{ $user->isKepalaProduksi() ? '0' : '-' }}</h3>
                <small class="text-muted">Transaksi menu makanan</small>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
