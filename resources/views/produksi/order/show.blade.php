@extends('template_produksi.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    <i class="bx bx-show"></i>
                                </span>
                            </div>
                            <div>
                                <h4 class="mb-1">Detail Input Paket Menu</h4>
                                <p class="mb-0 text-muted">Lihat detail paket menu yang telah dibuat</p>
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

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bx bx-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bx bx-x-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session("success") }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session("error"))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            {{ session("error") }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="fw-semibold" style="width: 40%">Tanggal Transaksi:</td>
                                    <td>{{ $transaksi->tanggal_transaksi->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Dapur:</td>
                                    <td>{{ $transaksi->dapur->nama_dapur }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Ahli Gizi:</td>
                                    <td>{{ $transaksi->createdBy->nama ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            @php
                                $totalPorsiBesar = $transaksi->detailTransaksiDapur->where('tipe_porsi', 'besar')->sum('jumlah_porsi') ?? 0;
                                $totalPorsiKecil = $transaksi->detailTransaksiDapur->where('tipe_porsi', 'kecil')->sum('jumlah_porsi') ?? 0;
                                $totalKeseluruhan = $totalPorsiBesar + $totalPorsiKecil;
                            @endphp
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="fw-semibold" style="width: 40%">Total Porsi Besar:</td>
                                    <td><span class="badge bg-label-success">{{ $totalPorsiBesar }} Porsi</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Total Porsi Kecil:</td>
                                    <td><span class="badge bg-label-warning">{{ $totalPorsiKecil }} Porsi</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Total Keseluruhan:</td>
                                    <td><span class="badge bg-label-primary">{{ $totalKeseluruhan }} Porsi</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @if ($transaksi->keterangan)
                        <div class="mt-3">
                            <strong>Keterangan:</strong>
                            <p class="mb-0 text-muted">{{ $transaksi->keterangan }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Menu Area -->
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
                        $porsiBesar = $transaksi->detailTransaksiDapur->where('tipe_porsi', 'besar');
                        $porsiKecil = $transaksi->detailTransaksiDapur->where('tipe_porsi', 'kecil');
                    @endphp

                    @if($porsiBesar->count() > 0)
                        <h6 class="text-success mb-3">Menu Porsi Besar</h6>
                        <div class="row mb-4">
                            @foreach($porsiBesar as $detail)
                                <div class="col-md-6 mb-3">
                                    <div class="card border border-success">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-start">
                                                @if($detail->menuMakanan->gambar_url)
                                                    <img src="{{ $detail->menuMakanan->gambar_url }}" 
                                                         alt="{{ $detail->menuMakanan->nama_menu }}" 
                                                         class="rounded me-3" 
                                                         style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                    <div class="avatar avatar-lg me-3">
                                                        <span class="avatar-initial rounded bg-label-success">
                                                            <i class="bx bx-bowl-hot"></i>
                                                        </span>
                                                    </div>
                                                @endif
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $detail->menuMakanan->nama_menu }}</h6>
                                                    <p class="text-muted small mb-2">{{ $detail->menuMakanan->kategori }}</p>
                                                    <span class="badge bg-success">{{ $detail->jumlah_porsi }} Porsi</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($porsiKecil->count() > 0)
                        <h6 class="text-warning mb-3">Menu Porsi Kecil</h6>
                        <div class="row mb-4">
                            @foreach($porsiKecil as $detail)
                                <div class="col-md-6 mb-3">
                                    <div class="card border border-warning">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-start">
                                                @if($detail->menuMakanan->gambar_url)
                                                    <img src="{{ $detail->menuMakanan->gambar_url }}" 
                                                         alt="{{ $detail->menuMakanan->nama_menu }}" 
                                                         class="rounded me-3" 
                                                         style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                    <div class="avatar avatar-lg me-3">
                                                        <span class="avatar-initial rounded bg-label-warning">
                                                            <i class="bx bx-bowl-hot"></i>
                                                        </span>
                                                    </div>
                                                @endif
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $detail->menuMakanan->nama_menu }}</h6>
                                                    <p class="text-muted small mb-2">{{ $detail->menuMakanan->kategori }}</p>
                                                    <span class="badge bg-warning">{{ $detail->jumlah_porsi }} Porsi</span>
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

    <!-- Kebutuhan Bahan Area -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bx bx-barcode me-2"></i>
                        Kebutuhan Bahan
                    </h5>
                </div>
                <div class="card-body">
                    @if (!empty($bahanKebutuhan))
                        <h6 class="mb-3">Total Kebutuhan Bahan</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Bahan</th>
                                        <th>Total Kebutuhan</th>
                                        <th>Stok Gudang (Aktual)</th>
                                        <th>Stok Siap Pakai</th>
                                        <th>Sisa (Estimasi)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bahanKebutuhan as $idTemplate => $bahan)
                                        @php
                                            $bahanArray = is_object($bahan) ? (array) $bahan : $bahan;
                                            $satuan = $bahanArray['satuan'] ?? 'N/A';
                                            $namaBahan = $bahanArray['nama_bahan'] ?? 'Unknown';
                                            $totalButuh = isset($bahanArray['total_kebutuhan']) ? (float)$bahanArray['total_kebutuhan'] : 0;
                                            
                                            $stockInfo = $stockData[$idTemplate] ?? ['stock_tersedia' => 0, 'stock_aktual' => 0, 'sufficient' => false];
                                            $stockTersedia = (float)$stockInfo['stock_tersedia'];
                                            $stockAktual = (float)$stockInfo['stock_aktual'];
                                            
                                            if ($order->status === 'belum_dibuat' || $order->status === 'stok_kurang') {
                                                $estimasiSisa = $stockTersedia - $totalButuh;
                                            } else {
                                                $estimasiSisa = $stockTersedia;
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $namaBahan }}</td>
                                            <td><strong>{{ number_format($totalButuh, 2) }} {{ $satuan }}</strong></td>
                                            <td>{{ number_format($stockAktual, 2) }} {{ $satuan }}</td>
                                            <td>{{ number_format($stockTersedia, 2) }} {{ $satuan }}</td>
                                            <td>
                                                <span class="{{ $estimasiSisa < 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ number_format($estimasiSisa, 2) }} {{ $satuan }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($stockTersedia <= 0 && ($order->status === 'belum_dibuat' || $order->status === 'stok_kurang'))
                                                    <span class="badge bg-danger">Stok Kosong</span>
                                                @elseif(!$stockInfo['sufficient'] && ($order->status === 'belum_dibuat' || $order->status === 'stok_kurang'))
                                                    <span class="badge bg-warning text-dark">Stok Kurang</span>
                                                @else
                                                    <span class="badge bg-success">Stok Mencukupi</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-secondary">Bahan kebutuhan tidak ditemukan.</div>
                    @endif

                    @if (!empty($bahanBesar))
                        <h6 class="text-success mb-3">Kebutuhan Bahan Porsi Besar</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Bahan</th>
                                        <th>Kebutuhan Besar</th>
                                        <th>Stok Gudang (Aktual)</th>
                                        <th>Stok Siap Pakai</th>
                                        <th>Sisa (Estimasi)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bahanBesar as $idTemplate => $bahan)
                                        @php
                                            $bahanArray = is_object($bahan) ? (array) $bahan : $bahan;
                                            $satuan = $bahanArray['satuan'] ?? 'N/A';
                                            $namaBahan = $bahanArray['nama_bahan'] ?? 'Unknown';
                                            $kebutuhanBesar = isset($bahanArray['total_kebutuhan']) ? (float)$bahanArray['total_kebutuhan'] : 0;
                                            
                                            $stockInfo = $stockData[$idTemplate] ?? ['stock_tersedia' => 0, 'stock_aktual' => 0, 'sufficient' => false];
                                            $stockTersedia = (float)$stockInfo['stock_tersedia'];
                                            $stockAktual = (float)$stockInfo['stock_aktual'];
                                            
                                            // Tetap tampilkan estimasi persisten terhadap total keseluruhan 
                                            // untuk menghindari kesalahan asumsi user
                                            $totalButuhKeseluruhan = isset($bahanKebutuhan[$idTemplate]['total_kebutuhan']) ? (float)$bahanKebutuhan[$idTemplate]['total_kebutuhan'] : 0;
                                            if ($order->status === 'belum_dibuat' || $order->status === 'stok_kurang') {
                                                $estimasiSisa = $stockTersedia - $totalButuhKeseluruhan;
                                            } else {
                                                $estimasiSisa = $stockTersedia;
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $namaBahan }}</td>
                                            <td><strong>{{ number_format($kebutuhanBesar, 2) }} {{ $satuan }}</strong></td>
                                            <td>{{ number_format($stockAktual, 2) }} {{ $satuan }}</td>
                                            <td>{{ number_format($stockTersedia, 2) }} {{ $satuan }}</td>
                                            <td>
                                                <span class="{{ $estimasiSisa < 0 ? 'text-danger' : 'text-success' }}" title="Dihitung berdasarkan total kebutuhan keseluruhan">
                                                    {{ number_format($estimasiSisa, 2) }} {{ $satuan }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($stockTersedia <= 0 && ($order->status === 'belum_dibuat' || $order->status === 'stok_kurang'))
                                                    <span class="badge bg-danger">Stok Kosong</span>
                                                @elseif(!$stockInfo['sufficient'] && ($order->status === 'belum_dibuat' || $order->status === 'stok_kurang'))
                                                    <span class="badge bg-warning text-dark">Stok Kurang</span>
                                                @else
                                                    <span class="badge bg-success">Stok Mencukupi</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if (!empty($bahanKecil))
                        <h6 class="text-warning mb-3">Kebutuhan Bahan Porsi Kecil</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Bahan</th>
                                        <th>Kebutuhan Kecil</th>
                                        <th>Stok Gudang (Aktual)</th>
                                        <th>Stok Siap Pakai</th>
                                        <th>Sisa (Estimasi)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bahanKecil as $idTemplate => $bahan)
                                        @php
                                            $bahanArray = is_object($bahan) ? (array) $bahan : $bahan;
                                            $satuan = $bahanArray['satuan'] ?? 'N/A';
                                            $namaBahan = $bahanArray['nama_bahan'] ?? 'Unknown';
                                            $kebutuhanKecil = isset($bahanArray['total_kebutuhan']) ? (float)$bahanArray['total_kebutuhan'] : 0;
                                            
                                            $stockInfo = $stockData[$idTemplate] ?? ['stock_tersedia' => 0, 'stock_aktual' => 0, 'sufficient' => false];
                                            $stockTersedia = (float)$stockInfo['stock_tersedia'];
                                            $stockAktual = (float)$stockInfo['stock_aktual'];
                                            
                                            // Tetap tampilkan estimasi persisten terhadap total keseluruhan 
                                            // untuk menghindari kesalahan asumsi user
                                            $totalButuhKeseluruhan = isset($bahanKebutuhan[$idTemplate]['total_kebutuhan']) ? (float)$bahanKebutuhan[$idTemplate]['total_kebutuhan'] : 0;
                                            if ($order->status === 'belum_dibuat' || $order->status === 'stok_kurang') {
                                                $estimasiSisa = $stockTersedia - $totalButuhKeseluruhan;
                                            } else {
                                                $estimasiSisa = $stockTersedia;
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $namaBahan }}</td>
                                            <td><strong>{{ number_format($kebutuhanKecil, 2) }} {{ $satuan }}</strong></td>
                                            <td>{{ number_format($stockAktual, 2) }} {{ $satuan }}</td>
                                            <td>{{ number_format($stockTersedia, 2) }} {{ $satuan }}</td>
                                            <td>
                                                <span class="{{ $estimasiSisa < 0 ? 'text-danger' : 'text-success' }}" title="Dihitung berdasarkan total kebutuhan keseluruhan">
                                                    {{ number_format($estimasiSisa, 2) }} {{ $satuan }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($stockTersedia <= 0 && ($order->status === 'belum_dibuat' || $order->status === 'stok_kurang'))
                                                    <span class="badge bg-danger">Stok Kosong</span>
                                                @elseif(!$stockInfo['sufficient'] && ($order->status === 'belum_dibuat' || $order->status === 'stok_kurang'))
                                                    <span class="badge bg-warning text-dark">Stok Kurang</span>
                                                @else
                                                    <span class="badge bg-success">Stok Mencukupi</span>
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
                                $prodStatusData = $mapStatusProduksi[$order->status] ?? ['badge' => 'bg-secondary', 'icon' => 'bx-help-circle', 'text' => 'Unknown'];
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
                            
                            @if ($order->status === 'selesai' && $order->dokumentasi->count() > 0)
                                <div class="mt-3">
                                    <p class="mb-2 text-muted small">Dokumentasi Produksi:</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($order->dokumentasi as $dok)
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
                            @if ($order->distribusiOrder)
                                @php
                                    $orderDistribusi = $order->distribusiOrder;
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

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <a href="{{ route('produksi.order.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar
                    </a>
                    
                    <div class="d-flex gap-2">
                        @if($order->status !== 'stok_kurang' && $order->status !== 'selesai')
                            <button type="button"
                                class="btn btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#updateStatusModal"
                                data-id="{{ $order->id_order }}"
                                data-status="{{ $order->status }}"
                                data-catatan="{{ $order->catatan }}">
                                <i class="bx bx-edit me-1"></i> Update Status
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Status -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateStatusForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" id="modalStatus" required>
                            <option value="belum_dibuat">Belum Dibuat</option>
                            <option value="sedang_dibuat">Sedang Dibuat</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                    <!-- Foto Dokumentasi - Muncul hanya jika Selesai -->
                    <div class="mb-3 d-none" id="dokumentasiWrapper">
                        <label class="form-label">Foto Dokumentasi (Wajib untuk Selesai, Minimal 1)</label>
                        <input type="file" name="dokumentasi[]" class="form-control" multiple accept="image/*" id="inputDokumentasi">
                        <small class="text-muted">Dapat memilih lebih dari 1 gambar.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="catatan" class="form-control" id="modalCatatan" rows="3" placeholder="Tambahkan catatan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const updateStatusModal = document.getElementById('updateStatusModal');
        const modalStatus = document.getElementById('modalStatus');
        const dokumentasiWrapper = document.getElementById('dokumentasiWrapper');
        const inputDokumentasi = document.getElementById('inputDokumentasi');

        if (updateStatusModal) {
            updateStatusModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const orderId = button.getAttribute('data-id');
                const status = button.getAttribute('data-status');
                const catatan = button.getAttribute('data-catatan');

                modalStatus.value = status;
                document.getElementById('modalCatatan').value = (catatan && catatan !== 'null') ? catatan : '';
                
                // Toggle dokumentasi visibility on modal open
                toggleDokumentasi(status);

                const baseUrl = '{{ route("produksi.order.update-status", ":id") }}';
                document.getElementById('updateStatusForm').action = baseUrl.replace(':id', orderId);
            });

            modalStatus.addEventListener('change', function() {
                toggleDokumentasi(this.value);
            });

            function toggleDokumentasi(status) {
                if (status === 'selesai') {
                    dokumentasiWrapper.classList.remove('d-none');
                    inputDokumentasi.setAttribute('required', 'required');
                } else {
                    dokumentasiWrapper.classList.add('d-none');
                    inputDokumentasi.removeAttribute('required');
                }
            }
        }
    });
</script>
@endpush
