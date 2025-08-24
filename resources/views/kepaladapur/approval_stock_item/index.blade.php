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
                        @if ($pendingApprovals > 0)
                            <button
                                type="button"
                                class="btn btn-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#bulkActionModal"
                            >
                                <i class="bx bx-check-double me-1"></i>
                                Aksi Massal
                            </button>
                        @endif
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
                    <!-- Bulk Action Checkboxes -->
                    @if ($approvals->where("status", "pending")->count() > 0)
                        <div class="mb-3 d-flex align-items-center">
                            <input
                                type="checkbox"
                                id="select-all"
                                class="form-check-input me-2"
                            />
                            <label for="select-all" class="form-check-label">
                                Pilih Semua Pending
                            </label>
                            <div class="ms-auto">
                                <span id="selected-count" class="text-muted">
                                    0 dipilih
                                </span>
                            </div>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    @if ($approvals->where("status", "pending")->count() > 0)
                                        <th width="50">
                                            <i class="bx bx-check-square"></i>
                                        </th>
                                    @endif

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
                                        @if ($approvals->where("status", "pending")->count() > 0)
                                            <td>
                                                @if ($approval->isPending())
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input bulk-checkbox"
                                                        value="{{ $approval->id_approval_stock_item }}"
                                                    />
                                                @endif
                                            </td>
                                        @endif

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
                                                <div
                                                    class="avatar avatar-sm me-2"
                                                >
                                                    <span
                                                        class="avatar-initial rounded-circle bg-label-info"
                                                    >
                                                        {{ strtoupper(substr($approval->adminGudang->user->nama ?? "AG", 0, 2)) }}
                                                    </span>
                                                </div>
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
                                                <a
                                                    href="{{ route("kepala-dapur.approvals.show", [$dapur, $approval]) }}"
                                                    class="btn btn-sm btn-outline-primary action-btn"
                                                    data-bs-toggle="tooltip"
                                                    title="Lihat Detail"
                                                >
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                @if ($approval->isPending())
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-success action-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#approveModal"
                                                        data-approval-id="{{ $approval->id_approval_stock_item }}"
                                                        data-bahan-name="{{ $approval->stockItem->templateItem->nama_bahan }}"
                                                        data-jumlah="{{ $approval->jumlah }}"
                                                        data-satuan="{{ $approval->satuan }}"
                                                        data-admin-name="{{ $approval->adminGudang->user->nama ?? "Admin Gudang" }}"
                                                        title="Setujui"
                                                    >
                                                        <i
                                                            class="bx bx-check"
                                                        ></i>
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-danger action-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#rejectModal"
                                                        data-approval-id="{{ $approval->id_approval_stock_item }}"
                                                        data-bahan-name="{{ $approval->stockItem->templateItem->nama_bahan }}"
                                                        data-jumlah="{{ $approval->jumlah }}"
                                                        data-satuan="{{ $approval->satuan }}"
                                                        data-admin-name="{{ $approval->adminGudang->user->nama ?? "Admin Gudang" }}"
                                                        title="Tolak"
                                                    >
                                                        <i class="bx bx-x"></i>
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

    <!-- Approve Modal -->
    <div
        class="modal fade"
        id="approveModal"
        tabindex="-1"
        aria-labelledby="approveModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">
                        Setujui Permintaan Stok
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <form id="approveForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-2"></i>
                            Dengan menyetujui permintaan ini, stok akan otomatis
                            ditambahkan ke sistem.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Bahan</label>
                            <input
                                type="text"
                                id="approveBahanName"
                                class="form-control"
                                readonly
                            />
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">
                                    Jumlah Permintaan
                                </label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        id="approveJumlah"
                                        class="form-control"
                                        readonly
                                    />
                                    <span
                                        class="input-group-text"
                                        id="approveSatuan"
                                    ></span>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Diminta Oleh</label>
                                <input
                                    type="text"
                                    id="approveAdminName"
                                    class="form-control"
                                    readonly
                                />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan_approval" class="form-label">
                                Catatan Approval (Opsional)
                            </label>
                            <textarea
                                name="keterangan_approval"
                                id="keterangan_approval"
                                class="form-control"
                                rows="3"
                                maxlength="500"
                                placeholder="Tambahkan catatan jika diperlukan..."
                            ></textarea>
                            <div class="form-text">Maksimal 500 karakter</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Batal
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-check me-1"></i>
                            Setujui Permintaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div
        class="modal fade"
        id="rejectModal"
        tabindex="-1"
        aria-labelledby="rejectModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">
                        Tolak Permintaan Stok
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="bx bx-error-circle me-2"></i>
                            Pastikan Anda memberikan alasan yang jelas untuk
                            penolakan ini.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Bahan</label>
                            <input
                                type="text"
                                id="rejectBahanName"
                                class="form-control"
                                readonly
                            />
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">
                                    Jumlah Permintaan
                                </label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        id="rejectJumlah"
                                        class="form-control"
                                        readonly
                                    />
                                    <span
                                        class="input-group-text"
                                        id="rejectSatuan"
                                    ></span>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Diminta Oleh</label>
                                <input
                                    type="text"
                                    id="rejectAdminName"
                                    class="form-control"
                                    readonly
                                />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="alasan_penolakan" class="form-label">
                                Alasan Penolakan
                                <span class="text-danger">*</span>
                            </label>
                            <textarea
                                name="alasan_penolakan"
                                id="alasan_penolakan"
                                class="form-control @error("alasan_penolakan") is-invalid @enderror"
                                rows="4"
                                maxlength="500"
                                required
                                placeholder="Jelaskan alasan penolakan permintaan ini..."
                            >
{{ old("alasan_penolakan") }}</textarea
                            >
                            @error("alasan_penolakan")
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="form-text">
                                Wajib diisi. Maksimal 500 karakter
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Batal
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bx bx-x me-1"></i>
                            Tolak Permintaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Action Modal -->
    <div
        class="modal fade"
        id="bulkActionModal"
        tabindex="-1"
        aria-labelledby="bulkActionModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkActionModalLabel">
                        Aksi Massal
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <form
                    action="{{ route("kepala-dapur.approvals.bulk-action", $dapur) }}"
                    method="POST"
                    id="bulkActionForm"
                >
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih Aksi</label>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="bulk_action"
                                    id="bulk_approve"
                                    value="approve"
                                    required
                                />
                                <label
                                    class="form-check-label"
                                    for="bulk_approve"
                                >
                                    <i
                                        class="bx bx-check text-success me-1"
                                    ></i>
                                    Setujui Semua Permintaan Terpilih
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="bulk_action"
                                    id="bulk_reject"
                                    value="reject"
                                    required
                                />
                                <label
                                    class="form-check-label"
                                    for="bulk_reject"
                                >
                                    <i class="bx bx-x text-danger me-1"></i>
                                    Tolak Semua Permintaan Terpilih
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="bulk_keterangan" class="form-label">
                                Keterangan
                            </label>
                            <textarea
                                name="bulk_keterangan"
                                id="bulk_keterangan"
                                class="form-control"
                                rows="3"
                                maxlength="500"
                                placeholder="Catatan untuk semua permintaan yang dipilih..."
                            ></textarea>
                            <div class="form-text">Maksimal 500 karakter</div>
                        </div>
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-2"></i>
                            <span id="bulk-selection-info">
                                Pilih permintaan dari tabel terlebih dahulu.
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="btn btn-primary"
                            id="bulk-action-submit"
                            disabled
                        >
                            <i class="bx bx-check-double me-1"></i>
                            Proses Permintaan
                        </button>
                    </div>
                </form>
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
        .avatar-sm .avatar-initial {
            width: 32px;
            height: 32px;
            font-size: 12px;
        }
        .table-warning-subtle {
            background-color: rgba(255, 243, 205, 0.3) !important;
        }
        .pulse {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
            100% {
                opacity: 1;
            }
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

            // Handle approve modal
            const approveModal = document.getElementById('approveModal');
            const approveForm = document.getElementById('approveForm');

            if (approveModal) {
                approveModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const approvalId = button.getAttribute('data-approval-id');
                    const bahanName = button.getAttribute('data-bahan-name');
                    const jumlah = button.getAttribute('data-jumlah');
                    const satuan = button.getAttribute('data-satuan');
                    const adminName = button.getAttribute('data-admin-name');

                    document.getElementById('approveBahanName').value = bahanName;
                    document.getElementById('approveJumlah').value = parseFloat(jumlah).toFixed(3);
                    document.getElementById('approveSatuan').textContent = satuan;
                    document.getElementById('approveAdminName').value = adminName;

                    const actionUrl = '{{ route('kepala-dapur.approvals.approve', [$dapur, ':approvalId']) }}';
                    approveForm.action = actionUrl.replace(':approvalId', approvalId);

                    // Reset form
                    document.getElementById('keterangan_approval').value = '';
                });
            }

            // Handle reject modal
            const rejectModal = document.getElementById('rejectModal');
            const rejectForm = document.getElementById('rejectForm');

            if (rejectModal) {
                rejectModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const approvalId = button.getAttribute('data-approval-id');
                    const bahanName = button.getAttribute('data-bahan-name');
                    const jumlah = button.getAttribute('data-jumlah');
                    const satuan = button.getAttribute('data-satuan');
                    const adminName = button.getAttribute('data-admin-name');

                    document.getElementById('rejectBahanName').value = bahanName;
                    document.getElementById('rejectJumlah').value = parseFloat(jumlah).toFixed(3);
                    document.getElementById('rejectSatuan').textContent = satuan;
                    document.getElementById('rejectAdminName').value = adminName;

                    const actionUrl = '{{ route('kepala-dapur.approvals.reject', [$dapur, ':approvalId']) }}';
                    rejectForm.action = actionUrl.replace(':approvalId', approvalId);

                    // Reset form
                    document.getElementById('alasan_penolakan').value = '';
                });
            }

            // Handle bulk actions
            const selectAllCheckbox = document.getElementById('select-all');
            const bulkCheckboxes = document.querySelectorAll('.bulk-checkbox');
            const selectedCountSpan = document.getElementById('selected-count');
            const bulkActionSubmit = document.getElementById('bulk-action-submit');
            const bulkSelectionInfo = document.getElementById('bulk-selection-info');
            const bulkActionForm = document.getElementById('bulkActionForm');

            function updateBulkSelection() {
                const checkedBoxes = document.querySelectorAll('.bulk-checkbox:checked');
                const count = checkedBoxes.length;

                if (selectedCountSpan) {
                    selectedCountSpan.textContent = count + ' dipilih';
                }

                if (bulkActionSubmit) {
                    bulkActionSubmit.disabled = count === 0;
                }

                if (bulkSelectionInfo) {
                    bulkSelectionInfo.textContent = count > 0 
                        ? `${count} permintaan dipilih untuk diproses.`
                        : 'Pilih permintaan dari tabel terlebih dahulu.';
                }

                // Update hidden input with selected IDs
                const existingInput = bulkActionForm.querySelector('input[name="approval_ids[]"]');
                if (existingInput) {
                    existingInput.remove();
                }

                checkedBoxes.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'approval_ids[]';
                    input.value = checkbox.value;
                    bulkActionForm.appendChild(input);
                });
            }

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function () {
                    bulkCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateBulkSelection();
                });
            }

            bulkCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkSelection);
            });

            // Show validation error modals
            @if($errors->has('alasan_penolakan'))
                const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
                rejectModal.show();
            @endif

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
@endsection
