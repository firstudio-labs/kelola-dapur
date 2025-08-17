@extends('template_kepala_dapur.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
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

    <!-- User Details -->
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

    <!-- Instructions Alert -->
    <div class="alert alert-info alert-dismissible" role="alert">
        <h6 class="alert-heading mb-2">Informasi Detail User</h6>
        <ul class="mb-0">
            <li>Hanya kepala dapur yang dapat mengelola user ini.</li>
            <li>User ini terikat dengan dapur: {{ $dapur->nama_dapur }}.</li>
            <li>Hapus user hanya jika tidak ada transaksi terkait.</li>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Back Button -->
    <div class="mt-4">
        <a href="{{ route('kepala-dapur.users.index', ['dapur' => $dapur]) }}" class="btn btn-label-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar
        </a>
    </div>
</div>
@endsection