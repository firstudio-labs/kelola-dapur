@extends("template_ahli_gizi.layout")

@section("content")
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div
                            class="d-flex align-items-center justify-content-between"
                        >
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-md me-3">
                                    <span
                                        class="avatar-initial rounded-circle bg-label-primary"
                                    >
                                        <i class="bx bx-show"></i>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="mb-1">
                                        Preview Input Paket Menu
                                    </h4>
                                    <p class="mb-0 text-muted">
                                        Review dan verifikasi sebelum mengajukan
                                        persetujuan
                                    </p>
                                </div>
                            </div>
                            <!-- Progress Steps -->
                            <div class="d-flex align-items-center">
                                <div class="step-indicator">
                                    <span class="badge bg-success me-2">1</span>
                                    <span class="badge bg-success me-2">2</span>
                                    <span class="badge bg-success me-2">3</span>
                                    <span class="badge bg-primary me-2">4</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Paket -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-info-circle me-2"></i>
                            Informasi Paket Menu
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless mb-0">
                                    {{--
                                        <tr>
                                        <td
                                        class="fw-semibold"
                                        style="width: 40%"
                                        >
                                        Nama Paket:
                                        </td>
                                        <td>{{ $transaksi->nama_paket }}</td>
                                        </tr>
                                    --}}
                                    <tr>
                                        <td class="fw-semibold">
                                            Tanggal Transaksi:
                                        </td>
                                        <td>
                                            {{ $transaksi->tanggal_transaksi->format("d F Y") }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Dapur:</td>
                                        <td>
                                            {{ $transaksi->dapur->nama_dapur }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Ahli Gizi:</td>
                                        <td>
                                            {{ $transaksi->createdBy->nama ?? "N/A" }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                @php
                                    $totalPorsiBesar = $transaksi->detailTransaksiDapur->where("tipe_porsi", "besar")->sum("jumlah_porsi") ?? 0;
                                    $totalPorsiKecil = $transaksi->detailTransaksiDapur->where("tipe_porsi", "kecil")->sum("jumlah_porsi") ?? 0;
                                    $totalKeseluruhan = $totalPorsiBesar + $totalPorsiKecil;
                                @endphp

                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td
                                            class="fw-semibold"
                                            style="width: 40%"
                                        >
                                            Total Porsi Besar:
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-label-success"
                                            >
                                                {{ $totalPorsiBesar }} Porsi
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">
                                            Total Porsi Kecil:
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-label-warning"
                                            >
                                                {{ $totalPorsiKecil }} Porsi
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">
                                            Total Keseluruhan:
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-label-primary"
                                            >
                                                {{ $totalKeseluruhan }} Porsi
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        @if ($transaksi->keterangan)
                            <div class="mt-3">
                                <strong>Keterangan:</strong>
                                <p class="mb-0 text-muted">
                                    {{ $transaksi->keterangan }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Menu -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-bowl-hot me-2"></i>
                            Detail Menu
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $porsiBesar = $transaksi->detailTransaksiDapur->where("tipe_porsi", "besar");
                            $porsiKecil = $transaksi->detailTransaksiDapur->where("tipe_porsi", "kecil");
                        @endphp

                        <!-- Porsi Besar -->
                        @if ($porsiBesar->count() > 0)
                            <h6 class="text-success mb-3">Menu Porsi Besar</h6>
                            <div class="row mb-4">
                                @foreach ($porsiBesar as $detail)
                                    <div class="col-md-6 mb-3">
                                        <div class="card border border-success">
                                            <div class="card-body p-3">
                                                <div
                                                    class="d-flex align-items-start"
                                                >
                                                    @if ($detail->menuMakanan->gambar_url)
                                                        <img
                                                            src="{{ $detail->menuMakanan->gambar_url }}"
                                                            alt="{{ $detail->menuMakanan->nama_menu }}"
                                                            class="rounded me-3"
                                                            style="
                                                                width: 60px;
                                                                height: 60px;
                                                                object-fit: cover;
                                                            "
                                                        />
                                                    @else
                                                        <div
                                                            class="avatar avatar-lg me-3"
                                                        >
                                                            <span
                                                                class="avatar-initial rounded bg-label-success"
                                                            >
                                                                <i
                                                                    class="bx bx-bowl-hot"
                                                                ></i>
                                                            </span>
                                                        </div>
                                                    @endif
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">
                                                            {{ $detail->menuMakanan->nama_menu }}
                                                        </h6>
                                                        <p
                                                            class="text-muted small mb-2"
                                                        >
                                                            {{ $detail->menuMakanan->kategori }}
                                                        </p>
                                                        <span
                                                            class="badge bg-success"
                                                        >
                                                            {{ $detail->jumlah_porsi }}
                                                            Porsi
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Porsi Kecil -->
                        @if ($porsiKecil->count() > 0)
                            <h6 class="text-warning mb-3">Menu Porsi Kecil</h6>
                            <div class="row mb-4">
                                @foreach ($porsiKecil as $detail)
                                    <div class="col-md-6 mb-3">
                                        <div class="card border border-warning">
                                            <div class="card-body p-3">
                                                <div
                                                    class="d-flex align-items-start"
                                                >
                                                    @if ($detail->menuMakanan->gambar_url)
                                                        <img
                                                            src="{{ $detail->menuMakanan->gambar_url }}"
                                                            alt="{{ $detail->menuMakanan->nama_menu }}"
                                                            class="rounded me-3"
                                                            style="
                                                                width: 60px;
                                                                height: 60px;
                                                                object-fit: cover;
                                                            "
                                                        />
                                                    @else
                                                        <div
                                                            class="avatar avatar-lg me-3"
                                                        >
                                                            <span
                                                                class="avatar-initial rounded bg-label-warning"
                                                            >
                                                                <i
                                                                    class="bx bx-bowl-hot"
                                                                ></i>
                                                            </span>
                                                        </div>
                                                    @endif
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">
                                                            {{ $detail->menuMakanan->nama_menu }}
                                                        </h6>
                                                        <p
                                                            class="text-muted small mb-2"
                                                        >
                                                            {{ $detail->menuMakanan->kategori }}
                                                        </p>
                                                        <span
                                                            class="badge bg-warning"
                                                        >
                                                            {{ $detail->jumlah_porsi }}
                                                            Porsi
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-2"></i>
                                Tidak ada menu porsi kecil yang dipilih
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if (! empty($bahanKebutuhan))
            @php
                // Pisahkan kebutuhan bahan per tipe porsi
                $bahanBesar = [];
                $bahanKecil = [];
                $totalKebutuhan = [];

                foreach ($bahanKebutuhan as $idTemplate => $bahan) {
                    foreach ($bahan["detail_penggunaan"] as $detail) {
                        if ($detail["tipe_porsi"] == "besar") {
                            if (! isset($bahanBesar[$idTemplate])) {
                                $bahanBesar[$idTemplate] = [
                                    "nama_bahan" => $bahan["nama_bahan"],
                                    "satuan" => $bahan["satuan"],
                                    "total_kebutuhan" => 0,
                                ];
                            }
                            $bahanBesar[$idTemplate]["total_kebutuhan"] += $detail["total_kebutuhan"];
                        } elseif ($detail["tipe_porsi"] == "kecil") {
                            if (! isset($bahanKecil[$idTemplate])) {
                                $bahanKecil[$idTemplate] = [
                                    "nama_bahan" => $bahan["nama_bahan"],
                                    "satuan" => $bahan["satuan"],
                                    "total_kebutuhan" => 0,
                                ];
                            }
                            $bahanKecil[$idTemplate]["total_kebutuhan"] += $detail["total_kebutuhan"];
                        }

                        // Total kebutuhan
                        if (! isset($totalKebutuhan[$idTemplate])) {
                            $totalKebutuhan[$idTemplate] = [
                                "nama_bahan" => $bahan["nama_bahan"],
                                "satuan" => $bahan["satuan"],
                                "total_kebutuhan" => 0,
                            ];
                        }
                        $totalKebutuhan[$idTemplate]["total_kebutuhan"] += $detail["total_kebutuhan"];
                    }
                }
            @endphp

            <!-- Kebutuhan Bahan Porsi Besar -->
            @if (! empty($bahanBesar))
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header text-white">
                                <h6 class="mb-0">
                                    <i class="bx bx-package me-2"></i>
                                    Kebutuhan Bahan - Porsi Besar
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nama Bahan</th>
                                                <th>Kebutuhan</th>
                                                <th>Kebutuhan : Stok</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($bahanBesar as $idTemplate => $bahan)
                                                <tr>
                                                    <td>
                                                        {{ $bahan["nama_bahan"] }}
                                                    </td>
                                                    <td>
                                                        {{ number_format($bahan["total_kebutuhan"], 2) }}
                                                        {{ $bahan["satuan"] }}
                                                    </td>
                                                    <td
                                                        class="stock-comparison-{{ $idTemplate }}"
                                                    >
                                                        <span
                                                            class="text-muted"
                                                        >
                                                            Memuat...
                                                        </span>
                                                    </td>
                                                    <td
                                                        class="stock-status-{{ $idTemplate }}"
                                                    >
                                                        <span
                                                            class="badge bg-secondary"
                                                        >
                                                            Memuat...
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Kebutuhan Bahan Porsi Kecil -->
            @if (! empty($bahanKecil))
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header text-white">
                                <h6 class="mb-0">
                                    <i class="bx bx-package me-2"></i>
                                    Kebutuhan Bahan - Porsi Kecil
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nama Bahan</th>
                                                <th>Kebutuhan</th>
                                                <th>Kebutuhan : Stok</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($bahanKecil as $idTemplate => $bahan)
                                                <tr>
                                                    <td>
                                                        {{ $bahan["nama_bahan"] }}
                                                    </td>
                                                    <td>
                                                        {{ number_format($bahan["total_kebutuhan"], 2) }}
                                                        {{ $bahan["satuan"] }}
                                                    </td>
                                                    <td
                                                        class="stock-comparison-kecil-{{ $idTemplate }}"
                                                    >
                                                        <span
                                                            class="text-muted"
                                                        >
                                                            Memuat...
                                                        </span>
                                                    </td>
                                                    <td
                                                        class="stock-status-kecil-{{ $idTemplate }}"
                                                    >
                                                        <span
                                                            class="badge bg-secondary"
                                                        >
                                                            Memuat...
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Total Kebutuhan Bahan -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div
                            class="card-header d-flex justify-content-between align-items-center text-white"
                        >
                            <h6 class="mb-0">
                                <i class="bx bx-package me-2"></i>
                                Total Kebutuhan Bahan
                            </h6>
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-light"
                                id="checkStockBtn"
                            >
                                <i class="bx bx-refresh me-1"></i>
                                Periksa Stock
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama Bahan</th>
                                            <th>Total Kebutuhan</th>
                                            <th>Kebutuhan : Stok</th>
                                            <th>Status Stock</th>
                                            <th>Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bahanKebutuhan as $idTemplate => $bahan)
                                            <tr>
                                                <td>
                                                    <div
                                                        class="d-flex align-items-center"
                                                    >
                                                        <div
                                                            class="avatar avatar-sm me-3"
                                                        >
                                                            <span
                                                                class="avatar-initial rounded bg-label-secondary"
                                                            >
                                                                <i
                                                                    class="bx bx-package"
                                                                ></i>
                                                            </span>
                                                        </div>
                                                        <span
                                                            class="fw-semibold"
                                                        >
                                                            {{ $bahan["nama_bahan"] }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span
                                                        class="fw-semibold text-primary"
                                                    >
                                                        {{ number_format($bahan["total_kebutuhan"], 2) }}
                                                        {{ $bahan["satuan"] }}
                                                    </span>
                                                </td>
                                                <td
                                                    class="stock-comparison-total-{{ $idTemplate }}"
                                                >
                                                    <span class="text-muted">
                                                        Memuat...
                                                    </span>
                                                </td>
                                                <td
                                                    class="stock-status-total-{{ $idTemplate }}"
                                                >
                                                    <span
                                                        class="badge bg-secondary"
                                                    >
                                                        Memuat...
                                                    </span>
                                                </td>
                                                <td>
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-info"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#detail-{{ $idTemplate }}"
                                                        aria-expanded="false"
                                                    >
                                                        <i
                                                            class="bx bx-info-circle"
                                                        ></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr
                                                class="collapse"
                                                id="detail-{{ $idTemplate }}"
                                            >
                                                <td
                                                    colspan="5"
                                                    class="bg-light"
                                                >
                                                    <div class="p-3">
                                                        <h6 class="mb-2">
                                                            Detail Penggunaan:
                                                        </h6>
                                                        <div class="row">
                                                            @foreach ($bahan["detail_penggunaan"] as $penggunaan)
                                                                <div
                                                                    class="col-md-6 mb-2"
                                                                >
                                                                    <div
                                                                        class="border rounded p-2"
                                                                    >
                                                                        <div
                                                                            class="d-flex justify-content-between"
                                                                        >
                                                                            <div>
                                                                                <small
                                                                                    class="fw-semibold"
                                                                                >
                                                                                    {{ $penggunaan["menu"] }}
                                                                                </small>
                                                                                <br />
                                                                                <small
                                                                                    class="text-muted"
                                                                                >
                                                                                    {{ ucfirst($penggunaan["tipe_porsi"]) }}
                                                                                    -
                                                                                    {{ $penggunaan["jumlah_porsi"] }}
                                                                                    porsi
                                                                                </small>
                                                                            </div>
                                                                            <div
                                                                                class="text-end"
                                                                            >
                                                                                <small
                                                                                    class="fw-semibold text-primary"
                                                                                >
                                                                                    {{ number_format($penggunaan["total_kebutuhan"], 2) }}
                                                                                    {{ $bahan["satuan"] }}
                                                                                </small>
                                                                                <br />
                                                                                <small
                                                                                    class="text-muted"
                                                                                >
                                                                                    @
                                                                                    {{ number_format($penggunaan["kebutuhan_per_porsi"], 2) }}/porsi
                                                                                </small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Stock Alert -->
        <div id="stockAlert" class="row mb-4" style="display: none">
            <div class="col-12">
                <div class="alert alert-danger">
                    <div class="d-flex">
                        <div class="alert-icon me-3">
                            <i class="bx bx-error-circle"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading">
                                Stock Tidak Mencukupi!
                            </h6>
                            <p class="mb-2">
                                Beberapa bahan mengalami kekurangan stock:
                            </p>
                            <div id="shortageList"></div>
                            <hr />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Instructions Alert -->
        <div class="alert alert-info alert-dismissible" role="alert">
            <h6 class="alert-heading mb-2">
                Informasi Laporan Kekurangan Stok
            </h6>
            <ul class="mb-0">
                <li>
                    Tampilan Informasi Laporan Kekurangan Stok Akan Muncul
                    Ketika Sudah Diajukan
                </li>
                <li>
                    Informasi Laporan Kekurangan Stok Akan Dikirim Ke Kepala
                    Dapur Berdasarkan Ajuan Laporan Terakhir
                </li>
                <li>
                    Status Pada Laporan Kekurangan Stok Menunggu Aksi Dari
                    Kepala Dapur
                </li>
                <li>
                    Anda Dapat Kembali Ke Edit Jika Sudah Ajukan Kekurangan Stok
                </li>
                <li>
                    Anda Dapat Ajukan Menu Dengan Menu Lain Jika Stok Ada
                    Walaupun Laporan Stok Sudah Terbuat
                </li>
            </ul>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>
        </div>
        <!-- Laporan Kekurangan yang Sudah Ada -->
        @if ($transaksi->laporanKekuranganStock->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-warning">
                        <div class="card-header bg-warning">
                            <h6 class="mb-0 text-white">
                                <i class="bx bx-error me-2"></i>
                                Laporan Kekurangan Stock
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="bx bx-info-circle me-2"></i>
                                Daftar laporan kekurangan stok yang pernah
                                diajukan ke Kepala Dapur
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Bahan</th>
                                            <th>Dibutuhkan</th>
                                            <th>Tersedia</th>
                                            <th>Kekurangan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transaksi->laporanKekuranganStock as $laporan)
                                            <tr>
                                                <td>
                                                    {{ $laporan->templateItem->nama_bahan }}
                                                </td>
                                                <td>
                                                    {{ number_format($laporan->jumlah_dibutuhkan, 2) }}
                                                    {{ $laporan->satuan }}
                                                </td>
                                                <td>
                                                    {{ number_format($laporan->jumlah_tersedia, 2) }}
                                                    {{ $laporan->satuan }}
                                                </td>
                                                <td class="text-danger">
                                                    {{ number_format($laporan->jumlah_kurang, 2) }}
                                                    {{ $laporan->satuan }}
                                                </td>
                                                <td>
                                                    @if ($laporan->status === "pending")
                                                        <span
                                                            class="badge bg-warning"
                                                        >
                                                            Menunggu
                                                        </span>
                                                    @elseif ($laporan->status === "resolved")
                                                        <span
                                                            class="badge bg-success"
                                                        >
                                                            Diselesaikan
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
            </div>
        @endif

        <!-- Form Pengajuan -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-send me-2"></i>
                            Pengajuan Persetujuan
                        </h5>
                    </div>
                    <div class="card-body">
                        <form
                            action="{{ route("ahli-gizi.transaksi.submit-approval", $transaksi) }}"
                            method="POST"
                            id="submitForm"
                        >
                            @csrf
                            <div class="mb-3">
                                <label
                                    for="keterangan_pengajuan"
                                    class="form-label"
                                >
                                    Keterangan Pengajuan
                                    <span class="text-muted">(Opsional)</span>
                                </label>
                                <textarea
                                    class="form-control"
                                    id="keterangan_pengajuan"
                                    name="keterangan_pengajuan"
                                    rows="3"
                                    placeholder="Tambahkan catatan khusus untuk Kepala Dapur..."
                                ></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <div>
                                    <a
                                        href="{{ route("ahli-gizi.transaksi.edit-porsi-besar", $transaksi) }}"
                                        class="btn btn-outline-secondary"
                                    >
                                        <i class="bx bx-arrow-back me-1"></i>
                                        Kembali ke Edit
                                    </a>
                                </div>
                                <div>
                                    <button
                                        type="submit"
                                        class="btn btn-success me-2"
                                        id="submitApprovalBtn"
                                    >
                                        <i class="bx bx-check me-1"></i>
                                        Ajukan Persetujuan
                                    </button>
                                    <form
                                        action="{{ route("ahli-gizi.transaksi.create-shortage-report", $transaksi) }}"
                                        method="POST"
                                        class="d-inline"
                                        id="shortageReportForm"
                                    >
                                        @csrf
                                        <button
                                            type="submit"
                                            class="btn btn-warning d-none"
                                            id="submitReportBtn"
                                        >
                                            <i class="bx bx-file-plus me-1"></i>
                                            Buat Laporan Kekurangan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // CSRF Token untuk Ajax
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Check stock on page load
            checkStock();

            // Check stock button
            document.getElementById('checkStockBtn').addEventListener('click', function() {
                checkStock();
            });

            // Create report button
            document.getElementById('createReportBtn')?.addEventListener('click', function() {
                document.getElementById('shortageReportForm').submit();
            });

            // Form submission with stock validation
            document.getElementById('submitForm').addEventListener('submit', function(e) {
                const stockAlert = document.getElementById('stockAlert');
                if (stockAlert && stockAlert.style.display !== 'none') {
                    e.preventDefault();
                    if (confirm('Stock tidak mencukupi! Laporan Kekurangan Akan Diajukan Ke Kepala Dapur')) {
                        // Remove event listener to prevent loop
                        this.removeEventListener('submit', arguments.callee);
                        this.submit();
                    }
                }
            });

            function checkStock() {
                const checkBtn = document.getElementById('checkStockBtn');
                const submitApprovalBtn = document.getElementById('submitApprovalBtn');
                const submitReportBtn = document.getElementById('submitReportBtn');

                checkBtn.disabled = true;
                checkBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Memeriksa...';

                // Fetch stock data
                fetch('{{ route("ahli-gizi.transaksi.check-stock-api", $transaksi) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('API Response Full:', data);
                    console.log('Debug id_dapur:', data.debug_id_dapur);
                    console.log('Stock Data Details:', Object.keys(data.stock_data).map(id => ({
                        id_template: id,
                        nama_bahan: data.stock_data[id].nama_bahan,
                        stock_tersedia: data.stock_data[id].stock_tersedia,
                        satuan: data.stock_data[id].satuan_stok,
                        debug: data.stock_data[id].debug
                    })));

                    if (data.success) {
                        updateStockDisplay(data);

                        // Atur visibilitas tombol berdasarkan kondisi stok
                        if (!data.can_produce && data.shortages.length > 0) {
                            showStockAlert(data.shortages);
                            // Tampilkan tombol Buat Laporan Kekurangan, sembunyikan Ajukan Persetujuan
                            submitReportBtn.classList.remove('d-none');
                            submitApprovalBtn.classList.add('d-none');
                        } else {
                            hideStockAlert();
                            // Tampilkan tombol Ajukan Persetujuan, sembunyikan Buat Laporan Kekurangan
                            submitApprovalBtn.classList.remove('d-none');
                            submitReportBtn.classList.add('d-none');
                        }
                    } else {
                        console.error('Error:', data.message);
                        showErrorMessage('Gagal memeriksa stock: ' + (data.message || 'Unknown error'));
                        // Default: Tampilkan Ajukan Persetujuan jika error
                        submitApprovalBtn.classList.remove('d-none');
                        submitReportBtn.classList.add('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error checking stock:', error);
                    showErrorMessage('Terjadi kesalahan saat memeriksa stock');
                    // Default: Tampilkan Ajukan Persetujuan jika error
                    submitApprovalBtn.classList.remove('d-none');
                    submitReportBtn.classList.add('d-none');
                })
                .finally(() => {
                    checkBtn.disabled = false;
                    checkBtn.innerHTML = '<i class="bx bx-refresh me-1"></i> Periksa Stock';
                });
            }

            function updateStockDisplay(data) {
                @if(!empty($bahanKebutuhan))
                    @foreach($bahanKebutuhan as $idTemplate => $bahan)
                        updateSingleStockDisplay('{{ $idTemplate }}', data, {{ $bahan["total_kebutuhan"] }});
                        @if(isset($bahanBesar[$idTemplate]))
                            updateSingleStockDisplayForType('{{ $idTemplate }}', '', data, {{ $bahanBesar[$idTemplate]["total_kebutuhan"] }});
                        @endif
                        @if(isset($bahanKecil[$idTemplate]))
                            updateSingleStockDisplayForType('{{ $idTemplate }}', 'kecil-', data, {{ $bahanKecil[$idTemplate]["total_kebutuhan"] }});
                        @endif
                    @endforeach
                @endif
            }

            function updateSingleStockDisplay(templateId, data, kebutuhan) {
                const stockInfo = data.stock_data[templateId] || { stock_tersedia: 0, sufficient: false, debug: 'unknown', satuan_stok: '' };
                const stockTersedia = parseFloat(stockInfo.stock_tersedia) || 0.0;
                console.log(`Debug for ${templateId} (${stockInfo.nama_bahan}):`, {
                    debug: stockInfo.debug,
                    stock_tersedia: stockTersedia,
                    satuan: stockInfo.satuan_stok
                });

                const comparisonEl = document.querySelector('.stock-comparison-total-' + templateId);
                if (comparisonEl) {
                    comparisonEl.innerHTML = `
                        <span class="fw-semibold">${parseFloat(kebutuhan).toFixed(2)} : ${stockTersedia.toFixed(2)}</span>
                        <small class="text-muted d-block">Kebutuhan : Stok Gudang</small>
                    `;
                }

                const statusEl = document.querySelector('.stock-status-total-' + templateId);
                if (statusEl) {
                    if (stockInfo.debug === 'not_found') {
                        statusEl.innerHTML = '<span class="badge bg-warning">Stok Tidak Ditemukan</span>';
                    } else if (stockTersedia === 0) {
                        statusEl.innerHTML = '<span class="badge bg-danger">Stok Kosong</span>';
                    } else if (stockInfo.sufficient) {
                        statusEl.innerHTML = '<span class="badge bg-success">Stok Tersedia</span>';
                    } else {
                        statusEl.innerHTML = '<span class="badge bg-danger">Stok Kurang</span>';
                    }
                }
            }

            function updateSingleStockDisplayForType(templateId, typePrefix, data, kebutuhan) {
                const stockInfo = data.stock_data[templateId] || { stock_tersedia: 0, sufficient: false, debug: 'unknown', satuan_stok: '' };
                const stockTersedia = parseFloat(stockInfo.stock_tersedia) || 0.0;
                console.log(`Debug for ${typePrefix}${templateId} (${stockInfo.nama_bahan}):`, {
                    debug: stockInfo.debug,
                    stock_tersedia: stockTersedia,
                    satuan: stockInfo.satuan_stok
                });

                const comparisonEl = document.querySelector('.stock-comparison-' + typePrefix + templateId);
                if (comparisonEl) {
                    comparisonEl.innerHTML = `
                        <span class="fw-semibold">${parseFloat(kebutuhan).toFixed(2)} : ${stockTersedia.toFixed(2)}</span>
                        <small class="text-muted d-block">Kebutuhan : Stok</small>
                    `;
                }

                const statusEl = document.querySelector('.stock-status-' + typePrefix + templateId);
                if (statusEl) {
                    if (stockInfo.debug === 'not_found') {
                        statusEl.innerHTML = '<span class="badge bg-warning">Stok Tidak Ditemukan</span>';
                    } else if (stockTersedia === 0) {
                        statusEl.innerHTML = '<span class="badge bg-danger">Stok Kosong</span>';
                    } else if (stockInfo.sufficient) {
                        statusEl.innerHTML = '<span class="badge bg-success">Stok Tersedia</span>';
                    } else {
                        statusEl.innerHTML = '<span class="badge bg-danger">Stok Kurang</span>';
                    }
                }
            }

            function showStockAlert(shortages) {
                const alertDiv = document.getElementById('stockAlert');
                const shortageList = document.getElementById('shortageList');

                let listHtml = '<ul class="mb-0">';
                shortages.forEach(shortage => {
                    listHtml += `
                        <li>
                            <strong>${shortage.nama_bahan}</strong>: 
                            Butuh ${parseFloat(shortage.kebutuhan).toFixed(2)} ${shortage.satuan}, 
                            tersedia ${parseFloat(shortage.stock_tersedia).toFixed(2)} ${shortage.satuan}
                            <span class="text-danger">(kurang ${parseFloat(shortage.kekurangan).toFixed(2)} ${shortage.satuan})</span>
                        </li>
                    `;
                });
                listHtml += '</ul>';

                shortageList.innerHTML = listHtml;
                alertDiv.style.display = 'block';
            }

            function hideStockAlert() {
                const alertDiv = document.getElementById('stockAlert');
                if (alertDiv) {
                    alertDiv.style.display = 'none';
                }
            }

            function showErrorMessage(message) {
                const alertHtml = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bx bx-error me-2"></i>
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;

                const container = document.querySelector('.container-xxl');
                if (container) {
                    container.insertAdjacentHTML('afterbegin', alertHtml);

                    setTimeout(() => {
                        const alert = container.querySelector('.alert-danger');
                        if (alert) {
                            alert.remove();
                        }
                    }, 5000);
                }
            }
        });
    </script>
@endsection
