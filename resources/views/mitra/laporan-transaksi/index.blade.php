@extends("template_mitra.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a
                                href="{{ route("mitra.dashboard") }}"
                                class="text-muted me-2"
                            >
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Laporan Transaksi</span>
                        </nav>
                        <h4 class="mb-1">
                            Laporan Transaksi
                        </h4>
                        <p class="mb-0 text-muted">
                            Lihat laporan transaksi untuk dapur Anda
                        </p>
                    </div>
                </div>
            </div>
        </div>

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
                                    Total Transaksi
                                </small>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-1">
                                        {{ $stats["total"] }}
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
                                    Menunggu
                                </small>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-1">
                                        {{ $stats["pending"] }}
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
                                    Disetujui
                                </small>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-1">
                                        {{ $stats["approved"] }}
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
                                        {{ $stats["rejected"] }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form
                    method="GET"
                    action="{{ route("mitra.laporan-transaksi.index") }}"
                    class="row g-3"
                >
                    <div class="col-md-2">
                        <label for="dapur-filter" class="form-label">
                            Dapur
                        </label>
                        <select
                            name="dapur"
                            id="dapur-filter"
                            class="form-select"
                        >
                            <option value="">Semua Dapur</option>
                            @foreach($dapurs as $d)
                                <option value="{{ $d->id_dapur }}" {{ request('dapur') == $d->id_dapur ? 'selected' : '' }}>
                                    {{ $d->nama_dapur }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="search-input" class="form-label">
                            Cari Transaksi
                        </label>
                        <div class="input-group">
                            <input
                                type="text"
                                name="search"
                                id="search-input"
                                value="{{ request("search") }}"
                                class="form-control"
                                placeholder="Cari..."
                            />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label for="status-filter" class="form-label">
                            Status
                        </label>
                        <select
                            name="status"
                            id="status-filter"
                            class="form-select"
                        >
                            <option
                                value=""
                                {{ request("status") === "" ? "selected" : "" }}
                            >
                                Semua Status
                            </option>
                            <option
                                value="pending"
                                {{ request("status") === "pending" ? "selected" : "" }}
                            >
                                Menunggu
                            </option>
                            <option
                                value="approved"
                                {{ request("status") === "approved" ? "selected" : "" }}
                            >
                                Disetujui
                            </option>
                            <option
                                value="rejected"
                                {{ request("status") === "rejected" ? "selected" : "" }}
                            >
                                Ditolak
                            </option>
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
                    <div class="col-md-2">
                        <label for="sort-filter" class="form-label">
                            Urutkan
                        </label>
                        <select
                            name="sort"
                            id="sort-filter"
                            class="form-select"
                        >
                            <option
                                value="created_at"
                                {{ request("sort") === "created_at" ? "selected" : "" }}
                            >
                                Tanggal Pengajuan
                            </option>
                            <option
                                value="tanggal_transaksi"
                                {{ request("sort") === "tanggal_transaksi" ? "selected" : "" }}
                            >
                                Tanggal Transaksi
                            </option>
                            <option
                                value="created_by"
                                {{ request("sort") === "created_by" ? "selected" : "" }}
                            >
                                Pembuat
                            </option>
                        </select>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                        @if (request()->hasAny(["search", "dapur", "status", "date_from", "date_to", "sort"]))
                            <a
                                href="{{ route("mitra.laporan-transaksi.index") }}"
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

        <div class="card">
            <div class="card-body">
                @if ($approvals->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Transaksi</th>
                                    <th>Dapur</th>
                                    <th>Keterangan</th>
                                    <th>Total Porsi</th>
                                    <th>Jumlah Menu</th>
                                    <th>Dibuat Oleh</th>
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
                                                    {{ $approval->transaksiDapur->tanggal_transaksi->format("d M Y") }}
                                                </span>
                                                <small class="text-muted">
                                                    {{ $approval->transaksiDapur->tanggal_transaksi->format("H:i") }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-dark">
                                                {{ $approval->transaksiDapur->dapur->nama_dapur ?? "-" }}
                                            </span>
                                        </td>
                                        <td>
                                            <div
                                                class="d-flex align-items-center"
                                            >
                                                <div>
                                                    <h6 class="mb-0">
                                                        {{ Str::limit($approval->transaksiDapur->keterangan, 30) ?? "Paket Menu Harian" }}
                                                    </h6>
                                                    <small class="text-muted">
                                                        ID:
                                                        {{ $approval->transaksiDapur->id_transaksi }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="fw-medium text-primary"
                                            >
                                                {{ $approval->transaksiDapur->total_porsi }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $approval->transaksiDapur->detailTransaksiDapur->count() }}
                                        </td>
                                        <td>
                                            <div
                                                class="d-flex align-items-center"
                                            >
                                                <div>
                                                    <span class="fw-medium">
                                                        {{ $approval->transaksiDapur->createdBy->nama ?? "Unknown" }}
                                                    </span>
                                                    <br />
                                                    <small class="text-muted">
                                                        Ahli Gizi
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $approval->getStatusBadgeClass() }}"
                                            >
                                                {{ ucfirst($approval->status) }}
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
                                                <a
                                                    href="{{ route("mitra.laporan-transaksi.show", ["approvalId" => $approval->id_approval_transaksi]) }}"
                                                    class="btn btn-sm btn-outline-primary action-btn"
                                                    data-bs-toggle="tooltip"
                                                    title="Lihat Detail"
                                                >
                                                    <i class="bx bx-show"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($approvals->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $approvals->appends(request()->query())->links("vendor.pagination.bootstrap-5") }}
                        </div>
                    @endif
                @else
                    
                    <div class="text-center py-6">
                        <i class="bx bx-receipt bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">
                            Tidak ada transaksi
                        </h5>
                        <p class="text-muted mb-3">
                            Belum ada laporan transaksi untuk dapur Anda.
                        </p>
                        @if (request()->hasAny(["search", "status", "date_from", "date_to", "sort", "dapur"]))
                            <a
                                href="{{ route("mitra.laporan-transaksi.index") }}"
                                class="btn btn-outline-primary"
                            >
                                Reset Filter
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <style>
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
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tooltipTriggerList = document.querySelectorAll(
                    '[data-bs-toggle="tooltip"]',
                );
                const tooltipList = [...tooltipTriggerList].map(
                    (tooltipTriggerEl) =>
                        new bootstrap.Tooltip(tooltipTriggerEl),
                );
            });
        </script>
    </div>
@endsection
