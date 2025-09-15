@extends("template_kepala_dapur.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a
                                href="{{ route("dashboard") }}"
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
                    action="{{ route("kepala-dapur.menu-makanan.index") }}"
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
                                placeholder="Cari nama menu..."
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
                        <label for="dapur-filter" class="form-label">
                            Filter Dapur
                        </label>
                        <select
                            name="dapur"
                            id="dapur-filter"
                            class="choices-select form-select"
                        >
                            <option
                                value="all"
                                {{ request("dapur") === "all" ? "selected" : "" }}
                            >
                                Semua Dapur
                            </option>

                            @foreach ($dapurs as $dapur)
                                <option
                                    value="{{ $dapur->id_dapur }}"
                                    {{ request("dapur") == $dapur->id_dapur ? "selected" : "" }}
                                >
                                    {{ $dapur->nama_dapur }}
                                </option>
                            @endforeach
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
                                {{ request("kategori") === "all" ? "selected" : "" }}
                            >
                                Semua Kategori
                            </option>
                            <option
                                value="Karbohidrat"
                                {{ request("kategori") === "Karbohidrat" ? "selected" : "" }}
                            >
                                Karbohidrat
                            </option>
                            <option
                                value="Lauk"
                                {{ request("kategori") === "Lauk" ? "selected" : "" }}
                            >
                                Lauk
                            </option>
                            <option
                                value="Sayur"
                                {{ request("kategori") === "Sayur" ? "selected" : "" }}
                            >
                                Sayur
                            </option>
                            <option
                                value="Tambahan"
                                {{ request("kategori") === "Tambahan" ? "selected" : "" }}
                            >
                                Tambahan
                            </option>
                        </select>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                        @if (request()->hasAny(["search", "status", "dapur", "kategori"]))
                            <a
                                href="{{ route("kepala-dapur.menu-makanan.index") }}"
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
                        <div class="col-md-2 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-info me-2">
                                    <i class="bx bx-bowl-hot"></i>
                                </span>
                                <div>
                                    <small class="text-muted">
                                        Karbohidrat
                                    </small>
                                    <h6 class="mb-0">
                                        {{ $kategoriStats["Karbohidrat"] ?? 0 }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-warning me-2">
                                    <i class="bx bx-dish"></i>
                                </span>
                                <div>
                                    <small class="text-muted">Lauk</small>
                                    <h6 class="mb-0">
                                        {{ $kategoriStats["Lauk"] ?? 0 }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-success me-2">
                                    <i class="bx bx-leaf"></i>
                                </span>
                                <div>
                                    <small class="text-muted">Sayur</small>
                                    <h6 class="mb-0">
                                        {{ $kategoriStats["Sayur"] ?? 0 }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Menu Cards -->
        <div class="card">
            <div class="card-body">
                @if ($menus->isNotEmpty())
                    <div class="row g-4" id="menu-cards-container">
                        @foreach ($menus as $menu)
                            <div
                                class="col-lg-4 col-md-6 menu-card-item"
                                data-search="{{ strtolower($menu->nama_menu . " " . $menu->deskripsi . " " . $menu->kategori) }}"
                                data-status="{{ $menu->is_active ? "1" : "0" }}"
                                data-kategori="{{ $menu->kategori }}"
                                data-dapur="{{ $menu->created_by_dapur_id ?? "unknown" }}"
                            >
                                <div
                                    class="card menu-card h-100 position-relative"
                                >
                                    <!-- Status Badge -->
                                    <div
                                        class="position-absolute top-0 end-0 m-2"
                                        style="z-index: 2"
                                    >
                                        <span
                                            class="badge bg-{{ $menu->is_active ? "success" : "danger" }}"
                                        >
                                            {{ $menu->is_active ? "Active" : "Inactive" }}
                                        </span>
                                    </div>

                                    <!-- Kategori Badge -->
                                    <div
                                        class="position-absolute top-0 start-0 m-2"
                                        style="z-index: 2"
                                    >
                                        <span
                                            class="badge {{ $menu->getKategoriBadgeClass() }}"
                                        >
                                            {{ $menu->kategori }}
                                        </span>
                                    </div>

                                    <!-- Menu Image -->
                                    <div class="card-img-top-wrapper">
                                        <img
                                            src="{{ $menu->gambar_url }}"
                                            alt="{{ $menu->nama_menu }}"
                                            class="card-img-top menu-image"
                                        />
                                    </div>

                                    <div class="card-body d-flex flex-column">
                                        <!-- Menu Name -->
                                        <h5
                                            class="card-title mb-2 text-truncate"
                                            title="{{ $menu->nama_menu }}"
                                        >
                                            {{ $menu->nama_menu }}
                                        </h5>

                                        <!-- Menu Description -->
                                        <p
                                            class="card-text text-muted small mb-2 flex-grow-1"
                                        >
                                            {{ Str::limit($menu->deskripsi, 80) }}
                                        </p>

                                        <!-- Menu Info -->
                                        <div class="mb-3">
                                            <div
                                                class="d-flex justify-content-between align-items-center mb-2"
                                            >
                                                <span class="text-muted small">
                                                    <i
                                                        class="bx bx-package me-1"
                                                    ></i>
                                                    {{ $menu->bahanMenu->count() }}
                                                    Bahan
                                                </span>
                                                <span class="text-muted small">
                                                    <i
                                                        class="bx bx-calendar me-1"
                                                    ></i>
                                                    {{ $menu->created_at->format("d M Y") }}
                                                </span>
                                            </div>

                                            @if ($menu->createdByDapur)
                                                <div
                                                    class="d-flex align-items-center"
                                                >
                                                    <i
                                                        class="bx bx-store-alt text-primary me-2"
                                                    ></i>
                                                    <span
                                                        class="small text-muted text-truncate"
                                                        title="{{ $menu->createdByDapur->nama_dapur }}"
                                                    >
                                                        {{ $menu->createdByDapur->nama_dapur }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="mt-auto">
                                            <div class="d-grid">
                                                <a
                                                    href="{{ route("kepala-dapur.menu-makanan.show", $menu) }}"
                                                    class="btn btn-primary btn-sm"
                                                >
                                                    <i
                                                        class="bx bx-show me-1"
                                                    ></i>
                                                    Lihat Detail
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
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
                        @if (request()->hasAny(["search", "status", "dapur", "kategori"]))
                            <i class="bx bx-search bx-lg text-muted mb-3"></i>
                            <h5 class="mb-1">Tidak ada hasil</h5>
                            <p class="text-muted mb-3">
                                Tidak ada menu yang sesuai dengan filter.
                            </p>
                            <a
                                href="{{ route("kepala-dapur.menu-makanan.index") }}"
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

    <!-- Custom Styling -->
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

        .menu-card {
            transition: all 0.3s ease;
            border: 1px solid #e7eaf3;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .menu-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .card-img-top-wrapper {
            height: 180px;
            overflow: hidden;
            position: relative;
        }

        .menu-image {
            height: 100%;
            width: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .menu-card:hover .menu-image {
            transform: scale(1.05);
        }

        .menu-card-item {
            transition: opacity 0.3s ease;
        }

        .menu-card-item.hidden {
            display: none;
        }

        .badge {
            font-size: 0.7rem;
            font-weight: 500;
        }

        .bg-label-primary {
            background-color: rgba(105, 108, 255, 0.16) !important;
            color: #696cff !important;
        }

        .bg-label-success {
            background-color: rgba(113, 221, 55, 0.16) !important;
            color: #71dd37 !important;
        }

        .bg-label-info {
            background-color: rgba(3, 195, 236, 0.16) !important;
            color: #03c3ec !important;
        }

        .bg-label-warning {
            background-color: rgba(255, 159, 67, 0.16) !important;
            color: #ff9f43 !important;
        }

        .text-truncate-2-lines {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    <!-- Choices.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>

    <!-- JavaScript for Filters and Client-Side Search -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusFilter = document.getElementById('status-filter');
            const dapurFilter = document.getElementById('dapur-filter');
            const kategoriFilter = document.getElementById('kategori-filter');
            const searchInput = document.getElementById('search-input');
            const menuCards = document.querySelectorAll('.menu-card-item');

            // Initialize Choices.js
            const statusChoices = new Choices(statusFilter, {
                searchEnabled: false,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'Semua Status',
            });

            const dapurChoices = new Choices(dapurFilter, {
                searchEnabled: false,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'Semua Dapur',
            });

            const kategoriChoices = new Choices(kategoriFilter, {
                searchEnabled: false,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'Semua Kategori',
            });

            function filterCards() {
                const searchText = searchInput.value.toLowerCase();
                const statusValue = statusChoices.getValue(true);
                const dapurValue = dapurChoices.getValue(true);
                const kategoriValue = kategoriChoices.getValue(true);

                let visibleCount = 0;

                menuCards.forEach((card) => {
                    const searchData = card.getAttribute('data-search') || '';
                    const statusData = card.getAttribute('data-status') || '';
                    const dapurData = card.getAttribute('data-dapur') || '';
                    const kategoriData =
                        card.getAttribute('data-kategori') || '';

                    const matchesSearch =
                        !searchText || searchData.includes(searchText);
                    const matchesStatus =
                        statusValue === 'all' || statusData === statusValue;
                    const matchesDapur =
                        dapurValue === 'all' || dapurData === dapurValue;
                    const matchesKategori =
                        kategoriValue === 'all' ||
                        kategoriData === kategoriValue;

                    const shouldShow =
                        matchesSearch &&
                        matchesStatus &&
                        matchesDapur &&
                        matchesKategori;

                    if (shouldShow) {
                        card.style.display = '';
                        card.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                        card.classList.add('hidden');
                    }
                });

                // Show/hide no results message if needed
                const noResultsEl =
                    document.getElementById('no-results-message');
                if (visibleCount === 0 && !noResultsEl) {
                    const container = document.getElementById(
                        'menu-cards-container',
                    );
                    const noResultsHtml = `
                        <div class="col-12" id="no-results-message">
                            <div class="text-center py-5">
                                <i class="bx bx-search bx-lg text-muted mb-3"></i>
                                <h5 class="mb-1">Tidak ada hasil</h5>
                                <p class="text-muted mb-3">
                                    Tidak ada menu yang sesuai dengan filter yang dipilih.
                                </p>
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', noResultsHtml);
                } else if (visibleCount > 0 && noResultsEl) {
                    noResultsEl.remove();
                }
            }

            // Add event listeners
            searchInput.addEventListener('input', filterCards);
            statusFilter.addEventListener('change', filterCards);
            dapurFilter.addEventListener('change', filterCards);
            kategoriFilter.addEventListener('change', filterCards);

            // Initialize Bootstrap tooltips if needed
            const tooltipTriggerList = document.querySelectorAll(
                '[data-bs-toggle="tooltip"]',
            );
            const tooltipList = [...tooltipTriggerList].map(
                (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl),
            );
        });
    </script>
@endsection
