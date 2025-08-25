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
                                        Detail Input Paket Menu
                                    </h4>
                                    <p class="mb-0 text-muted">
                                        Lihat detail paket menu yang telah
                                        dibuat
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

                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td
                                            class="fw-semibold"
                                            style="width: 40%"
                                        >
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
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Kebutuhan Bahan -->
        @if (! empty($bahanKebutuhan))
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div
                            class="card-header d-flex justify-content-between align-items-center"
                        >
                            <h5 class="mb-0">
                                <i class="bx bx-barcode me-2"></i>
                                Kebutuhan Bahan
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Total Kebutuhan -->
                            <h6 class="mb-3">Total Kebutuhan Bahan</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nama Bahan</th>
                                            <th>Kebutuhan</th>
                                            <th>Stok Gudang</th>
                                            <th>Perbandingan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bahanKebutuhan as $idTemplate => $bahan)
                                            @php
                                                $bahanArray = is_object($bahan) ? (array) $bahan : $bahan;
                                                $satuan = $bahanArray["satuan"] ?? "N/A";
                                                $namaBahan = $bahanArray["nama_bahan"] ?? "Unknown";
                                                $totalKebutuhan = isset($bahanArray["total_kebutuhan"]) ? (float) $bahanArray["total_kebutuhan"] : 0;
                                                $stockInfo = isset($stockData[$idTemplate])
                                                    ? (is_object($stockData[$idTemplate])
                                                        ? (array) $stockData[$idTemplate]
                                                        : $stockData[$idTemplate])
                                                    : [
                                                        "stock_tersedia" => 0,
                                                        "sufficient" => false,
                                                        "debug" => "not_found",
                                                        "satuan_stok" => $satuan,
                                                    ];
                                                $stockTersedia = (float) ($stockInfo["stock_tersedia"] ?? 0);
                                            @endphp

                                            <tr>
                                                <td>{{ $namaBahan }}</td>
                                                <td>
                                                    {{ number_format($totalKebutuhan, 2) }}
                                                    {{ $satuan }}
                                                </td>
                                                <td>
                                                    {{ number_format($stockTersedia, 2) }}
                                                    {{ $stockInfo["satuan_stok"] }}
                                                </td>
                                                <td
                                                    class="stock-comparison-total-{{ $idTemplate }}"
                                                >
                                                    <span class="fw-semibold">
                                                        {{ number_format($totalKebutuhan, 2) }}
                                                        :
                                                        {{ number_format($stockTersedia, 2) }}
                                                    </span>
                                                    <small
                                                        class="text-muted d-block"
                                                    >
                                                        Kebutuhan : Stok Gudang
                                                    </small>
                                                </td>
                                                <td
                                                    class="stock-status-total-{{ $idTemplate }}"
                                                >
                                                    @if ($stockInfo["debug"] == "not_found")
                                                        <span
                                                            class="badge bg-warning"
                                                        >
                                                            Stok Tidak Ditemukan
                                                        </span>
                                                    @elseif ($stockTersedia == 0)
                                                        <span
                                                            class="badge bg-danger"
                                                        >
                                                            Stok Kosong
                                                        </span>
                                                    @elseif ($stockInfo["sufficient"])
                                                        <span
                                                            class="badge bg-success"
                                                        >
                                                            Stok Tersedia
                                                        </span>
                                                    @else
                                                        <span
                                                            class="badge bg-danger"
                                                        >
                                                            Stok Kurang
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Porsi Besar -->
                            @if (! empty($bahanBesar))
                                <h6 class="text-success mb-3">
                                    Kebutuhan Bahan Porsi Besar
                                </h6>
                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Nama Bahan</th>
                                                <th>Kebutuhan</th>
                                                <th>Stok Gudang</th>
                                                <th>Perbandingan</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($bahanBesar as $idTemplate => $bahan)
                                                @php
                                                    $bahanArray = is_object($bahan) ? (array) $bahan : $bahan;
                                                    $satuan = $bahanArray["satuan"] ?? "N/A";
                                                    $namaBahan = $bahanArray["nama_bahan"] ?? "Unknown";
                                                    $totalKebutuhan = isset($bahanArray["total_kebutuhan"]) ? (float) $bahanArray["total_kebutuhan"] : 0;
                                                    $stockInfo = isset($stockData[$idTemplate])
                                                        ? (is_object($stockData[$idTemplate])
                                                            ? (array) $stockData[$idTemplate]
                                                            : $stockData[$idTemplate])
                                                        : [
                                                            "stock_tersedia" => 0,
                                                            "sufficient" => false,
                                                            "debug" => "not_found",
                                                            "satuan_stok" => $satuan,
                                                        ];
                                                    $stockTersedia = (float) ($stockInfo["stock_tersedia"] ?? 0);
                                                @endphp

                                                <tr>
                                                    <td>{{ $namaBahan }}</td>
                                                    <td>
                                                        {{ number_format($totalKebutuhan, 2) }}
                                                        {{ $satuan }}
                                                    </td>
                                                    <td>
                                                        {{ number_format($stockTersedia, 2) }}
                                                        {{ $stockInfo["satuan_stok"] }}
                                                    </td>
                                                    <td
                                                        class="stock-comparison-{{ $idTemplate }}"
                                                    >
                                                        <span
                                                            class="fw-semibold"
                                                        >
                                                            {{ number_format($totalKebutuhan, 2) }}
                                                            :
                                                            {{ number_format($stockTersedia, 2) }}
                                                        </span>
                                                        <small
                                                            class="text-muted d-block"
                                                        >
                                                            Kebutuhan : Stok
                                                        </small>
                                                    </td>
                                                    <td
                                                        class="stock-status-{{ $idTemplate }}"
                                                    >
                                                        @if ($stockInfo["debug"] == "not_found")
                                                            <span
                                                                class="badge bg-warning"
                                                            >
                                                                Stok Tidak
                                                                Ditemukan
                                                            </span>
                                                        @elseif ($stockTersedia == 0)
                                                            <span
                                                                class="badge bg-danger"
                                                            >
                                                                Stok Kosong
                                                            </span>
                                                        @elseif ($stockInfo["sufficient"])
                                                            <span
                                                                class="badge bg-success"
                                                            >
                                                                Stok Tersedia
                                                            </span>
                                                        @else
                                                            <span
                                                                class="badge bg-danger"
                                                            >
                                                                Stok Kurang
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            <!-- Porsi Kecil -->
                            @if (! empty($bahanKecil))
                                <h6 class="text-warning mb-3">
                                    Kebutuhan Bahan Porsi Kecil
                                </h6>
                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Nama Bahan</th>
                                                <th>Kebutuhan</th>
                                                <th>Stok Gudang</th>
                                                <th>Perbandingan</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($bahanKecil as $idTemplate => $bahan)
                                                @php
                                                    $bahanArray = is_object($bahan) ? (array) $bahan : $bahan;
                                                    $satuan = $bahanArray["satuan"] ?? "N/A";
                                                    $namaBahan = $bahanArray["nama_bahan"] ?? "Unknown";
                                                    $totalKebutuhan = isset($bahanArray["total_kebutuhan"]) ? (float) $bahanArray["total_kebutuhan"] : 0;
                                                    $stockInfo = isset($stockData[$idTemplate])
                                                        ? (is_object($stockData[$idTemplate])
                                                            ? (array) $stockData[$idTemplate]
                                                            : $stockData[$idTemplate])
                                                        : [
                                                            "stock_tersedia" => 0,
                                                            "sufficient" => false,
                                                            "debug" => "not_found",
                                                            "satuan_stok" => $satuan,
                                                        ];
                                                    $stockTersedia = (float) ($stockInfo["stock_tersedia"] ?? 0);
                                                @endphp

                                                <tr>
                                                    <td>{{ $namaBahan }}</td>
                                                    <td>
                                                        {{ number_format($totalKebutuhan, 2) }}
                                                        {{ $satuan }}
                                                    </td>
                                                    <td>
                                                        {{ number_format($stockTersedia, 2) }}
                                                        {{ $stockInfo["satuan_stok"] }}
                                                    </td>
                                                    <td
                                                        class="stock-comparison-kecil-{{ $idTemplate }}"
                                                    >
                                                        <span
                                                            class="fw-semibold"
                                                        >
                                                            {{ number_format($totalKebutuhan, 2) }}
                                                            :
                                                            {{ number_format($stockTersedia, 2) }}
                                                        </span>
                                                        <small
                                                            class="text-muted d-block"
                                                        >
                                                            Kebutuhan : Stok
                                                        </small>
                                                    </td>
                                                    <td
                                                        class="stock-status-kecil-{{ $idTemplate }}"
                                                    >
                                                        @if ($stockInfo["debug"] == "not_found")
                                                            <span
                                                                class="badge bg-warning"
                                                            >
                                                                Stok Tidak
                                                                Ditemukan
                                                            </span>
                                                        @elseif ($stockTersedia == 0)
                                                            <span
                                                                class="badge bg-danger"
                                                            >
                                                                Stok Kosong
                                                            </span>
                                                        @elseif ($stockInfo["sufficient"])
                                                            <span
                                                                class="badge bg-success"
                                                            >
                                                                Stok Tersedia
                                                            </span>
                                                        @else
                                                            <span
                                                                class="badge bg-danger"
                                                            >
                                                                Stok Kurang
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Laporan Kekurangan -->
        @if ($transaksi->laporanKekuranganStock->isNotEmpty())
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bx bx-error me-2"></i>
                                Laporan Kekurangan Stock Yang Pernah Diajukan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nama Bahan</th>
                                            <th>Jumlah Dibutuhkan</th>
                                            <th>Jumlah Tersedia</th>
                                            <th>Jumlah Kurang</th>
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

        <!-- Stock Alert (if any shortages) -->
        @if (! empty($shortages))
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div
                                class="alert alert-warning"
                                role="alert"
                                id="stockAlert"
                            >
                                <h6 class="alert-heading mb-2">
                                    <i class="bx bx-error-circle me-2"></i>
                                    Peringatan: Kekurangan Stock Terdeteksi
                                </h6>
                                <p class="mb-2">
                                    Beberapa bahan tidak tersedia dalam jumlah
                                    yang cukup:
                                </p>
                                <ul class="mb-0" id="shortageList">
                                    @foreach ($shortages as $shortage)
                                        @php
                                            $shortageArray = is_object($shortage) ? (array) $shortage : $shortage;
                                            $namaBahan = $shortageArray["nama_bahan"] ?? "Unknown";
                                            $kebutuhan = isset($shortageArray["kebutuhan"]) ? (float) $shortageArray["kebutuhan"] : 0;
                                            $stockTersedia = isset($shortageArray["stock_tersedia"]) ? (float) $shortageArray["stock_tersedia"] : 0;
                                            $kekurangan = isset($shortageArray["kekurangan"]) ? (float) $shortageArray["kekurangan"] : 0;
                                            $satuan = $shortageArray["satuan"] ?? "N/A";
                                        @endphp

                                        <li>
                                            <strong>{{ $namaBahan }}</strong>
                                            : Butuh
                                            {{ number_format($kebutuhan, 2) }}
                                            {{ $satuan }}, tersedia
                                            {{ number_format($stockTersedia, 2) }}
                                            {{ $satuan }}
                                            <span class="text-danger">
                                                (kurang
                                                {{ number_format($kekurangan, 2) }}
                                                {{ $satuan }})
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
