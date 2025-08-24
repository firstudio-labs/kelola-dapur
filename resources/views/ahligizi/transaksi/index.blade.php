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
                            <span class="text-dark">
                                Daftar Input Paket Menu
                            </span>
                        </nav>
                        <h4 class="mb-1">Daftar Input Paket Menu</h4>
                        <p class="mb-0 text-muted">
                            Kelola semua input paket menu untuk dapur:
                            {{ $ahliGizi->dapur->nama_dapur ?? "Tidak Tersedia" }}
                        </p>
                    </div>
                    <a
                        href="{{ route("ahli-gizi.transaksi.create") }}"
                        class="btn btn-primary btn-sm"
                    >
                        <i class="bx bx-plus me-1"></i>
                        Buat Paket Baru
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
                    action="{{ route("ahli-gizi.transaksi.index") }}"
                    class="row g-3"
                >
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
                                value="draft"
                                {{ request("status") === "draft" ? "selected" : "" }}
                            >
                                Draft
                            </option>
                            <option
                                value="pending_approval"
                                {{ request("status") === "pending_approval" ? "selected" : "" }}
                            >
                                Menunggu Persetujuan
                            </option>
                            <option
                                value="completed"
                                {{ request("status") === "completed" ? "selected" : "" }}
                            >
                                Selesai
                            </option>
                            <option
                                value="rejected"
                                {{ request("status") === "rejected" ? "selected" : "" }}
                            >
                                Ditolak
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date-from" class="form-label">
                            Dari Tanggal
                        </label>
                        <input
                            type="date"
                            name="date_from"
                            id="date-from"
                            value="{{ request("date_from") }}"
                            class="form-control"
                        />
                    </div>
                    <div class="col-md-3">
                        <label for="date-to" class="form-label">
                            Sampai Tanggal
                        </label>
                        <input
                            type="date"
                            name="date_to"
                            id="date-to"
                            value="{{ request("date_to") }}"
                            class="form-control"
                        />
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                        @if (request()->hasAny(["search", "status", "date_from", "date_to"]))
                            <a
                                href="{{ route("ahli-gizi.transaksi.index") }}"
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
        @if ($transaksi->total() > 0)
            <div class="card mb-4">
                <div class="card-body py-2 px-4">
                    <div class="row justify-content-center g-3">
                        <div class="col-md-3 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-primary me-2">
                                    <i class="bx bx-package"></i>
                                </span>
                                <div>
                                    <small class="text-muted">
                                        Total Paket
                                    </small>
                                    <h6 class="mb-0">
                                        {{ $transaksi->total() }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-warning me-2">
                                    <i class="bx bx-time"></i>
                                </span>
                                <div>
                                    <small class="text-muted">Draft</small>
                                    <h6 class="mb-0">
                                        {{ $transaksi->where("status", "draft")->count() }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-info me-2">
                                    <i class="bx bx-hourglass"></i>
                                </span>
                                <div>
                                    <small class="text-muted">
                                        Menunggu Persetujuan
                                    </small>
                                    <h6 class="mb-0">
                                        {{ $transaksi->where("status", "pending_approval")->count() }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-danger me-2">
                                    <i class="bx bx-error"></i>
                                </span>
                                <div>
                                    <small class="text-muted">
                                        Kekurangan Stock
                                    </small>
                                    <h6 class="mb-0">
                                        {{ $transaksi->filter(function ($t) { return $t->laporanKekuranganStock->isNotEmpty();})->count() }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Transaksi List -->
        <div class="card">
            <div class="card-body">
                @if ($transaksi->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    {{-- <th>Nama Paket</th> --}}
                                    <th>Tanggal Transaksi</th>
                                    <th>Total Porsi</th>
                                    <th>Status</th>
                                    <th>Kekurangan Stock</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="transaksi-table-body">
                                @foreach ($transaksi as $item)
                                    @php
                                        $hasShortage = $item->laporanKekuranganStock->isNotEmpty();
                                        $statusClasses = [
                                            "draft" => "bg-label-warning",
                                            "pending_approval" => "bg-label-info",
                                            "completed" => "bg-label-success",
                                            "rejected" => "bg-label-danger",
                                        ];
                                    @endphp

                                    <tr
                                        data-search="{{ strtolower($item->nama_paket . " " . ($item->keterangan ?? "")) }}"
                                        data-status="{{ $item->status }}"
                                    >
                                        <td>
                                            {{ $loop->iteration + ($transaksi->currentPage() - 1) * $transaksi->perPage() }}
                                        </td>
                                        {{--
                                            <td>
                                            <div class="fw-semibold">
                                            {{ $item->nama_paket }}
                                            </div>
                                            </td>
                                        --}}
                                        <td>
                                            <small class="text-muted">
                                                {{ $item->tanggal_transaksi->format("d M Y") }}
                                            </small>
                                        </td>
                                        <td>
                                            <div
                                                class="d-flex align-items-center"
                                            >
                                                <i
                                                    class="bx bx-bowl-hot me-1"
                                                ></i>
                                                <span class="fw-semibold">
                                                    {{ $item->total_porsi }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $statusClasses[$item->status] ?? "bg-label-secondary" }}"
                                            >
                                                {{ ucfirst(str_replace("_", " ", $item->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($hasShortage)
                                                <span
                                                    class="badge bg-label-danger"
                                                >
                                                    <i
                                                        class="bx bx-error me-1"
                                                    ></i>
                                                    {{ $item->laporanKekuranganStock->count() }}
                                                    Item
                                                </span>
                                            @else
                                                <span class="text-muted">
                                                    -
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $item->created_at->format("d M Y") }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a
                                                    href="{{ route("ahli-gizi.transaksi.show", $item) }}"
                                                    class="btn btn-sm btn-outline-primary action-btn"
                                                    data-bs-toggle="tooltip"
                                                    title="Lihat Detail"
                                                >
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                @if ($item->status === "draft")
                                                    <a
                                                        href="{{ route("ahli-gizi.transaksi.edit-porsi-besar", $item) }}"
                                                        class="btn btn-sm btn-outline-info action-btn"
                                                        data-bs-toggle="tooltip"
                                                        title="Edit Paket"
                                                    >
                                                        <i
                                                            class="bx bx-edit"
                                                        ></i>
                                                    </a>
                                                    <form
                                                        action="{{ route("ahli-gizi.transaksi.destroy", $item) }}"
                                                        method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus draft ini?');"
                                                    >
                                                        @csrf
                                                        @method("DELETE")
                                                        <button
                                                            type="submit"
                                                            class="btn btn-sm btn-outline-danger action-btn"
                                                            data-bs-toggle="tooltip"
                                                            title="Hapus Draft"
                                                        >
                                                            <i
                                                                class="bx bx-trash"
                                                            ></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    @if ($transaksi->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $transaksi->appends(request()->query())->links("vendor.pagination.bootstrap-5") }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-6">
                        @if (request()->hasAny(["search", "status", "date_from", "date_to"]))
                            <i class="bx bx-search bx-lg text-muted mb-3"></i>
                            <h5 class="mb-1">Tidak ada hasil</h5>
                            <p class="text-muted mb-3">
                                Tidak ada paket menu yang sesuai dengan filter.
                            </p>
                            <a
                                href="{{ route("ahli-gizi.transaksi.index") }}"
                                class="btn btn-outline-primary"
                            >
                                Reset Filter
                            </a>
                        @else
                            <i class="bx bx-package bx-lg text-muted mb-3"></i>
                            <h5 class="mb-1">Belum ada paket menu</h5>
                            <p class="text-muted mb-3">
                                Mulai dengan membuat paket menu pertama.
                            </p>
                            <a
                                href="{{ route("ahli-gizi.transaksi.create") }}"
                                class="btn btn-primary"
                            >
                                <i class="bx bx-plus me-1"></i>
                                Buat Paket Pertama
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
            const searchInput = document.getElementById('search-input');
            const dateFrom = document.getElementById('date-from');
            const dateTo = document.getElementById('date-to');
            const tableBody = document.getElementById('transaksi-table-body');
            const rows = tableBody ? tableBody.getElementsByTagName('tr') : [];

            const statusChoices = new Choices(statusFilter, {
                searchEnabled: false,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'Semua Status',
            });

            function filterTable() {
                const searchText = searchInput.value.toLowerCase();
                const statusValue = statusChoices.getValue(true);

                Array.from(rows).forEach((row) => {
                    const searchData = row.getAttribute('data-search');
                    const statusData = row.getAttribute('data-status');

                    const matchesSearch = searchText
                        ? searchData.includes(searchText)
                        : true;
                    const matchesStatus =
                        statusValue === 'all' || statusData === statusValue;

                    row.style.display =
                        matchesSearch && matchesStatus ? '' : 'none';
                });
            }

            searchInput.addEventListener('input', filterTable);
            statusFilter.addEventListener('change', filterTable);

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
