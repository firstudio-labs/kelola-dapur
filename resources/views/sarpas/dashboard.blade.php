@extends('template_sarpas.layout')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md me-3">
                            <span
                                class="avatar-initial rounded-circle bg-label-primary"
                            >
                                <i class="bx bx-user"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-1">Dashboard Sarpas</h4>
                            <p class="mb-0 text-muted">
                                <i class="bx bx-user me-1"></i>
                                {{ $user->nama ?? 'N/A' }} (Sarpas)
                            </p>
                            <p class="mb-0 text-muted">
                                <i class="bx bx-building me-1"></i>
                                Dapur: {{ $dapur->nama_dapur ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Akun</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Nama</dt>
                        <dd class="col-sm-8">{{ $user->nama ?? 'N/A' }}</dd>
                        <dt class="col-sm-4">Username</dt>
                        <dd class="col-sm-8">{{ $user->username ?? 'N/A' }}</dd>
                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">{{ $user->email ?? 'N/A' }}</dd>
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">
                            <span
                                class="badge bg-label-{{ ($user->is_active ?? false) ? 'success' : 'danger' }}"
                            >
                                {{ ($user->is_active ?? false) ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </dd>
                        <dt class="col-sm-4">Role</dt>
                        <dd class="col-sm-8">Sarpas</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Dapur</h5>
                </div>
                <div class="card-body">
                    @if($dapur)
                    <dl class="row">
                        <dt class="col-sm-4">Nama Dapur</dt>
                        <dd class="col-sm-8">
                            {{ $dapur->nama_dapur ?? 'N/A' }}
                        </dd>
                        <dt class="col-sm-4">Kepala Dapur</dt>
                        <dd class="col-sm-8">
                            @if($dapur->kepalaDapur &&
                            $dapur->kepalaDapur->isNotEmpty())
                            {{ $dapur->kepalaDapur->first()->user->nama ?? 'N/A' }}
                            @else N/A @endif
                        </dd>
                        <dt class="col-sm-4">Alamat</dt>
                        <dd class="col-sm-8">{{ $dapur->alamat ?? 'N/A' }}</dd>
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">
                            <span
                                class="badge bg-label-{{ $dapur->isActive() ? 'success' : 'danger' }}"
                            >
                                {{ $dapur->isActive() ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </dd>
                    </dl>
                    @else
                    <p class="text-muted">Data dapur tidak tersedia.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
