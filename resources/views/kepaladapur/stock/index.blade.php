@extends("template_kepala_dapur.layout")
{{-- Asumsi ada layout untuk kepala dapur, sesuaikan jika berbeda --}}

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a
                                href="{{ route("kepala-dapur.dashboard", $dapur) }}"
                                {{-- Sesuaikan route dashboard kepala dapur --}}
                                class="text-muted me-2"
                            >
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Lihat Stok</span>
                        </nav>
                        <h4 class="mb-1">
                            Lihat Stok - {{ $dapur->nama_dapur }}
                        </h4>
                        <p class="mb-0 text-muted">
                            Lihat stok bahan makanan untuk dapur
                            {{ $dapur->nama_dapur }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span
                                    class="avatar-initial rounded bg-label-primary"
                                >
                                    <i class="bx bx-package"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">
                                    Total Item
                                </small>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-1">
                                        {{ $totalItems }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span
                                    class="avatar-initial rounded bg-label-success"
                                >
                                    <i class="bx bx-check-circle"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">
                                    Stok Normal
                                </small>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-1">
                                        {{ $normalStok }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span
                                    class="avatar-initial rounded bg-label-warning"
                                >
                                    <i class="bx bx-error-circle"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">
                                    Stok Rendah
                                </small>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-1">
                                        {{ $rendahStok }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span
                                    class="avatar-initial rounded bg-label-danger"
                                >
                                    <i class="bx bx-x-circle"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">
                                    Stok Habis
                                </small>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-1">{{ $habisStok }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <form
                    method="GET"
                    action="{{ route("kepala-dapur.stock.index", $dapur) }}"
                    {{-- Sesuaikan route --}}
                    class="row g-3"
                >
                    <div class="col-md-3">
                        <label for="search-input" class="form-label">
                            Cari Bahan
                        </label>
                        <div class="input-group">
                            <input
                                type="text"
                                name="search"
                                id="search-input"
                                value="{{ request("search") }}"
                                class="form-control"
                                placeholder="Cari nama bahan..."
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
                            Status Stok
                        </label>
                        <select
                            name="status"
                            id="status-filter"
                            class="choices-select form-select"
                        >
                            <option
                                value=""
                                {{ request("status") === "" ? "selected" : "" }}
                            >
                                Semua Status
                            </option>
                            <option
                                value="normal"
                                {{ request("status") === "normal" ? "selected" : "" }}
                            >
                                Normal (> 10)
                            </option>
                            <option
                                value="rendah"
                                {{ request("status") === "rendah" ? "selected" : "" }}
                            >
                                Rendah (1-10)
                            </option>
                            <option
                                value="habis"
                                {{ request("status") === "habis" ? "selected" : "" }}
                            >
                                Habis (0)
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="satuan-filter" class="form-label">
                            Satuan
                        </label>
                        <select
                            name="satuan"
                            id="satuan-filter"
                            class="choices-select form-select"
                        >
                            <option
                                value=""
                                {{ request("satuan") === "" ? "selected" : "" }}
                            >
                                Semua Satuan
                            </option>
                            @foreach ($availableSatuans as $satuan)
                                <option
                                    value="{{ $satuan }}"
                                    {{ request("satuan") === $satuan ? "selected" : "" }}
                                >
                                    {{ $satuan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="sort-filter" class="form-label">
                            Urutkan
                        </label>
                        <select
                            name="sort"
                            id="sort-filter"
                            class="choices-select form-select"
                        >
                            <option
                                value="nama_bahan"
                                {{ request("sort") === "nama_bahan" ? "selected" : "" }}
                            >
                                Nama Bahan
                            </option>
                            <option
                                value="jumlah"
                                {{ request("sort") === "jumlah" ? "selected" : "" }}
                            >
                                Jumlah Stok
                            </option>
                            <option
                                value="tanggal_restok"
                                {{ request("sort") === "tanggal_restok" ? "selected" : "" }}
                            >
                                Tanggal Restok
                            </option>
                        </select>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                        @if (request()->hasAny(["search", "status", "satuan", "sort"]))
                            <a
                                href="{{ route("kepala-dapur.stock.index", $dapur) }}"
                                {{-- Sesuaikan route --}}
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

        <!-- Stock Items Table -->
        <div class="card">
            <div class="card-body">
                @if ($stockItems->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Bahan</th>
                                    <th>Jumlah Stok</th>
                                    <th>Satuan</th>
                                    <th>Status</th>
                                    <th>Tanggal Restok Terakhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stockItems as $index => $stockItem)
                                    <tr>
                                        <td>
                                            {{ $stockItems->firstItem() + $index }}
                                        </td>
                                        <td>
                                            <div
                                                class="d-flex align-items-center"
                                            >
                                                <div>
                                                    <h6 class="mb-0">
                                                        {{ $stockItem->templateItem->nama_bahan }}
                                                    </h6>
                                                    @if ($stockItem->templateItem->keterangan)
                                                        <small
                                                            class="text-muted"
                                                        >
                                                            {{ Str::limit($stockItem->templateItem->keterangan, 30) }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-medium">
                                                {{ rtrim(rtrim(number_format($stockItem->jumlah, 3), "0"), ".") }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-info">
                                                {{ $stockItem->satuan }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $status = $stockItem->getStockStatus();
                                                $badgeClass = match ($status) {
                                                    "habis" => "bg-label-danger",
                                                    "rendah" => "bg-label-warning",
                                                    "normal" => "bg-label-success",
                                                    default => "bg-label-secondary",
                                                };
                                            @endphp

                                            <span
                                                class="badge {{ $badgeClass }}"
                                            >
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $latestRestockDate = $stockItem->getLatestRestockDate();
                                            @endphp

                                            {{ $latestRestockDate ? $latestRestockDate->format("d M Y") : "-" }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($stockItems->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $stockItems->appends(request()->query())->links("vendor.pagination.bootstrap-5") }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-6">
                        <i class="bx bx-package bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">Tidak ada data stok</h5>
                        <p class="text-muted mb-3">
                            Belum ada data stok yang sesuai dengan filter.
                        </p>
                        @if (request()->hasAny(["search", "status", "satuan"]))
                            <a
                                href="{{ route("kepala-dapur.stock.index", $dapur) }}"
                                {{-- Sesuaikan route --}}
                                class="btn btn-outline-primary"
                            >
                                Reset Filter
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
        .table td {
            vertical-align: middle;
        }
        .avatar-initial {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    <!-- Choices.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Choices.js
            const selects = document.querySelectorAll('.choices-select');
            selects.forEach((select) => {
                new Choices(select, {
                    searchEnabled: false,
                    itemSelectText: '',
                    shouldSort: false,
                });
            });
        });
    </script>
@endsection
