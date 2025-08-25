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
                                href="{{ route('superadmin.dashboard') }}"
                                class="text-muted me-2"
                            >
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Kelola Menu Makanan</span>
                        </nav>
                        <h4 class="mb-1">Kelola Menu Makanan</h4>
                        <p class="mb-0 text-muted">
                            Kelola semua menu makanan dalam sistem
                        </p>
                    </div>
                    <a
                        href="{{ route('superadmin.menu-makanan.create') }}"
                        class="btn btn-primary btn-sm"
                    >
                        <i class="bx bx-plus me-1"></i>
                        Tambah Menu
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div
                class="alert alert-success alert-dismissible mb-4"
                role="alert"
            >
                {{ session('success') }}
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"
                ></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                {{ session('error') }}
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
                    action="{{ route('superadmin.menu-makanan.index') }}"
                    class="row g-3"
                >
                    <div class="col-md-3">
                        <label for="search-input" class="form-label">
                            Cari Menu
                        </label>
                        <div class="input-group">
                            <input
                                type="text"
                                name="search"
                                id="search-input"
                                value="{{ request('search') }}"
                                class="form-control"
                                placeholder="Cari nama menu atau deskripsi..."
                            />
                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                onclick="document.getElementById('search-input').value='';this.form.submit();"
                            >
                                <i class="bx bx-x"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="status-filter" class="form-label">
                            Filter Status
                        </label>
                        <select
                            name="status"
                            id="status-filter"
                            class="choices-select form-select"
                        >
                            <option
                                value="all"
                                {{ request('status') === 'all' ? 'selected' : '' }}
                            >
                                Semua Status
                            </option>
                            <option
                                value="1"
                                {{ request('status') === '1' ? 'selected' : '' }}
                            >
                                Active
                            </option>
                            <option
                                value="0"
                                {{ request('status') === '0' ? 'selected' : '' }}
                            >
                                Inactive
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="kategori-filter" class="form-label">
                            Filter Kategori
                        </label>
                        <select
                            name="kategori"
                            id="kategori-filter"
                            class="choices-select form-select"
                        >
                            <option
                                value="all"
                                {{ request('kategori') === 'all' ? 'selected' : '' }}
                            >
                                Semua Kategori
                            </option>
                            <option
                                value="Karbohidrat"
                                {{ request('kategori') === 'Karbohidrat' ? 'selected' : '' }}
                            >
                                Karbohidrat
                            </option>
                            <option
                                value="Lauk"
                                {{ request('kategori') === 'Lauk' ? 'selected' : '' }}
                            >
                                Lauk
                            </option>
                            <option
                                value="Sayur"
                                {{ request('kategori') === 'Sayur' ? 'selected' : '' }}
                            >
                                Sayur
                            </option>
                            <option
                                value="Tambahan"
                                {{ request('kategori') === 'Tambahan' ? 'selected' : '' }}
                            >
                                Tambahan
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="bahan-basah-filter" class="form-label">
                            Filter Bahan Basah
                        </label>
                        <select
                            name="bahan_basah"
                            id="bahan-basah-filter"
                            class="choices-select form-select"
                        >
                            <option
                                value="all"
                                {{ request('bahan_basah') === 'all' ? 'selected' : '' }}
                            >
                                Semua Menu
                            </option>
                            <option
                                value="1"
                                {{ request('bahan_basah') === '1' ? 'selected' : '' }}
                            >
                                Memiliki Bahan Basah
                            </option>
                            <option
                                value="0"
                                {{ request('bahan_basah') === '0' ? 'selected' : '' }}
                            >
                                Tanpa Bahan Basah
                            </option>
                        </select>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                        @if (request()->hasAny(['search', 'status', 'kategori', 'bahan_basah']))
                            <a
                                href="{{ route('superadmin.menu-makanan.index') }}"
                                class="btn btn-outline-secondary"
                            >
                                Reset Filter
                            </a>
                        @endif
                        <button type="submit" class="btn btn-primary">
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Section -->
        @if ($menus->total() > 0)
            <div class="card mb-4">
                <div class="card-body py-2 px-4">
                    <div class="row justify-content-center g-3">
                        <div class="col-md-4 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-primary me-2">
                                    <i class="bx bx-restaurant"></i>
                                </span>
                                <div>
                                    <small class="text-muted">Total Menu</small>
                                    <h6 class="mb-0">{{ $menus->total() }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Menu List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Daftar Menu Makanan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Gambar</th>
                                    <th>Nama Menu</th>
                                    <th>Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Dibuat Oleh</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="menu-table-body">
                                @foreach ($menus as $index => $menu)
                                    <tr
                                        data-search="{{ strtolower($menu->nama_menu . ' ' . ($menu->deskripsi ?? '') . ' ' . $menu->kategori) }}"
                                        data-status="{{ $menu->is_active }}"
                                        data-kategori="{{ $menu->kategori }}"
                                        data-bahan-basah="{{ $menu->bahanMenu->where('is_bahan_basah', true)->isNotEmpty() ? '1' : '0' }}"
                                    >
                                        <td>{{ $menus->firstItem() + $index }}</td>
                                        <td>
                                            <img
                                                src="{{ $menu->gambar_url }}"
                                                alt="{{ $menu->nama_menu }}"
                                                class="rounded"
                                                style="width: 50px; height: 50px; object-fit: cover;"
                                                onerror="this.src='{{ asset('images/menu/default-menu.jpg') }}'"
                                            />
                                        </td>
                                        <td>{{ $menu->nama_menu }}</td>
                                        <td>{{ $menu->kategori }}</td>
                                        <td>{{ Str::limit($menu->deskripsi ?? '-', 50) }}</td>
                                        <td>
                                            <span
                                                class="badge {{ $menu->is_active ? 'bg-label-success' : 'bg-label-danger' }}"
                                            >
                                                {{ $menu->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $menu->createdByDapur->nama_dapur ?? 'Tidak ada dapur' }}</td>
                                        <td>{{ $menu->created_at->format('d M Y') }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a
                                                    href="{{ route('superadmin.menu-makanan.show', $menu) }}"
                                                    class="btn btn-sm btn-outline-primary action-btn"
                                                    data-bs-toggle="tooltip"
                                                    title="Lihat Detail"
                                                >
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                <a
                                                    href="{{ route('superadmin.menu-makanan.edit', $menu) }}"
                                                    class="btn btn-sm btn-outline-primary action-btn"
                                                    data-bs-toggle="tooltip"
                                                    title="Edit"
                                                >
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <form
                                                    action="{{ route('superadmin.menu-makanan.toggleStatus', $menu) }}"
                                                    method="POST"
                                                    style="display: inline;"
                                                >
                                                    @csrf
                                                    @method('PATCH')
                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-{{ $menu->is_active ? 'danger' : 'success' }} action-btn"
                                                        data-bs-toggle="tooltip"
                                                        title="{{ $menu->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                                    >
                                                        <i class="bx bx-{{ $menu->is_active ? 'block' : 'check' }}"></i>
                                                    </button>
                                                </form>
                                                {{-- <form
                                                    action="{{ route('superadmin.menu-makanan.destroy', $menu) }}"
                                                    method="POST"
                                                    style="display: inline;"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu ini?');"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-danger action-btn"
                                                        data-bs-toggle="tooltip"
                                                        title="Hapus"
                                                    >
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form> --}}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($menus->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $menus->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-6">
                        @if (request()->hasAny(['search', 'status', 'kategori', 'bahan_basah']))
                            <i class="bx bx-search bx-lg text-muted mb-3"></i>
                            <h5 class="mb-1">Tidak ada hasil</h5>
                            <p class="text-muted mb-3">
                                Tidak ada menu yang sesuai dengan filter.
                            </p>
                            <a
                                href="{{ route('superadmin.menu-makanan.index') }}"
                                class="btn btn-outline-primary"
                            >
                                Reset Filter
                            </a>
                        @else
                            <i
                                class="bx bx-restaurant bx-lg text-muted mb-3"
                            ></i>
                            <h5 class="mb-1">Belum ada menu</h5>
                            <p class="text-muted mb-3">
                                Mulai dengan membuat menu pertama.
                            </p>
                            <a
                                href="{{ route('superadmin.menu-makanan.create') }}"
                                class="btn btn-primary"
                            >
                                <i class="bx bx-plus me-1"></i>
                                Tambah Menu Pertama
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Choices.js CSS -->
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css"
        />

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
            .choices[data-type*='select-one'] .choices__inner {
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
                transition:
                    transform 0.2s ease,
                    opacity 0.2s ease;
            }
            .action-btn:hover:not(.disabled) {
                transform: scale(1.1);
                opacity: 0.9;
            }
            .table th {
                background-color: #f8f9fa;
                font-weight: 600;
                border-top: none;
                white-space: nowrap;
            }
            .badge {
                font-size: 0.75rem;
            }
            .badge i {
                font-size: 0.7rem;
            }
        </style>

        <!-- Choices.js JS -->
        <script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>

        <!-- JavaScript for Filters, Client-Side Search, and Tooltips -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const statusFilter = document.getElementById('status-filter');
                const kategoriFilter = document.getElementById('kategori-filter');
                const bahanBasahFilter = document.getElementById('bahan-basah-filter');
                const searchInput = document.getElementById('search-input');
                const tableBody = document.getElementById('menu-table-body');
                const rows = tableBody ? tableBody.getElementsByTagName('tr') : [];

                const statusChoices = new Choices(statusFilter, {
                    searchEnabled: false,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: 'Semua Status',
                });

                const kategoriChoices = new Choices(kategoriFilter, {
                    searchEnabled: false,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: 'Semua Kategori',
                });

                const bahanBasahChoices = new Choices(bahanBasahFilter, {
                    searchEnabled: false,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: 'Semua Menu',
                });

                function filterTable() {
                    const searchText = searchInput.value.toLowerCase();
                    const statusValue = statusChoices.getValue(true);
                    const kategoriValue = kategoriChoices.getValue(true);
                    const bahanBasahValue = bahanBasahChoices.getValue(true);

                    Array.from(rows).forEach((row) => {
                        const searchData = row.getAttribute('data-search');
                        const statusData = row.getAttribute('data-status');
                        const kategoriData = row.getAttribute('data-kategori');
                        const bahanBasahData = row.getAttribute('data-bahan-basah');

                        const matchesSearch = searchText
                            ? searchData.includes(searchText)
                            : true;
                        const matchesStatus =
                            statusValue === 'all' || statusData === statusValue;
                        const matchesKategori =
                            kategoriValue === 'all' ||
                            kategoriData === kategoriValue;
                        const matchesBahanBasah =
                            bahanBasahValue === 'all' ||
                            bahanBasahData === bahanBasahValue;

                        row.style.display =
                            matchesSearch &&
                            matchesStatus &&
                            matchesKategori &&
                            matchesBahanBasah
                                ? ''
                                : 'none';
                    });
                }

                searchInput.addEventListener('input', filterTable);
                statusFilter.addEventListener('change', filterTable);
                kategoriFilter.addEventListener('change', filterTable);
                bahanBasahFilter.addEventListener('change', filterTable);

                // Initialize Bootstrap tooltips
                const tooltipTriggerList = document.querySelectorAll(
                    '[data-bs-toggle="tooltip"]',
                );
                const tooltipList = [...tooltipTriggerList].map(
                    (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl),
                );
            });
        </script>
    @endsection