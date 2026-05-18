@extends("template_ahli_gizi.layout")

@section("content")
    @php
        if (!function_exists('formatIndonesianNumber')) {
            function formatIndonesianNumber($value) {
                if ($value === null || $value === '' || $value === 0 || $value === 0.0) return '0';
                $num = (float)$value;
                $parts = explode('.', (string)$num);
                $formattedInt = number_format((float)$parts[0], 0, '', '.');
                if (isset($parts[1])) {
                    $decimals = rtrim($parts[1], '0');
                    if (strlen($decimals) > 0) {
                        return $formattedInt . ',' . $decimals;
                    }
                }
                return $formattedInt;
            }
        }
    @endphp
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <div class="container-xxl flex-grow-1 container-p-y">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible mb-4" role="alert">
                <i class="bx bx-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible mb-4" role="alert">
                <i class="bx bx-error me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                <i class="bx bx-x-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
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
                                    $totalPorsiBesar = $transaksi->detailTransaksiDapur->where('tipe_porsi', 'besar')->first()?->jumlah_porsi ?? 0;
                                    $totalPorsiKecil = $transaksi->detailTransaksiDapur->where('tipe_porsi', 'kecil')->first()?->jumlah_porsi ?? 0;
                                    $totalKeseluruhan = $totalPorsiBesar;
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
                                                {{ formatIndonesianNumber($totalPorsiBesar) }} Porsi
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
                                                {{ formatIndonesianNumber($totalPorsiKecil) }} Porsi
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
                                                {{ formatIndonesianNumber($totalKeseluruhan) }} Porsi
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
                            <h6 class="mb-3">Total Kebutuhan Bahan</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nama Bahan</th>
                                            <th>Kebutuhan</th>
                                            <th>Stok Gudang</th>
                                            <th>Estimasi Stok Gudang</th>
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
                                                $totalKebutuhanAsli = isset($bahanArray["total_kebutuhan"]) ? (float) $bahanArray["total_kebutuhan"] : 0;
                                                
                                                $stockItemData = $stockData[$idTemplate] ?? null;
                                                $stockInfo = is_object($stockItemData) ? (array) $stockItemData : ($stockItemData ?: [
                                                    "stock_tersedia" => 0,
                                                    "sufficient" => false,
                                                    "debug" => "not_found",
                                                    "satuan_stok" => $satuan,
                                                    "konversi_nilai" => null,
                                                    "konversi_satuan" => null,
                                                ]);
                                                
                                                $stockTersediaDisplay = (float) ($stockInfo["display_stok"] ?? ($stockInfo["stock_tersedia"] ?? 0));
                                                $konversiNilai = (float) ($stockInfo["konversi_nilai"] ?? 0);
                                                $totalKebutuhanDisplay = $konversiNilai > 0 ? $totalKebutuhanAsli / $konversiNilai : $totalKebutuhanAsli;
                                                $satuanDisplay = $stockInfo["konversi_satuan"] ?? $stockInfo["satuan_stok"] ?? $satuan;
                                                
                                                $estimasiStok = $stockTersediaDisplay - $totalKebutuhanDisplay;
                                                $realSufficient = $estimasiStok >= 0;
                                            @endphp

                                            <tr>
                                                <td>{{ $namaBahan }}</td>
                                                <td>
                                                    {{ formatIndonesianNumber($totalKebutuhanDisplay) }}
                                                    {{ $satuanDisplay }}
                                                </td>
                                                <td>
                                                    {{ formatIndonesianNumber($stockTersediaDisplay) }}
                                                    {{ $satuanDisplay }}
                                                </td>
                                                <td>
                                                    <span class="{{ $estimasiStok < 0 ? 'text-danger' : ($estimasiStok == 0 ? 'text-warning' : 'text-success') }}">
                                                        {{ formatIndonesianNumber($estimasiStok) }}
                                                        {{ $satuanDisplay }}
                                                    </span>
                                                </td>
                                                <td
                                                    class="stock-comparison-total-{{ $idTemplate }}"
                                                >
                                                    <span class="fw-semibold">
                                                        {{ formatIndonesianNumber($totalKebutuhanDisplay) }}
                                                        :
                                                        {{ formatIndonesianNumber($stockTersediaDisplay) }}
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
                                                    @elseif ($stockTersediaDisplay == 0)
                                                        <span
                                                            class="badge bg-danger"
                                                        >
                                                            Stok Kosong
                                                        </span>
                                                    @elseif ($realSufficient)
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
                                                <th>Estimasi Stok Gudang</th>
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
                                                    $totalKebutuhanAsli = isset($bahanArray["total_kebutuhan"]) ? (float) $bahanArray["total_kebutuhan"] : 0;
                                                    
                                                    $stockItemData = $stockData[$idTemplate] ?? null;
                                                    $stockInfo = is_object($stockItemData) ? (array) $stockItemData : ($stockItemData ?: [
                                                        "stock_tersedia" => 0,
                                                        "sufficient" => false,
                                                        "debug" => "not_found",
                                                        "satuan_stok" => $satuan,
                                                        "konversi_nilai" => null,
                                                        "konversi_satuan" => null,
                                                    ]);
                                                    
                                                    $stockTersediaDisplay = (float) ($stockInfo["display_stok"] ?? ($stockInfo["stock_tersedia"] ?? 0));
                                                    $konversiNilai = (float) ($stockInfo["konversi_nilai"] ?? 0);
                                                    $totalKebutuhanDisplay = $konversiNilai > 0 ? $totalKebutuhanAsli / $konversiNilai : $totalKebutuhanAsli;
                                                    $satuanDisplay = $stockInfo["konversi_satuan"] ?? $stockInfo["satuan_stok"] ?? $satuan;
                                                    
                                                    $estimasiStok = $stockTersediaDisplay - $totalKebutuhanDisplay;
                                                @endphp

                                                <tr>
                                                    <td>{{ $namaBahan }}</td>
                                                    <td>
                                                        {{ formatIndonesianNumber($totalKebutuhanDisplay) }}
                                                        {{ $satuanDisplay }}
                                                    </td>
                                                    <td>
                                                        {{ formatIndonesianNumber($stockTersediaDisplay) }}
                                                        {{ $satuanDisplay }}
                                                    </td>
                                                    <td>
                                                        <span class="{{ $estimasiStok < 0 ? 'text-danger' : ($estimasiStok == 0 ? 'text-warning' : 'text-success') }}">
                                                            {{ formatIndonesianNumber($estimasiStok) }}
                                                            {{ $satuanDisplay }}
                                                        </span>
                                                    </td>
                                                    <td
                                                        class="stock-comparison-{{ $idTemplate }}"
                                                    >
                                                        <span
                                                            class="fw-semibold"
                                                        >
                                                            {{ formatIndonesianNumber($totalKebutuhanDisplay) }}
                                                            :
                                                            {{ formatIndonesianNumber($stockTersediaDisplay) }}
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
                                                            <span class="badge bg-warning">Stok Tidak Ditemukan</span>
                                                        @elseif ($stockTersediaDisplay == 0)
                                                            <span class="badge bg-danger">Stok Kosong</span>
                                                        @elseif ($estimasiStok >= 0)
                                                            <span class="badge bg-success">Stok Tersedia</span>
                                                        @else
                                                            <span class="badge bg-danger">Stok Kurang</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
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
                                                <th>Estimasi Stok Gudang</th>
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
                                                    $totalKebutuhanAsli = isset($bahanArray["total_kebutuhan"]) ? (float) $bahanArray["total_kebutuhan"] : 0;
                                                    
                                                    $stockItemData = $stockData[$idTemplate] ?? null;
                                                    $stockInfo = is_object($stockItemData) ? (array) $stockItemData : ($stockItemData ?: [
                                                        "stock_tersedia" => 0,
                                                        "sufficient" => false,
                                                        "debug" => "not_found",
                                                        "satuan_stok" => $satuan,
                                                        "konversi_nilai" => null,
                                                        "konversi_satuan" => null,
                                                    ]);
                                                    
                                                    $stockTersediaDisplay = (float) ($stockInfo["display_stok"] ?? ($stockInfo["stock_tersedia"] ?? 0));
                                                    $konversiNilai = (float) ($stockInfo["konversi_nilai"] ?? 0);
                                                    $totalKebutuhanDisplay = $konversiNilai > 0 ? $totalKebutuhanAsli / $konversiNilai : $totalKebutuhanAsli;
                                                    $satuanDisplay = $stockInfo["konversi_satuan"] ?? $stockInfo["satuan_stok"] ?? $satuan;
                                                    
                                                    $estimasiStok = $stockTersediaDisplay - $totalKebutuhanDisplay;
                                                @endphp

                                                <tr>
                                                    <td>{{ $namaBahan }}</td>
                                                    <td>
                                                        {{ formatIndonesianNumber($totalKebutuhanDisplay) }}
                                                        {{ $satuanDisplay }}
                                                    </td>
                                                    <td>
                                                        {{ formatIndonesianNumber($stockTersediaDisplay) }}
                                                        {{ $satuanDisplay }}
                                                    </td>
                                                    <td>
                                                        <span class="{{ $estimasiStok < 0 ? 'text-danger' : ($estimasiStok == 0 ? 'text-warning' : 'text-success') }}">
                                                            {{ formatIndonesianNumber($estimasiStok) }}
                                                            {{ $satuanDisplay }}
                                                        </span>
                                                    </td>
                                                    <td
                                                        class="stock-comparison-kecil-{{ $idTemplate }}"
                                                    >
                                                        <span
                                                            class="fw-semibold"
                                                        >
                                                            {{ formatIndonesianNumber($totalKebutuhanDisplay) }}
                                                            :
                                                            {{ formatIndonesianNumber($stockTersediaDisplay) }}
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
                                                            <span class="badge bg-warning">Stok Tidak Ditemukan</span>
                                                        @elseif ($stockTersediaDisplay == 0)
                                                            <span class="badge bg-danger">Stok Kosong</span>
                                                        @elseif ($estimasiStok >= 0)
                                                            <span class="badge bg-success">Stok Tersedia</span>
                                                        @else
                                                            <span class="badge bg-danger">Stok Kurang</span>
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

        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card h-100 shadow-none border">
                    <div class="card-header border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="bx bx-list-ul me-1"></i> Kebutuhan Bahan per Menu
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-4">
                            @php
                                $porsiBesarDetails = $transaksi->detailTransaksiDapur->where('tipe_porsi', 'besar');
                                $porsiKecilDetails = $transaksi->detailTransaksiDapur->where('tipe_porsi', 'kecil');
                            @endphp

                            @if($porsiBesarDetails->count() > 0)
                                <div class="col-12">
                                    <h6 class="text-success mb-0 fw-bold"><i class="bx bx-chevron-right"></i> Porsi Besar : {{ formatIndonesianNumber($totalPorsiBesar) }} Porsi</h6>
                                </div>
                                @foreach($porsiBesarDetails as $detail)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="border rounded p-3 h-100 bg-light-success">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar avatar-sm me-2">
                                                    @if($detail->menuMakanan->gambar_url)
                                                        <img src="{{ $detail->menuMakanan->gambar_url }}" class="rounded" style="object-fit: cover;">
                                                    @else
                                                        <span class="avatar-initial rounded bg-label-success"><i class="bx bx-dish"></i></span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold small">{{ $detail->menuMakanan->nama_menu }}</h6>
                                                    <span class="badge bg-label-success" style="font-size: 0.6rem;">
                                                        Besar - {{ formatIndonesianNumber($detail->jumlah_porsi) }} Porsi
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tbody>
                                                        @foreach($detail->menuMakanan->bahanMenu as $bahanMenu)
                                                            @php
                                                                $idTemplate = $bahanMenu->id_template_item;
                                                                $namaBahan = $bahanMenu->templateItem->nama_bahan;
                                                                $satuan = $bahanMenu->templateItem->satuan;
                                                                $kebutuhanPerPorsi = $bahanMenu->jumlah_per_porsi;
                                                                $totalButuhMenu = $kebutuhanPerPorsi * $detail->jumlah_porsi;
                                                                
                                                                $stockInfo = null;
                                                                if(isset($stockData) && isset($stockData[$idTemplate])) {
                                                                    $stockInfo = is_object($stockData[$idTemplate]) ? (array) $stockData[$idTemplate] : $stockData[$idTemplate];
                                                                }
                                                            @endphp
                                                            <tr>
                                                                <td class="ps-0 py-1" style="font-size: 0.75rem;">
                                                                    <span class="text-dark">{{ $namaBahan }}</span>
                                                                </td>
                                                                 <td class="pe-0 py-1 text-end" style="font-size: 0.75rem;">
                                                                    @if(isset($stockInfo['konversi_nilai']) && $stockInfo['konversi_nilai'] > 0)
                                                                        @php
                                                                            $butuhKonversiMenu = $totalButuhMenu / $stockInfo['konversi_nilai'];
                                                                        @endphp
                                                                        <span class="fw-bold">{{ formatIndonesianNumber($butuhKonversiMenu) }} {{ $stockInfo['konversi_satuan'] }}</span>
                                                                        <div class="text-muted mt-1" style="font-size: 0.65rem;">({{ formatIndonesianNumber($totalButuhMenu) }} {{ $satuan }})</div>
                                                                    @else
                                                                        <span class="fw-bold">{{ formatIndonesianNumber($totalButuhMenu) }}</span> <span class="text-muted">{{ $satuan }}</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if($porsiKecilDetails->count() > 0)
                                <div class="col-12 mt-4">
                                    <h6 class="text-warning mb-0 fw-bold"><i class="bx bx-chevron-right"></i> Porsi Kecil : {{ formatIndonesianNumber($totalPorsiKecil) }} Porsi</h6>
                                </div>
                                @foreach($porsiKecilDetails as $detail)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="border rounded p-3 h-100 bg-light-warning">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar avatar-sm me-2">
                                                    @if($detail->menuMakanan->gambar_url)
                                                        <img src="{{ $detail->menuMakanan->gambar_url }}" class="rounded" style="object-fit: cover;">
                                                    @else
                                                        <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-dish"></i></span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold small">{{ $detail->menuMakanan->nama_menu }}</h6>
                                                    <span class="badge bg-label-warning" style="font-size: 0.6rem;">
                                                        Kecil - {{ formatIndonesianNumber($detail->jumlah_porsi) }} Porsi
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tbody>
                                                        @foreach($detail->menuMakanan->bahanMenu as $bahanMenu)
                                                            @php
                                                                $idTemplate = $bahanMenu->id_template_item;
                                                                $namaBahan = $bahanMenu->templateItem->nama_bahan;
                                                                $satuan = $bahanMenu->templateItem->satuan;
                                                                $kebutuhanPerPorsi = $bahanMenu->jumlah_per_porsi;
                                                                $totalButuhMenu = $kebutuhanPerPorsi * $detail->jumlah_porsi;
                                                                
                                                                $stockInfo = null;
                                                                if(isset($stockData) && isset($stockData[$idTemplate])) {
                                                                    $stockInfo = is_object($stockData[$idTemplate]) ? (array) $stockData[$idTemplate] : $stockData[$idTemplate];
                                                                }
                                                            @endphp
                                                            <tr>
                                                                <td class="ps-0 py-1" style="font-size: 0.75rem;">
                                                                    <span class="text-dark">{{ $namaBahan }}</span>
                                                                </td>
                                                                 <td class="pe-0 py-1 text-end" style="font-size: 0.75rem;">
                                                                    @if(isset($stockInfo['konversi_nilai']) && $stockInfo['konversi_nilai'] > 0)
                                                                        @php
                                                                            $butuhKonversiMenu = $totalButuhMenu / $stockInfo['konversi_nilai'];
                                                                        @endphp
                                                                        <span class="fw-bold">{{ formatIndonesianNumber($butuhKonversiMenu) }} {{ $stockInfo['konversi_satuan'] }}</span>
                                                                        <div class="text-muted mt-1" style="font-size: 0.65rem;">({{ formatIndonesianNumber($totalButuhMenu) }} {{ $satuan }})</div>
                                                                    @else
                                                                        <span class="fw-bold">{{ formatIndonesianNumber($totalButuhMenu) }}</span> <span class="text-muted">{{ $satuan }}</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                                                    {{ formatIndonesianNumber($laporan->jumlah_dibutuhkan) }}
                                                    {{ $laporan->satuan }}
                                                </td>
                                                <td>
                                                    {{ formatIndonesianNumber($laporan->jumlah_tersedia) }}
                                                    {{ $laporan->satuan }}
                                                </td>
                                                <td class="text-danger">
                                                    {{ formatIndonesianNumber($laporan->jumlah_kurang) }}
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
                                            {{ formatIndonesianNumber($kebutuhan) }}
                                            {{ $satuan }}, tersedia
                                            {{ formatIndonesianNumber($stockTersedia) }}
                                            {{ $satuan }}
                                            <span class="text-danger">
                                                (kurang
                                                {{ formatIndonesianNumber($kekurangan) }}
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

        @if ($transaksi->orderProduksi)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">
                                <i class="bx bx-check-shield me-2"></i>
                                Status Produksi & Distribusi
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-4 mb-md-0">
                                    <h6 class="fw-semibold">
                                        Status Produksi
                                    </h6>
                                    @php
                                        $mapStatusProduksi = [
                                            'stok_kurang' => ['badge' => 'bg-danger', 'icon' => 'bx-error-circle', 'text' => 'Stok Kurang'],
                                            'belum_dibuat' => ['badge' => 'bg-secondary', 'icon' => 'bx-time', 'text' => 'Belum Dibuat'],
                                            'sedang_dibuat' => ['badge' => 'bg-warning', 'icon' => 'bx-loader-circle', 'text' => 'Sedang Dibuat'],
                                            'selesai' => ['badge' => 'bg-success', 'icon' => 'bx-check-circle', 'text' => 'Selesai']
                                        ];
                                        $prodStatusData = $mapStatusProduksi[$transaksi->orderProduksi->status] ?? ['badge' => 'bg-secondary', 'icon' => 'bx-help-circle', 'text' => 'Unknown'];
                                    @endphp
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial rounded-circle {{ $prodStatusData['badge'] }}">
                                                <i class="bx {{ $prodStatusData['icon'] }}"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <p class="mb-0 text-muted">Status</p>
                                            <h6 class="mb-0">{{ $prodStatusData['text'] }}</h6>
                                        </div>
                                    </div>
                                    
                                    @if ($transaksi->orderProduksi->status === 'selesai' && $transaksi->orderProduksi->dokumentasi->count() > 0)
                                        <div class="mt-3">
                                            <p class="mb-2 text-muted small">Dokumentasi Produksi:</p>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach ($transaksi->orderProduksi->dokumentasi as $dok)
                                                    <a href="{{ $dok->url }}" target="_blank">
                                                        <img src="{{ $dok->url }}" alt="Dokumentasi Produksi" class="rounded border" style="width: 80px; height: 80px; object-fit: cover;">
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-semibold">
                                        Status Distribusi
                                    </h6>
                                    @if ($transaksi->orderProduksi->distribusiOrder)
                                        @php
                                            $orderDistribusi = $transaksi->orderProduksi->distribusiOrder;
                                            $mapStatusDistribusi = [
                                                'belum_dikirim' => ['badge' => 'bg-secondary', 'icon' => 'bx-time', 'text' => 'Belum Dikirim'],
                                                'sedang_dikirim' => ['badge' => 'bg-warning', 'icon' => 'bx-car', 'text' => 'Sedang Dikirim'],
                                                'sudah_dikirim' => ['badge' => 'bg-success', 'icon' => 'bx-check-double', 'text' => 'Sudah Dikirim']
                                            ];
                                            $distStatusData = $mapStatusDistribusi[$orderDistribusi->status] ?? ['badge' => 'bg-secondary', 'icon' => 'bx-help-circle', 'text' => 'Unknown'];
                                        @endphp
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-initial rounded-circle {{ $distStatusData['badge'] }}">
                                                    <i class="bx {{ $distStatusData['icon'] }}"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <p class="mb-0 text-muted">Status</p>
                                                <h6 class="mb-0">{{ $distStatusData['text'] }}</h6>
                                            </div>
                                        </div>
                                        
                                        @if ($orderDistribusi->status === 'sudah_dikirim' && $orderDistribusi->dokumentasi->count() > 0)
                                            <div class="mt-3">
                                                <p class="mb-2 text-muted small">Dokumentasi Distribusi:</p>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach ($orderDistribusi->dokumentasi as $dok)
                                                        <a href="{{ $dok->url }}" target="_blank">
                                                            <img src="{{ $dok->url }}" alt="Dokumentasi Distribusi" class="rounded border" style="width: 80px; height: 80px; object-fit: cover;">
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-secondary mb-0 py-2">
                                            <p class="mb-0 small"><i class="bx bx-info-circle me-1"></i> Data distribusi belum tersedia.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <a href="{{ route('ahli-gizi.transaksi.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>
                            Kembali ke Daftar
                        </a>
                        <form action="{{ route('ahli-gizi.transaksi.create-next', $transaksi) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-plus-circle me-1"></i>
                                Buat Transaksi Selanjutnya
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
