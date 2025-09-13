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
                            <span class="text-dark">Paket Subscription</span>
                        </nav>
                        <h4 class="mb-1">Kelola Paket Subscription</h4>
                        <p class="mb-0 text-muted">
                            Kelola semua paket subscription dalam sistem
                        </p>
                    </div>
                    <a
                        href="{{ route("superadmin.subscription-packages.create") }}"
                        class="btn btn-primary btn-sm"
                    >
                        <i class="bx bx-plus me-1"></i>
                        Tambah Paket
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
                    action="{{ route("superadmin.subscription-packages.index") }}"
                    class="row g-3"
                >
                    <div class="col-md-6">
                        <label for="search" class="form-label">
                            Cari Paket
                        </label>
                        <div class="input-group">
                            <input
                                type="text"
                                name="search"
                                id="search"
                                value="{{ request("search") }}"
                                class="form-control"
                                placeholder="Cari nama paket..."
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
                                href="{{ route("superadmin.subscription-packages.index") }}"
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
        @if ($packages->total() > 0)
            <div class="card mb-4">
                <div class="card-body py-2 px-4">
                    <div class="row justify-content-center g-3">
                        <div class="col-md-3 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-primary me-2">
                                    <i class="bx bx-package"></i>
                                </span>
                                <div>
                                    <small class="text-muted">
                                        Total Paket
                                    </small>
                                    <h6 class="mb-0">
                                        {{ $packages->total() }}
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
                                        Paket Aktif
                                    </small>
                                    <h6 class="mb-0">
                                        {{ $packages->where("is_active", true)->count() }}
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
                                        Paket Tidak Aktif
                                    </small>
                                    <h6 class="mb-0">
                                        {{ $packages->where("is_active", false)->count() }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Packages List -->
        <div class="card mb-4">
            <div class="card-body">
                @if ($packages->count() > 0)
                    <div class="table-responsive">
                        <table
                            class="table table-bordered table-striped table-hover"
                        >
                            <thead>
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th style="width: 20%">Nama Paket</th>
                                    <th>Deskripsi</th>
                                    <th style="width: 12%">Harga</th>
                                    <th style="width: 10%">Durasi</th>
                                    <th style="width: 8%">Status</th>
                                    <th style="width: 15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($packages as $package)
                                    <tr>
                                        <td>
                                            {{ $packages->firstItem() + $loop->index }}
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
                                                            class="bx bx-package"
                                                        ></i>
                                                    </span>
                                                </div>
                                                <strong>
                                                    {{ $package->nama_paket }}
                                                </strong>
                                            </div>
                                        </td>
                                        <td>
                                            {{ Str::limit($package->deskripsi, 50) }}
                                        </td>
                                        <td>
                                            <strong>
                                                {{ $package->formatted_harga }}
                                            </strong>
                                        </td>
                                        <td>{{ $package->durasi_text }}</td>
                                        <td>
                                            <span
                                                class="badge {{ $package->is_active ? "bg-label-success" : "bg-label-danger" }}"
                                            >
                                                {{ $package->is_active ? "Aktif" : "Tidak Aktif" }}
                                            </span>
                                        </td>
                                        <td>
                                            <div
                                                class="d-flex justify-content-center gap-2"
                                            >
                                                <a
                                                    href="{{ route("superadmin.subscription-packages.show", $package) }}"
                                                    class="btn btn-sm btn-outline-primary btn-icon action-btn"
                                                    title="Detail"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                >
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                <a
                                                    href="{{ route("superadmin.subscription-packages.edit", $package) }}"
                                                    class="btn btn-sm btn-outline-info btn-icon action-btn"
                                                    title="Edit"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                >
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <form
                                                    method="POST"
                                                    action="{{ route("superadmin.subscription-packages.toggle-status", $package) }}"
                                                >
                                                    @csrf
                                                    @method("PATCH")
                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-{{ $package->is_active ? "warning" : "success" }} btn-icon action-btn"
                                                        title="{{ $package->is_active ? "Nonaktifkan" : "Aktifkan" }}"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                    >
                                                        <i
                                                            class="bx bx-{{ $package->is_active ? "pause" : "play" }}"
                                                        ></i>
                                                    </button>
                                                </form>
                                                @if ($package->subscriptionRequests->isEmpty())
                                                    <form
                                                        method="POST"
                                                        action="{{ route("superadmin.subscription-packages.destroy", $package) }}"
                                                        onsubmit="return confirm('Yakin ingin menghapus paket {{ $package->nama_paket }}?')"
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
                    @if ($packages->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $packages->appends(request()->query())->links("vendor.pagination.bootstrap-5") }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-6">
                        @if (request()->hasAny(["search", "status"]))
                            <i class="bx bx-search bx-lg text-muted mb-3"></i>
                            <h5 class="mb-1">Tidak ada hasil</h5>
                            <p class="text-muted mb-3">
                                Tidak ada paket yang sesuai dengan filter.
                            </p>
                            <a
                                href="{{ route("superadmin.subscription-packages.index") }}"
                                class="btn btn-outline-primary"
                            >
                                Reset Filter
                            </a>
                        @else
                            <i class="bx bx-package bx-lg text-muted mb-3"></i>
                            <h5 class="mb-1">Belum ada paket subscription</h5>
                            <p class="text-muted mb-3">
                                Mulai dengan membuat paket subscription pertama.
                            </p>
                            <a
                                href="{{ route("superadmin.subscription-packages.create") }}"
                                class="btn btn-primary"
                            >
                                <i class="bx bx-plus me-1"></i>
                                Tambah Paket Pertama
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
