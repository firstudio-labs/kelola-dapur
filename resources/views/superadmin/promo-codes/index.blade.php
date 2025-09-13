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
                            <span class="text-dark">Kode Promo</span>
                        </nav>
                        <h4 class="mb-1">Kelola Kode Promo</h4>
                        <p class="mb-0 text-muted">
                            Kelola semua kode promo dalam sistem
                        </p>
                    </div>
                    <a
                        href="{{ route("superadmin.promo-codes.create") }}"
                        class="btn btn-primary btn-sm"
                    >
                        <i class="bx bx-plus me-1"></i>
                        Tambah Promo
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
                    action="{{ route("superadmin.promo-codes.index") }}"
                    class="row g-3"
                >
                    <div class="col-md-6">
                        <label for="search" class="form-label">
                            Cari Promo
                        </label>
                        <div class="input-group">
                            <input
                                type="text"
                                name="search"
                                id="search"
                                value="{{ request("search") }}"
                                class="form-control"
                                placeholder="Cari kode promo..."
                            />
                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                onclick="document.getElementById('search').value='';this.form.submit();"
                            >
                                <i class="bx bx-x"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option
                                value="1"
                                {{ request("status") === "1" ? "selected" : "" }}
                            >
                                Aktif
                            </option>
                            <option
                                value="0"
                                {{ request("status") === "0" ? "selected" : "" }}
                            >
                                Tidak Aktif
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        @if (request()->hasAny(["search", "status"]))
                            <a
                                href="{{ route("superadmin.promo-codes.index") }}"
                                class="btn btn-outline-secondary me-2"
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
        @if ($promoCodes->total() > 0)
            <div class="card mb-4">
                <div class="card-body py-2 px-4">
                    <div class="row justify-content-center g-3">
                        <div class="col-md-3 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-info me-2">
                                    <i class="bx bx-tag"></i>
                                </span>
                                <div>
                                    <small class="text-muted">
                                        Total Promo
                                    </small>
                                    <h6 class="mb-0">
                                        {{ $promoCodes->total() }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-success me-2">
                                    <i class="bx bx-check-circle"></i>
                                </span>
                                <div>
                                    <small class="text-muted">
                                        Promo Aktif
                                    </small>
                                    <h6 class="mb-0">
                                        {{ $promoCodes->where("is_active", true)->count() }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-danger me-2">
                                    <i class="bx bx-x-circle"></i>
                                </span>
                                <div>
                                    <small class="text-muted">
                                        Promo Tidak Aktif
                                    </small>
                                    <h6 class="mb-0">
                                        {{ $promoCodes->where("is_active", false)->count() }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-warning me-2">
                                    <i class="bx bx-calendar-x"></i>
                                </span>
                                <div>
                                    <small class="text-muted">
                                        Promo Kadaluarsa
                                    </small>
                                    <h6 class="mb-0">
                                        {{ $promoCodes->where("tanggal_berakhir", "<", now())->count() }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Promo Codes List -->
        <div class="card mb-4">
            <div class="card-body">
                @if ($promoCodes->count() > 0)
                    <div class="table-responsive">
                        <table
                            class="table table-bordered table-striped table-hover"
                        >
                            <thead>
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th style="width: 15%">Kode Promo</th>
                                    <th style="width: 10%">Diskon</th>
                                    <th style="width: 15%">Periode</th>
                                    <th style="width: 8%">Status</th>
                                    <th style="width: 15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($promoCodes as $promoCode)
                                    <tr>
                                        <td>
                                            {{ $promoCodes->firstItem() + $loop->index }}
                                        </td>
                                        <td>
                                            <div
                                                class="d-flex align-items-center"
                                            >
                                                <div
                                                    class="avatar flex-shrink-0 me-3"
                                                >
                                                    <span
                                                        class="avatar-initial rounded bg-label-info"
                                                    >
                                                        <i
                                                            class="bx bx-tag"
                                                        ></i>
                                                    </span>
                                                </div>
                                                <strong>
                                                    {{ $promoCode->kode_promo }}
                                                </strong>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>
                                                {{ $promoCode->persentase_diskon }}%
                                            </strong>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($promoCode->tanggal_mulai)->format("d M Y") }}
                                            <i
                                                class="bx bx-chevrons-right mx-1"
                                            ></i>
                                            {{ \Carbon\Carbon::parse($promoCode->tanggal_berakhir)->format("d M Y") }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $promoCode->is_active ? "bg-label-success" : "bg-label-danger" }} {{ $promoCode->isExpired() ? "bg-label-secondary" : "" }}"
                                            >
                                                {{ $promoCode->isExpired() ? "Kadaluarsa" : ($promoCode->is_active ? "Aktif" : "Tidak Aktif") }}
                                            </span>
                                        </td>
                                        <td>
                                            <div
                                                class="d-flex justify-content-center gap-2"
                                            >
                                                <a
                                                    href="{{ route("superadmin.promo-codes.show", $promoCode) }}"
                                                    class="btn btn-sm btn-outline-primary btn-icon action-btn"
                                                    title="Detail"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                >
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                <a
                                                    href="{{ route("superadmin.promo-codes.edit", $promoCode) }}"
                                                    class="btn btn-sm btn-outline-info btn-icon action-btn"
                                                    title="Edit"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                >
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <form
                                                    method="POST"
                                                    action="{{ route("superadmin.promo-codes.toggle-status", $promoCode) }}"
                                                >
                                                    @csrf
                                                    @method("PATCH")
                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-{{ $promoCode->is_active ? "warning" : "success" }} btn-icon action-btn"
                                                        title="{{ $promoCode->is_active ? "Nonaktifkan" : "Aktifkan" }}"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                    >
                                                        <i
                                                            class="bx bx-{{ $promoCode->is_active ? "pause" : "play" }}"
                                                        ></i>
                                                    </button>
                                                </form>
                                                @if ($promoCode->subscriptionRequests->isEmpty())
                                                    <form
                                                        method="POST"
                                                        action="{{ route("superadmin.promo-codes.destroy", $promoCode) }}"
                                                        onsubmit="return confirm('Yakin ingin menghapus promo {{ $promoCode->kode_promo }}?')"
                                                    >
                                                        @csrf
                                                        @method("DELETE")
                                                        <button
                                                            type="submit"
                                                            class="btn btn-sm btn-outline-danger btn-icon action-btn"
                                                            title="Hapus"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                        >
                                                            <i
                                                                class="bx bx-trash"
                                                            ></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button
                                                        class="btn btn-sm btn-outline-secondary btn-icon action-btn disabled"
                                                        title="Tidak bisa dihapus karena sudah digunakan"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                    >
                                                        <i
                                                            class="bx bx-trash"
                                                        ></i>
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
                    @if ($promoCodes->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $promoCodes->appends(request()->query())->links("vendor.pagination.bootstrap-5") }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-6">
                        @if (request()->hasAny(["search", "status"]))
                            <i class="bx bx-search bx-lg text-muted mb-3"></i>
                            <h5 class="mb-1">Tidak ada hasil</h5>
                            <p class="text-muted mb-3">
                                Tidak ada kode promo yang sesuai dengan filter.
                            </p>
                            <a
                                href="{{ route("superadmin.promo-codes.index") }}"
                                class="btn btn-outline-primary"
                            >
                                Reset Filter
                            </a>
                        @else
                            <i class="bx bx-tag bx-lg text-muted mb-3"></i>
                            <h5 class="mb-1">Belum ada kode promo</h5>
                            <p class="text-muted mb-3">
                                Mulai dengan membuat kode promo pertama.
                            </p>
                            <a
                                href="{{ route("superadmin.promo-codes.create") }}"
                                class="btn btn-primary"
                            >
                                <i class="bx bx-plus me-1"></i>
                                Tambah Promo Pertama
                            </a>
                        @endif
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
        });
    </script>
@endsection
