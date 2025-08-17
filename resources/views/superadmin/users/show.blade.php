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
                        <span class="text-dark">{{ $user->nama }}</span>
                    </nav>
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="mb-1">{{ $user->nama }}</h4>
                            <p class="mb-0 text-muted">Detail informasi user dan role assignments</p>
                        </div>
                        <a href="{{ route('superadmin.users.edit', $user) }}" class="btn btn-primary">
                            <i class="bx bx-edit me-1"></i>Edit User
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Information -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Informasi User</h5>
            <p class="text-muted mb-4">Detail lengkap tentang user ini.</p>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-user me-2"></i>
                        <div>
                            <small class="text-muted">Nama Lengkap</small>
                            <p class="mb-0">{{ $user->nama }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-id-card me-2"></i>
                        <div>
                            <small class="text-muted">Username</small>
                            <p class="mb-0">{{ $user->username }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-envelope me-2"></i>
                        <div>
                            <small class="text-muted">Email</small>
                            <p class="mb-0">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-check-circle me-2"></i>
                        <div>
                            <small class="text-muted">Status</small>
                            <span class="badge {{ $user->is_active ? 'bg-label-success' : 'bg-label-danger' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-calendar me-2"></i>
                        <div>
                            <small class="text-muted">Terdaftar</small>
                            <p class="mb-0">{{ $user->created_at->format('d M Y H:i') }} 
                                <span class="text-muted">({{ $user->created_at->diffForHumans() }})</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-calendar-edit me-2"></i>
                        <div>
                            <small class="text-muted">Terakhir Diupdate</small>
                            <p class="mb-0">{{ $user->updated_at->format('d M Y H:i') }}
                                @if($user->updated_at != $user->created_at)
                                    <span class="text-muted">({{ $user->updated_at->diffForHumans() }})</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Role Assignments -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="card-title mb-0">Role Assignments</h5>
                    <p class="text-muted">Semua role yang dimiliki user ini</p>
                </div>
                <button type="button" onclick="openAssignRoleModal()" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i>Assign Role
                </button>
            </div>

            @if(count($roles) > 0)
                <div class="row g-4">
                    @foreach($roles as $role)
                        <div class="col-md-6">
                            <div class="card h-100 
                                @switch($role['type'])
                                    @case('Super Admin') bg-light-danger border-danger @break
                                    @case('Kepala Dapur') bg-light-purple border-purple @break
                                    @case('Admin Gudang') bg-light-success border-success @break
                                    @case('Ahli Gizi') bg-light-info border-info @break
                                    @default bg-light border-secondary
                                @endswitch">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3">
                                                <span class="avatar-initial rounded 
                                                    @switch($role['type'])
                                                        @case('Super Admin') bg-label-danger @break
                                                        @case('Kepala Dapur') bg-label-purple @break
                                                        @case('Admin Gudang') bg-label-success @break
                                                        @case('Ahli Gizi') bg-label-info @break
                                                        @default bg-label-secondary
                                                    @endswitch">
                                                    @switch($role['type'])
                                                        @case('Super Admin')
                                                            <i class="bx bx-shield-alt-2"></i>
                                                            @break
                                                        @case('Kepala Dapur')
                                                            <i class="bx bx-user"></i>
                                                            @break
                                                        @case('Admin Gudang')
                                                            <i class="bx bx-package"></i>
                                                            @break
                                                        @case('Ahli Gizi')
                                                            <i class="bx bx-food-menu"></i>
                                                            @break
                                                        @default
                                                            <i class="bx bx-user"></i>
                                                    @endswitch
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $role['type'] }}</h6>
                                                @if($role['dapur'])
                                                    <small class="text-muted">{{ $role['dapur']->nama_dapur }}</small><br>
                                                    <small class="text-muted">{{ $role['dapur']->alamat ?: 'Alamat belum diset' }}</small>
                                                @else
                                                    <small class="text-muted">Global access</small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($role['dapur'])
                                                <a href="{{ route('superadmin.dapur.show', $role['dapur']) }}" class="btn btn-sm btn-outline-primary">Lihat Dapur</a>
                                            @endif
                                            @if($user->id_user !== auth()->id() || $role['type'] !== 'Super Admin')
                                                <button type="button" 
                                                        onclick="removeRole('{{ $role['type'] }}', {{ $role['dapur'] ? $role['dapur']->id_dapur : 'null' }})"
                                                        class="btn btn-sm btn-outline-danger">Hapus</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-6">
                    <i class="bx bx-group bx-lg text-muted mb-3"></i>
                    <h5 class="mb-1">Belum ada role</h5>
                    <p class="text-muted mb-3">User ini belum memiliki role apapun.</p>
                    <button type="button" onclick="openAssignRoleModal()" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i>Assign Role Pertama
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete User Section -->
    @if($user->id_user !== auth()->id())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex">
                <div class="flex-shrink-0">
                    <i class="bx bx-error me-2"></i>
                </div>
                <div>
                    <h6 class="alert-heading">Danger Zone</h6>
                    <p class="mb-2">Menghapus user akan menghilangkan semua data dan role yang terkait. Tindakan ini tidak dapat dibatalkan.</p>
                    <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}" 
                          onsubmit="return confirm('Yakin ingin menghapus user {{ $user->nama }}? Semua data dan role akan terhapus!')" 
                          class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus User</button>
                    </form>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>

