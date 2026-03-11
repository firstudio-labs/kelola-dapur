@extends('template_distributor.layout')

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
                            Anda masuk sebagai role <span class="fw-bold">Distributor</span> di <span class="fw-bold">{{ $dapur->nama_dapur }}</span>.
                            @if(isset($distributor) && $distributor->jabatan === 'Penanggung jawab')
                                Anda memiliki akses sebagai <span class="badge bg-label-success">Penanggung Jawab (Kepala Distributor)</span>.
                            @else
                                Jabatan Anda saat ini adalah <span class="badge bg-label-info">Anggota</span>.
                            @endif
                        </p>
                        <a href="{{ route('distributor.profile.edit') }}" class="btn btn-sm btn-outline-primary">Lihat Profil</a>
                    </div>
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-4 text-center">
                        <img src="{{ (isset($distributor) && $distributor->foto_diri) ? Storage::url($distributor->foto_diri) : asset('admin/assets/img/illustrations/man-with-laptop-light.png') }}" 
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
    <!-- Quick Stats -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <i class="bx bx-package p-2 bg-label-primary rounded"></i>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Pengiriman Hari Ini</span>
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
                        <i class="bx bx-check-double p-2 bg-label-success rounded"></i>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Total Distributor</span>
                <h3 class="card-title mb-2">0</h3>
                <small class="text-success fw-semibold"><i class='bx bx-up-arrow-alt'></i> 0% dari bulan lalu</small>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
