@extends('template_admin_gudang.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a href="{{ route('admin-gudang.dashboard', $dapur) }}" class="text-muted me-2">
                                <i class="bx bx-home-alt me-1"></i> Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Keamanan</span>
                        </nav>
                        <h4 class="mb-0">Pengaturan Akun</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-pills flex-column flex-md-row mb-3">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin-gudang.profile.edit', $dapur) }}">
                            <i class="bx bx-user me-1"></i> Profil Saya
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="javascript:void(0);">
                            <i class="bx bx-lock-alt me-1"></i> Keamanan
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4">
            <h5 class="card-header fw-semibold border-bottom mb-4">Ubah Password</h5>
            <div class="card-body">
                <form id="formAccountSettings" action="{{ route('admin-gudang.profile.security.update', $dapur) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="current_password">Password Saat Ini <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <input class="form-control @error('current_password') is-invalid @enderror" 
                                       type="password" 
                                       name="current_password" 
                                       id="current_password" 
                                       placeholder="············" 
                                       required />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                            @error('current_password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label" for="new_password">Password Baru <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <input class="form-control @error('new_password') is-invalid @enderror" 
                                       type="password" 
                                       name="new_password" 
                                       id="new_password" 
                                       placeholder="············" 
                                       required />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                            @error('new_password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text mt-2">
                                <p class="mb-1 fw-semibold small">Persyaratan Password:</p>
                                <ul class="ps-3 mb-0 small">
                                    <li>Minimal 8 karakter</li>
                                    <li>Mengandung huruf besar (A-Z)</li>
                                    <li>Mengandung huruf kecil (a-z)</li>
                                    <li>Mengandung angka (0-9)</li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="new_password_confirmation">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <input class="form-control" 
                                       type="password" 
                                       name="new_password_confirmation" 
                                       id="new_password_confirmation" 
                                       placeholder="············" 
                                       required />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
