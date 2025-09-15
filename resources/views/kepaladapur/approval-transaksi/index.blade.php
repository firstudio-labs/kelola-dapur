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
                                href="{{ route("kepala-dapur.dashboard", ["dapur" => $dapur->id_dapur]) }}"
                                class="text-muted me-2"
                            >
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Persetujuan Transaksi</span>
                        </nav>
                        <h4 class="mb-1">
                            Persetujuan Transaksi Dapur -
                            {{ $dapur->nama_dapur }}
                        </h4>
                        <p class="mb-0 text-muted">
                            Kelola persetujuan transaksi paket menu untuk dapur
                            Anda
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        @if ($stats["pending"] > 0)
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
                                    Menunggu Persetujuan
                                </small>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-1">
                                        {{ $stats["pending"] }}
                                    </h6>
                                    @if ($stats["pending"] > 0)
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

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <form
                    method="GET"
                    action="{{ route("kepala-dapur.approval-transaksi.index", ["dapur" => $dapur->id_dapur]) }}"
                    class="row g-3"
                >
                    <div class="col-md-3">
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
                                placeholder="Cari keterangan, pembuat..."
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
                        @if (request()->hasAny(["search", "status", "date_from", "date_to", "sort"]))
                            <a
                                href="{{ route("kepala-dapur.approval-transaksi.index", ["dapur" => $dapur->id_dapur]) }}"
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
                                    <th>Tanggal Transaksi</th>
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
                                        @if ($approvals->where("status", "pending")->count() > 0)
                                            <td>
                                                @if ($approval->isPending())
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input bulk-checkbox"
                                                        value="{{ $approval->id_approval_transaksi }}"
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
                                                    {{ $approval->transaksiDapur->tanggal_transaksi->format("d M Y") }}
                                                </span>
                                                <small class="text-muted">
                                                    {{ $approval->transaksiDapur->tanggal_transaksi->format("H:i") }}
                                                </small>
                                            </div>
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
                                                {{--
                                                    <div
                                                    class="avatar avatar-sm me-2"
                                                    >
                                                    <span
                                                    class="avatar-initial rounded-circle bg-label-info"
                                                    >
                                                    {{ strtoupper(substr($approval->transaksiDapur->createdBy->nama ?? "NA", 0, 2)) }}
                                                    </span>
                                                    </div>
                                                --}}
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
                                                    href="{{ route("kepala-dapur.approval-transaksi.show", ["approval" => $approval->id_approval_transaksi, "dapur" => $dapur->id_dapur]) }}"
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
                                                        data-approval-id="{{ $approval->id_approval_transaksi }}"
                                                        data-keterangan="{{ $approval->transaksiDapur->keterangan ?? "Paket Menu Harian" }}"
                                                        data-created-by="{{ $approval->transaksiDapur->createdBy->nama ?? "Unknown" }}"
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
                                                        data-approval-id="{{ $approval->id_approval_transaksi }}"
                                                        data-keterangan="{{ $approval->transaksiDapur->keterangan ?? "Paket Menu Harian" }}"
                                                        data-created-by="{{ $approval->transaksiDapur->createdBy->nama ?? "Unknown" }}"
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
                        <h5 class="mb-1">
                            Tidak ada transaksi untuk disetujui
                        </h5>
                        <p class="text-muted mb-3">
                            Belum ada transaksi yang perlu persetujuan sesuai
                            dengan filter.
                        </p>
                        @if (request()->hasAny(["search", "status", "date_from", "date_to", "sort"]))
                            <a
                                href="{{ route("kepala-dapur.approval-transaksi.index", ["dapur" => $dapur->id_dapur]) }}"
                                class="btn btn-outline-primary"
                            >
                                Reset Filter
                            </a>
                        @endif
                    </div>
                @endif
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
                            Setujui Transaksi
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <form id="approveForm" method="POST" action="">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-success">
                                <i class="bx bx-check-circle me-2"></i>
                                Dengan menyetujui transaksi ini, stock bahan
                                akan otomatis dikurangi sesuai kebutuhan
                                produksi.
                            </div>
                            <div class="mb-3">
                                <label
                                    for="approveKeterangan"
                                    class="form-label"
                                >
                                    Keterangan Transaksi
                                </label>
                                <input
                                    type="text"
                                    id="approveKeterangan"
                                    class="form-control"
                                    readonly
                                />
                            </div>
                            <div class="mb-3">
                                <label
                                    for="approveCreatedBy"
                                    class="form-label"
                                >
                                    Dibuat Oleh
                                </label>
                                <input
                                    type="text"
                                    id="approveCreatedBy"
                                    class="form-control"
                                    readonly
                                />
                            </div>
                            <div class="mb-3">
                                <label
                                    for="catatan_approval"
                                    class="form-label"
                                >
                                    Catatan Persetujuan (Opsional)
                                </label>
                                <textarea
                                    name="catatan_approval"
                                    id="catatan_approval"
                                    class="form-control"
                                    rows="3"
                                    maxlength="500"
                                    placeholder="Tambahkan catatan jika diperlukan..."
                                ></textarea>
                                <div class="form-text">
                                    Maksimal 500 karakter
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
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-check me-1"></i>
                                Setujui Transaksi
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
                            Tolak Transaksi
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <form id="rejectForm" method="POST" action="">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-danger">
                                <i class="bx bx-x-circle me-2"></i>
                                Transaksi yang ditolak akan dibatalkan dan tidak
                                dapat diproses.
                            </div>
                            <div class="mb-3">
                                <label
                                    for="rejectKeterangan"
                                    class="form-label"
                                >
                                    Keterangan Transaksi
                                </label>
                                <input
                                    type="text"
                                    id="rejectKeterangan"
                                    class="form-control"
                                    readonly
                                />
                            </div>
                            <div class="mb-3">
                                <label for="rejectCreatedBy" class="form-label">
                                    Dibuat Oleh
                                </label>
                                <input
                                    type="text"
                                    id="rejectCreatedBy"
                                    class="form-control"
                                    readonly
                                />
                            </div>
                            <div class="mb-3">
                                <label
                                    for="alasan_penolakan"
                                    class="form-label"
                                >
                                    Alasan Penolakan
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea
                                    name="alasan_penolakan"
                                    id="alasan_penolakan"
                                    class="form-control"
                                    rows="3"
                                    maxlength="500"
                                    placeholder="Jelaskan alasan penolakan..."
                                    required
                                ></textarea>
                                <div class="form-text">
                                    Maksimal 500 karakter. Wajib diisi.
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
                                Tolak Transaksi
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
                        id="bulkActionForm"
                        action="{{ route("kepala-dapur.approval-transaksi.bulk-action", ["dapur" => $dapur->id_dapur]) }}"
                        method="POST"
                    >
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="bulk_action" class="form-label">
                                    Pilih Aksi
                                </label>
                                <select
                                    name="bulk_action"
                                    id="bulk_action"
                                    class="form-select"
                                    required
                                >
                                    <option value="">Pilih aksi...</option>
                                    <option value="approve">
                                        Setujui Semua
                                    </option>
                                    <option value="reject">Tolak Semua</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="bulk_keterangan" class="form-label">
                                    Catatan
                                </label>
                                <textarea
                                    name="bulk_keterangan"
                                    id="bulk_keterangan"
                                    class="form-control"
                                    rows="3"
                                    maxlength="500"
                                    placeholder="Catatan untuk semua transaksi yang dipilih..."
                                ></textarea>
                                <div class="form-text">
                                    Maksimal 500 karakter
                                </div>
                                <div
                                    id="reject-warning"
                                    class="text-danger mt-2"
                                    style="display: none"
                                >
                                    <i class="bx bx-error-circle me-1"></i>
                                    Alasan penolakan wajib diisi untuk aksi
                                    tolak.
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-2"></i>
                                <span id="bulk-selection-info">
                                    Pilih transaksi dari tabel terlebih dahulu.
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
                                id="bulk-action-submit"
                                class="btn btn-primary"
                                disabled
                            >
                                <i class="bx bx-check-double me-1"></i>
                                Proses Transaksi
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
                const selects = document.querySelectorAll('.choices-select');
                selects.forEach((select) => {
                    new Choices(select, {
                        searchEnabled: false,
                        itemSelectText: '',
                        shouldSort: false,
                    });
                });

                const tooltipTriggerList = document.querySelectorAll(
                    '[data-bs-toggle="tooltip"]',
                );
                const tooltipList = [...tooltipTriggerList].map(
                    (tooltipTriggerEl) =>
                        new bootstrap.Tooltip(tooltipTriggerEl),
                );

                const approveModal = document.getElementById('approveModal');
                const approveForm = document.getElementById('approveForm');

                if (approveModal) {
                    approveModal.addEventListener(
                        'show.bs.modal',
                        function (event) {
                            const button = event.relatedTarget;
                            const approvalId =
                                button.getAttribute('data-approval-id');
                            const keterangan =
                                button.getAttribute('data-keterangan');
                            const createdBy =
                                button.getAttribute('data-created-by');

                            document.getElementById('approveKeterangan').value =
                                keterangan;
                            document.getElementById('approveCreatedBy').value =
                                createdBy;

                            const actionUrl =
                                '{{ route("kepala-dapur.approval-transaksi.approve", ["dapur" => $dapur->id_dapur, "approval" => "approval_placeholder"]) }}'.replace(
                                    'approval_placeholder',
                                    approvalId,
                                );
                            approveForm.action = actionUrl;

                            document.getElementById('catatan_approval').value =
                                '';
                        },
                    );
                }

                const rejectModal = document.getElementById('rejectModal');
                const rejectForm = document.getElementById('rejectForm');

                if (rejectModal) {
                    rejectModal.addEventListener(
                        'show.bs.modal',
                        function (event) {
                            const button = event.relatedTarget;
                            const approvalId =
                                button.getAttribute('data-approval-id');
                            const keterangan =
                                button.getAttribute('data-keterangan');
                            const createdBy =
                                button.getAttribute('data-created-by');

                            document.getElementById('rejectKeterangan').value =
                                keterangan;
                            document.getElementById('rejectCreatedBy').value =
                                createdBy;

                            const actionUrl =
                                '{{ route("kepala-dapur.approval-transaksi.reject", ["dapur" => $dapur->id_dapur, "approval" => "approval_placeholder"]) }}'.replace(
                                    'approval_placeholder',
                                    approvalId,
                                );
                            rejectForm.action = actionUrl;

                            document.getElementById('alasan_penolakan').value =
                                '';
                        },
                    );
                }

                const selectAllCheckbox = document.getElementById('select-all');
                const bulkCheckboxes =
                    document.querySelectorAll('.bulk-checkbox');
                const selectedCountSpan =
                    document.getElementById('selected-count');
                const bulkActionSubmit =
                    document.getElementById('bulk-action-submit');
                const bulkSelectionInfo = document.getElementById(
                    'bulk-selection-info',
                );
                const bulkActionForm =
                    document.getElementById('bulkActionForm');
                const bulkActionSelect = document.getElementById('bulk_action');
                const bulkKeteranganField =
                    document.getElementById('bulk_keterangan');
                const rejectWarning = document.getElementById('reject-warning');

                function updateBulkSelection() {
                    const checkedBoxes = document.querySelectorAll(
                        '.bulk-checkbox:checked',
                    );
                    const count = checkedBoxes.length;

                    if (selectedCountSpan) {
                        selectedCountSpan.textContent = count + ' dipilih';
                    }

                    if (bulkActionSubmit) {
                        bulkActionSubmit.disabled = count === 0;
                    }

                    if (bulkSelectionInfo) {
                        bulkSelectionInfo.textContent =
                            count > 0
                                ? `${count} transaksi dipilih untuk diproses.`
                                : 'Pilih transaksi dari tabel terlebih dahulu.';
                    }

                    const existingInputs = bulkActionForm.querySelectorAll(
                        'input[name="approval_ids[]"]',
                    );
                    existingInputs.forEach((input) => input.remove());

                    checkedBoxes.forEach((checkbox) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'approval_ids[]';
                        input.value = checkbox.value;
                        bulkActionForm.appendChild(input);
                    });
                }

                if (bulkActionSelect) {
                    bulkActionSelect.addEventListener('change', function () {
                        const isReject = this.value === 'reject';

                        if (isReject) {
                            bulkKeteranganField.required = true;
                            rejectWarning.style.display = 'block';
                            bulkKeteranganField.placeholder =
                                'Alasan penolakan (wajib diisi)...';
                        } else {
                            bulkKeteranganField.required = false;
                            rejectWarning.style.display = 'none';
                            bulkKeteranganField.placeholder =
                                'Catatan untuk semua transaksi yang dipilih...';
                        }
                    });
                }

                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function () {
                        bulkCheckboxes.forEach((checkbox) => {
                            checkbox.checked = this.checked;
                        });
                        updateBulkSelection();
                    });
                }

                bulkCheckboxes.forEach((checkbox) => {
                    checkbox.addEventListener('change', updateBulkSelection);
                });

                const alerts = document.querySelectorAll('.alert-dismissible');
                alerts.forEach((alert) => {
                    setTimeout(() => {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }, 5000);
                });
            });
        </script>
    </div>
@endsection