<!-- Assign Role Modal -->
<div id="assignRoleModal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="assignRoleForm" method="POST" action="{{ route('superadmin.users.assign-role', $user) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="modal-role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" id="modal-role" required class="form-select">
                            <option value="">Pilih Role</option>
                            <option value="super_admin">Super Admin</option>
                            <option value="kepala_dapur">Kepala Dapur</option>
                            <option value="admin_gudang">Admin Gudang</option>
                            < carabinieri
                            <option value="ahli_gizi">Ahli Gizi</option>
                        </select>
                    </div>
                    <div class="mb-3" id="modal-dapur-section">
                        <label for="modal-id_dapur" class="form-label">Dapur <span class="text-danger" id="modal-dapur-required">*</span></label>
                        <select name="id_dapur" id="modal-id_dapur" class="form-select">
                            <option value="">Pilih Dapur</option>
                            @foreach(\App\Models\Dapur::where('status', 'active')->get() as $dapur)
                                <option value="{{ $dapur->id_dapur }}">{{ $dapur->nama_dapur }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Assign Role</button>
                    </div>
                </form>
            </div>
        </div>
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

<!-- JavaScript for Modal and Role Management -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Choices.js for modal selects
    const roleSelect = new Choices('#modal-role', {
        searchEnabled: true,
        placeholderValue: 'Pilih Role',
        searchPlaceholderValue: 'Cari role...',
        itemSelectText: ''
    });

    const dapurSelect = new Choices('#modal-id_dapur', {
        searchEnabled: true,
        placeholderValue: 'Pilih Dapur',
        searchPlaceholderValue: 'Cari dapur...',
        itemSelectText: ''
    });

    // Handle role change in modal
    roleSelect.passedElement.element.addEventListener('change', function() {
        const dapurSection = document.getElementById('modal-dapur-section');
        const dapurRequired = document.getElementById('modal-dapur-required');
        
        if (this.value === 'super_admin') {
            dapurSection.style.display = 'none';
            dapurSelect.disable();
            dapurRequired.style.display = 'none';
        } else {
            dapurSection.style.display = 'block';
            dapurSelect.enable();
            dapurRequired.style.display = 'inline';
        }
    });

    // Initialize modal state
    if (roleSelect.getValue(true) === 'super_admin') {
        document.getElementById('modal-dapur-section').style.display = 'none';
        dapurSelect.disable();
        document.getElementById('modal-dapur-required').style.display = 'none';
    }
});

function openAssignRoleModal() {
    const modal = new bootstrap.Modal(document.getElementById('assignRoleModal'));
    modal.show();
}

function removeRole(roleType, dapurId) {
    if (confirm('Yakin ingin menghapus role ' + roleType + '?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("superadmin.users.remove-role", $user) }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        const roleTypeField = document.createElement('input');
        roleTypeField.type = 'hidden';
        roleTypeField.name = 'role_type';
        roleTypeField.value = roleType.toLowerCase().replace(' ', '_');
        
        const roleIdField = document.createElement('input');
        roleIdField.type = 'hidden';
        roleIdField.name = 'role_id';
        roleIdField.value = dapurId || '';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        form.appendChild(roleTypeField);
        form.appendChild(roleIdField);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection