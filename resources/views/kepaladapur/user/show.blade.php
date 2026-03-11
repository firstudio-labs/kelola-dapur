@extends('template_kepala_dapur.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <nav class="d-flex align-items-center mb-2">
                        <a href="{{ route('dashboard') }}" class="text-muted me-2">
                            <i class="bx bx-home-alt me-1"></i>Dashboard
                        </a>
                        <i class="bx bx-chevron-right me-2"></i>
                        <a href="{{ route('kepala-dapur.users.index', ['dapur' => $dapur]) }}" class="text-muted me-2">Kelola User</a>
                        <i class="bx bx-chevron-right me-2"></i>
                        <span class="text-dark">{{ $user->nama }}</span>
                    </nav>
                    <h4 class="mb-1">{{ $user->nama }}</h4>
                    <p class="mb-0 text-muted">Detail user dan aksesnya</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Informasi User</h5>
            @if (session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="row g-3">
                <div class="col-md-6">
                    <p><strong>Nama:</strong> {{ $user->nama }}</p>
                    <p><strong>Username:</strong> {{ $user->username }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Role:</strong> {{ ucfirst(str_replace('_', ' ', $user->userRole->role_type)) }}</p>
                    <p><strong>Status:</strong> <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">{{ $user->is_active ? 'Aktif' : 'Non-Aktif' }}</span></p>
                    <p><strong>Dibuat Pada:</strong> {{ $user->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('kepala-dapur.users.edit', ['dapur' => $dapur, 'user' => $user]) }}" class="btn btn-primary">
                    <i class="bx bx-edit-alt me-1"></i> Edit User
                </a>
            </div>
        </div>
    </div>

    @if(isset($ahliGizi) && $user->userRole->role_type === 'ahli_gizi')
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Profil Spesifik: Ahli Gizi</h5>
            <div class="row g-3">
                <div class="col-md-3 text-center mb-3 mb-md-0">
                    <img src="{{ $ahliGizi->foto_diri ? Storage::url($ahliGizi->foto_diri) : asset('assets/img/avatars/1.png') }}" 
                         alt="Foto Ahli Gizi" 
                         class="img-fluid rounded border" 
                         style="max-height: 200px; object-fit: cover;">
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <p class="mb-1"><strong>NIK:</strong> {{ $ahliGizi->nik_ahli_gizi ?: '-' }}</p>
                            <p class="mb-1"><strong>Jabatan:</strong> {{ $ahliGizi->jabatan ?: '-' }}</p>
                            <p class="mb-1"><strong>Pendidikan:</strong> {{ $ahliGizi->pendidikan_terakhir ?: '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-2">
                            <p class="mb-1"><strong>Kontak WA:</strong> {{ $ahliGizi->kontak_wa ?: '-' }}</p>
                            <p class="mb-1"><strong>Jenis Kelamin:</strong> {{ $ahliGizi->jenis_kelamin ?: '-' }}</p>
                        </div>
                        <div class="col-12 mt-2">
                            <p class="mb-1"><strong>Alamat Lengkap:</strong></p>
                            <p class="text-muted mb-0">
                                {{ $ahliGizi->alamat_detail ?: '-' }}<br>
                                @if($ahliGizi->village_name || $ahliGizi->district_name)
                                    Kel. {{ $ahliGizi->village_name ?: '-' }}, Kec. {{ $ahliGizi->district_name ?: '-' }}<br>
                                    {{ $ahliGizi->regency_name ?: '-' }}, Prov. {{ $ahliGizi->province_name ?: '-' }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(isset($produksi) && $user->userRole->role_type === 'produksi')
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Profil Spesifik: Produksi</h5>
            <div class="row g-3">
                <div class="col-md-3 text-center mb-3 mb-md-0">
                    <img src="{{ $produksi->foto_diri ? Storage::url($produksi->foto_diri) : asset('assets/img/avatars/1.png') }}" 
                         alt="Foto Produksi" 
                         class="img-fluid rounded border" 
                         style="max-height: 200px; object-fit: cover;">
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <p class="mb-1"><strong>NIK:</strong> {{ $produksi->nik_produksi ?: '-' }}</p>
                            <p class="mb-1"><strong>Nama Lengkap:</strong> {{ $produksi->nama_lengkap ?: '-' }}</p>
                            <p class="mb-1"><strong>Jabatan:</strong> {{ $produksi->jabatan ?: '-' }}</p>
                            <p class="mb-1"><strong>Pendidikan:</strong> {{ $produksi->pendidikan ?: '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-2">
                            <p class="mb-1"><strong>Kontak WA:</strong> {{ $produksi->kontak_wa ?: '-' }}</p>
                            <p class="mb-1"><strong>Jenis Kelamin:</strong> {{ $produksi->jenis_kelamin ?: '-' }}</p>
                        </div>
                        <div class="col-12 mt-2">
                            <p class="mb-1"><strong>Alamat Lengkap:</strong></p>
                            <p class="text-muted mb-0">
                                {{ $produksi->alamat_detail ?: '-' }}<br>
                                @if($produksi->village_name || $produksi->district_name)
                                    Kel. {{ $produksi->village_name ?: '-' }}, Kec. {{ $produksi->district_name ?: '-' }}<br>
                                    {{ $produksi->regency_name ?: '-' }}, Prov. {{ $produksi->province_name ?: '-' }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(isset($distributor) && $user->userRole->role_type === 'distributor')
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Profil Spesifik: Distributor</h5>
            <div class="row g-3">
                <div class="col-md-3 text-center mb-3 mb-md-0">
                    <img src="{{ $distributor->foto_diri ? Storage::url($distributor->foto_diri) : asset('assets/img/avatars/1.png') }}" 
                         alt="Foto Distributor" 
                         class="img-fluid rounded border" 
                         style="max-height: 200px; object-fit: cover;">
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <p class="mb-1"><strong>NIK:</strong> {{ $distributor->nik_distribusi ?: '-' }}</p>
                            <p class="mb-1"><strong>Nama Lengkap:</strong> {{ $distributor->nama_lengkap ?: '-' }}</p>
                            <p class="mb-1"><strong>Jabatan:</strong> {{ $distributor->jabatan ?: '-' }}</p>
                            <p class="mb-1"><strong>Pendidikan:</strong> {{ $distributor->pendidikan ?: '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-2">
                            <p class="mb-1"><strong>Kontak WA:</strong> {{ $distributor->kontak_wa ?: '-' }}</p>
                            <p class="mb-1"><strong>Jenis Kelamin:</strong> {{ $distributor->jenis_kelamin ?: '-' }}</p>
                        </div>
                        <div class="col-12 mt-2">
                            <p class="mb-1"><strong>Alamat Lengkap:</strong></p>
                            <p class="text-muted mb-0">
                                {{ $distributor->alamat_detail ?: '-' }}<br>
                                @if($distributor->village_name || $distributor->district_name)
                                    Kel. {{ $distributor->village_name ?: '-' }}, Kec. {{ $distributor->district_name ?: '-' }}<br>
                                    {{ $distributor->regency_name ?: '-' }}, Prov. {{ $distributor->province_name ?: '-' }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="alert alert-info alert-dismissible" role="alert">
        <h6 class="alert-heading mb-2">Informasi Detail User</h6>
        <ul class="mb-0">
            <li>Hanya kepala dapur yang dapat mengelola user ini.</li>
            <li>User ini terikat dengan dapur: {{ $dapur->nama_dapur }}.</li>
            <li>Hapus user hanya jika tidak ada transaksi terkait.</li>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="mt-4">
        <a href="{{ route('kepala-dapur.users.index', ['dapur' => $dapur]) }}" class="btn btn-label-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar
        </a>
    </div>
</div>
@endsection