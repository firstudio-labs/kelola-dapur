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
                        <span class="text-dark">Kelola Dapur</span>
                    </nav>
                    <h4 class="mb-1">Kelola Dapur</h4>
                    <p class="mb-0 text-muted">Kelola semua dapur dalam sistem</p>
                </div>
                <a href="{{ route('superadmin.dapur.create') }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-plus me-1"></i>Tambah Dapur
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
            <form method="GET" action="{{ route('superadmin.dapur.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Cari Nama Dapur</label>
                    <div class="input-group">
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               class="form-control" placeholder="Cari nama dapur...">
                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('search').value='';this.form.submit();">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="filter_provinsi" class="form-label">Provinsi</label>
                    <select name="filter_provinsi" id="filter_provinsi" class="choices-select form-select">
                        <option value="">Semua Provinsi</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter_kabupaten" class="form-label">Kabupaten/Kota</label>
                    <select name="filter_kabupaten" id="filter_kabupaten" disabled class="choices-select form-select">
                        <option value="">Semua Kabupaten/Kota</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="choices-select form-select">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    @if(request()->hasAny(['search', 'filter_provinsi', 'filter_kabupaten', 'status']))
                        <a href="{{ route('superadmin.dapur.index') }}" class="btn btn-outline-secondary">Reset Filter</a>
                    @endif
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Section -->
    @if($dapurList->total() > 0)
        <div class="card mb-4">
            <div class="card-body py-2 px-4">
                <div class="row justify-content-center g-3">
                    <div class="col-md-3 text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="badge bg-label-primary me-2">
                                <i class="bx bx-buildings"></i>
                            </span>
                            <div>
                                <small class="text-muted">Total Dapur</small>
                                <h6 class="mb-0">{{ $dapurList->total() }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="badge bg-label-success me-2">
                                <i class="bx bx-check-circle"></i>
                            </span>
                            <div>
                                <small class="text-muted">Dapur Aktif</small>
                                <h6 class="mb-0">{{ $dapurList->where('status', 'active')->count() }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="badge bg-label-danger me-2">
                                <i class="bx bx-x-circle"></i>
                            </span>
                            <div>
                                <small class="text-muted">Dapur Tidak Aktif</small>
                                <h6 class="mb-0">{{ $dapurList->where('status', 'inactive')->count() }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="badge bg-label-info me-2">
                                <i class="bx bx-user"></i>
                            </span>
                            <div>
                                <small class="text-muted">Total Staff</small>
                                <h6 class="mb-0">{{ $dapurList->sum(function($dapur) { return $dapur->kepala_dapur_count + $dapur->admin_gudang_count + $dapur->ahli_gizi_count; }) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Dapur List -->
    <div class="card mb-4">
        <div class="card-body">
            @if($dapurList->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 20%;">Nama Dapur</th>
                                <th>Wilayah</th>
                                <th>Alamat</th>
                                <th style="width: 10%;">Telepon</th>
                                <th style="width: 10%;">Staff</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="dapur-table-body">
                            @foreach($dapurList as $dapur)
                                <tr data-search="{{ strtolower($dapur->nama_dapur . ' ' . ($dapur->wilayah ?? '')) }}"
                                    data-status="{{ $dapur->status }}">
                                    <td>{{ $dapurList->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded bg-label-primary">
                                                    <i class="bx bx-buildings"></i>
                                                </span>
                                            </div>
                                            {{ $dapur->nama_dapur }}
                                        </div>
                                    </td>
                                    <td>{{ $dapur->wilayah ?? 'Wilayah belum diset' }}</td>
                                    <td>{{ $dapur->alamat ? Str::limit($dapur->alamat, 30) : '-' }}</td>
                                    <td>{{ $dapur->telepon ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-label-info" 
                                              title="KD: {{ $dapur->kepala_dapur_count }} | AG: {{ $dapur->admin_gudang_count }} | AGz: {{ $dapur->ahli_gizi_count }}"
                                              data-bs-toggle="tooltip" data-bs-placement="top">
                                            {{ $dapur->kepala_dapur_count + $dapur->admin_gudang_count + $dapur->ahli_gizi_count }} Staff
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $dapur->status === 'active' ? 'bg-label-success' : 'bg-label-danger' }}">
                                            {{ $dapur->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('superadmin.dapur.show', $dapur) }}" 
                                               class="btn btn-sm btn-outline-primary btn-icon action-btn"
                                               title="Detail" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            <a href="{{ route('superadmin.dapur.edit', $dapur) }}" 
                                               class="btn btn-sm btn-outline-info btn-icon action-btn"
                                               title="Edit" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            @if($dapur->kepala_dapur_count + $dapur->admin_gudang_count + $dapur->ahli_gizi_count == 0)
                                                <form method="POST" action="{{ route('superadmin.dapur.destroy', $dapur) }}" 
                                                      onsubmit="return confirm('Yakin ingin menghapus dapur {{ $dapur->nama_dapur }}?')">
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
                                                        title="Tidak bisa dihapus karena dapur memiliki staff" 
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
                @if($dapurList->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $dapurList->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-6">
                    @if(request()->hasAny(['search', 'filter_provinsi', 'filter_kabupaten', 'status']))
                        <i class="bx bx-search bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">Tidak ada hasil</h5>
                        <p class="text-muted mb-3">Tidak ada dapur yang sesuai dengan filter.</p>
                        <a href="{{ route('superadmin.dapur.index') }}" class="btn btn-outline-primary">
                            Reset Filter
                        </a>
                    @else
                        <i class="bx bx-buildings bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">Belum ada dapur</h5>
                        <p class="text-muted mb-3">Mulai dengan membuat dapur pertama.</p>
                        <a href="{{ route('superadmin.dapur.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Tambah Dapur Pertama
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
    const provinsiSelect = document.getElementById('filter_provinsi');
    const kabupatenSelect = document.getElementById('filter_kabupaten');
    const statusSelect = document.getElementById('status');
    const searchInput = document.getElementById('search');
    const tableBody = document.getElementById('dapur-table-body');
    const rows = tableBody.getElementsByTagName('tr');

    const currentProvinsi = '{{ request("filter_provinsi") }}';
    const currentKabupaten = '{{ request("filter_kabupaten") }}';

    const provinsiChoices = new Choices(provinsiSelect, {
        searchEnabled: true,
        searchPlaceholderValue: 'Cari provinsi...',
        noResultsText: 'Tidak ada hasil',
        itemSelectText: '',
        placeholder: true,
        placeholderValue: 'Semua Provinsi'
    });

    const kabupatenChoices = new Choices(kabupatenSelect, {
        searchEnabled: true,
        searchPlaceholderValue: 'Cari kabupaten/kota...',
        noResultsText: 'Tidak ada hasil',
        itemSelectText: '',
        placeholder: true,
        placeholderValue: 'Semua Kabupaten/Kota'
    });

    const statusChoices = new Choices(statusSelect, {
        searchEnabled: false,
        itemSelectText: '',
        placeholder: true,
        placeholderValue: 'Semua Status'
    });

    if (!currentProvinsi) {
        kabupatenChoices.disable();
    }

    loadProvinsi();

    async function loadProvinsi() {
        try {
            const response = await fetch('/api/wilayah/provinces');
            const result = await response.json();
            
            if (result.success && result.data) {
                const choices = result.data.map(province => ({
                    value: province.name,
                    label: province.name,
                    customProperties: { id: province.id },
                    selected: province.name === currentProvinsi
                }));

                provinsiChoices.setChoices(choices, 'value', 'label', true);

                if (currentProvinsi) {
                    const selectedChoice = choices.find(c => c.value === currentProvinsi);
                    if (selectedChoice && selectedChoice.customProperties) {
                        setTimeout(() => {
                            loadKabupaten(selectedChoice.customProperties.id);
                        }, 100);
                    }
                }
            }
        } catch (error) {
            console.error('Error loading provinces:', error);
        }
    }

    async function loadKabupaten(provinceId) {
        try {
            kabupatenChoices.clearStore();
            kabupatenChoices.disable();

            const response = await fetch(`/api/wilayah/regencies/${provinceId}`);
            const result = await response.json();
            
            if (result.success && result.data) {
                const choices = result.data.map(regency => ({
                    value: regency.name,
                    label: regency.name,
                    selected: regency.name === currentKabupaten
                }));

                kabupatenChoices.setChoices(choices, 'value', 'label', true);
                kabupatenChoices.enable();
            }
        } catch (error) {
            console.error('Error loading regencies:', error);
            kabupatenChoices.enable();
        }
    }

    provinsiSelect.addEventListener('change', function() {
        const selectedChoice = provinsiChoices.getValue();
        if (selectedChoice && selectedChoice.customProperties && selectedChoice.customProperties.id) {
            loadKabupaten(selectedChoice.customProperties.id);
        } else {
            kabupatenChoices.clearStore();
            kabupatenChoices.disable();
        }
    });

    // Client-side search filtering
    function filterTable() {
        const searchText = searchInput.value.toLowerCase();
        const statusValue = statusChoices.getValue(true);

        Array.from(rows).forEach(row => {
            const searchData = row.getAttribute('data-search');
            const statusData = row.getAttribute('data-status');

            const matchesSearch = searchText ? searchData.includes(searchText) : true;
            const matchesStatus = statusValue === '' || statusData === statusValue;

            row.style.display = matchesSearch && matchesStatus ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterTable);
    statusSelect.addEventListener('change', filterTable);

    // Initialize Bootstrap tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
});
</script>
@endsection