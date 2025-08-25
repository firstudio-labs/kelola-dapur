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
                                href="{{ route("superadmin.template-items.index") }}"
                                class="text-muted me-2"
                            >
                                Kelola Template Bahan
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">
                                {{ $templateItem->nama_bahan }}
                            </span>
                        </nav>
                        <h4 class="mb-1">{{ $templateItem->nama_bahan }}</h4>
                        <p class="mb-0 text-muted">
                            Detail template bahan dan penggunaannya
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Template Item Details -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Informasi Template Bahan</h5>
                @if (session("success"))
                    <div
                        class="alert alert-success alert-dismissible"
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
                    <div
                        class="alert alert-danger alert-dismissible"
                        role="alert"
                    >
                        {{ session("error") }}
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close"
                        ></button>
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <p>
                            <strong>Nama Bahan:</strong>
                            {{ $templateItem->nama_bahan }}
                        </p>
                        <p>
                            <strong>Satuan:</strong>
                            {{ ucfirst($templateItem->satuan) }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <strong>Keterangan:</strong>
                            {{ $templateItem->keterangan ?: "-" }}
                        </p>
                        <p>
                            <strong>Dibuat Pada:</strong>
                            {{ $templateItem->created_at->format("d M Y H:i") }}
                        </p>
                    </div>
                </div>
                <div class="mt-4">
                    <a
                        href="{{ route("superadmin.template-items.edit", $templateItem) }}"
                        class="btn btn-primary"
                    >
                        <i class="bx bx-edit-alt me-1"></i>
                        Edit Template Bahan
                    </a>
                </div>
            </div>
        </div>

        <!-- Related Menu Items -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Menu yang Menggunakan Bahan Ini</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Menu</th>
                                <th>Jumlah per Porsi</th>
                                <th>Status Menu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($templateItem->bahanMenu as $index => $bahan)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{ $bahan->menuMakanan->nama_menu }}
                                    </td>
                                    <td>
                                        {{ $bahan->jumlah_per_porsi }}
                                        {{ $templateItem->satuan }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-label-{{ $bahan->menuMakanan->is_active ? "success" : "danger" }}"
                                        >
                                            {{ $bahan->menuMakanan->is_active ? "Active" : "Inactive" }}
                                        </span>
                                    </td>
                                    <td>
                                        <a
                                            href="{{ route("superadmin.menu-makanan.show", $bahan->menuMakanan) }}"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            <i class="bx bx-show"></i>
                                            Lihat Menu
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        Tidak ada menu yang menggunakan bahan
                                        ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Related Stock Items -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Stock Bahan di Dapur</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Dapur</th>
                                <th>Jumlah Stock</th>
                                <th>Tanggal Update</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($templateItem->stockItems as $index => $stock)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $stock->dapur->nama_dapur }}</td>
                                    <td>
                                        {{ $stock->jumlah_stock }}
                                        {{ $templateItem->satuan }}
                                    </td>
                                    <td>
                                        {{ $stock->updated_at->format("d M Y H:i") }}
                                    </td>
                                    <td>
                                        <a
                                            href="{{ route("superadmin.dapur.show", $stock->dapur) }}"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            <i class="bx bx-show"></i>
                                            Lihat Dapur
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        Tidak ada stock untuk bahan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Instructions Alert -->
        <div class="alert alert-info alert-dismissible" role="alert">
            <h6 class="alert-heading mb-2">Informasi Detail Template Bahan</h6>
            <ul class="mb-0">
                <li>Semua pengguna dapat melihat detail template bahan ini.</li>
                <li>
                    Hanya Super Admin dan Ahli Gizi yang dapat mengedit template
                    bahan.
                </li>
                <li>
                    Template bahan yang digunakan dalam menu atau stock tidak
                    dapat dihapus.
                </li>
                <li>
                    Gunakan tautan "Lihat Menu" atau "Lihat Dapur" untuk melihat
                    detail terkait.
                </li>
            </ul>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>
        </div>

        <!-- Back Button -->
        <div class="mt-4">
            <a
                href="{{ route("superadmin.template-items.index") }}"
                class="btn btn-label-secondary"
            >
                <i class="bx bx-arrow-back me-1"></i>
                Kembali ke Daftar
            </a>
        </div>
    </div>
@endsection
