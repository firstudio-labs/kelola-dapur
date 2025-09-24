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
                            <a
                                href="{{ route("superadmin.dapur.index") }}"
                                class="text-muted me-2"
                            >
                                Kelola Dapur
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">
                                {{ $dapur->nama_dapur }}
                            </span>
                        </nav>
                        <div class="d-flex align-items-center">
                            <h4 class="mb-1 me-3">{{ $dapur->nama_dapur }}</h4>
                            <span
                                class="badge bg-label-{{ $dapur->status === "active" ? "success" : "danger" }}"
                                title="Status: {{ $dapur->status === "active" ? "Aktif" : "Tidak Aktif" }}"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                            >
                                {{ $dapur->status === "active" ? "Aktif" : "Tidak Aktif" }}
                            </span>
                        </div>
                        <p class="mb-0 text-muted">
                            @if ($dapur->full_wilayah)
                                <i class="bx bx-map me-1"></i>
                                {{ $dapur->full_wilayah }}
                            @else
                                    Wilayah belum diset
                            @endif
                        </p>
                    </div>
                    <div class="d-flex gap-2 no-print">
                        <button
                            onclick="window.print()"
                            class="btn btn-sm btn-outline-secondary btn-icon action-btn"
                            title="Print"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                        >
                            <i class="bx bx-printer"></i>
                        </button>
                        <a
                            href="{{ route("superadmin.dapur.edit", $dapur) }}"
                            class="btn btn-sm btn-outline-primary btn-icon action-btn"
                            title="Edit Dapur"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                        >
                            <i class="bx bx-edit"></i>
                        </a>
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

        <!-- Statistics Section -->
        @if ($stats["total_staff"] > 0)
            <div class="card mb-4">
                <div class="card-body py-2 px-4">
                    <div class="row justify-content-center g-3">
                        <div class="col-md-3 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-primary me-2">
                                    <i class="bx bx-group"></i>
                                </span>
                                <div>
                                    <small class="text-muted">
                                        Total Staff
                                    </small>
                                    <h6 class="mb-0">
                                        {{ $stats["total_staff"] }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-purple me-2">
                                    <i class="bx bx-user"></i>
                                </span>
                                <div>
                                    <small class="text-muted">
                                        Kepala Dapur
                                    </small>
                                    <h6 class="mb-0">
                                        {{ $stats["kepala_dapur_count"] }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-success me-2">
                                    <i class="bx bx-warehouse"></i>
                                </span>
                                <div>
                                    <small class="text-muted">
                                        Admin Gudang
                                    </small>
                                    <h6 class="mb-0">
                                        {{ $stats["admin_gudang_count"] }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div
                                class="d-flex align-items-center justify-content-center"
                            >
                                <span class="badge bg-label-warning me-2">
                                    <i class="bx bx-book"></i>
                                </span>
                                <div>
                                    <small class="text-muted">Ahli Gizi</small>
                                    <h6 class="mb-0">
                                        {{ $stats["ahli_gizi_count"] ?? 0 }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Dapur Information and Quick Actions -->
        <div class="row g-4 mb-4">
            <!-- Dapur Information -->
            <div class="col-lg-0">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informasi Dapur</h5>
                        <small class="text-muted">
                            Detail lengkap tentang dapur ini
                        </small>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4 d-flex align-items-center">
                                <i class="bx bx-buildings me-2"></i>
                                Nama Dapur
                            </dt>
                            <dd class="col-sm-8">{{ $dapur->nama_dapur }}</dd>
                            <dt class="col-sm-4 d-flex align-items-center">
                                <i class="bx bx-user-circle me-2"></i>
                                Kepala Dapur
                            </dt>
                            <dd class="col-sm-8">
                                {{ $dapur->kepalaDapur->pluck("user.nama")->join(", ") ?: "Belum diset" }}
                            </dd>
                            <dt class="col-sm-4 d-flex align-items-center">
                                <i class="bx bx-map me-2"></i>
                                Wilayah
                            </dt>
                            <dd class="col-sm-8">
                                {{ $dapur->full_wilayah ?: "Belum diset" }}
                            </dd>
                            <dt class="col-sm-4 d-flex align-items-center">
                                <i class="bx bx-home me-2"></i>
                                Alamat Lengkap
                            </dt>
                            <dd class="col-sm-8">
                                {{ $dapur->alamat ?: "Belum diset" }}
                            </dd>
                            <dt class="col-sm-4 d-flex align-items-center">
                                <i class="bx bx-phone me-2"></i>
                                Telepon
                            </dt>
                            <dd class="col-sm-8">
                                @if ($dapur->telepon)
                                    <a
                                        href="tel:{{ $dapur->telepon }}"
                                        class="text-primary"
                                    >
                                        {{ $dapur->telepon }}
                                    </a>
                                @else
                                        Belum diset
                                @endif
                            </dd>
                            <dt class="col-sm-4 d-flex align-items-center">
                                <i class="bx bx-check-circle me-2"></i>
                                Status
                            </dt>
                            <dd class="col-sm-8">
                                <span
                                    class="badge bg-label-{{ $dapur->status === "active" ? "success" : "danger" }}"
                                >
                                    {{ $dapur->status === "active" ? "Aktif" : "Tidak Aktif" }}
                                </span>
                            </dd>
                            <dt class="col-sm-4 d-flex align-items-center">
                                <i class="bx bx-calendar me-2"></i>
                                Dibuat
                            </dt>
                            <dd class="col-sm-8">
                                {{ $dapur->created_at->format("d M Y H:i") }}
                            </dd>
                            <dt class="col-sm-4 d-flex align-items-center">
                                <i class="bx bx-edit me-2"></i>
                                Terakhir Diupdate
                            </dt>
                            <dd class="col-sm-8">
                                {{ $dapur->updated_at->format("d M Y H:i") }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff List -->
        <div class="card mt-4">
            <div
                class="card-header d-flex justify-content-between align-items-center"
            >
                <div>
                    <h5 class="card-title mb-0">Daftar Staff Dapur</h5>
                    <small class="text-muted">
                        Staff yang bertugas di dapur ini
                    </small>
                </div>
                {{--
                    <a
                    href="{{ route("superadmin.users.create") }}?dapur={{ $dapur->id_dapur }}"
                    class="btn btn-sm btn-outline-primary btn-icon action-btn"
                    title="Tambah Staff"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    >
                    <i class="bx bx-plus"></i>
                    </a>
                --}}
            </div>
            <div class="card-body">
                @if ($stats["total_staff"] > 0)
                    <div class="table-responsive">
                        <table
                            class="table table-bordered table-striped table-hover"
                        >
                            <thead>
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th style="width: 10%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $counter = 1;
                                @endphp

                                @foreach ($dapur->kepalaDapur as $kd)
                                    <tr>
                                        <td>{{ $counter++ }}</td>
                                        <td>{{ $kd->user->nama }}</td>
                                        <td>{{ $kd->user->email }}</td>
                                        <td>
                                            <span
                                                class="badge bg-label-primary"
                                            >
                                                Kepala Dapur
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-label-{{ $kd->user->is_active ? "success" : "danger" }}"
                                                title="Status: {{ $kd->user->is_active ? "Active" : "Inactive" }}"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                            >
                                                {{ $kd->user->is_active ? "Active" : "Inactive" }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach

                                @foreach ($dapur->adminGudang as $ag)
                                    <tr>
                                        <td>{{ $counter++ }}</td>
                                        <td>{{ $ag->user->nama }}</td>
                                        <td>{{ $ag->user->email }}</td>
                                        <td>
                                            <span
                                                class="badge bg-label-secondary"
                                            >
                                                Admin Gudang
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-label-{{ $ag->user->is_active ? "success" : "danger" }}"
                                                title="Status: {{ $ag->user->is_active ? "Active" : "Inactive" }}"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                            >
                                                {{ $ag->user->is_active ? "Active" : "Inactive" }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach

                                @foreach ($dapur->ahliGizi as $ag)
                                    <tr>
                                        <td>{{ $counter++ }}</td>
                                        <td>{{ $ag->user->nama }}</td>
                                        <td>{{ $ag->user->email }}</td>
                                        <td>
                                            <span
                                                class="badge bg-label-warning"
                                            >
                                                Ahli Gizi
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-label-{{ $ag->user->is_active ? "success" : "danger" }}"
                                                title="Status: {{ $ag->user->is_active ? "Active" : "Inactive" }}"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                            >
                                                {{ $ag->user->is_active ? "Active" : "Inactive" }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-6">
                        <i class="bx bx-group bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">Belum ada staff</h5>
                        <p class="text-muted mb-3">
                            Mulai dengan menambahkan staff pertama untuk dapur
                            ini.
                        </p>
                        <a
                            href="{{ route("superadmin.users.create") }}?dapur={{ $dapur->id_dapur }}"
                            class="btn btn-primary"
                        >
                            <i class="bx bx-plus me-1"></i>
                            Tambah Staff Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Custom Styling for Action Buttons -->
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
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>

    <!-- JavaScript for Tooltips -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tooltipTriggerList = document.querySelectorAll(
                '[data-bs-toggle="tooltip"]',
            );
            const tooltipList = [...tooltipTriggerList].map(
                (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl),
            );
        });
    </script>
@endsection
