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
                        <span class="text-dark">Edit {{ $user->nama }}</span>
                    </nav>
                    <h4 class="mb-1">Edit User</h4>
                    <p class="mb-0 text-muted">Perbarui detail user</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="card mb-4">
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('kepala-dapur.users.update', ['dapur' => $dapur, 'user' => $user]) }}" method="POST" class="row g-4">
                @csrf
                @method('PUT')

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
                                   value="{{ old('nama', $user->nama) }}">
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
                                   value="{{ old('username', $user->username) }}">
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
                                   value="{{ old('email', $user->email) }}">
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
                                    <option value="{{ $value }}" {{ old('role_type', $user->userRole->role_type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('role_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label for="is_active" class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Aktif</label>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password Baru (Opsional)</label>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Kosongkan jika tidak ingin ubah">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation" 
                                   class="form-control @error('password_confirmation') is-invalid @enderror"
                                   placeholder="Ulangi password baru">
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
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Instructions Alert -->
    <div class="alert alert-info alert-dismissible" role="alert">
        <h6 class="alert-heading mb-2">Instruksi Edit User</h6>
        <ul class="mb-0">
            <li>Username dan email harus unik.</li>
            <li>Kosongkan password jika tidak ingin mengubah.</li>
            <li>Perubahan role akan menyesuaikan akses user.</li>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
@endsection