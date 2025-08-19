@extends("template_admin_gudang.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a
                                href="{{ route("admin-gudang.dashboard", $dapur) }}"
                                class="text-muted me-2"
                            >
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <a
                                href="{{ route("admin-gudang.stock.index", $dapur) }}"
                                class="text-muted me-2"
                            >
                                Kelola Stok
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">
                                {{ $stockItem->templateItem->nama_bahan }}
                            </span>
                        </nav>
                        <h4 class="mb-1">
                            Detail Stok -
                            {{ $stockItem->templateItem->nama_bahan }}
                        </h4>
                        <p class="mb-0 text-muted">
                            Informasi detail dan riwayat stok bahan
                            {{ $stockItem->templateItem->nama_bahan }}
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a
                            href="{{ route("admin-gudang.stock.index", $dapur) }}"
                            class="btn btn-outline-secondary btn-sm"
                        >
                            <i class="bx bx-arrow-back me-1"></i>
                            Kembali
                        </a>
                        <button
                            type="button"
                            class="btn btn-success btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#requestStockModal"
                        >
                            <i class="bx bx-plus-circle me-1"></i>
                            Ajukan Tambah Stok
                        </button>
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

        <div class="row">
            <!-- Stock Information -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-package me-2"></i>
                            Informasi Stok
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Nama Bahan</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bx bx-cube"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="form-control"
                                        value="{{ $stockItem->templateItem->nama_bahan }}"
                                        readonly
                                    />
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Jumlah Stok</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bx bx-calculator"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="form-control"
                                        value="{{ number_format($stockItem->jumlah, 3) }}"
                                        readonly
                                    />
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Satuan</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bx bx-tag"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="form-control"
                                        value="{{ $stockItem->satuan }}"
                                        readonly
                                    />
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Status Stok</label>
                                <div class="input-group">
                                    @php
                                        $status = $stockItem->getStockStatus();
                                        $badgeClass = match ($status) {
                                            "habis" => "bg-danger",
                                            "rendah" => "bg-warning",
                                            "normal" => "bg-success",
                                            default => "bg-secondary",
                                        };
                                    @endphp

                                    <span
                                        class="input-group-text badge {{ $badgeClass }}"
                                    >
                                        {{ ucfirst($status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">
                                    Tanggal Restok Terakhir
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bx bx-calendar"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="form-control"
                                        value="{{ $stockItem->tanggal_restok ? $stockItem->tanggal_restok->format("d M Y") : "-" }}"
                                        readonly
                                    />
                                </div>
                            </div>
                            {{--
                                @if ($stockItem->keterangan)
                                <div class="col-12 mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea
                                class="form-control"
                                rows="2"
                                readonly
                                >
                                {{ $stockItem->keterangan }}</textarea
                                >
                                </div>
                                @endif
                            --}}

                            @if ($stockItem->templateItem->keterangan)
                                <div class="col-12">
                                    <label class="form-label">
                                        Deskripsi Bahan
                                    </label>
                                    <textarea
                                        class="form-control"
                                        rows="2"
                                        readonly
                                    >
{{ $stockItem->templateItem->keterangan }}</textarea
                                    >
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Request Statistics -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-bar-chart me-2"></i>
                            Statistik Permintaan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="card border">
                                    <div class="card-body text-center p-3">
                                        <div class="avatar mx-auto mb-2">
                                            <span
                                                class="avatar-initial rounded bg-label-primary"
                                            >
                                                <i class="bx bx-receipt"></i>
                                            </span>
                                        </div>
                                        <h5 class="mb-1">
                                            {{ $totalRequests }}
                                        </h5>
                                        <small class="text-muted">
                                            Total Permintaan
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="card border">
                                    <div class="card-body text-center p-3">
                                        <div class="avatar mx-auto mb-2">
                                            <span
                                                class="avatar-initial rounded bg-label-success"
                                            >
                                                <i
                                                    class="bx bx-check-circle"
                                                ></i>
                                            </span>
                                        </div>
                                        <h5 class="mb-1">
                                            {{ $approvedRequests }}
                                        </h5>
                                        <small class="text-muted">
                                            Disetujui
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="card border">
                                    <div class="card-body text-center p-3">
                                        <div class="avatar mx-auto mb-2">
                                            <span
                                                class="avatar-initial rounded bg-label-warning"
                                            >
                                                <i class="bx bx-time"></i>
                                            </span>
                                        </div>
                                        <h5 class="mb-1">
                                            {{ $pendingRequests }}
                                        </h5>
                                        <small class="text-muted">
                                            Menunggu
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="card border">
                                    <div class="card-body text-center p-3">
                                        <div class="avatar mx-auto mb-2">
                                            <span
                                                class="avatar-initial rounded bg-label-danger"
                                            >
                                                <i class="bx bx-x-circle"></i>
                                            </span>
                                        </div>
                                        <h5 class="mb-1">
                                            {{ $rejectedRequests }}
                                        </h5>
                                        <small class="text-muted">
                                            Ditolak
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($totalRequests > 0)
                            <div class="mt-3">
                                <small class="text-muted">
                                    Tingkat Persetujuan:
                                </small>
                                <div class="progress mt-1">
                                    @php
                                        $approvalRate = $totalRequests > 0 ? ($approvedRequests / $totalRequests) * 100 : 0;
                                    @endphp

                                    <div
                                        class="progress-bar bg-success"
                                        role="progressbar"
                                        style="width: {{ $approvalRate }}%"
                                        aria-valuenow="{{ $approvalRate }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                    >
                                        {{ number_format($approvalRate, 1) }}%
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Request History -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bx bx-history me-2"></i>
                    Riwayat Permintaan Stok
                </h5>
            </div>
            <div class="card-body">
                @if ($approvalHistory->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal Permintaan</th>
                                    <th>Jumlah Diminta</th>
                                    <th>Diminta Oleh</th>
                                    <th>Diproses Oleh</th>
                                    <th>Status</th>
                                    <th>Tanggal Diproses</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($approvalHistory as $history)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-medium">
                                                    {{ $history->created_at->format("d M Y") }}
                                                </span>
                                                <small class="text-muted">
                                                    {{ $history->created_at->format("H:i") }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-medium">
                                                {{ number_format($history->jumlah, 3) }}
                                            </span>
                                            <small class="text-muted">
                                                {{ $history->satuan }}
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
                                                        {{ strtoupper(substr($history->adminGudang->user->nama ?? "AG", 0, 2)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="fw-medium">
                                                        {{ $history->adminGudang->user->nama ?? "Admin Gudang" }}
                                                    </span>
                                                    <br />
                                                    <small class="text-muted">
                                                        Admin Gudang
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($history->kepalaDapur && $history->kepalaDapur->user)
                                                <div
                                                    class="d-flex align-items-center"
                                                >
                                                    <div
                                                        class="avatar avatar-sm me-2"
                                                    >
                                                        <span
                                                            class="avatar-initial rounded-circle bg-label-primary"
                                                        >
                                                            {{ strtoupper(substr($history->kepalaDapur->user->nama, 0, 2)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span class="fw-medium">
                                                            {{ $history->kepalaDapur->user->nama }}
                                                        </span>
                                                        <br />
                                                        <small
                                                            class="text-muted"
                                                        >
                                                            Kepala Dapur
                                                        </small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">
                                                    -
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match ($history->status) {
                                                    "approved" => "bg-label-success",
                                                    "rejected" => "bg-label-danger",
                                                    "pending" => "bg-label-warning",
                                                    default => "bg-label-secondary",
                                                };
                                                $statusText = match ($history->status) {
                                                    "approved" => "Disetujui",
                                                    "rejected" => "Ditolak",
                                                    "pending" => "Menunggu",
                                                    default => ucfirst($history->status),
                                                };
                                            @endphp

                                            <span
                                                class="badge {{ $statusClass }}"
                                            >
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($history->approved_at)
                                                <div class="d-flex flex-column">
                                                    <span class="fw-medium">
                                                        {{ $history->approved_at->format("d M Y") }}
                                                    </span>
                                                    <small class="text-muted">
                                                        {{ $history->approved_at->format("H:i") }}
                                                    </small>
                                                </div>
                                            @else
                                                <span class="text-muted">
                                                    -
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($history->keterangan)
                                                <span
                                                    data-bs-toggle="tooltip"
                                                    title="{{ $history->keterangan }}"
                                                >
                                                    {{ Str::limit($history->keterangan, 30) }}
                                                </span>
                                            @else
                                                <span class="text-muted">
                                                    -
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($approvalHistory->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $approvalHistory->links("vendor.pagination.bootstrap-5") }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-6">
                        <i class="bx bx-history bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">Belum ada riwayat permintaan</h5>
                        <p class="text-muted mb-3">
                            Belum ada permintaan penambahan stok untuk bahan
                            ini.
                        </p>
                        <button
                            type="button"
                            class="btn btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#requestStockModal"
                        >
                            <i class="bx bx-plus me-1"></i>
                            Buat Permintaan Pertama
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Request Stock Modal -->
    <div
        class="modal fade"
        id="requestStockModal"
        tabindex="-1"
        aria-labelledby="requestStockModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="requestStockModalLabel">
                        Ajukan Tambah Stok
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <form
                    action="{{ route("admin-gudang.stock.request", [$dapur, $stockItem]) }}"
                    method="POST"
                >
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Bahan</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $stockItem->templateItem->nama_bahan }}"
                                readonly
                            />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stok Saat Ini</label>
                            <div class="input-group">
                                <input
                                    type="text"
                                    class="form-control"
                                    value="{{ number_format($stockItem->jumlah, 3) }}"
                                    readonly
                                />
                                <span class="input-group-text">
                                    {{ $stockItem->satuan }}
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">
                                Jumlah Penambahan
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input
                                    type="number"
                                    name="jumlah"
                                    id="jumlah"
                                    class="form-control @error("jumlah") is-invalid @enderror"
                                    step="0.001"
                                    min="0.1"
                                    max="999999.999"
                                    required
                                    placeholder="0.000"
                                    value="{{ old("jumlah") }}"
                                />
                                <span class="input-group-text">
                                    {{ $stockItem->satuan }}
                                </span>
                            </div>
                            @error("jumlah")
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">
                                Keterangan
                            </label>
                            <textarea
                                name="keterangan"
                                id="keterangan"
                                class="form-control @error("keterangan") is-invalid @enderror"
                                rows="3"
                                maxlength="500"
                                placeholder="Alasan penambahan stok (opsional)..."
                            >
{{ old("keterangan") }}</textarea
                            >
                            @error("keterangan")
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="form-text">Maksimal 500 karakter</div>
                        </div>

                        <!-- Preview Section -->
                        <div class="bg-light rounded p-3">
                            <h6 class="mb-2">Preview Permintaan:</h6>
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">
                                        Stok Saat Ini:
                                    </small>
                                    <div class="fw-medium">
                                        {{ number_format($stockItem->jumlah, 3) }}
                                        {{ $stockItem->satuan }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">
                                        Stok Setelah Disetujui:
                                    </small>
                                    <div
                                        class="fw-medium text-success"
                                        id="previewStock"
                                    >
                                        -
                                    </div>
                                </div>
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
                            <i class="bx bx-send me-1"></i>
                            Ajukan Permintaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Custom Styling -->
    <style>
        .avatar-initial {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
        }
        .avatar-sm .avatar-initial {
            width: 32px;
            height: 32px;
            font-size: 12px;
        }
        .card-body .input-group-text {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
        .progress {
            height: 8px;
        }
        .table td {
            vertical-align: middle;
        }
        .bg-light {
            background-color: #f8f9fa !important;
        }
    </style>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Bootstrap tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(
                tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl)
            );

            // Handle request stock modal
            const requestStockModal = document.getElementById('requestStockModal');
            const jumlahInput = document.getElementById('jumlah');
            const previewStock = document.getElementById('previewStock');
            const currentStock = {{ $stockItem->jumlah }};
            const satuan = '{{ $stockItem->satuan }}';

            // Update preview when amount changes
            if (jumlahInput && previewStock) {
                function updatePreview() {
                    const additionalAmount = parseFloat(jumlahInput.value) || 0;
                    const newStock = currentStock + additionalAmount;
                    previewStock.textContent = newStock.toFixed(3) + ' ' + satuan;
                }

                jumlahInput.addEventListener('input', updatePreview);

                // Initialize preview
                updatePreview();
            }

            // Show modal if there are validation errors
            @if($errors->any())
                const modal = new bootstrap.Modal(requestStockModal);
                modal.show();
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
