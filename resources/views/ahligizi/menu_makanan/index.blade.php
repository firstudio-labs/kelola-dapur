@extends("template_ahli_gizi.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a
                                href="{{ route("ahli-gizi.dashboard") }}"
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
                        href="{{ route("ahli-gizi.menu-makanan.create") }}"
                        class="btn btn-primary btn-sm"
                    >
                        <i class="bx bx-plus me-1"></i>
                        Tambah Menu
                    </a>
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
                    action="{{ route("ahli-gizi.menu-makanan.index") }}"
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
                                value="{{ request("search") }}"
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
                                {{ request("kategori") === "all" ? "selected" : "" }}
                            >
                                Semua Kategori
                            </option>
                            @foreach (App\Models\MenuMakanan::KATEGORI_OPTIONS as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    {{ request("kategori") === $value ? "selected" : "" }}
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
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
                                {{ request("status") === "all" ? "selected" : "" }}
                            >
                                Semua Status
                            </option>
                            <option
                                value="1"
                                {{ request("status") === "1" ? "selected" : "" }}
                            >
                                Active
                            </option>
                            <option
                                value="0"
                                {{ request("status") === "0" ? "selected" : "" }}
                            >
                                Inactive
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
                                {{ request("bahan_basah") === "all" ? "selected" : "" }}
                            >
                                Semua Menu
                            </option>
                            <option
                                value="1"
                                {{ request("bahan_basah") === "1" ? "selected" : "" }}
                            >
                                Ada Bahan Basah
                            </option>
                            <option
                                value="0"
                                {{ request("bahan_basah") === "0" ? "selected" : "" }}
                            >
                                Tidak Ada Bahan Basah
                            </option>
                        </select>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                        @if (request()->hasAny(["search", "status", "kategori", "bahan_basah"]))
                            <a
                                href="{{ route("ahli-gizi.menu-makanan.index") }}"
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
                        <div class="col-md-2 text-center">
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
                        <div class="col-md-2 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-success me-2">
                                    <i class="bx bx-check-circle"></i>
                                </span>
                                <div>
                                    <small class="text-muted">Active</small>
                                    <h6 class="mb-0">
                                        {{ $menus->where("is_active", true)->count() }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-danger me-2">
                                    <i class="bx bx-x-circle"></i>
                                </span>
                                <div>
                                    <small class="text-muted">Inactive</small>
                                    <h6 class="mb-0">
                                        {{ $menus->where("is_active", false)->count() }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-info me-2">
                                    <i class="bx bx-droplet"></i>
                                </span>
                                <div>
                                    <small class="text-muted">
                                        Bahan Basah
                                    </small>
                                    <h6 class="mb-0">
                                        {{
                                            $menus
                                                ->filter(function ($menu) {
                                                    return $menu->bahanMenu->where("is_bahan_basah", true)->count() > 0;
                                                })
                                                ->count()
                                        }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-warning me-2">
                                    <i class="bx bx-category"></i>
                                </span>
                                <div>
                                    <small class="text-muted">Kategori</small>
                                    <h6 class="mb-0">
                                        {{ $menus->groupBy("kategori")->count() }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Menu List -->
        <div class="card">
            <div class="card-body">
                @if ($menus->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Gambar</th>
                                    <th>Nama Menu</th>
                                    <th>Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Bahan</th>
                                    <th>Bahan Basah</th>
                                    <th>Status</th>
                                    <th>Dibuat Oleh Dapur</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="menu-table-body">
                                @foreach ($menus as $menu)
                                    @php
                                        $hasBahanBasah = $menu->bahanMenu->where("is_bahan_basah", true)->count() > 0;
                                        $kategoriClasses = [
                                            "Karbohidrat" => "bg-label-primary",
                                            "Lauk" => "bg-label-success",
                                            "Sayur" => "bg-label-info",
                                            "Tambahan" => "bg-label-warning",
                                        ];
                                    @endphp

                                    <tr
                                        data-search="{{ strtolower($menu->nama_menu . " " . $menu->deskripsi . " " . $menu->kategori) }}"
                                        data-status="{{ $menu->is_active ? "1" : "0" }}"
                                        data-kategori="{{ $menu->kategori }}"
                                        data-bahan-basah="{{ $hasBahanBasah ? "1" : "0" }}"
                                    >
                                        <td>
                                            <img
                                                src="{{ $menu->gambar_url }}"
                                                alt="{{ $menu->nama_menu }}"
                                                class="rounded"
                                                style="
                                                    width: 60px;
                                                    height: 40px;
                                                    object-fit: cover;
                                                "
                                            />
                                        </td>
                                        <td>
                                            <div class="fw-semibold">
                                                {{ $menu->nama_menu }}
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $kategoriClasses[$menu->kategori] ?? "bg-label-secondary" }}"
                                            >
                                                {{ $menu->kategori }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                {{ Str::limit($menu->deskripsi, 40) ?: "Tidak ada deskripsi" }}
                                            </span>
                                        </td>
                                        <td>
                                            <div
                                                class="d-flex align-items-center"
                                            >
                                                <i
                                                    class="bx bx-package me-1"
                                                ></i>
                                                <span class="fw-semibold">
                                                    {{ $menu->bahanMenu->count() }}
                                                </span>
                                                <small class="text-muted ms-1">
                                                    bahan
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($hasBahanBasah)
                                                <div
                                                    class="d-flex align-items-center"
                                                >
                                                    <span
                                                        class="badge bg-label-info"
                                                    >
                                                        <i
                                                            class="bx bx-droplet me-1"
                                                        ></i>
                                                        {{ $menu->bahanMenu->where("is_bahan_basah", true)->count() }}
                                                    </span>
                                                    <small
                                                        class="text-muted ms-1"
                                                    >
                                                        +7%
                                                    </small>
                                                </div>
                                            @else
                                                <span class="text-muted">
                                                    -
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-label-{{ $menu->is_active ? "success" : "danger" }}"
                                            >
                                                {{ $menu->is_active ? "Active" : "Inactive" }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $menu->createdByDapur->nama_dapur ?? "Tidak ada dapur terkait" }}
                                            </small>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $menu->created_at->format("d M Y") }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a
                                                    href="{{ route("ahli-gizi.menu-makanan.show", $menu) }}"
                                                    class="btn btn-sm btn-outline-primary action-btn"
                                                    data-bs-toggle="tooltip"
                                                    title="Lihat Detail"
                                                >
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                <a
                                                    href="{{ route("ahli-gizi.menu-makanan.edit", $menu) }}"
                                                    class="btn btn-sm btn-outline-info action-btn"
                                                    data-bs-toggle="tooltip"
                                                    title="Edit Menu"
                                                >
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <form
                                                    action="{{ route("ahli-gizi.menu-makanan.toggle-status", $menu) }}"
                                                    method="POST"
                                                    class="d-inline"
                                                >
                                                    @csrf
                                                    @method("PATCH")
                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-{{ $menu->is_active ? "danger" : "success" }} action-btn"
                                                        data-bs-toggle="tooltip"
                                                        title="{{ $menu->is_active ? "Nonaktifkan" : "Aktifkan" }}"
                                                    >
                                                        <i
                                                            class="bx {{ $menu->is_active ? "bx-block" : "bx-check-circle" }}"
                                                        ></i>
                                                    </button>
                                                </form>
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
                            {{ $menus->appends(request()->query())->links("vendor.pagination.bootstrap-5") }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-6">
                        @if (request()->hasAny(["search", "status", "kategori", "bahan_basah"]))
                            <i class="bx bx-search bx-lg text-muted mb-3"></i>
                            <h5 class="mb-1">Tidak ada hasil</h5>
                            <p class="text-muted mb-3">
                                Tidak ada menu yang sesuai dengan filter.
                            </p>
                            <a
                                href="{{ route("ahli-gizi.menu-makanan.index") }}"
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
                                href="{{ route("ahli-gizi.menu-makanan.create") }}"
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
            const bahanBasahFilter =
                document.getElementById('bahan-basah-filter');
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
