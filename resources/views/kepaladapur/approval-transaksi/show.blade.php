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
                            <a
                                href="{{ route("kepala-dapur.approval-transaksi.index", ["dapur" => $dapur->id_dapur]) }}"
                                class="text-muted me-2"
                            >
                                Persetujuan Transaksi
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Detail Transaksi</span>
                        </nav>
                        <h4 class="mb-1">
                            Detail Transaksi - {{ $dapur->nama_dapur }}
                        </h4>
                        <p class="mb-0 text-muted">
                            ID Transaksi:
                            {{ $approval->transaksiDapur->id_transaksi }}
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

        <div class="row">
            <!-- Left Column - Transaction Details -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-receipt me-2"></i>
                            Informasi Transaksi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">
                                        Tanggal Transaksi
                                    </label>
                                    <div class="fw-medium">
                                        {{ $approval->transaksiDapur->tanggal_transaksi->format("d F Y, H:i") }}
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">
                                        Status Approval
                                    </label>
                                    <div>
                                        <span
                                            class="badge {{ $approval->getStatusBadgeClass() }} fs-6"
                                        >
                                            {{ ucfirst($approval->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">
                                        Total Porsi
                                    </label>
                                    <div class="fw-medium text-primary fs-5">
                                        {{ $approval->transaksiDapur->total_porsi }}
                                        porsi
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">
                                        Dibuat Oleh
                                    </label>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span
                                                class="avatar-initial rounded-circle bg-label-info"
                                            >
                                                {{ strtoupper(substr($approval->transaksiDapur->createdBy->nama ?? "NA", 0, 2)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-medium">
                                                {{ $approval->transaksiDapur->createdBy->nama ?? "Unknown" }}
                                            </div>
                                            <small class="text-muted">
                                                Ahli Gizi
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">
                                        Keterangan
                                    </label>
                                    <div class="fw-medium">
                                        {{ $approval->transaksiDapur->keterangan ?? "Paket Menu Harian" }}
                                    </div>
                                </div>
                                @if ($approval->approved_at)
                                    <div class="mb-3">
                                        <label class="form-label text-muted">
                                            Tanggal Diproses
                                        </label>
                                        <div class="fw-medium">
                                            {{ $approval->approved_at->format("d F Y, H:i") }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($approval->keterangan)
                            <div class="mt-4">
                                <label class="form-label text-muted">
                                    Catatan Approval
                                </label>
                                <div class="p-3 bg-light rounded">
                                    {{ $approval->keterangan }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Menu Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-food-menu me-2"></i>
                            Detail Menu
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Menu</th>
                                        <th>Tipe Porsi</th>
                                        <th>Jumlah</th>
                                        <th>Bahan Diperlukan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($approval->transaksiDapur->detailTransaksiDapur as $detail)
                                        <tr>
                                            <td>
                                                <div
                                                    class="d-flex align-items-center"
                                                >
                                                    <div
                                                        class="avatar avatar-sm me-2"
                                                    >
                                                        <span
                                                            class="avatar-initial rounded bg-label-primary"
                                                        >
                                                            {{ strtoupper(substr($detail->menuMakanan->nama_menu ?? "M", 0, 2)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">
                                                            {{ $detail->menuMakanan->nama_menu ?? "Menu Tidak Ditemukan" }}
                                                        </h6>
                                                        <small
                                                            class="text-muted"
                                                        >
                                                            {{ $detail->menuMakanan->jenis_menu ?? "" }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ $detail->getTipePorsiBadgeClass() }}"
                                                >
                                                    {{ $detail->getTipePorsiText() }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-medium">
                                                    {{ $detail->jumlah_porsi }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $ingredients = $detail->getRequiredIngredients();
                                                @endphp

                                                <div
                                                    class="d-flex flex-wrap gap-1"
                                                >
                                                    @foreach (array_slice($ingredients, 0, 3) as $ingredient)
                                                        <span
                                                            class="badge bg-light text-dark"
                                                        >
                                                            {{ $ingredient["nama_bahan"] }}:
                                                            {{ $ingredient["total_needed"] }}
                                                            {{ $ingredient["satuan"] }}
                                                        </span>
                                                    @endforeach

                                                    @if (count($ingredients) > 3)
                                                        <span
                                                            class="badge bg-secondary"
                                                        >
                                                            +{{ count($ingredients) - 3 }}
                                                            lainnya
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Stock Availability -->
                <div class="card">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5 class="card-title mb-0">
                            <i class="bx bx-package me-2"></i>
                            Ketersediaan Stock
                        </h5>
                        @if (! $stockCheck["can_produce"])
                            <span class="badge bg-danger">
                                Stock Tidak Mencukupi
                            </span>
                        @else
                            <span class="badge bg-success">
                                Stock Mencukupi
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if (! $stockCheck["can_produce"])
                            <div class="alert alert-danger mb-4">
                                <i class="bx bx-error-circle me-2"></i>
                                <strong>Peringatan:</strong>
                                Beberapa bahan tidak tersedia dalam jumlah yang
                                cukup untuk produksi ini.
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Bahan</th>
                                        <th>Diperlukan</th>
                                        <th>Tersedia</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stockCheck["ingredients_summary"] as $ingredient)
                                        <tr
                                            class="{{ ! $ingredient["sufficient"] ? "table-danger-subtle" : "" }}"
                                        >
                                            <td>
                                                <div class="fw-medium">
                                                    {{ $ingredient["nama_bahan"] }}
                                                </div>
                                                <small class="text-muted">
                                                    {{ $ingredient["satuan"] }}
                                                </small>
                                            </td>
                                            <td>
                                                {{ $ingredient["needed"] }}
                                            </td>
                                            <td>
                                                {{ $ingredient["available"] }}
                                            </td>
                                            <td>
                                                @if ($ingredient["sufficient"])
                                                    <span
                                                        class="badge bg-success"
                                                    >
                                                        Cukup
                                                    </span>
                                                @else
                                                    <span
                                                        class="badge bg-danger"
                                                    >
                                                        Kurang
                                                        {{ $ingredient["needed"] - $ingredient["available"] }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Summary & Actions -->
            <div class="col-lg-4">
                <!-- Summary Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-pie-chart-alt me-2"></i>
                            Ringkasan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span
                                    class="avatar-initial rounded bg-label-primary"
                                >
                                    <i class="bx bx-food-menu"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">
                                    Total Menu
                                </small>
                                <h6 class="mb-0">
                                    {{ $approval->transaksiDapur->detailTransaksiDapur->count() }}
                                </h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span
                                    class="avatar-initial rounded bg-label-success"
                                >
                                    <i class="bx bx-user-plus"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">
                                    Porsi Besar
                                </small>
                                <h6 class="mb-0">
                                    {{ $approval->transaksiDapur->getTotalPorsiBesar() }}
                                </h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span
                                    class="avatar-initial rounded bg-label-info"
                                >
                                    <i class="bx bx-user"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">
                                    Porsi Kecil
                                </small>
                                <h6 class="mb-0">
                                    {{ $approval->transaksiDapur->getTotalPorsiKecil() }}
                                </h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span
                                    class="avatar-initial rounded bg-label-warning"
                                >
                                    <i class="bx bx-package"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">
                                    Total Bahan
                                </small>
                                <h6 class="mb-0">
                                    {{ count($stockCheck["ingredients_summary"]) }}
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline Card -->
                @if ($approval->approved_at || $approval->created_at)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-time me-2"></i>
                                Timeline
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline timeline-sm">
                                <div class="timeline-item">
                                    <span
                                        class="timeline-indicator timeline-indicator-success"
                                    >
                                        <i class="bx bx-plus"></i>
                                    </span>
                                    <div class="timeline-content">
                                        <div class="fw-medium">
                                            Transaksi Dibuat
                                        </div>
                                        <small class="text-muted">
                                            {{ $approval->transaksiDapur->created_at->format("d M Y, H:i") }}
                                        </small>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <span
                                        class="timeline-indicator timeline-indicator-warning"
                                    >
                                        <i class="bx bx-time"></i>
                                    </span>
                                    <div class="timeline-content">
                                        <div class="fw-medium">
                                            Diajukan untuk Approval
                                        </div>
                                        <small class="text-muted">
                                            {{ $approval->created_at->format("d M Y, H:i") }}
                                        </small>
                                    </div>
                                </div>
                                @if ($approval->approved_at)
                                    <div class="timeline-item">
                                        <span
                                            class="timeline-indicator {{ $approval->isApproved() ? "timeline-indicator-success" : "timeline-indicator-danger" }}"
                                        >
                                            <i
                                                class="bx {{ $approval->isApproved() ? "bx-check" : "bx-x" }}"
                                            ></i>
                                        </span>
                                        <div class="timeline-content">
                                            <div class="fw-medium">
                                                {{ $approval->isApproved() ? "Disetujui" : "Ditolak" }}
                                            </div>
                                            <small class="text-muted">
                                                {{ $approval->approved_at->format("d M Y, H:i") }}
                                            </small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Status Actions -->
                @if ($approval->isPending())
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-cog me-2"></i>
                                Aksi
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button
                                    type="button"
                                    class="btn btn-success"
                                    data-bs-toggle="modal"
                                    data-bs-target="#approveModal"
                                >
                                    <i class="bx bx-check me-1"></i>
                                    Setujui Transaksi
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectModal"
                                >
                                    <i class="bx bx-x me-1"></i>
                                    Tolak Transaksi
                                </button>
                            </div>
                            @if (! $stockCheck["can_produce"])
                                <div class="alert alert-warning mt-3">
                                    <i class="bx bx-error-circle me-1"></i>
                                    <small>
                                        <strong>Perhatian:</strong>
                                        Stock tidak mencukupi. Pastikan untuk
                                        menolak atau tunggu stock tersedia.
                                    </small>
                                </div>
                            @endif
                        </div>
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
                    <form
                        method="POST"
                        action="{{ route("kepala-dapur.approval-transaksi.approve", ["dapur" => $dapur->id_dapur, "approval" => $approval->id_approval_transaksi]) }}"
                    >
                        @csrf
                        <div class="modal-body">
                            @if (! $stockCheck["can_produce"])
                                <div class="alert alert-warning">
                                    <i class="bx bx-error-circle me-2"></i>
                                    <strong>Peringatan:</strong>
                                    Stock tidak mencukupi untuk produksi ini.
                                    Pastikan Anda yakin ingin menyetujui
                                    transaksi ini.
                                </div>
                            @else
                                <div class="alert alert-success">
                                    <i class="bx bx-check-circle me-2"></i>
                                    Dengan menyetujui transaksi ini, stock bahan
                                    akan otomatis dikurangi sesuai kebutuhan
                                    produksi.
                                </div>
                            @endif
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
                    <form
                        method="POST"
                        action="{{ route("kepala-dapur.approval-transaksi.reject", ["dapur" => $dapur->id_dapur, "approval" => $approval->id_approval_transaksi]) }}"
                    >
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-danger">
                                <i class="bx bx-x-circle me-2"></i>
                                Transaksi yang ditolak akan dibatalkan dan tidak
                                dapat diproses.
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

        <!-- Custom Styling -->
        <style>
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
            .table-danger-subtle {
                background-color: rgba(220, 53, 69, 0.1) !important;
            }
            .timeline {
                position: relative;
                padding-left: 1.5rem;
            }
            .timeline::before {
                content: '';
                position: absolute;
                left: 0.5rem;
                top: 0;
                bottom: 0;
                width: 2px;
                background: #e3e3e3;
            }
            .timeline-item {
                position: relative;
                margin-bottom: 1.5rem;
            }
            .timeline-indicator {
                position: absolute;
                left: -2rem;
                top: 0.25rem;
                width: 2rem;
                height: 2rem;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.75rem;
                z-index: 1;
            }
            .timeline-indicator-success {
                background: #28a745;
                color: white;
            }
            .timeline-indicator-warning {
                background: #ffc107;
                color: #212529;
            }
            .timeline-indicator-danger {
                background: #dc3545;
                color: white;
            }
            .timeline-content {
                padding-left: 1rem;
            }
        </style>

        <!-- JavaScript -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
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
