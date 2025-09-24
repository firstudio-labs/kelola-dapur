@extends("template_ahli_gizi.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <nav class="d-flex align-items-center mb-2">
                            <a
                                href="{{ route("ahli-gizi.dashboard") }}"
                                class="text-muted me-2"
                            >
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <a
                                href="{{ route("ahli-gizi.menu-makanan.index") }}"
                                class="text-muted me-2"
                            >
                                Kelola Menu Makanan
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">
                                Detail Menu: {{ $menuMakanan->nama_menu }}
                            </span>
                        </nav>
                        <div
                            class="d-flex justify-content-between align-items-center"
                        >
                            <div>
                                <h4 class="mb-1">Detail Menu Makanan</h4>
                                <p class="mb-0 text-muted">
                                    Informasi lengkap menu makanan dan
                                    bahan-bahannya
                                </p>
                            </div>
                            <div>
                                <a
                                    href="{{ route("ahli-gizi.menu-makanan.edit", $menuMakanan->id_menu) }}"
                                    class="btn btn-primary me-2"
                                >
                                    <i class="bx bx-edit"></i>
                                    Edit Menu
                                </a>
                                <a
                                    href="{{ route("ahli-gizi.menu-makanan.index") }}"
                                    class="btn btn-label-secondary"
                                >
                                    <i class="bx bx-arrow-back"></i>
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Detail -->
        <div class="row">
            <!-- Menu Information -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5 class="card-title mb-0">Informasi Menu</h5>
                        <span
                            class="badge {{ $menuMakanan->is_active ? "bg-label-success" : "bg-label-danger" }}"
                        >
                            {{ $menuMakanan->is_active ? "Active" : "Inactive" }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <img
                                    src="{{ $menuMakanan->gambar_url }}"
                                    alt="{{ $menuMakanan->nama_menu }}"
                                    class="img-fluid rounded mb-3"
                                    style="
                                        max-height: 250px;
                                        width: 100%;
                                        object-fit: cover;
                                    "
                                    onerror="this.src='{{ asset("images/menu/default-menu.jpg") }}'"
                                />
                            </div>
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-3">
                                    <h4 class="me-3">
                                        {{ $menuMakanan->nama_menu }}
                                    </h4>
                                    <span
                                        class="badge {{ $menuMakanan->getKategoriBadgeClass() }}"
                                    >
                                        {{ $menuMakanan->kategori ?? "Kategori" }}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Deskripsi:
                                    </label>
                                    <p class="mb-0">
                                        {{ $menuMakanan->deskripsi ?: "Tidak ada deskripsi" }}
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Dibuat Oleh Dapur:
                                    </label>
                                    <p class="mb-0">
                                        {{ $menuMakanan->createdByDapur->nama_dapur ?? "Tidak ada dapur terkait" }}
                                    </p>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Status:
                                        </label>
                                        <p class="mb-0">
                                            <span
                                                class="badge {{ $menuMakanan->is_active ? "bg-success" : "bg-danger" }}"
                                            >
                                                {{ $menuMakanan->is_active ? "Active" : "Inactive" }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Total Bahan:
                                        </label>
                                        <p class="mb-0">
                                            {{ $menuMakanan->bahanMenu->count() }}
                                            Bahan
                                        </p>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Dibuat:
                                        </label>
                                        <p class="mb-0">
                                            {{ $menuMakanan->created_at->format("d M Y H:i") }}
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Terakhir Update:
                                        </label>
                                        <p class="mb-0">
                                            {{ $menuMakanan->updated_at->format("d M Y H:i") }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bahan Menu -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Bahan-bahan Menu</h5>
                    </div>
                    <div class="card-body">
                        @if ($menuMakanan->bahanMenu && $menuMakanan->bahanMenu->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach ($menuMakanan->bahanMenu as $bahan)
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center"
                                    >
                                        <div>
                                            <h6 class="mb-1">
                                                {{ $bahan->templateItem->nama_bahan ?? "Bahan Tidak Diketahui" }}
                                            </h6>
                                            <p class="mb-0 text-muted">
                                                @php
                                                    $satuan = isset($bahan->templateItem->satuan) ? strtolower($bahan->templateItem->satuan) : "";
                                                    $jumlah = $bahan->jumlah_per_porsi ?? 0;
                                                    $displayUnit = $satuan;

                                                    // Convert to display unit
                                                    if ($satuan === "kg") {
                                                        $jumlah = $jumlah * 1000;
                                                        $displayUnit = "gram";
                                                    } elseif ($satuan === "liter" || $satuan === "l") {
                                                        $jumlah = $jumlah * 1000;
                                                        $displayUnit = "ml";
                                                    }

                                                    // Format jumlah
                                                    $formattedJumlah = rtrim(rtrim(number_format($jumlah, 4, ".", ""), "0"), ".");

                                                    if ($bahan->is_bahan_basah) {
                                                        // Calculate final weight with 7% increase
                                                        $finalJumlah = $jumlah * 1.07;
                                                        $formattedFinalJumlah = rtrim(rtrim(number_format($finalJumlah, 4, ".", ""), "0"), ".");

                                                        echo $formattedJumlah . " " . $displayUnit . " Bahan Matang - " . $formattedFinalJumlah . " " . $displayUnit . " per porsi";
                                                    } else {
                                                        echo $formattedJumlah . " " . $displayUnit . " per porsi ";
                                                    }
                                                @endphp
                                            </p>
                                        </div>
                                        @if ($bahan->is_bahan_basah)
                                            <span class="badge bg-label-info">
                                                Bahan Basah +7%
                                            </span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-4">
                                <i
                                    class="bx bx-package text-muted"
                                    style="font-size: 48px"
                                ></i>
                                <h6 class="mt-2 text-muted">
                                    Belum ada bahan yang ditambahkan
                                </h6>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Panel -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Aksi</h5>
                    </div>
                    <div class="card-body">
                        <a
                            href="{{ route("ahli-gizi.menu-makanan.edit", $menuMakanan->id_menu) }}"
                            class="btn btn-primary w-100 mb-2"
                        >
                            <i class="bx bx-edit me-2"></i>
                            Edit Menu
                        </a>
                        <form
                            action="{{ route("ahli-gizi.menu-makanan.toggle-status", $menuMakanan->id_menu) }}"
                            method="POST"
                            class="d-inline"
                        >
                            @csrf
                            @method("PATCH")
                            <button
                                type="submit"
                                class="btn {{ $menuMakanan->is_active ? "btn-warning" : "btn-success" }} w-100 mb-2"
                            >
                                <i
                                    class="bx {{ $menuMakanan->is_active ? "bx-lock" : "bx-lock-open" }} me-2"
                                ></i>
                                {{ $menuMakanan->is_active ? "Nonaktifkan" : "Aktifkan" }}
                                Menu
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Statistik Menu</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span
                                            class="avatar-initial rounded bg-label-primary"
                                        >
                                            <i class="bx bx-package"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="mb-0 text-muted small">
                                            Bahan
                                        </p>
                                        <h6 class="mb-0">
                                            {{ $menuMakanan->bahanMenu->count() }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span
                                            class="avatar-initial rounded bg-label-info"
                                        >
                                            <i class="bx bx-receipt"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="mb-0 text-muted small">
                                            Transaksi
                                        </p>
                                        <h6 class="mb-0">
                                            {{ $menuMakanan->detailTransaksiDapur ? $menuMakanan->detailTransaksiDapur->count() : 0 }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        @if ($menuMakanan->detailTransaksiDapur && $menuMakanan->detailTransaksiDapur->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Transaksi Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jumlah Porsi</th>
                                    <th>Dapur</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($menuMakanan->detailTransaksiDapur->take(5) as $detail)
                                    <tr>
                                        <td>
                                            {{ $detail->transaksiDapur->tanggal_transaksi->format("d M Y") }}
                                        </td>
                                        <td>
                                            {{ $detail->jumlah_porsi }} porsi
                                        </td>
                                        <td>
                                            {{ $detail->transaksiDapur->dapur->nama_dapur ?? "Dapur Tidak Diketahui" }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-label-success"
                                            >
                                                Selesai
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
