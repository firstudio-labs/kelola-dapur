@extends('template_admin.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav class="d-flex align-items-center mb-2">
                        <a href="{{ route('superadmin.dashboard') }}" class="text-muted me-2">
                            <i class="bx bx-home-alt me-1"></i>Dashboard
                        </a>
                        <i class="bx bx-chevron-right me-2"></i>
                        <span class="text-dark">Kelola Users</span>
                    </nav>
                    <h4 class="mb-1">Kelola Users</h4>
                    <p class="mb-0 text-muted">Kelola semua pengguna dan role dalam sistem</p>
                </div>
                <a href="{{ route('superadmin.users.create') }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-plus me-1"></i>Tambah User
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('superadmin.users.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search-input" class="form-label">Cari User</label>
                    <div class="input-group">
                        <input type="text" name="search" id="search-input" value="{{ request('search') }}"
                               class="form-control" placeholder="Cari nama, email, atau username...">
                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('search-input').value='';this.form.submit();">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="status-filter" class="form-label">Filter Status</label>
                    <select name="status" id="status-filter" class="choices-select form-select">
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="role-filter" class="form-label">Filter Role</label>
                    <select name="role" id="role-filter" class="choices-select form-select">
                        <option value="all" {{ request('role') === 'all' ? 'selected' : '' }}>Semua Role</option>
                        <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="kepala_dapur" {{ request('role') === 'kepala_dapur' ? 'selected' : '' }}>Kepala Dapur</option>
                        <option value="admin_gudang" {{ request('role') === 'admin_gudang' ? 'selected' : '' }}>Admin Gudang</option>
                        <option value="ahli_gizi" {{ request('role') === 'ahli_gizi' ? 'selected' : '' }}>Ahli Gizi</option>
                        <option value="no_role" {{ request('role') === 'no_role' ? 'selected' : '' }}>No Role</option>
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    @if(request()->hasAny(['search', 'status', 'role']))
                        <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline-secondary">Reset Filter</a>
                    @endif
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Section -->
    @if($users->total() > 0)
        <div class="card mb-4">
            <div class="card-body py-2 px-4">
                <div class="row justify-content-center g-3">
                    <div class="col-md-3 text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="badge bg-label-primary me-2">
                                <i class="bx bx-group"></i>
                            </span>
                            <div>
                                <small class="text-muted">Total Users</small>
                                <h6 class="mb-0">{{ $users->total() }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="badge bg-label-danger me-2">
                                <i class="bx bx-shield-alt-2"></i>
                            </span>
                            <div>
                                <small class="text-muted">Super Admins</small>
                                <h6 class="mb-0">{{ $users->filter(function($user) { return $user->superAdmin; })->count() }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="badge bg-label-success me-2">
                                <i class="bx bx-check-circle"></i>
                            </span>
                            <div>
                                <small class="text-muted">Active Users</small>
                                <h6 class="mb-0">{{ $users->filter(function($user) { return $user->is_active; })->count() }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="badge bg-label-secondary me-2">
                                <i class="bx bx-block"></i>
                            </span>
                            <div>
                                <small class="text-muted">No Role</small>
                                <h6 class="mb-0">{{ $users->filter(function($user) { return !$user->superAdmin && $user->kepalaDapur->isEmpty() && $user->adminGudang->isEmpty() && $user->ahliGizi->isEmpty(); })->count() }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Users List -->
    <div class="card mb-4">
        <div class="card-body">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>User Info</th>
                                <th>Roles</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="user-table-body">
                            @foreach($users as $user)
                                <tr data-status="{{ $user->is_active ? '1' : '0' }}"
                                    data-roles="{{ $user->superAdmin ? 'super_admin ' : '' }}{{ $user->kepalaDapur->isNotEmpty() ? 'kepala_dapur ' : '' }}{{ $user->adminGudang->isNotEmpty() ? 'admin_gudang ' : '' }}{{ $user->ahliGizi->isNotEmpty() ? 'ahli_gizi ' : '' }}{{ !$user->superAdmin && $user->kepalaDapur->isEmpty() && $user->adminGudang->isEmpty() && $user->ahliGizi->isEmpty() ? 'no_role' : '' }}"
                                    data-search="{{ strtolower($user->nama . ' ' . $user->email . ' ' . $user->username) }}">
                                    <td>{{ $users->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3">
                                                <span class="avatar-initial rounded {{ $user->isSuperAdmin() ? 'bg-label-danger' : 'bg-label-primary' }}">
                                                    @if($user->isSuperAdmin())
                                                        <i class="bx bx-shield-alt-2"></i>
                                                    @else
                                                        <i class="bx bx-user"></i>
                                                    @endif
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $user->nama }}</h6>
                                                <small class="text-muted">{{ $user->email }}</small><br>
                                                <span class="badge bg-label-{{ $user->is_active ? 'success' : 'danger' }}"
                                                      title="Status: {{ $user->is_active ? 'Active' : 'Inactive' }}"
                                                      data-bs-toggle="tooltip" data-bs-placement="top">
                                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @if($user->superAdmin)
                                                <span class="badge bg-label-danger">Super Admin</span>
                                            @endif
                                            @if($user->kepalaDapur->isNotEmpty())
                                                @foreach($user->kepalaDapur as $kd)
                                                    @if($kd->dapur)
                                                        <span class="badge bg-label-primary">Kepala Dapur: {{ $kd->dapur->nama_dapur }}</span>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if($user->adminGudang->isNotEmpty())
                                                @foreach($user->adminGudang as $ag)
                                                    @if($ag->dapur)
                                                        <span class="badge bg-label-secondary">Admin Gudang: {{ $ag->dapur->nama_dapur }}</span>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if($user->ahliGizi->isNotEmpty())
                                                @foreach($user->ahliGizi as $ag)
                                                    @if($ag->dapur)
                                                        <span class="badge bg-label-warning">Ahli Gizi: {{ $ag->dapur->nama_dapur }}</span>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if(!$user->superAdmin && $user->kepalaDapur->isEmpty() && $user->adminGudang->isEmpty() && $user->ahliGizi->isEmpty())
                                                <span class="badge bg-label-secondary">No Role</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('superadmin.users.show', $user) }}" 
                                               class="btn btn-sm btn-outline-primary btn-icon action-btn"
                                               title="Detail" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            <a href="{{ route('superadmin.users.edit', $user) }}" 
                                               class="btn btn-sm btn-outline-info btn-icon action-btn"
                                               title="Edit" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            @if($user->id_user !== auth()->id())
                                                <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}" 
                                                      onsubmit="return confirm('Yakin ingin menghapus user {{ $user->nama }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger btn-icon action-btn"
                                                            title="Hapus" data-bs-toggle="tooltip" data-bs-placement="top">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-sm btn-outline-secondary btn-icon action-btn disabled"
                                                        title="Tidak bisa menghapus user saat ini" 
                                                        data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $users->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-6">
                    @if(request()->hasAny(['search', 'status', 'role']))
                        <i class="bx bx-search bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">Tidak ada hasil</h5>
                        <p class="text-muted mb-3">Tidak ada user yang sesuai dengan filter.</p>
                        <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline-primary">
                            Reset Filter
                        </a>
                    @else
                        <i class="bx bx-group bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">Belum ada users</h5>
                        <p class="text-muted mb-3">Mulai dengan membuat user pertama.</p>
                        <a href="{{ route('superadmin.users.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Tambah User Pertama
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Choices.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css">

<!-- Custom Styling for Action Buttons and Choices.js -->
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
.choices.is-disabled .choices__inner {
    background-color: #f8f9fa;
}
.action-btn {
    min-width: 40px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s ease, opacity 0.2s ease;
}
.action-btn:hover:not(.disabled) {
    transform: scale(1.1);
    opacity: 0.9;
}
</style>

<!-- Choices.js JS -->
<script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>

<!-- JavaScript for Filters, Client-Side Search, and Tooltips -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('status-filter');
    const roleFilter = document.getElementById('role-filter');
    const searchInput = document.getElementById('search-input');
    const tableBody = document.getElementById('user-table-body');
    const rows = tableBody.getElementsByTagName('tr');

    const currentStatus = '{{ request("status") }}';
    const currentRole = '{{ request("role") }}';

    const statusChoices = new Choices(statusFilter, {
        searchEnabled: false,
        itemSelectText: '',
        placeholder: true,
        placeholderValue: 'Semua Status'
    });

    const roleChoices = new Choices(roleFilter, {
        searchEnabled: false,
        itemSelectText: '',
        placeholder: true,
        placeholderValue: 'Semua Role'
    });

    function filterTable() {
        const searchText = searchInput.value.toLowerCase();
        const statusValue = statusChoices.getValue(true);
        const roleValue = roleChoices.getValue(true);

        Array.from(rows).forEach(row => {
            const searchData = row.getAttribute('data-search');
            const statusData = row.getAttribute('data-status');
            const rolesData = row.getAttribute('data-roles');

            const matchesSearch = searchText ? searchData.includes(searchText) : true;
            const matchesStatus = statusValue === 'all' || statusData === statusValue;
            const matchesRole = roleValue === 'all' || rolesData.includes(roleValue);

            row.style.display = matchesSearch && matchesStatus && matchesRole ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    roleFilter.addEventListener('change', filterTable);

    // Initialize Bootstrap tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
});
</script>
@endsection