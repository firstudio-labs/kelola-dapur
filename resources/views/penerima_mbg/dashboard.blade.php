@extends('template_penerima_mbg.layout')
@section('title', 'Dashboard')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        Selamat Datang, <span class="text-primary">{{ $user->nama }}</span>!
    </h4>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($penerima)
        @php
            $statusColor = match($penerima->status_approval) {
                'approved' => 'success',
                'rejected'  => 'danger',
                default     => 'warning',
            };
            $statusLabel = match($penerima->status_approval) {
                'approved' => 'Disetujui',
                'rejected'  => 'Ditolak',
                default     => 'Menunggu Persetujuan',
            };
        @endphp
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card border-{{ $statusColor }} border-2">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="card-title mb-0">Status Pengajuan MBG</h5>
                            <span class="badge bg-label-{{ $statusColor }} fs-6">{{ $statusLabel }}</span>
                        </div>
                        @if($penerima->isApproved())
                            <div class="alert alert-success mb-0">
                                <i class="bx bx-check-circle me-1"></i>
                                Selamat! Pengajuan Anda telah disetujui oleh Dapur SPPG <strong>{{ $penerima->dapur?->nama_dapur }}</strong>.
                            </div>
                        @elseif($penerima->isRejected())
                            <div class="alert alert-danger mb-0">
                                <i class="bx bx-x-circle me-1"></i>
                                Pengajuan Anda ditolak oleh Dapur SPPG <strong>{{ $penerima->dapur?->nama_dapur }}</strong>.
                                @if($penerima->catatan_approval)
                                    <br><strong>Alasan:</strong> {{ $penerima->catatan_approval }}
                                @endif
                                <hr>
                                Anda dapat memperbarui data profil Anda dan mencoba lagi dengan menghubungi Dapur SPPG terkait.
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="bx bx-time me-1"></i>
                                Pengajuan Anda sedang menunggu persetujuan dari Dapur SPPG <strong>{{ $penerima->dapur?->nama_dapur }}</strong>.
                                Harap bersabar, Anda akan dihubungi jika ada perkembangan.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bx bx-restaurant mb-2" style="font-size: 2.5rem; color: #696cff;"></i>
                        <h3 class="card-title mb-1">{{ $penerima->jumlah_porsi }}</h3>
                        <p class="text-muted mb-0">Porsi MBG</p>
                        @if($penerima->isApproved())
                            <a href="{{ route('penerima-mbg.porsi.edit') }}" class="btn btn-sm btn-outline-primary mt-2">Ubah Porsi</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bx bx-home mb-2" style="font-size: 2.5rem; color: #28c76f;"></i>
                        <h5 class="card-title mb-1">{{ $penerima->dapur?->nama_dapur ?? '-' }}</h5>
                        <p class="text-muted mb-0">Dapur SPPG Pilihan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bx bx-id-card mb-2" style="font-size: 2.5rem; color: #ff9f43;"></i>
                        <h5 class="card-title mb-1">{{ $penerima->id_type_label }}: {{ $penerima->id_number }}</h5>
                        <p class="text-muted mb-0">Identitas</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('penerima-mbg.profile.edit') }}" class="btn btn-primary">
                <i class="bx bx-edit me-1"></i> Edit Profil Saya
            </a>
        </div>
    @else
        <div class="alert alert-warning">
            Data profil Anda belum ditemukan. Silakan hubungi administrator.
        </div>
    @endif
</div>
@endsection
