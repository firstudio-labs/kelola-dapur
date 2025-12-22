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
                                href="{{ route("kepala-dapur.dashboard", $dapur) }}"
                                class="text-muted me-2"
                            >
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Approval Stok</span>
                        </nav>
                        <h4 class="mb-1">
                            Approval Permintaan Stok - {{ $dapur->nama_dapur }}
                        </h4>
                        <p class="mb-0 text-muted">
                            Kelola permintaan penambahan stok dari admin gudang
                        </p>
                    </div>
                    <div class="d-flex gap-2">
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
                                    <i class="bx bx-receipt"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">
                                    Total Permintaan
                                </small>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-1">
                                        {{ $totalApprovals }}
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
                                    <i class="bx bx-time"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">
                                    Menunggu Approval
                                </small>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-1">
                                        {{ $pendingApprovals }}
                                    </h6>
                                    @if ($pendingApprovals > 0)
                                        <span
                                            class="badge bg-warning ms-1 pulse"
                                        >
                                            Baru
                                        </span>
                                    @endif
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
                                    Disetujui
                                </small>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-1">
                                        {{ $approvedApprovals }}
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
                                    Ditolak
                                </small>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-1">
                                        {{ $rejectedApprovals }}
                                    </h6>
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
                    action="{{ route("kepala-dapur.approvals.index", $dapur) }}"
                    class="row g-3"
                >
                    <div class="col-md-3">
                        <label for="search-input" class="form-label">
                            Cari Permintaan
                        </label>
                        <div class="input-group">
                            <input
                                type="text"
                                name="search"
                                id="search-input"
                                value="{{ request("search") }}"
                                class="form-control"
                                placeholder="Cari bahan, admin..."
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
                    <div class="col-md-2">
                        <label for="status-filter" class="form-label">
                            Status
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
                            @foreach ($statusOptions as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    {{ request("status") === $value ? "selected" : "" }}
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
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
                    <div class="col-md-2">
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
                                value="created_at"
                                {{ request("sort") === "created_at" ? "selected" : "" }}
                            >
                                Tanggal Permintaan
                            </option>
                            <option
                                value="nama_bahan"
                                {{ request("sort") === "nama_bahan" ? "selected" : "" }}
                            >
                                Nama Bahan
                            </option>
                            <option
                                value="admin_name"
                                {{ request("sort") === "admin_name" ? "selected" : "" }}
                            >
                                Nama Admin
                            </option>
                            <option
                                value="jumlah"
                                {{ request("sort") === "jumlah" ? "selected" : "" }}
                            >
                                Jumlah
                            </option>
                        </select>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                        @if (request()->hasAny(["search", "status", "date_from", "date_to", "sort"]))
                            <a
                                href="{{ route("kepala-dapur.approvals.index", $dapur) }}"
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

        <!-- Approvals Table -->
        <div class="card">
            <div class="card-body">
                @if ($approvals->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Permintaan</th>
                                    <th>Bahan</th>
                                    <th>Jumlah Diminta</th>
                                    <th>Admin Gudang</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($approvals as $index => $approval)
                                    <tr
                                        class="{{ $approval->isPending() ? "table-warning-subtle" : "" }}"
                                    >
                                        <td>
                                            {{ $approvals->firstItem() + $index }}
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-medium">
                                                    {{ $approval->created_at->format("d M Y") }}
                                                </span>
                                                <small class="text-muted">
                                                    {{ $approval->created_at->format("H:i") }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div
                                                class="d-flex align-items-center"
                                            >
                                                <div>
                                                    <h6 class="mb-0">
                                                        {{ $approval->stockItem->templateItem->nama_bahan }}
                                                    </h6>
                                                    <small class="text-muted">
                                                        Stok saat ini:
                                                        {{ rtrim(rtrim(number_format($approval->stockItem->jumlah, 3, ".", ""), "0"), ".") }}
                                                        {{ $approval->stockItem->satuan }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="fw-medium text-primary"
                                            >
                                                +{{ rtrim(rtrim(number_format($approval->jumlah, 3, ".", ""), "0"), ".") }}
                                            </span>
                                            <small class="text-muted d-block">
                                                {{ $approval->stockItem->satuan }}
                                            </small>
                                        </td>
                                        <td>
                                            <div
                                                class="d-flex align-items-center"
                                            >
                                                {{--
                                                    <div
                                                    class="avatar avatar-sm me-2"
                                                    >
                                                    <span
                                                    class="avatar-initial rounded-circle bg-label-info"
                                                    >
                                                    {{ strtoupper(substr($approval->adminGudang->user->nama ?? "AG", 0, 2)) }}
                                                    </span>
                                                    </div>
                                                --}}
                                                <div>
                                                    <span class="fw-medium">
                                                        {{ $approval->adminGudang->user->nama ?? "Admin Gudang" }}
                                                    </span>
                                                    <br />
                                                    <small class="text-muted">
                                                        Admin Gudang
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match ($approval->status) {
                                                    "approved" => "bg-label-success",
                                                    "rejected" => "bg-label-danger",
                                                    "pending" => "bg-label-warning",
                                                    default => "bg-label-secondary",
                                                };
                                                $statusText = match ($approval->status) {
                                                    "approved" => "Disetujui",
                                                    "rejected" => "Ditolak",
                                                    "pending" => "Menunggu",
                                                    default => ucfirst($approval->status),
                                                };
                                            @endphp

                                            <span
                                                class="badge {{ $statusClass }}"
                                            >
                                                {{ $statusText }}
                                            </span>
                                            @if ($approval->approved_at)
                                                <small
                                                    class="text-muted d-block"
                                                >
                                                    {{ $approval->approved_at->format("d/m/Y H:i") }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                    <button
                                                        type="button"
                                                    class="btn btn-sm btn-outline-primary action-btn"
                                                        data-bs-toggle="modal"
                                                    data-bs-target="#approvalStockDetailModal"
                                                    data-approval-data="{{ json_encode([
                                                        'tanggal_permintaan' => $approval->created_at->format('d M Y H:i'),
                                                        'jumlah_diminta' => rtrim(rtrim(number_format($approval->jumlah, 3), '0'), '.') . ' ' . $approval->satuan,
                                                        'status' => $approval->status,
                                                        'tanggal_diproses' => $approval->approved_at ? $approval->approved_at->format('d M Y H:i') : '-',
                                                        'nama_pemohon' => $approval->adminGudang->user->nama ?? 'Admin Gudang',
                                                        'nama_pemroses' => $approval->kepalaDapur && $approval->kepalaDapur->user ? $approval->kepalaDapur->user->nama : null,
                                                        'jam_kedatangan' => $approval->jam_kedatangan ? substr($approval->jam_kedatangan, 0, 5) : null,
                                                        'tanggal_produksi' => $approval->tanggal_produksi ? $approval->tanggal_produksi->format('d/m/Y') : null,
                                                        'tanggal_expired' => $approval->tanggal_expired ? $approval->tanggal_expired->format('d/m/Y') : null,
                                                        'suhu' => $approval->suhu_bahan_makanan,
                                                        'warna' => $approval->warna_bahan_makanan,
                                                        'foto_bahan' => $approval->foto_bahan ? asset('storage/' . $approval->foto_bahan) : null,
                                                        'keterangan' => $approval->keterangan
                                                    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) }}"
                                                    onclick="showApprovalStockDetailModal(JSON.parse(this.getAttribute('data-approval-data')))"
                                                    title="Lihat Detail"
                                                >
                                                    <i class="bx bx-show"></i>
                                                    </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($approvals->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $approvals->appends(request()->query())->links("vendor.pagination.bootstrap-5") }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-6">
                        <i class="bx bx-receipt bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">Tidak ada permintaan stok</h5>
                        <p class="text-muted mb-3">
                            Belum ada permintaan penambahan stok yang sesuai
                            dengan filter.
                        </p>
                        @if (request()->hasAny(["search", "status", "date_from", "date_to"]))
                            <a
                                href="{{ route("kepala-dapur.approvals.index", $dapur) }}"
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

    <!-- Approval Stock Detail Modal -->
    @include('partials.approval-stock-detail-modal')

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
        .action-btn {
            min-width: 40px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.2s ease;
        }
        .action-btn:hover:not(.disabled) {
            opacity: 0.8;
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
        .table-warning-subtle {
            background-color: rgba(255, 243, 205, 0.3) !important;
        }
        .pulse {
            animation: pulse 3s infinite;
        }
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }
        /* Ensure button close is visible - completely static, no hover effects */
        .modal-header .btn-close,
        .alert .btn-close,
        .modal-header .btn-close:hover,
        .modal-header .btn-close:focus,
        .modal-header .btn-close:active,
        .alert .btn-close:hover,
        .alert .btn-close:focus,
        .alert .btn-close:active {
            opacity: 1 !important;
            filter: none !important;
            background-size: 1em !important;
            padding: 0.5em !important;
            background-color: #ffffff !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z'/%3e%3c/svg%3e") !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 0.375rem !important;
            transition: none !important;
            transform: none !important;
            box-shadow: none !important;
            outline: none !important;
        }
    </style>

    <!-- Choices.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Choices.js
            const selects = document.querySelectorAll('.choices-select');
            selects.forEach(select => {
                new Choices(select, {
                    searchEnabled: false,
                    itemSelectText: '',
                    shouldSort: false,
                });
            });

            // Initialize Bootstrap tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(
                tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl)
            );

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });

        // Function to show approval stock detail modal
        function showApprovalStockDetailModal(data) {
            // Cleanup any existing backdrops before showing new modal
            if (window.cleanupModalBackdrop) {
                window.cleanupModalBackdrop();
            }
            
            if (window.showApprovalStockDetail) {
                window.showApprovalStockDetail(data);
            }
        }
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (window.cleanupModalBackdrop) {
                window.cleanupModalBackdrop();
            }
        });
    </script>
@endsection
