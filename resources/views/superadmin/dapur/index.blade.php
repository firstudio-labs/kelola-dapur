@extends("template_admin.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a
                                href="{{ route("superadmin.dashboard") }}"
                                class="text-muted me-2"
                            >
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Kelola Dapur</span>
                        </nav>
                        <h4 class="mb-1">Kelola Dapur</h4>
                        <p class="mb-0 text-muted">
                            Kelola semua dapur dalam sistem
                        </p>
                    </div>
                    {{--
                        <a
                        href="{{ route("superadmin.dapur.create") }}"
                        class="btn btn-primary btn-sm"
                        >
                        <i class="bx bx-plus me-1"></i>
                        Tambah Dapur
                        </a>
                    --}}
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session("success"))
            <div
                class="alert alert-success alert-dismissible mb-4"
                role="alert"
            >
                {{ session("success") }}
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"
                ></button>
            </div>
        @endif

        @if (session("error"))
            <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                {{ session("error") }}
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"
                ></button>
            </div>
        @endif

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <form
                    method="GET"
                    action="{{ route("superadmin.dapur.index") }}"
                    class="row g-3"
                >
                    <div class="col-md-3">
                        <label for="search" class="form-label">Pencarian</label>
                        <div class="input-group">
                            <input
                                type="text"
                                name="search"
                                id="search"
                                value="{{ request("search") }}"
                                class="form-control"
                                placeholder="Cari nama dapur, alamat, wilayah..."
                            />
                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                onclick="document.getElementById('search').value='';this.closest('form').submit();"
                            >
                                <i class="bx bx-x"></i>
                            </button>
                        </div>
                    </div>
                    {{--
                        <div class="col-md-3">
                        <label for="filter_provinsi" class="form-label">
                        Provinsi
                        </label>
                        <select
                        name="filter_provinsi"
                        id="filter_provinsi"
                        class="form-select"
                        >
                        <option value="">Semua Provinsi</option>
                        </select>
                        </div>
                        <div class="col-md-3">
                        <label for="filter_kabupaten" class="form-label">
                        Kabupaten/Kota
                        </label>
                        <select
                        name="filter_kabupaten"
                        id="filter_kabupaten"
                        disabled
                        class="form-select"
                        >
                        <option value="">Semua Kabupaten/Kota</option>
                        </select>
                        </div>
                        <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option
                        value="active"
                        {{ request("status") === "active" ? "selected" : "" }}
                        >
                        Aktif
                        </option>
                        <option
                        value="inactive"
                        {{ request("status") === "inactive" ? "selected" : "" }}
                        >
                        Tidak Aktif
                        </option>
                        </select>
                        </div>
                    --}}
                    {{--
                        <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                        @if (request()->hasAny(["search", "filter_provinsi", "filter_kabupaten", "status"]))
                        <a
                        href="{{ route("superadmin.dapur.index") }}"
                        class="btn btn-outline-secondary"
                        >
                        Reset Filter
                        </a>
                        @endif
                        
                        <button type="submit" class="btn btn-primary">
                        Terapkan Filter
                        </button>
                        </div>
                    --}}
                </form>
            </div>
        </div>

        <!-- Statistics Section -->
        @if ($dapurList->total() > 0)
            <div class="card mb-4">
                <div class="card-body py-2 px-4">
                    <div class="row justify-content-center g-3">
                        <div class="col-md-3 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-primary me-2">
                                    <i class="bx bx-buildings"></i>
                                </span>
                                <div>
                                    <small class="text-muted">
                                        Total Dapur
                                    </small>
                                    <h6 class="mb-0">
                                        {{ $dapurList->total() }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-success me-2">
                                    <i class="bx bx-check-circle"></i>
                                </span>
                                <div>
                                    <small class="text-muted">
                                        Dapur Aktif
                                    </small>
                                    <h6 class="mb-0">
                                        {{ $dapurList->where("status", "active")->count() }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-danger me-2">
                                    <i class="bx bx-x-circle"></i>
                                </span>
                                <div>
                                    <small class="text-muted">
                                        Dapur Tidak Aktif
                                    </small>
                                    <h6 class="mb-0">
                                        {{ $dapurList->where("status", "inactive")->count() }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-info me-2">
                                    <i class="bx bx-user"></i>
                                </span>
                                <div>
                                    <small class="text-muted">
                                        Total Staff
                                    </small>
                                    <h6 class="mb-0">
                                        {{ $dapurList->sum(function ($dapur) { return $dapur->kepala_dapur_count + $dapur->admin_gudang_count + $dapur->ahli_gizi_count;}) }}
                                    </h6>
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
                @if ($dapurList->count() > 0)
                    <div class="table-responsive">
                        <table
                            class="table table-bordered table-striped table-hover"
                        >
                            <thead>
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th style="width: 20%">Nama Dapur</th>
                                    <th>Wilayah</th>
                                    <th>Alamat</th>
                                    <th style="width: 10%">Telepon</th>
                                    <th style="width: 10%">Staff</th>
                                    <th style="width: 10%">Status</th>
                                    <th style="width: 15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="dapur-table-body">
                                @foreach ($dapurList as $dapur)
                                    <tr
                                        data-search="{{ strtolower($dapur->nama_dapur . " " . $dapur->getFullWilayahAttribute() . " " . $dapur->alamat) }}"
                                        data-status="{{ $dapur->status }}"
                                        data-provinsi="{{ $dapur->province_name }}"
                                        data-kabupaten="{{ $dapur->regency_name }}"
                                    >
                                        <td>
                                            {{ $dapurList->firstItem() + $loop->index }}
                                        </td>
                                        <td>
                                            <div
                                                class="d-flex align-items-center"
                                            >
                                                <div
                                                    class="avatar flex-shrink-0 me-3"
                                                >
                                                    <span
                                                        class="avatar-initial rounded bg-label-primary"
                                                    >
                                                        <i
                                                            class="bx bx-buildings"
                                                        ></i>
                                                    </span>
                                                </div>
                                                {{ $dapur->nama_dapur }}
                                            </div>
                                        </td>
                                        <td>
                                            @if ($dapur->getFullWilayahAttribute())
                                                {{ $dapur->getFullWilayahAttribute() }}
                                            @else
                                                <span class="text-muted">
                                                    Wilayah belum diset
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $dapur->alamat ? Str::limit($dapur->alamat, 30) : "-" }}
                                        </td>
                                        <td>{{ $dapur->telepon ?? "-" }}</td>
                                        <td>
                                            <span
                                                class="badge bg-label-info"
                                                title="KD: {{ $dapur->kepala_dapur_count }} | AG: {{ $dapur->admin_gudang_count }} | AGz: {{ $dapur->ahli_gizi_count }}"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                            >
                                                {{ $dapur->kepala_dapur_count + $dapur->admin_gudang_count + $dapur->ahli_gizi_count }}
                                                Staff
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $dapur->status === "active" ? "bg-label-success" : "bg-label-danger" }}"
                                            >
                                                {{ $dapur->status === "active" ? "Aktif" : "Tidak Aktif" }}
                                            </span>
                                        </td>
                                        <td>
                                            <div
                                                class="d-flex justify-content-center gap-1"
                                            >
                                                <a
                                                    href="{{ route("superadmin.dapur.show", $dapur) }}"
                                                    class="btn btn-sm btn-outline-primary action-btn"
                                                    title="Detail"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                >
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                <a
                                                    href="{{ route("superadmin.dapur.edit", $dapur) }}"
                                                    class="btn btn-sm btn-outline-info action-btn"
                                                    title="Edit"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                >
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                @if ($dapur->kepala_dapur_count + $dapur->admin_gudang_count + $dapur->ahli_gizi_count == 0)
                                                    <form
                                                        method="POST"
                                                        action="{{ route("superadmin.dapur.destroy", $dapur) }}"
                                                        onsubmit="return confirm('Yakin ingin menghapus dapur {{ $dapur->nama_dapur }}?')"
                                                    >
                                                        @csrf
                                                        @method("DELETE")
                                                        <button
                                                            type="submit"
                                                            class="btn btn-sm btn-outline-danger action-btn"
                                                            title="Hapus"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                        >
                                                            <i
                                                                class="bx bx-trash"
                                                            ></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button
                                                        class="btn btn-sm btn-outline-secondary action-btn disabled"
                                                        title="Tidak bisa dihapus karena dapur memiliki staff"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                    >
                                                        <i
                                                            class="bx bx-trash"
                                                        ></i>
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
                    @if ($dapurList->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $dapurList->appends(request()->query())->links("vendor.pagination.bootstrap-5") }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        @if (request()->hasAny(["search", "filter_provinsi", "filter_kabupaten", "status"]))
                            <i class="bx bx-search bx-lg text-muted mb-3"></i>
                            <h5 class="mb-1">Tidak ada hasil</h5>
                            <p class="text-muted mb-3">
                                Tidak ada dapur yang sesuai dengan filter.
                            </p>
                            <a
                                href="{{ route("superadmin.dapur.index") }}"
                                class="btn btn-outline-primary"
                            >
                                Reset Filter
                            </a>
                        @else
                            <i
                                class="bx bx-buildings bx-lg text-muted mb-3"
                            ></i>
                            <h5 class="mb-1">Belum ada dapur</h5>
                            <p class="text-muted mb-3">
                                Mulai dengan membuat dapur pertama.
                            </p>
                            <a
                                href="{{ route("superadmin.dapur.create") }}"
                                class="btn btn-primary"
                            >
                                <i class="bx bx-plus me-1"></i>
                                Tambah Dapur Pertama
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Custom Styling -->
    <style>
        .form-select:disabled {
            background-color: #f8f9fa;
            opacity: 0.65;
            cursor: not-allowed;
        }

        .action-btn {
            min-width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.2s ease;
        }

        .action-btn:hover:not(.disabled) {
            opacity: 0.8;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
    </style>

    <!-- JavaScript for Search and Filters -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Elements
            const provinsiSelect = document.getElementById('filter_provinsi');
            const kabupatenSelect = document.getElementById('filter_kabupaten');
            const searchInput = document.getElementById('search');
            const statusSelect = document.getElementById('status');
            const tableBody = document.getElementById('dapur-table-body');
            const form = document.querySelector('form[method="GET"]');

            // Current values from request
            const currentProvinsi = '{{ request("filter_provinsi") }}';
            const currentKabupaten = '{{ request("filter_kabupaten") }}';

            // Loading state management
            function setLoading(select, loading) {
                if (loading) {
                    const placeholder =
                        select.querySelector('option[value=""]').textContent;
                    select.innerHTML = `<option value="">${placeholder} (memuat...)</option>`;
                    select.disabled = true;
                } else {
                    select.disabled = false;
                }
            }

            // Populate select options
            function populateSelect(select, data, selectedValue = '') {
                const placeholder = select.dataset.placeholder || 'Pilih...';
                let options = `<option value="">${placeholder}</option>`;

                data.forEach((item) => {
                    const selected =
                        item.name === selectedValue ? 'selected' : '';
                    options += `<option value="${item.name}" data-code="${item.id}" ${selected}>${item.name}</option>`;
                });

                select.innerHTML = options;
            }

            // Load provinces
            async function loadProvinsi() {
                try {
                    setLoading(provinsiSelect, true);

                    const response = await fetch('/api/wilayah/provinces', {
                        headers: { Accept: 'application/json' },
                    });

                    if (!response.ok)
                        throw new Error(`HTTP ${response.status}`);

                    const result = await response.json();
                    if (!result.success || !Array.isArray(result.data)) {
                        throw new Error(
                            result.message || 'Invalid data format',
                        );
                    }

                    populateSelect(
                        provinsiSelect,
                        result.data,
                        currentProvinsi,
                    );
                    setLoading(provinsiSelect, false);

                    // Load kabupaten if province is selected
                    if (currentProvinsi) {
                        const selectedOption = provinsiSelect.querySelector(
                            `option[value="${currentProvinsi}"]`,
                        );
                        if (selectedOption && selectedOption.dataset.code) {
                            await loadKabupaten(selectedOption.dataset.code);
                        }
                    }
                } catch (error) {
                    console.error('Error loading provinces:', error);
                    provinsiSelect.innerHTML =
                        '<option value="">Error loading provinces</option>';
                    setLoading(provinsiSelect, false);
                }
            }

            // Load regencies
            async function loadKabupaten(provinceId) {
                if (!provinceId) return;

                try {
                    setLoading(kabupatenSelect, true);

                    const response = await fetch(
                        `/api/wilayah/regencies/${provinceId}`,
                        {
                            headers: { Accept: 'application/json' },
                        },
                    );

                    if (!response.ok)
                        throw new Error(`HTTP ${response.status}`);

                    const result = await response.json();
                    if (!result.success || !Array.isArray(result.data)) {
                        throw new Error(
                            result.message || 'Invalid data format',
                        );
                    }

                    populateSelect(
                        kabupatenSelect,
                        result.data,
                        currentKabupaten,
                    );
                    setLoading(kabupatenSelect, false);
                } catch (error) {
                    console.error('Error loading regencies:', error);
                    kabupatenSelect.innerHTML =
                        '<option value="">Error loading kabupaten</option>';
                    setLoading(kabupatenSelect, false);
                }
            }

            // Real-time search functionality
            let searchTimeout;
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    filterTableRows();
                }, 300);
            });

            // Filter table rows based on search and status
            function filterTableRows() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                const statusValue = statusSelect.value;
                const rows = tableBody.querySelectorAll('tr');

                let visibleRows = 0;

                rows.forEach((row) => {
                    const searchData = row.dataset.search || '';
                    const statusData = row.dataset.status || '';

                    const matchesSearch =
                        !searchTerm || searchData.includes(searchTerm);
                    const matchesStatus =
                        !statusValue || statusData === statusValue;

                    const shouldShow = matchesSearch && matchesStatus;
                    row.style.display = shouldShow ? '' : 'none';

                    if (shouldShow) visibleRows++;
                });

                // Update row numbers for visible rows
                let visibleIndex = 1;
                rows.forEach((row) => {
                    if (row.style.display !== 'none') {
                        const firstCell = row.querySelector('td:first-child');
                        if (firstCell) {
                            firstCell.textContent = visibleIndex++;
                        }
                    }
                });
            }

            // Status filter change
            statusSelect.addEventListener('change', filterTableRows);

            // Province change handler
            provinsiSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const provinceCode = selectedOption.dataset.code;

                // Reset kabupaten
                kabupatenSelect.innerHTML =
                    '<option value="">Semua Kabupaten/Kota</option>';
                kabupatenSelect.disabled = !provinceCode;

                if (provinceCode) {
                    loadKabupaten(provinceCode);
                }
            });

            // Store original placeholder text
            provinsiSelect.dataset.placeholder = 'Semua Provinsi';
            kabupatenSelect.dataset.placeholder = 'Semua Kabupaten/Kota';

            // Initialize
            loadProvinsi();

            // Initialize tooltips
            const tooltipTriggerList = document.querySelectorAll(
                '[data-bs-toggle="tooltip"]',
            );
            const tooltipList = [...tooltipTriggerList].map(
                (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl),
            );

            // Disable kabupaten initially if no province selected
            if (!currentProvinsi) {
                kabupatenSelect.disabled = true;
            }
        });
    </script>
@endsection
