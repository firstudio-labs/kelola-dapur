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
                        <span class="text-dark">Tambah User</span>
                    </nav>
                    <h4 class="mb-1">Tambah User Baru</h4>
                    <p class="mb-0 text-muted">Buat user baru dan assign role dalam sistem</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('superadmin.users.store') }}" method="POST" class="row g-4">
                @csrf

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
                                   value="{{ old('email') }}">
                            @error('email')
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

                        <!-- Status -->
                        <div class="col-md-6">
                            <label for="is_active" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="is_active" 
                                    id="is_active" 
                                    required
                                    class="form-select @error('is_active') is-invalid @enderror">
                                <option value="">Pilih Status</option>
                                <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Role Assignment -->
                <div class="col-12 mt-4">
                    <h5 class="card-title mb-0">Role Assignment</h5>
                    <div class="row g-4 mt-2">
                        <!-- Role -->
                        <div class="col-md-6">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" 
                                    id="role" 
                                    required
                                    class="form-select @error('role') is-invalid @enderror">
                                <option value="">Pilih Role</option>
                                <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                <option value="kepala_dapur" {{ old('role') === 'kepala_dapur' ? 'selected' : '' }}>Kepala Dapur</option>
                                <option value="admin_gudang" {{ old('role') === 'admin_gudang' ? 'selected' : '' }}>Admin Gudang</option>
                                <option value="ahli_gizi" {{ old('role') === 'ahli_gizi' ? 'selected' : '' }}>Ahli Gizi</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dapur -->
                        <div class="col-md-6" id="dapur-section" style="{{ old('role') === 'super_admin' ? 'display: none;' : '' }}">
                            <label for="id_dapur" class="form-label">Dapur <span class="text-danger" id="dapur-required">*</span></label>
                            <select name="id_dapur" 
                                    id="id_dapur"
                                    class="form-select @error('id_dapur') is-invalid @enderror">
                                <option value="">Pilih Dapur</option>
                                @foreach($dapurList as $dapur)
                                    <option value="{{ $dapur->id_dapur }}" {{ old('id_dapur') == $dapur->id_dapur ? 'selected' : '' }}>
                                        {{ $dapur->nama_dapur }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_dapur')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Dapur tempat user bertugas</small>
                        </div>
                    </div>

                    <!-- Role Descriptions -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="mb-2">Deskripsi Role:</h6>
                        <div class="text-muted small" id="role-description">
                            <p class="d-none" data-role="super_admin">
                                <strong>Super Admin:</strong> Akses penuh ke semua dapur dan fitur sistem. Dapat mengelola users, dapur, dan semua data.
                            </p>
                            <p class="d-none" data-role="kepala_dapur">
                                <strong>Kepala Dapur:</strong> Mengelola operasional dapur tertentu. Dapat approve/reject permintaan stock, melihat laporan, dan mengelola tim.
                            </p>
                            <p class="d-none" data-role="admin_gudang">
                                <strong>Admin Gudang:</strong> Mengelola stock dan inventory dapur. Dapat membuat permintaan stock dan mengelola data gudang.
                            </p>
                            <p class="d-none" data-role="ahli_gizi">
                                <strong>Ahli Gizi:</strong> Mengelola menu dan nutrisi. Dapat membuat menu baru, menganalisis nutrisi, dan mengelola resep.
                            </p>
                            <p id="default-description">Pilih role untuk melihat deskripsi</p>
                        </div>
                    </div>
                </div>

                <!-- Preview Card -->
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title mb-2">Preview User:</h6>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    <span class="avatar-initial rounded" id="preview-avatar">
                                        <i class="bx bx-user"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center">
                                        <h6 class="mb-0" id="preview-nama">{{ old('nama') ?: 'Nama User' }}</h6>
                                        <span class="badge ms-2" id="preview-status-badge">Status</span>
                                    </div>
                                    <small class="text-muted" id="preview-email">{{ old('email') ?: 'email@example.com' }}</small><br>
                                    <small class="text-muted" id="preview-role">
                                        <span id="preview-role-text">Role belum dipilih</span>
                                        <span id="preview-dapur-text" style="display: none;"></span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline-secondary">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Help Section -->
    <div class="alert alert-info alert-dismissible" role="alert">
        <div class="d-flex">
            <div class="flex-shrink-0">
                <i class="bx bx-info-circle me-2"></i>
            </div>
            <div>
                <h6 class="alert-heading">Tips:</h6>
                <ul class="list-disc list-inside">
                    <li>Username dan email harus unik dalam sistem</li>
                    <li>Password minimal 8 karakter untuk keamanan</li>
                    <li>Super Admin tidak perlu memilih dapur</li>
                    <li>Role lain harus di-assign ke dapur tertentu</li>
                    <li>Status "Active" memungkinkan user login ke sistem</li>
                </ul>
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

<!-- JavaScript for Dynamic Form -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Choices.js
    const roleSelect = new Choices('#role', {
        searchEnabled: true,
        placeholderValue: 'Pilih Role',
        searchPlaceholderValue: 'Cari role...',
        itemSelectText: ''
    });
    
    const dapurSelect = new Choices('#id_dapur', {
        searchEnabled: true,
        placeholderValue: 'Pilih Dapur',
        searchPlaceholderValue: 'Cari dapur...',
        itemSelectText: ''
    });

    const dapurSection = document.getElementById('dapur-section');
    const dapurRequired = document.getElementById('dapur-required');
    
    const namaInput = document.getElementById('nama');
    const emailInput = document.getElementById('email');
    const statusSelect = document.getElementById('is_active');
    
    const previewNama = document.getElementById('preview-nama');
    const previewEmail = document.getElementById('preview-email');
    const previewStatusBadge = document.getElementById('preview-status-badge');
    const previewAvatar = document.getElementById('preview-avatar');
    const previewRoleText = document.getElementById('preview-role-text');
    const previewDapurText = document.getElementById('preview-dapur-text');
    
    const roleDescriptions = document.querySelectorAll('[data-role]');
    const defaultDescription = document.getElementById('default-description');

    // Handle role change
    roleSelect.passedElement.element.addEventListener('change', function() {
        const roleValue = this.value;
        if (roleValue === 'super_admin') {
            dapurSection.style.display = 'none';
            dapurSelect.disable();
            dapurRequired.style.display = 'none';
        } else {
            dapurSection.style.display = 'block';
            dapurSelect.enable();
            dapurRequired.style.display = 'inline';
        }
        
        updateRoleDescription();
        updatePreview();
    });

    // Handle dapur change
    dapurSelect.passedElement.element.addEventListener('change', updatePreview);

    // Handle input changes
    namaInput.addEventListener('input', function() {
        previewNama.textContent = this.value || 'Nama User';
    });

    emailInput.addEventListener('input', function() {
        previewEmail.textContent = this.value || 'email@example.com';
    });

    statusSelect.addEventListener('change', function() {
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
        updatePreview();
    });

    function updateRoleDescription() {
        roleDescriptions.forEach(desc => desc.classList.add('d-none'));
        defaultDescription.style.display = 'none';
        
        const roleValue = roleSelect.getValue(true);
        if (roleValue) {
            const selectedDesc = document.querySelector(`[data-role="${roleValue}"]`);
            if (selectedDesc) {
                selectedDesc.classList.remove('d-none');
            }
        } else {
            defaultDescription.style.display = 'block';
        }
    }

    function updatePreview() {
        const roleValue = roleSelect.getValue(true);
        const dapurValue = dapurSelect.getValue(true);
        
        // Update avatar color based on role
        if (roleValue === 'super_admin') {
            previewAvatar.className = 'avatar-initial rounded bg-label-danger';
        } else {
            previewAvatar.className = 'avatar-initial rounded bg-label-primary';
        }
        
        // Update role text
        if (roleValue) {
            const roleNames = {
                'super_admin': 'Super Admin',
                'kepala_dapur': 'Kepala Dapur',
                'admin_gudang': 'Admin Gudang',
                'ahli_gizi': 'Ahli Gizi'
            };
            previewRoleText.textContent = roleNames[roleValue];
            
            // Update dapur text
            if (roleValue === 'super_admin') {
                previewDapurText.style.display = 'none';
            } else if (dapurValue) {
                const selectedOption = dapurSelect.getValue();
                previewDapurText.textContent = ' - ' + selectedOption.label;
                previewDapurText.style.display = 'inline';
            } else {
                previewDapurText.textContent = ' - Belum pilih dapur';
                previewDapurText.style.display = 'inline';
            }
        } else {
            previewRoleText.textContent = 'Role belum dipilih';
            previewDapurText.style.display = 'none';
        }
    }

    // Initialize based on old values
    if (roleSelect.getValue(true)) {
        roleSelect.passedElement.element.dispatchEvent(new Event('change'));
    }
    if (statusSelect.value) {
        statusSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection