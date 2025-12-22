@php
    $layoutTemplate = $layoutTemplate ?? 'template_admin_gudang.layout';
    $routePrefix = $routePrefix ?? 'admin-gudang';
    $stockIndexLabel = $stockIndexLabel ?? 'Kelola Stok';
    $roleType = $roleType ?? 'admin_gudang';
@endphp
@extends($layoutTemplate)

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a
                                href="{{ route($routePrefix . ".dashboard", $dapur) }}"
                                class="text-muted me-2"
                            >
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <a
                                href="{{ route($routePrefix . ".stock.index", $dapur) }}"
                                class="text-muted me-2"
                            >
                                {{ $stockIndexLabel }}
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
                            href="{{ route($routePrefix . ".stock.index", $dapur) }}"
                            class="btn btn-outline-secondary btn-sm"
                        >
                            <i class="bx bx-arrow-back me-1"></i>
                            Kembali
                        </a>
                        @if($roleType === 'admin_gudang')
                            <button
                                type="button"
                                class="btn btn-success btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#requestStockModal"
                            >
                                <i class="bx bx-plus-circle me-1"></i>
                                Ajukan Tambah Stok
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
                                        value="{{ rtrim(rtrim(number_format($stockItem->jumlah, 3), "0"), ".") }}"
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
                                        value="{{ $stockItem->templateItem->satuan }}"
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
                                    <th style="width: 50px;">No</th>
                                    <th>Tanggal Permintaan</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th>Diminta Oleh</th>
                                    <th style="width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($approvalHistory as $index => $history)
                                    <tr>
                                        <td>
                                            {{ $approvalHistory->firstItem() + $index }}
                                        </td>
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
                                                {{ rtrim(rtrim(number_format($history->jumlah, 3), "0"), ".") }}
                                            </span>
                                            <small class="text-muted">
                                                {{ $stockItem->templateItem->satuan }}
                                            </small>
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

                                            <span class="badge {{ $statusClass }}">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded-circle bg-label-info">
                                                        {{ strtoupper(substr($history->adminGudang->user->nama ?? "AG", 0, 2)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="fw-medium">
                                                        {{ $history->adminGudang->user->nama ?? "Admin Gudang" }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#approvalStockDetailModal"
                                                data-approval-data="{{ json_encode([
                                                    'tanggal_permintaan' => $history->created_at->format('d M Y H:i'),
                                                    'jumlah_diminta' => rtrim(rtrim(number_format($history->jumlah, 3), '0'), '.') . ' ' . $stockItem->templateItem->satuan,
                                                    'status' => $history->status,
                                                    'tanggal_diproses' => $history->approved_at ? $history->approved_at->format('d M Y H:i') : '-',
                                                    'nama_pemohon' => $history->adminGudang->user->nama ?? 'Admin Gudang',
                                                    'nama_pemroses' => $history->kepalaDapur && $history->kepalaDapur->user ? $history->kepalaDapur->user->nama : null,
                                                    'jam_kedatangan' => $history->jam_kedatangan ? substr($history->jam_kedatangan, 0, 5) : null,
                                                    'tanggal_produksi' => $history->tanggal_produksi ? $history->tanggal_produksi->format('d/m/Y') : null,
                                                    'tanggal_expired' => $history->tanggal_expired ? $history->tanggal_expired->format('d/m/Y') : null,
                                                    'suhu' => $history->suhu_bahan_makanan,
                                                    'warna' => $history->warna_bahan_makanan,
                                                    'foto_bahan' => $history->foto_bahan ? asset('storage/' . $history->foto_bahan) : null,
                                                    'keterangan' => $history->keterangan
                                                ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) }}"
                                                onclick="showApprovalStockDetailModal(JSON.parse(this.getAttribute('data-approval-data')))"
                                                title="Lihat Detail"
                                            >
                                                <i class="bx bx-show"></i>
                                            </button>
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
                        @if($roleType === 'admin_gudang')
                            <button
                                type="button"
                                class="btn btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#requestStockModal"
                            >
                                <i class="bx bx-plus me-1"></i>
                                Buat Permintaan Pertama
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Request Stock Modal -->
    @if($roleType === 'admin_gudang')
        @include('partials.request-stock-modal')
    @endif

    <!-- Approval Stock Detail Modal -->
    @include('partials.approval-stock-detail-modal')

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

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
            
            // Cleanup any stuck modal backdrops on page load
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
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
