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
                                href="{{ route('superadmin.dashboard') }}"
                                class="text-muted me-2"
                            >
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <a
                                href="{{ route('superadmin.menu-makanan.index') }}"
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
                                    href="{{ route('superadmin.menu-makanan.edit', $menuMakanan->id_menu) }}"
                                    class="btn btn-primary me-2"
                                >
                                    <i class="bx bx-edit"></i>
                                    Edit Menu
                                </a>
                                <a
                                    href="{{ route('superadmin.menu-makanan.index') }}"
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
                            class="badge {{ $menuMakanan->is_active ? 'bg-label-success' : 'bg-label-danger' }}"
                        >
                            {{ $menuMakanan->is_active ? 'Active' : 'Inactive' }}
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
                                    onerror="this.src='{{ asset('images/menu/default-menu.jpg') }}'"
                                />
                            </div>
                            <div class="col-md-8">
                                <h4 class="mb-3">
                                    {{ $menuMakanan->nama_menu }}
                                </h4>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Kategori:
                                    </label>
                                    <p class="mb-0">
                                        {{ $menuMakanan->kategori }}
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Deskripsi:
                                    </label>
                                    <p class="mb-0">
                                        {{ $menuMakanan->deskripsi ?: 'Tidak ada deskripsi' }}
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Dibuat Oleh Dapur:
                                    </label>
                                    <p class="mb-0">
                                        {{ $menuMakanan->createdByDapur->nama_dapur ?? 'Tidak ada dapur terkait' }}
                                    </p>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Status:
                                        </label>
                                        <p class="mb-0">
                                            <span
                                                class="badge {{ $menuMakanan->is_active ? 'bg-success' : 'bg-danger' }}"
                                            >
                                                {{ $menuMakanan->is_active ? 'Active' : 'Inactive' }}
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
                                            {{ $menuMakanan->created_at->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Terakhir Update:
                                        </label>
                                        <p class="mb-0">
                                            {{ $menuMakanan->updated_at->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ingredients List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Daftar Bahan</h5>
                    </div>
                    @if ($menuMakanan->bahanMenu->count() > 0)
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Nama Bahan</th>
                                            <th>Jumlah per Porsi</th>
                                            <th>Satuan</th>
                                            <th>Bahan Basah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($menuMakanan->bahanMenu as $bahan)
                                            <tr>
                                                <td>
                                                    {{ $bahan->templateItem->nama_bahan ?? 'Bahan tidak ditemukan' }}
                                                </td>
                                                <td>
                                                    @php
                                                        $jumlah = $bahan->jumlah_per_porsi ?? 0;
                                                        $displayJumlah = rtrim(rtrim(number_format($jumlah, 4, '.', ''), '0'), '.');
                                                        $jumlahBasah = $bahan->is_bahan_basah ? ($jumlah * 1.07) : $jumlah;
                                                        $displayJumlahBasah = rtrim(rtrim(number_format($jumlahBasah, 4, '.', ''), '0'), '.');
                                                    @endphp
                                                    @if ($bahan->is_bahan_basah)
                                                        {{ $displayJumlah }} (Matang) - {{ $displayJumlahBasah }} (Basah)
                                                    @else
                                                        {{ $displayJumlah }}
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $bahan->templateItem->satuan ?: 'tidak ada satuan' }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge {{ $bahan->is_bahan_basah ? 'bg-label-info' : 'bg-label-secondary' }}"
                                                    >
                                                        {{ $bahan->is_bahan_basah ? 'Ya' : 'Tidak' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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
                                            {{ $detail->transaksiDapur->tanggal_transaksi->format('d M Y') }}
                                        </td>
                                        <td>
                                            {{ $detail->jumlah_porsi }} porsi
                                        </td>
                                        <td>
                                            {{ $detail->transaksiDapur->dapur->nama_dapur ?? 'Dapur Tidak Diketahui' }}
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