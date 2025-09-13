@extends("template_admin.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a
                                href="{{ route("superadmin.dashboard") }}"
                                class="text-muted me-2"
                            >
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Request Subscription</span>
                        </nav>
                        <h4 class="mb-1">Kelola Request Subscription</h4>
                        <p class="mb-0 text-muted">
                            Approve atau reject request subscription dari dapur
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

        <!-- Filter Tabs -->
        <div class="card mb-4">
            <div class="card-body">
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item">
                        <a
                            class="nav-link {{ request()->routeIs("superadmin.subscription-requests.index") ? "active" : "" }}"
                            href="{{ route("superadmin.subscription-requests.index") }}"
                        >
                            <i class="bx bx-list-ul me-1"></i>
                            Semua
                            <span class="badge bg-secondary ms-1">
                                {{ $subscriptionRequests->total() }}
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a
                            class="nav-link {{ request()->routeIs("superadmin.subscription-requests.pending") ? "active" : "" }}"
                            href="{{ route("superadmin.subscription-requests.pending") }}"
                        >
                            <i class="bx bx-time me-1"></i>
                            Pending
                            <span class="badge bg-warning ms-1">
                                {{ $subscriptionRequests->where("status", "pending")->count() }}
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a
                            class="nav-link {{ request()->routeIs("superadmin.subscription-requests.approved") ? "active" : "" }}"
                            href="{{ route("superadmin.subscription-requests.approved") }}"
                        >
                            <i class="bx bx-check me-1"></i>
                            Approved
                            <span class="badge bg-success ms-1">
                                {{ $subscriptionRequests->where("status", "approved")->count() }}
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a
                            class="nav-link {{ request()->routeIs("superadmin.subscription-requests.rejected") ? "active" : "" }}"
                            href="{{ route("superadmin.subscription-requests.rejected") }}"
                        >
                            <i class="bx bx-x me-1"></i>
                            Rejected
                            <span class="badge bg-danger ms-1">
                                {{ $subscriptionRequests->where("status", "rejected")->count() }}
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bulk Action for Pending -->
        @if (request()->routeIs("superadmin.subscription-requests.pending") && $subscriptionRequests->count() > 0)
            <div class="card mb-4">
                <div class="card-body">
                    <form
                        method="POST"
                        action="{{ route("superadmin.subscription-requests.bulk-action") }}"
                        onsubmit="return confirmBulkAction()"
                    >
                        @csrf
                        <div
                            class="d-flex justify-content-between align-items-center"
                        >
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="select-all"
                                />
                                <label
                                    class="form-check-label"
                                    for="select-all"
                                >
                                    Pilih Semua
                                </label>
                            </div>
                            <div class="d-flex gap-2">
                                <select
                                    name="action"
                                    class="form-select form-select-sm"
                                    style="width: auto"
                                    required
                                >
                                    <option value="">Pilih Aksi</option>
                                    <option value="approve">
                                        Approve Terpilih
                                    </option>
                                    <option value="reject">
                                        Reject Terpilih
                                    </option>
                                </select>
                                <input
                                    type="text"
                                    name="catatan"
                                    class="form-control form-control-sm"
                                    placeholder="Catatan (opsional)"
                                    style="width: 200px"
                                />
                                <button
                                    type="submit"
                                    class="btn btn-primary btn-sm"
                                >
                                    Jalankan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Subscription Requests List -->
        <div class="card mb-4">
            <div class="card-body">
                @if ($subscriptionRequests->count() > 0)
                    <div class="table-responsive">
                        <table
                            class="table table-bordered table-striped table-hover"
                        >
                            <thead>
                                <tr>
                                    @if (request()->routeIs("superadmin.subscription-requests.pending"))
                                        <th style="width: 5%">
                                            <input
                                                type="checkbox"
                                                class="form-check-input"
                                                id="check-all"
                                            />
                                        </th>
                                    @endif

                                    <th style="width: 5%">No</th>
                                    <th style="width: 20%">Dapur</th>
                                    <th style="width: 15%">Paket</th>
                                    <th style="width: 10%">Promo</th>
                                    <th style="width: 12%">Harga Final</th>
                                    <th style="width: 10%">Tanggal Request</th>
                                    <th style="width: 8%">Status</th>
                                    <th style="width: 15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subscriptionRequests as $request)
                                    <tr>
                                        @if (request()->routeIs("superadmin.subscription-requests.pending"))
                                            <td>
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input request-checkbox"
                                                    name="selected_requests[]"
                                                    value="{{ $request->id_subscription_request }}"
                                                />
                                            </td>
                                        @endif

                                        <td>
                                            {{ $subscriptionRequests->firstItem() + $loop->index }}
                                        </td>
                                        <td>
                                            <div
                                                class="d-flex align-items-center"
                                            >
                                                <div
                                                    class="avatar flex-shrink-0 me-3"
                                                >
                                                    <span
                                                        class="avatar-initial rounded bg-label-primary"
                                                    >
                                                        <i
                                                            class="bx bx-buildings"
                                                        ></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <strong>
                                                        {{ $request->dapur->nama_dapur }}
                                                    </strong>
                                                    <br />
                                                    <small class="text-muted">
                                                        ID:
                                                        {{ $request->dapur->id_dapur }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>
                                                {{ $request->package->nama_paket }}
                                            </strong>
                                            <br />
                                            <small class="text-muted">
                                                {{ $request->package->durasi_text }}
                                            </small>
                                        </td>
                                        <td>
                                            @if ($request->promoCode)
                                                <span
                                                    class="badge bg-label-info"
                                                >
                                                    {{ $request->promoCode->kode_promo }}
                                                </span>
                                                <br />
                                                <small class="text-muted">
                                                    -{{ $request->promoCode->persentase_diskon }}%
                                                </small>
                                            @else
                                                <span class="text-muted">
                                                    -
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong class="text-primary">
                                                {{ $request->formatted_harga_final }}
                                            </strong>
                                            @if ($request->diskon > 0)
                                                <br />
                                                <small class="text-muted">
                                                    <del>
                                                        {{ $request->formatted_harga_asli }}
                                                    </del>
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $request->tanggal_request->format("d M Y H:i") }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-label-{{ $request->status_badge }}"
                                            >
                                                {{ $request->status_text }}
                                            </span>
                                        </td>
                                        <td>
                                            <div
                                                class="d-flex justify-content-center gap-2"
                                            >
                                                <a
                                                    href="{{ route("superadmin.subscription-requests.show", $request) }}"
                                                    class="btn btn-sm btn-outline-primary btn-icon action-btn"
                                                    title="Detail"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                >
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                @if ($request->status === "pending")
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-success btn-icon action-btn"
                                                        title="Approve"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#approveModal{{ $request->id_subscription_request }}"
                                                    >
                                                        <i
                                                            class="bx bx-check"
                                                        ></i>
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-danger btn-icon action-btn"
                                                        title="Reject"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#rejectModal{{ $request->id_subscription_request }}"
                                                    >
                                                        <i class="bx bx-x"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Approve Modal -->
                                    @if ($request->status === "pending")
                                        <div
                                            class="modal fade"
                                            id="approveModal{{ $request->id_subscription_request }}"
                                            tabindex="-1"
                                            aria-hidden="true"
                                        >
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            Approve Subscription
                                                        </h5>
                                                        <button
                                                            type="button"
                                                            class="btn-close"
                                                            data-bs-dismiss="modal"
                                                        ></button>
                                                    </div>
                                                    <form
                                                        method="POST"
                                                        action="{{ route("superadmin.subscription-requests.approve", $request) }}"
                                                    >
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>
                                                                Yakin ingin
                                                                menyetujui
                                                                request
                                                                subscription
                                                                dari
                                                                <strong>
                                                                    {{ $request->dapur->nama_dapur }}
                                                                </strong>
                                                                ?
                                                            </p>
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label"
                                                                >
                                                                    Catatan
                                                                    (opsional)
                                                                </label>
                                                                <textarea
                                                                    name="catatan"
                                                                    class="form-control"
                                                                    rows="3"
                                                                    placeholder="Masukkan catatan..."
                                                                ></textarea>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="modal-footer"
                                                        >
                                                            <button
                                                                type="button"
                                                                class="btn btn-outline-secondary"
                                                                data-bs-dismiss="modal"
                                                            >
                                                                Batal
                                                            </button>
                                                            <button
                                                                type="submit"
                                                                class="btn btn-success"
                                                            >
                                                                Approve
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div
                                            class="modal fade"
                                            id="rejectModal{{ $request->id_subscription_request }}"
                                            tabindex="-1"
                                            aria-hidden="true"
                                        >
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            Reject Subscription
                                                        </h5>
                                                        <button
                                                            type="button"
                                                            class="btn-close"
                                                            data-bs-dismiss="modal"
                                                        ></button>
                                                    </div>
                                                    <form
                                                        method="POST"
                                                        action="{{ route("superadmin.subscription-requests.reject", $request) }}"
                                                    >
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>
                                                                Yakin ingin
                                                                menolak request
                                                                subscription
                                                                dari
                                                                <strong>
                                                                    {{ $request->dapur->nama_dapur }}
                                                                </strong>
                                                                ?
                                                            </p>
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label"
                                                                >
                                                                    Alasan
                                                                    Penolakan
                                                                    <span
                                                                        class="text-danger"
                                                                    >
                                                                        *
                                                                    </span>
                                                                </label>
                                                                <textarea
                                                                    name="catatan"
                                                                    class="form-control"
                                                                    rows="3"
                                                                    placeholder="Masukkan alasan penolakan..."
                                                                    required
                                                                ></textarea>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="modal-footer"
                                                        >
                                                            <button
                                                                type="button"
                                                                class="btn btn-outline-secondary"
                                                                data-bs-dismiss="modal"
                                                            >
                                                                Batal
                                                            </button>
                                                            <button
                                                                type="submit"
                                                                class="btn btn-danger"
                                                            >
                                                                Reject
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($subscriptionRequests->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $subscriptionRequests->appends(request()->query())->links("vendor.pagination.bootstrap-5") }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-6">
                        <i class="bx bx-receipt bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">Tidak ada request subscription</h5>
                        <p class="text-muted mb-3">
                            Belum ada request subscription yang masuk.
                        </p>
                    </div>
                @endif
            </div>
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
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Bootstrap tooltips
            const tooltipTriggerList = document.querySelectorAll(
                '[data-bs-toggle="tooltip"]',
            );
            const tooltipList = [...tooltipTriggerList].map(
                (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl),
            );

            // Handle select all checkbox
            const selectAllCheckbox = document.getElementById('select-all');
            const checkAllCheckbox = document.getElementById('check-all');
            const requestCheckboxes =
                document.querySelectorAll('.request-checkbox');

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function () {
                    requestCheckboxes.forEach((checkbox) => {
                        checkbox.checked = this.checked;
                    });
                });
            }

            if (checkAllCheckbox) {
                checkAllCheckbox.addEventListener('change', function () {
                    requestCheckboxes.forEach((checkbox) => {
                        checkbox.checked = this.checked;
                    });
                });
            }

            // Update select all when individual checkboxes change
            requestCheckboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', function () {
                    const checkedCount = document.querySelectorAll(
                        '.request-checkbox:checked',
                    ).length;
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked =
                            checkedCount === requestCheckboxes.length;
                        selectAllCheckbox.indeterminate =
                            checkedCount > 0 &&
                            checkedCount < requestCheckboxes.length;
                    }
                });
            });
        });

        function confirmBulkAction() {
            const selectedRequests = document.querySelectorAll(
                '.request-checkbox:checked',
            );
            const action = document.querySelector(
                'select[name="action"]',
            ).value;

            if (selectedRequests.length === 0) {
                alert('Pilih minimal satu request subscription');
                return false;
            }

            if (!action) {
                alert('Pilih aksi yang akan dilakukan');
                return false;
            }

            const actionText = action === 'approve' ? 'menyetujui' : 'menolak';
            return confirm(
                `Yakin ingin ${actionText} ${selectedRequests.length} request subscription?`,
            );
        }
    </script>
@endsection
