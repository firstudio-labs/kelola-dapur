@extends('template_admin.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <nav class="d-flex align-items-center mb-2">
                        <a href="{{ route('superadmin.dashboard') }}" class="text-muted me-2">
                            <i class="bx bx-home-alt me-1"></i>Dashboard
                        </a>
                        <i class="bx bx-chevron-right me-2"></i>
                        <a href="{{ route('superadmin.users.index') }}" class="text-muted me-2">Kelola Users</a>
                        <i class="bx bx-chevron-right me-2"></i>
                        <span class="text-dark">Edit User</span>
                    </nav>
                    <h4 class="mb-1">Edit User</h4>
                    <p class="mb-0 text-muted">Edit informasi user: {{ $user->nama }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('superadmin.users.update', $user) }}" method="POST" class="row g-4">
                @csrf
                @method('PUT')

                <!-- Personal Information -->
                <div class="col-12">
                    <h5 class="card-title mb-0">Informasi Personal</h5>
                    <div class="row g-4 mt-2">
                        <!-- Nama -->
                        <div class="col-md-6">
                            <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
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
                            <small class="text-muted">Username unik untuk login</small>
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

                        <!-- Password -->
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" 
                                   name="password" 
                                   id="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Kosongkan jika tidak ingin mengubah">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimal 8 karakter. Kosongkan jika tidak ingin mengubah.</small>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label for="is_active" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="is_active" 
                                    id="is_active" 
                                    required
                                    class="form-select @error('is_active') is-invalid @enderror">
                                <option value="">Pilih Status</option>
                                <option value="1" {{ old('is_active', $user->is_active) == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $user->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Current Roles -->
                <div class="col-12 mt-4">
                    <h5 class="card-title mb-0">Current Roles</h5>
                    <div class="mt-3">
                        @if($user->superAdmin || $user->kepalaDapur->count() > 0 || $user->adminGudang->count() > 0 || $user->ahliGizi->count() > 0)
                            <div class="p-3 bg-light rounded">
                                <h6 class="mb-2">Roles yang dimiliki:</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @if($user->superAdmin)
                                        <span class="badge bg-label-danger">Super Admin</span>
                                    @endif
                                    @foreach($user->kepalaDapur as $kd)
                                        <span class="badge bg-label-purple">Kepala Dapur: {{ $kd->dapur->nama_dapur }}</span>
                                    @endforeach
                                    @foreach($user->adminGudang as $ag)
                                        <span class="badge bg-label-success">Admin Gudang: {{ $ag->dapur->nama_dapur }}</span>
                                    @endforeach
                                    @foreach($user->ahliGizi as $ag)
                                        <span class="badge bg-label-info">Ahli Gizi: {{ $ag->dapur->nama_dapur }}</span>
                                    @endforeach
                                </div>
                                <p class="mt-2 text-muted small">
                                    Untuk mengelola roles, gunakan fitur "Assign Role" di halaman detail user.
                                </p>
                            </div>
                        @else
                            <div class="alert alert-warning alert-dismissible" role="alert">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="bx bx-error me-2"></i>
                                    </div>
                                    <div>
                                        <h6 class="alert-heading">User belum memiliki role</h6>
                                        <p class="mb-0">User ini belum memiliki role apapun. Setelah update, assign role di halaman detail user.</p>
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Preview Card -->
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title mb-2">Preview User:</h6>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    <span class="avatar-initial rounded {{ $user->isSuperAdmin() ? 'bg-label-danger' : 'bg-label-primary' }}" id="preview-avatar">
                                        <i class="bx {{ $user->isSuperAdmin() ? 'bx-shield-alt-2' : 'bx-user' }}"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center">
                                        <h6 class="mb-0" id="preview-nama">{{ old('nama', $user->nama) }}</h6>
                                        <span class="badge ms-2 {{ old('is_active', $user->is_active) ? 'bg-label-success' : 'bg-label-danger' }}" id="preview-status-badge">
                                            {{ old('is_active', $user->is_active) ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                    <small class="text-muted" id="preview-email">{{ old('email', $user->email) }}</small><br>
                                    <small class="text-muted">Username: <span id="preview-username">{{ old('username', $user->username) }}</span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="col-12">
                    <div class="alert alert-info alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bx bx-info-circle me-2"></i>
                            </div>
                            <div>
                                <h6 class="alert-heading">Informasi Akun</h6>
                                <p class="mb-1"><strong>Terdaftar:</strong> {{ $user->created_at->format('d M Y H:i') }}</p>
                                @if($user->updated_at != $user->created_at)
                                    <p class="mb-1"><strong>Terakhir diupdate:</strong> {{ $user->updated_at->format('d M Y H:i') }}</p>
                                @endif
                                <p class="mb-0"><strong>ID User:</strong> {{ $user->id_user }}</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="{{ route('superadmin.users.show', $user) }}" class="btn btn-outline-secondary">Batal</a>
                    <a href="{{ route('superadmin.users.show', $user) }}" class="btn btn-outline-primary">Lihat Detail</a>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Role Management Note -->
    <div class="alert alert-info alert-dismissible" role="alert">
        <div class="d-flex">
            <div class="flex-shrink-0">
                <i class="bx bx-user me-2"></i>
            </div>
            <div>
                <h6 class="alert-heading">Role Management</h6>
                <p class="mb-2">Untuk mengelola roles user (assign atau remove), gunakan halaman detail user setelah update.</p>
                <a href="{{ route('superadmin.users.show', $user) }}" class="text-primary font-medium">
                    Kelola Roles <i class="bx bx-right-arrow-alt"></i>
                </a>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>

<!-- Choices.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css">

<!-- Custom Choices.js Styling -->
<style>
.choices__inner {
    background-color: #fff;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.5rem;
    font-size: 0.875rem;
}
.choices__list--dropdown {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
.choices__list--dropdown .choices__item--selectable.is-highlighted {
    background-color: #f8f9fa;
}
.choices[data-type*="select-one"] .choices__inner {
    padding-bottom: 0;
}
.is-invalid .choices__inner {
    border-color: #dc3545;
}
</style>

<!-- Choices.js JS -->
<script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>

<!-- JavaScript for Live Preview -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Choices.js for status select
    const statusSelect = new Choices('#is_active', {
        searchEnabled: false,
        placeholderValue: 'Pilih Status',
        itemSelectText: ''
    });

    const namaInput = document.getElementById('nama');
    const emailInput = document.getElementById('email');
    const usernameInput = document.getElementById('username');
    
    const previewNama = document.getElementById('preview-nama');
    const previewEmail = document.getElementById('preview-email');
    const previewUsername = document.getElementById('preview-username');
    const previewStatusBadge = document.getElementById('preview-status-badge');
    const previewAvatar = document.getElementById('preview-avatar');

    // Update preview nama
    namaInput.addEventListener('input', function() {
        previewNama.textContent = this.value || 'Nama User';
    });

    // Update preview email
    emailInput.addEventListener('input', function() {
        previewEmail.textContent = this.value || 'email@example.com';
    });

    // Update preview username
    usernameInput.addEventListener('input', function() {
        previewUsername.textContent = this.value || 'username';
    });

    // Update preview status
    statusSelect.passedElement.element.addEventListener('change', function() {
        const badge = previewStatusBadge;
        if (this.value === '1') {
            badge.textContent = 'Active';
            badge.className = 'badge bg-label-success ms-2';
        } else if (this.value === '0') {
            badge.textContent = 'Inactive';
            badge.className = 'badge bg-label-danger ms-2';
        } else {
            badge.textContent = 'Status';
            badge.className = 'badge bg-label-secondary ms-2';
        }
    });

    // Initialize status badge
    statusSelect.passedElement.element.dispatchEvent(new Event('change'));
});
</script>
@endsection