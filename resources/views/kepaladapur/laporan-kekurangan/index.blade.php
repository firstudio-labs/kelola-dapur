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
                                href="{{ route('kepala-dapur.dashboard', $currentDapur->id_dapur ?? (auth()->user()->userRole->id_dapur ?? null)) }}"
                                class="text-muted me-2"
                            >
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Laporan Kekurangan Stok</span>
                        </nav>
                        <h4 class="mb-1">
                            Laporan Kekurangan Stok - {{ $currentDapur->nama_dapur ?? 'Dapur' }}
                        </h4>
                        <p class="mb-0 text-muted">
                            Kelola laporan kekurangan stok untuk dapur Anda
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        @if ($stats['pending'] > 0)
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
        @if (session('success'))
            <div
                class="alert alert-success alert-dismissible mb-4"
                role="alert"
            >
                {{ session('success') }}
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"
                ></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                {{ session('error') }}
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
                                    Total Transaksi dengan Kekurangan
                                </small>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-1">
                                        {{ $stats['total'] }}
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
                                    Menunggu Penyelesaian
                                </small>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-1">
                                        {{ $stats['pending'] }}
                                    </h6>
                                    @if ($stats['pending'] > 0)
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
                                    Diselesaikan
                                </small>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-1">
                                        {{ $stats['resolved'] }}
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
                                    Total Kekurangan Bahan
                                </small>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-1">
                                        {{ $stats['total_kekurangan_bahan'] }}
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
                    action="{{ route('kepala-dapur.laporan-kekurangan.index') }}"
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
                                value="{{ request('search') }}"
                                class="form-control"
                                placeholder="Cari pembuat..."
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
                                {{ request('status') === '' ? 'selected' : '' }}
                            >
                                Semua Status
                            </option>
                            <option
                                value="pending"
                                {{ request('status') === 'pending' ? 'selected' : '' }}
                            >
                                Menunggu
                            </option>
                            <option
                                value="resolved"
                                {{ request('status') === 'resolved' ? 'selected' : '' }}
                            >
                                Diselesaikan
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
                            value="{{ request('date_from') }}"
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
                            value="{{ request('date_to') }}"
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
                                {{ request('sort') === 'created_at' ? 'selected' : '' }}
                            >
                                Tanggal Transaksi
                            </option>
                            <option
                                value="created_by"
                                {{ request('sort') === 'created_by' ? 'selected' : '' }}
                            >
                                Dibuat Oleh
                            </option>
                            <option
                                value="total_porsi"
                                {{ request('sort') === 'total_porsi' ? 'selected' : '' }}
                            >
                                Total Porsi
                            </option>
                        </select>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                        @if (request()->hasAny(['search', 'status', 'date_from', 'date_to', 'sort']))
                            <a
                                href="{{ route('kepala-dapur.laporan-kekurangan.index') }}"
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

        <!-- Reports Table -->
        <div class="card">
            <div class="card-body">
                @if ($transaksi->isNotEmpty())
                    <!-- Bulk Action Checkboxes -->
                    @if ($transaksi->where('laporanKekuranganStock.status', 'pending')->count() > 0)
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
                                    @if ($transaksi->where('laporanKekuranganStock.status', 'pending')->count() > 0)
                                        <th width="50">
                                            <i class="bx bx-check-square"></i>
                                        </th>
                                    @endif
                                    <th>No</th>
                                    <th>Tanggal Transaksi</th>
                                    <th>Nama Paket</th>
                                    <th>Total Porsi</th>
                                    <th>Jumlah Kekurangan</th>
                                    <th>Dibuat Oleh</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transaksi as $index => $transaksiItem)
                                    <tr
                                        class="{{ $transaksiItem->laporanKekuranganStock->where('status', 'pending')->isNotEmpty() ? 'table-warning-subtle' : '' }}"
                                    >
                                        @if ($transaksi->where('laporanKekuranganStock.status', 'pending')->count() > 0)
                                            <td>
                                                @if ($transaksiItem->laporanKekuranganStock->where('status', 'pending')->isNotEmpty())
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input bulk-checkbox"
                                                        value="{{ $transaksiItem->id_transaksi }}"
                                                        data-laporan-ids="{{ json_encode($transaksiItem->laporanKekuranganStock->where('status', 'pending')->pluck('id_laporan')) }}"
                                                    />
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            {{ $transaksi->firstItem() + $index }}
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-medium">
                                                    {{ $transaksiItem->tanggal_transaksi->format('d M Y') }}
                                                </span>
                                                <small class="text-muted">
                                                    {{ $transaksiItem->tanggal_transaksi->format('H:i') }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <h6 class="mb-0">
                                                        {{ $transaksiItem->nama_paket }}
                                                    </h6>
                                                    <small class="text-muted">
                                                        ID: {{ $transaksiItem->id_transaksi }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-primary">
                                                {{ $transaksiItem->total_porsi }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $transaksiItem->laporanKekuranganStock->count() }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                {{-- <div class="avatar avatar-sm me-2">
                                                    <span
                                                        class="avatar-initial rounded-circle bg-label-info"
                                                    >
                                                        {{ strtoupper(substr($transaksiItem->createdBy->nama ?? 'NA', 0, 2)) }}
                                                    </span>
                                                </div> --}}
                                                <div>
                                                    <span class="fw-medium">
                                                        {{ $transaksiItem->createdBy->nama ?? 'Unknown' }}
                                                    </span>
                                                    <br />
                                                    <small class="text-muted">
                                                        Pembuat
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = $transaksiItem->laporanKekuranganStock->where('status', 'pending')->isNotEmpty() ? 'bg-label-warning' : 'bg-label-success';
                                                $statusText = $transaksiItem->laporanKekuranganStock->where('status', 'pending')->isNotEmpty() ? 'Menunggu' : 'Diselesaikan';
                                            @endphp
                                            <span class="badge {{ $statusClass }}">
                                                {{ $statusText }}
                                            </span>
                                            @if ($transaksiItem->laporanKekuranganStock->where('status', 'resolved')->isNotEmpty())
                                                <small class="text-muted d-block">
                                                    {{ $transaksiItem->laporanKekuranganStock->where('status', 'resolved')->first()->updated_at->format('d/m/Y H:i') }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a
                                                    href="{{ route('kepala-dapur.laporan-kekurangan.show', $transaksiItem) }}"
                                                    class="btn btn-sm btn-outline-primary action-btn"
                                                    data-bs-toggle="tooltip"
                                                    title="Lihat Detail"
                                                >
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                @if ($transaksiItem->laporanKekuranganStock->where('status', 'pending')->isNotEmpty())
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-success action-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#resolveModal"
                                                        data-transaksi-id="{{ $transaksiItem->id_transaksi }}"
                                                        data-created-by="{{ $transaksiItem->createdBy->nama ?? 'Unknown' }}"
                                                        data-laporan-ids="{{ json_encode($transaksiItem->laporanKekuranganStock->where('status', 'pending')->pluck('id_laporan')) }}"
                                                        title="Selesaikan"
                                                    >
                                                        <i class="bx bx-check"></i>
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
                    @if ($transaksi->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $transaksi->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-6">
                        <i class="bx bx-receipt bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">Tidak ada laporan kekurangan stok</h5>
                        <p class="text-muted mb-3">
                            Belum ada laporan kekurangan stok yang sesuai dengan filter.
                        </p>
                        @if (request()->hasAny(['search', 'status', 'date_from', 'date_to', 'sort']))
                            <a
                                href="{{ route('kepala-dapur.laporan-kekurangan.index') }}"
                                class="btn btn-outline-primary"
                            >
                                Reset Filter
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Resolve Modal -->
        <div
            class="modal fade"
            id="resolveModal"
            tabindex="-1"
            aria-labelledby="resolveModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <form
                        id="resolveForm"
                        method="POST"
                        action="{{ route('kepala-dapur.laporan-kekurangan.bulk-resolve') }}"
                    >
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="resolveModalLabel">
                                Selesaikan Laporan Kekurangan
                            </h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label
                                    for="resolveCreatedBy"
                                    class="form-label"
                                >
                                    Dibuat Oleh
                                </label>
                                <input
                                    type="text"
                                    id="resolveCreatedBy"
                                    class="form-control"
                                    readonly
                                />
                            </div>
                            <div class="mb-3">
                                <label
                                    for="keterangan_resolve"
                                    class="form-label"
                                >
                                    Keterangan (Opsional)
                                </label>
                                <textarea
                                    id="keterangan_resolve"
                                    name="catatan"
                                    class="form-control"
                                    rows="4"
                                ></textarea>
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
                            >
                                Selesaikan
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
                        action="{{ route('kepala-dapur.laporan-kekurangan.bulk-resolve') }}"
                        method="POST"
                    >
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="bulk_keterangan" class="form-label">
                                    Catatan Penyelesaian (Opsional)
                                </label>
                                <textarea
                                    name="catatan"
                                    id="bulk_keterangan"
                                    class="form-control"
                                    rows="3"
                                    maxlength="500"
                                    placeholder="Catatan untuk semua laporan yang dipilih..."
                                ></textarea>
                                <div class="form-text">Maksimal 500 karakter</div>
                            </div>
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-2"></i>
                                <span id="bulk-selection-info">
                                    Pilih laporan dari tabel terlebih dahulu.
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
                                Selesaikan Laporan
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
                selects.forEach((select) => {
                    new Choices(select, {
                        searchEnabled: false,
                        itemSelectText: '',
                        shouldSort: false,
                    });
                });

                // Initialize Bootstrap tooltips
                const tooltipTriggerList = document.querySelectorAll(
                    '[data-bs-toggle="tooltip"]',
                );
                const tooltipList = [...tooltipTriggerList].map(
                    (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl),
                );

                // Handle resolve modal
                const resolveModal = document.getElementById('resolveModal');
                if (resolveModal) {
                    resolveModal.addEventListener('show.bs.modal', function (event) {
                        const button = event.relatedTarget;
                        const createdBy = button.getAttribute('data-created-by');
                        const laporanIds = JSON.parse(button.getAttribute('data-laporan-ids') || '[]');

                        document.getElementById('resolveCreatedBy').value = createdBy;

                        const form = document.getElementById('resolveForm');
                        // Remove existing laporan_ids inputs
                        const existingInputs = form.querySelectorAll('input[name="laporan_ids[]"]');
                        existingInputs.forEach(input => input.remove());

                        // Add new laporan_ids inputs
                        laporanIds.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'laporan_ids[]';
                            input.value = id;
                            form.appendChild(input);
                        });

                        // Reset textarea
                        document.getElementById('keterangan_resolve').value = '';
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
                    let totalLaporan = 0;
                    const allLaporanIds = [];

                    checkedBoxes.forEach(checkbox => {
                        const laporanIds = JSON.parse(checkbox.getAttribute('data-laporan-ids') || '[]');
                        totalLaporan += laporanIds.length;
                        allLaporanIds.push(...laporanIds);
                    });

                    if (selectedCountSpan) {
                        selectedCountSpan.textContent = count + ' transaksi dipilih (' + totalLaporan + ' laporan)';
                    }

                    if (bulkActionSubmit) {
                        bulkActionSubmit.disabled = count === 0;
                    }

                    if (bulkSelectionInfo) {
                        bulkSelectionInfo.textContent = count > 0 
                            ? `${count} transaksi dipilih (${totalLaporan} laporan kekurangan akan diselesaikan).`
                            : 'Pilih transaksi dari tabel terlebih dahulu.';
                    }

                    // Update hidden inputs with laporan_ids
                    const existingInputs = bulkActionForm.querySelectorAll('input[name="laporan_ids[]"]');
                    existingInputs.forEach(input => input.remove());

                    allLaporanIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'laporan_ids[]';
                        input.value = id;
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

                // Auto-hide alerts after 5 seconds
                const alerts = document.querySelectorAll('.alert-dismissible');
                alerts.forEach((alert) => {
                    setTimeout(() => {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }, 5000);
                });
            });
        </script>
    @endsection