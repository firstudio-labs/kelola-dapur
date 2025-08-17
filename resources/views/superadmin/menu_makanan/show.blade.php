@extends("template_admin.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
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
                                href="{{ route("superadmin.menu-makanan.index") }}"
                                class="text-muted me-2"
                            >
                                Kelola Menu Makanan
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">
                                {{ $menuMakanan->nama_menu }}
                            </span>
                        </nav>
                        <div
                            class="d-flex justify-content-between align-items-start"
                        >
                            <div>
                                <h4 class="mb-1">
                                    {{ $menuMakanan->nama_menu }}
                                </h4>
                                <p class="mb-0 text-muted">
                                    Detail informasi menu dan bahan-bahannya
                                </p>
                            </div>
                            <a
                                href="{{ route("superadmin.menu-makanan.edit", $menuMakanan) }}"
                                class="btn btn-primary"
                            >
                                <i class="bx bx-edit me-1"></i>
                                Edit Menu
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Information -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Informasi Menu</h5>
                <p class="text-muted mb-4">Detail lengkap tentang menu ini.</p>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <i class="bx bx-restaurant me-2"></i>
                            <div>
                                <small class="text-muted">Nama Menu</small>
                                <p class="mb-0">
                                    {{ $menuMakanan->nama_menu }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <i class="bx bx-check-circle me-2"></i>
                            <div>
                                <small class="text-muted">Status</small>
                                <span
                                    class="badge {{ $menuMakanan->is_active ? "bg-label-success" : "bg-label-danger" }}"
                                >
                                    {{ $menuMakanan->is_active ? "Active" : "Inactive" }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <i class="bx bx-calendar me-2"></i>
                            <div>
                                <small class="text-muted">Dibuat</small>
                                <p class="mb-0">
                                    {{ $menuMakanan->created_at->format("d M Y H:i") }}
                                    <span class="text-muted">
                                        ({{ $menuMakanan->created_at->diffForHumans() }})
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <i class="bx bx-calendar-edit me-2"></i>
                            <div>
                                <small class="text-muted">
                                    Terakhir Diupdate
                                </small>
                                <p class="mb-0">
                                    {{ $menuMakanan->updated_at->format("d M Y H:i") }}
                                    @if ($menuMakanan->updated_at != $menuMakanan->created_at)
                                        <span class="text-muted">
                                            ({{ $menuMakanan->updated_at->diffForHumans() }})
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <i class="bx bx-file me-2"></i>
                            <div>
                                <small class="text-muted">Deskripsi</small>
                                <p class="mb-0">
                                    {{ $menuMakanan->deskripsi ?: "Tidak ada deskripsi" }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <i class="bx bx-image me-2"></i>
                            <div>
                                <small class="text-muted">Gambar Menu</small>
                                <div>
                                    <img
                                        src="{{ $menuMakanan->gambar_url }}"
                                        alt="{{ $menuMakanan->nama_menu }}"
                                        class="rounded"
                                        style="max-width: 200px; height: auto"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ingredients (Bahan Menu) -->
        <div class="card mb-4">
            <div class="card-body">
                <div
                    class="d-flex justify-content-between align-items-center mb-4"
                >
                    <div>
                        <h5 class="card-title mb-0">Bahan Menu</h5>
                        <p class="text-muted">
                            Daftar bahan yang digunakan untuk menu ini
                        </p>
                    </div>
                    <a
                        href="{{ route("superadmin.bahan-menu.create") }}?menu_id={{ $menuMakanan->id_menu }}"
                        class="btn btn-primary"
                    >
                        <i class="bx bx-plus me-1"></i>
                        Tambah Bahan
                    </a>
                </div>

                @if ($menuMakanan->bahanMenu->count() > 0)
                    <div class="row g-4">
                        @foreach ($menuMakanan->bahanMenu as $bahan)
                            <div class="col-md-6">
                                <div
                                    class="card h-100 bg-light-primary border-primary"
                                >
                                    <div class="card-body">
                                        <div
                                            class="d-flex align-items-center justify-content-between"
                                        >
                                            <div
                                                class="d-flex align-items-center"
                                            >
                                                <div class="avatar me-3">
                                                    <span
                                                        class="avatar-initial rounded bg-label-primary"
                                                    >
                                                        <i
                                                            class="bx bx-food-menu"
                                                        ></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">
                                                        {{ $bahan->templateItem->nama_bahan }}
                                                    </h6>
                                                    <small class="text-muted">
                                                        Jumlah:
                                                        {{ $bahan->jumlah_per_porsi }}
                                                        {{ $bahan->templateItem->satuan }}
                                                    </small>
                                                </div>
                                            </div>
                                            <div
                                                class="d-flex align-items-center gap-2"
                                            >
                                                <a
                                                    href="{{ route("superadmin.bahan-menu.edit", $bahan) }}"
                                                    class="btn btn-sm btn-outline-info"
                                                >
                                                    <i class="bx bx-edit"></i>
                                                    Edit
                                                </a>
                                                <form
                                                    method="POST"
                                                    action="{{ route("superadmin.bahan-menu.destroy", $bahan) }}"
                                                    onsubmit="return confirm('Yakin ingin menghapus bahan {{ $bahan->templateItem->nama_bahan }} dari menu ini?')"
                                                >
                                                    @csrf
                                                    @method("DELETE")
                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                    >
                                                        <i
                                                            class="bx bx-trash"
                                                        ></i>
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-6">
                        <i class="bx bx-food-menu bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">Belum ada bahan</h5>
                        <p class="text-muted mb-3">
                            Menu ini belum memiliki bahan apapun.
                        </p>
                        <a
                            href="{{ route("superadmin.bahan-menu.create") }}?menu_id={{ $menuMakanan->id_menu }}"
                            class="btn btn-primary"
                        >
                            <i class="bx bx-plus me-1"></i>
                            Tambah Bahan Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Transaction History -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Riwayat Transaksi</h5>
                <p class="text-muted mb-4">
                    Daftar transaksi yang melibatkan menu ini
                </p>
                @if ($menuMakanan->detailTransaksiDapur->count() > 0)
                    <div class="table-responsive">
                        <table
                            class="table table-bordered table-striped table-hover"
                        >
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jumlah Porsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($menuMakanan->detailTransaksiDapur as $transaksi)
                                    <tr>
                                        <td>
                                            {{ $transaksi->transaksiDapur->tanggal_transaksi->format("d M Y H:i") }}
                                        </td>
                                        <td>{{ $transaksi->jumlah_porsi }}</td>
                                        <td>
                                            <span
                                                class="badge bg-label-{{ $transaksi->transaksiDapur->status === "completed" ? "success" : "warning" }}"
                                            >
                                                {{ ucfirst($transaksi->transaksiDapur->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a
                                                href="{{ route("superadmin.dapur.show", $transaksi->transaksiDapur->dapur) }}"
                                                class="btn btn-sm btn-outline-primary"
                                            >
                                                <i class="bx bx-show"></i>
                                                Lihat Transaksi
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-6">
                        <i class="bx bx-history bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">Belum ada transaksi</h5>
                        <p class="text-muted mb-3">
                            Menu ini belum digunakan dalam transaksi apapun.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Delete Menu Section -->
        @if (! $menuMakanan->detailTransaksiDapur()->exists())
            <div class="alert alert-danger alert-dismissible" role="alert">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="bx bx-error me-2"></i>
                    </div>
                    <div>
                        <h6 class="alert-heading">Danger Zone</h6>
                        <p class="mb-2">
                            Menghapus menu akan menghilangkan semua data dan
                            bahan yang terkait. Tindakan ini tidak dapat
                            dibatalkan.
                        </p>
                        <form
                            method="POST"
                            action="{{ route("superadmin.menu-makanan.destroy", $menuMakanan) }}"
                            onsubmit="return confirm('Yakin ingin menghapus menu {{ $menuMakanan->nama_menu }}? Semua data dan bahan akan terhapus!')"
                            class="d-inline"
                        >
                            @csrf
                            @method("DELETE")
                            <button type="submit" class="btn btn-danger">
                                Hapus Menu
                            </button>
                        </form>
                    </div>
                </div>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"
                ></button>
            </div>
        @endif
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
        .is-invalid .choices__inner {
            border-color: #dc3545;
        }
        .bg-light-primary {
            background-color: rgba(105, 108, 255, 0.1) !important;
        }
        .border-primary {
            border-color: rgba(105, 108, 255, 0.5) !important;
        }
    </style>

    <!-- Choices.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>

    <!-- JavaScript for Tooltips -->
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
