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
                        <span class="text-dark">Tambah User</span>
                    </nav>
                    <h4 class="mb-1">Tambah User</h4>
                    <p class="mb-0 text-muted">Buat user baru untuk admin gudang atau ahli gizi</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('kepala-dapur.users.store', ['dapur' => $dapur]) }}" method="POST" class="row g-4">
                @csrf

                <!-- User Information -->
                <div class="col-12">
                    <h5 class="card-title mb-0">Informasi User</h5>
                    <div class="row g-4 mt-2">
                        <!-- Nama -->
                        <div class="col-md-6">
                            <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="nama" 
                                   id="nama" 
                                   required
                                   class="form-control @error('nama') is-invalid @enderror"
                                   placeholder="Contoh: John Doe"
                                   value="{{ old('nama') }}">
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="username" 
                                   id="username" 
                                   required
                                   class="form-control @error('username') is-invalid @enderror"
                                   placeholder="Contoh: johndoe"
                                   value="{{ old('username') }}">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   required
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="Contoh: john@example.com"
                                   value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="col-md-6">
                            <label for="role_type" class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role_type" 
                                    id="role_type" 
                                    required
                                    class="form-select @error('role_type') is-invalid @enderror">
                                <option value="">Pilih Role</option>
                                @foreach ($roles as $value => $label)
                                    <option value="{{ $value }}" {{ old('role_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('role_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   required
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Minimal 8 karakter">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation" 
                                   required
                                   class="form-control @error('password_confirmation') is-invalid @enderror"
                                   placeholder="Ulangi password">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="col-12 mt-4">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('kepala-dapur.users.index', ['dapur' => $dapur]) }}" class="btn btn-label-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Instructions Alert -->
    <div class="alert alert-info alert-dismissible" role="alert">
        <h6 class="alert-heading mb-2">Instruksi Tambah User</h6>
        <ul class="mb-0">
            <li>Username dan email harus unik.</li>
            <li>Password minimal 8 karakter.</li>
            <li>User akan otomatis ditambahkan ke dapur ini: {{ $dapur->nama_dapur }}.</li>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
@endsection