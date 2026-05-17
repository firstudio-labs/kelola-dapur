@extends('template_mitra.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a href="{{ route('mitra.dashboard') }}"
                                class="text-muted me-2">
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <a href="{{ route('mitra.laporan-transaksi.index') }}"
                                class="text-muted me-2">
                                Laporan Transaksi
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Detail Transaksi</span>
                        </nav>
                        <h4 class="mb-1">
                            Detail Transaksi - {{ $dapur->nama_dapur }}
                        </h4>
                        <p class="mb-0 text-muted">
                            ID Transaksi:
                            {{ $approval->transaksiDapur->id_transaksi }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">

            <div class="col-lg-8">

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-receipt me-2"></i>
                            Informasi Transaksi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">
                                        Tanggal Transaksi
                                    </label>
                                    <div class="fw-medium">
                                        {{ $approval->transaksiDapur->tanggal_transaksi->format('d F Y') }}, {{ $approval->transaksiDapur->created_at->format('H:i') }}
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">
                                        Status Approval
                                    </label>
                                    <div>
                                        <span class="badge {{ $approval->getStatusBadgeClass() }} fs-6">
                                            {{ ucfirst($approval->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">
                                        Total Porsi
                                    </label>
                                    <div class="fw-medium text-primary fs-5">
                                        {{ $approval->transaksiDapur->total_porsi }}
                                        porsi
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">
                                        Dapur Asal
                                    </label>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                <i class="bx bx-building-house"></i>
                                            </span>
                                        </div>
                                        <div class="fw-medium">
                                            {{ $dapur->nama_dapur ?? 'Unknown Dapur' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">
                                        Dibuat Oleh
                                    </label>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-info">
                                                {{ strtoupper(substr($approval->transaksiDapur->createdBy->nama ?? 'NA', 0, 2)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-medium">
                                                {{ $approval->transaksiDapur->createdBy->nama ?? 'Unknown' }}
                                            </div>
                                            <small class="text-muted">
                                                Ahli Gizi
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">
                                        Keterangan
                                    </label>
                                    <div class="fw-medium">
                                        {{ $approval->transaksiDapur->keterangan ?? 'Paket Menu Harian' }}
                                    </div>
                                </div>
                                @if ($approval->approved_at)
                                    <div class="mb-3">
                                        <label class="form-label text-muted">
                                            Tanggal Diproses
                                        </label>
                                        <div class="fw-medium">
                                            {{ $approval->approved_at->format('d F Y, H:i') }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($approval->catatan_approval)
                            <div class="mt-4">
                                <label class="form-label text-muted">
                                    Catatan Approval
                                </label>
                                <div class="p-3 bg-light rounded">
                                    {{ $approval->catatan_approval }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-food-menu me-2"></i>
                            Detail Menu
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $porsiBesar = $approval->transaksiDapur->detailTransaksiDapur->where('tipe_porsi', 'besar');
                            $porsiKecil = $approval->transaksiDapur->detailTransaksiDapur->where('tipe_porsi', 'kecil');
                            $totalPorsiBesar = $approval->transaksiDapur->getTotalPorsiBesar();
                            $totalPorsiKecil = $approval->transaksiDapur->getTotalPorsiKecil();
                        @endphp

                        <div class="row">
                            @if ($porsiBesar->count() > 0)
                                <div class="col-md-6 mb-4 mb-md-0">
                                    <div
                                        class="p-2 bg-label-primary rounded mb-3 d-flex justify-content-between align-items-center">
                                        <span class="fw-bold small text-uppercase">Porsi Besar :</span>
                                        <span class="badge bg-primary">{{ (float) $totalPorsiBesar }} Porsi</span>
                                    </div>
                                    <div class="list-group list-group-flush">
                                        @foreach ($porsiBesar as $detail)
                                            <div class="list-group-item px-0 py-2 border-0 d-flex align-items-center">
                                                <img src="{{ $detail->menuMakanan->gambar_url ?? asset('images/menu/default-menu.jpg') }}"
                                                    class="rounded me-3"
                                                    style="width: 45px; height: 45px; object-fit: cover;"
                                                    onerror="this.src='{{ asset('images/menu/default-menu.jpg') }}'">
                                                <div>
                                                    <h6 class="mb-0">{{ $detail->menuMakanan->nama_menu }}</h6>
                                                    <small
                                                        class="text-muted">{{ $detail->menuMakanan->jenis_menu }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if ($porsiKecil->count() > 0)
                                <div class="col-md-6">
                                    <div
                                        class="p-2 bg-label-warning rounded mb-3 d-flex justify-content-between align-items-center">
                                        <span class="fw-bold small text-uppercase">Porsi Kecil :</span>
                                        <span class="badge bg-warning">{{ (float) $totalPorsiKecil }} Porsi</span>
                                    </div>
                                    <div class="list-group list-group-flush">
                                        @foreach ($porsiKecil as $detail)
                                            <div class="list-group-item px-0 py-2 border-0 d-flex align-items-center">
                                                <img src="{{ $detail->menuMakanan->gambar_url ?? asset('images/menu/default-menu.jpg') }}"
                                                    class="rounded me-3"
                                                    style="width: 45px; height: 45px; object-fit: cover;"
                                                    onerror="this.src='{{ asset('images/menu/default-menu.jpg') }}'">
                                                <div>
                                                    <h6 class="mb-0">{{ $detail->menuMakanan->nama_menu }}</h6>
                                                    <small
                                                        class="text-muted">{{ $detail->menuMakanan->jenis_menu }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($approval->approved_at || $approval->created_at)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-time me-2"></i>
                                Timeline
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline timeline-sm">
                                <div class="timeline-item">
                                    <span class="timeline-indicator timeline-indicator-success">
                                        <i class="bx bx-plus"></i>
                                    </span>
                                    <div class="timeline-content">
                                        <div class="fw-medium">
                                            Transaksi Dibuat
                                        </div>
                                        <small class="text-muted">
                                            Update: {{ $approval->transaksiDapur->created_at->format('d M Y, H:i') }}
                                        </small>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <span class="timeline-indicator timeline-indicator-warning">
                                        <i class="bx bx-time"></i>
                                    </span>
                                    <div class="timeline-content">
                                        <div class="fw-medium">
                                            Diajukan untuk Approval
                                        </div>
                                        <small class="text-muted">
                                            Update: {{ $approval->created_at->format('d M Y, H:i') }}
                                        </small>
                                        @if ($stockCheck['has_snapshots'])
                                            <small class="text-info d-block">
                                                Stock snapshot dibuat
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                @if ($approval->approved_at)
                                    <div class="timeline-item">
                                        <span
                                            class="timeline-indicator {{ $approval->isApproved() ? 'timeline-indicator-success' : 'timeline-indicator-danger' }}">
                                            <i class="bx {{ $approval->isApproved() ? 'bx-check' : 'bx-x' }}"></i>
                                        </span>
                                        <div class="timeline-content border-bottom pb-3 mb-3">
                                            <div class="fw-medium">
                                                {{ $approval->isApproved() ? 'Disetujui' : 'Ditolak' }}
                                            </div>
                                            <small class="text-muted">
                                                Update: {{ $approval->approved_at->format('d M Y, H:i') }}
                                            </small>
                                            @if ($approval->catatan_approval)
                                                <small class="text-muted d-block mt-1"><i
                                                        class="bx bx-note me-1"></i>{{ $approval->catatan_approval }}</small>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($approval->isApproved())
                                        @php
                                            $orderProd = $approval->transaksiDapur->orderProduksi;
                                            $orderDist = $orderProd ? $orderProd->distribusiOrder : null;
                                        @endphp

                                        @if ($orderProd)
                                            @php
                                                $mapStatusProd = [
                                                    'stok_kurang' => [
                                                        'badge' => 'bg-danger',
                                                        'icon' => 'bx-error-circle',
                                                        'text' => 'Stok Kurang',
                                                    ],
                                                    'belum_dibuat' => [
                                                        'badge' => 'bg-secondary',
                                                        'icon' => 'bx-time',
                                                        'text' => 'Belum Dibuat',
                                                    ],
                                                    'sedang_dibuat' => [
                                                        'badge' => 'bg-warning',
                                                        'icon' => 'bx-loader-circle',
                                                        'text' => 'Sedang Dibuat',
                                                    ],
                                                    'selesai' => [
                                                        'badge' => 'bg-success',
                                                        'icon' => 'bx-check-circle',
                                                        'text' => 'Selesai',
                                                    ],
                                                ];
                                                $prodData =
                                                    $mapStatusProd[$orderProd->status] ??
                                                    $mapStatusProd['belum_dibuat'];
                                            @endphp
                                            <div class="timeline-item">
                                                <span
                                                    class="timeline-indicator {{ str_replace('bg-', 'timeline-indicator-', $prodData['badge']) }}">
                                                    <i class="bx {{ $prodData['icon'] }}"></i>
                                                </span>
                                                <div
                                                    class="timeline-content {{ $orderProd->status === 'selesai' ? 'border-bottom pb-3 mb-3' : '' }}">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <div class="fw-medium">Status Produksi</div>
                                                        <span
                                                            class="badge {{ $prodData['badge'] }}">{{ $prodData['text'] }}</span>
                                                    </div>
                                                    <small class="text-muted d-block mb-2">Update:
                                                        {{ $orderProd->updated_at->format('d M Y, H:i') }}</small>
                                                    <div class="mt-2 text-start">
                                                        <button type="button" class="btn btn-xs btn-outline-primary"
                                                            data-bs-toggle="modal" data-bs-target="#modalDetailProduksi">
                                                            <i class="bx bx-info-circle me-1"></i>Lihat Detail
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($orderDist)
                                            @php
                                                $mapStatusDist = [
                                                    'belum_dikirim' => [
                                                        'badge' => 'bg-secondary',
                                                        'icon' => 'bx-time',
                                                        'text' => 'Belum Dikirim',
                                                    ],
                                                    'sedang_dikirim' => [
                                                        'badge' => 'bg-warning',
                                                        'icon' => 'bx-car',
                                                        'text' => 'Sedang Dikirim',
                                                    ],
                                                    'sudah_dikirim' => [
                                                        'badge' => 'bg-success',
                                                        'icon' => 'bx-check-double',
                                                        'text' => 'Sudah Dikirim',
                                                    ],
                                                ];
                                                $distData =
                                                    $mapStatusDist[$orderDist->status] ??
                                                    $mapStatusDist['belum_dikirim'];
                                            @endphp
                                            <div class="timeline-item">
                                                <span
                                                    class="timeline-indicator {{ str_replace('bg-', 'timeline-indicator-', $distData['badge']) }}">
                                                    <i class="bx {{ $distData['icon'] }}"></i>
                                                </span>
                                                <div class="timeline-content border-bottom pb-3 mb-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="fw-medium">Status Distribusi</div>
                                                        <span
                                                            class="badge {{ $distData['badge'] }}">{{ $distData['text'] }}</span>
                                                    </div>
                                                    <small class="text-muted">Update:
                                                        {{ $orderDist->updated_at->format('d M Y, H:i') }}</small>
                                                </div>
                                            </div>

                                            @php
                                                $jumlahPenerima = $orderDist->details->count();
                                                $penerimaSelesai = $orderDist->details
                                                    ->where('status', 'sudah_dikirim')
                                                    ->count();
                                                $penerimaDiterima = $orderDist->details
                                                    ->where('status_penerimaan', 'diterima')
                                                    ->count();

                                                $pctDelivered =
                                                    $jumlahPenerima > 0
                                                        ? round(($penerimaSelesai / $jumlahPenerima) * 100)
                                                        : 0;
                                                $pctAccepted =
                                                    $jumlahPenerima > 0
                                                        ? round(($penerimaDiterima / $jumlahPenerima) * 100)
                                                        : 0;

                                                $lastDeliveryUpdate =
                                                    $orderDist->details->where('status', 'sudah_dikirim')->max('updated_at') ??
                                                    $orderDist->updated_at;
                                                $lastAcceptanceUpdate =
                                                    $orderDist->details->where('status_penerimaan', 'diterima')->max('updated_at') ??
                                                    $orderDist->updated_at;
                                            @endphp
                                            <div class="timeline-item">
                                                <span
                                                    class="timeline-indicator {{ $pctDelivered == 100 ? 'timeline-indicator-success' : 'timeline-indicator-warning' }}">
                                                    <i class="bx bx-package"></i>
                                                </span>
                                                <div class="timeline-content border-bottom pb-3 mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <div class="fw-medium">Progres Pengiriman (Distribusi)</div>
                                                        <span
                                                            class="text-warning fw-semibold">{{ $penerimaSelesai }}/{{ $jumlahPenerima }}
                                                            Terkirim</span>
                                                    </div>
                                                    <div class="progress mb-1" style="height:6px">
                                                        <div class="progress-bar {{ $pctDelivered == 100 ? 'bg-success' : 'bg-warning' }}"
                                                            style="width:{{ $pctDelivered }}%" role="progressbar"></div>
                                                    </div>
                                                    <small class="text-muted d-block mb-1">Update: {{ $lastDeliveryUpdate->format('d M Y, H:i') }}</small>
                                                    <small class="text-muted d-block mb-2">{{ $pctDelivered }}% Pengiriman
                                                        selesai</small>
                                                    <div class="mt-2 text-start">
                                                        <button type="button" class="btn btn-xs btn-outline-warning"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalDetailDistribusi">
                                                            <i class="bx bx-info-circle me-1"></i>Lihat Detail
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="timeline-item">
                                                <span
                                                    class="timeline-indicator {{ $pctAccepted == 100 ? 'timeline-indicator-success' : 'timeline-indicator-primary' }}">
                                                    <i class="bx bx-check-shield"></i>
                                                </span>
                                                <div class="timeline-content">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <div class="fw-medium">Konfirmasi Penerima MBG</div>
                                                        <span
                                                            class="text-primary fw-semibold">{{ $penerimaDiterima }}/{{ $jumlahPenerima }}
                                                            Dikonfirmasi</span>
                                                    </div>
                                                    <div class="progress mb-1" style="height:6px">
                                                        <div class="progress-bar {{ $pctAccepted == 100 ? 'bg-success' : 'bg-primary' }}"
                                                            style="width:{{ $pctAccepted }}%" role="progressbar"></div>
                                                    </div>
                                                    <small class="text-muted d-block mb-1">Update: {{ $lastAcceptanceUpdate->format('d M Y, H:i') }}</small>
                                                    <small class="text-muted d-block mb-2">{{ $pctAccepted }}%
                                                        Dikonfirmasi Makanan Diterima</small>
                                                    <div class="mt-2 text-start">
                                                        <button type="button" class="btn btn-xs btn-outline-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalDetailPenerimaan">
                                                            <i class="bx bx-list-ul me-1"></i>Lihat Detail
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-pie-chart-alt me-2"></i>
                            Ringkasan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-food-menu"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">
                                    Total Menu
                                </small>
                                <h6 class="mb-0">
                                    {{ $approval->transaksiDapur->detailTransaksiDapur->count() }}
                                </h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="bx bx-user-plus"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">
                                    Porsi Besar
                                </small>
                                <h6 class="mb-0">
                                    {{ $approval->transaksiDapur->getTotalPorsiBesar() }}
                                </h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="bx bx-user"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">
                                    Porsi Kecil
                                </small>
                                <h6 class="mb-0">
                                    {{ $approval->transaksiDapur->getTotalPorsiKecil() }}
                                </h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-package"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">
                                    Total Bahan
                                </small>
                                <h6 class="mb-0">
                                    {{ count($stockCheck['ingredients_summary']) }}
                                </h6>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="bx bx-minus-circle"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">
                                    Sisa Distribusi
                                </small>
                                <h6 class="mb-0">
                                    B: {{ (float) $summarySisa['remaining_dist_besar'] }} | K: {{ (float) $summarySisa['remaining_dist_kecil'] }}
                                </h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-secondary">
                                    <i class="bx bx-error-circle"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">
                                    Sisa Penerimaan
                                </small>
                                <h6 class="mb-0">
                                    B: {{ (float) $summarySisa['remaining_recv_besar'] }} | K: {{ (float) $summarySisa['remaining_recv_kecil'] }}
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-package me-2"></i>
                            Ketersediaan Stock
                            @if ($stockCheck['has_snapshots'])
                                <small class="text-muted">
                                    (berdasarkan snapshot saat approval)
                                </small>
                            @endif
                        </h5>
                        @if (!$stockCheck['can_produce'])
                            <span class="badge bg-danger">
                                Laporan Kekurangan Stok
                            </span>
                        @else
                            <span class="badge bg-success">
                                Stock Mencukupi
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if (!$stockCheck['can_produce'])
                            <div class="alert alert-danger mb-4">
                                <i class="bx bx-error-circle me-2"></i>
                                <strong>Peringatan:</strong>
                                Kekurangan stok di bawah merupakan laporan kekurangan stok pada saat pertama kali diajukan.
                            </div>
                        @endif

                        @if ($stockCheck['has_snapshots'])
                            <div class="alert alert-info mb-4">
                                <i class="bx bx-camera me-2"></i>
                                <strong>Info:</strong>
                                Data stock yang ditampilkan adalah snapshot saat
                                approval transaksi dibuat pada
                                {{ $approval->created_at->format('d M Y, H:i') }}.
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Bahan</th>
                                        <th>Diperlukan</th>
                                        <th>Snapshot Stock</th>
                                        @if ($stockCheck['has_snapshots'] && isset($stockCheck['ingredients_summary'][0]['current_available']))
                                            <th>Stock Saat Ini</th>
                                        @endif

                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stockCheck['ingredients_summary'] as $ingredient)
                                        @php
                                            $neededAmount = $ingredient['needed'];
                                            $isSufficient = $ingredient['sufficient'];
                                            $isBahanBasah = isset($ingredient['is_bahan_basah'])
                                                ? $ingredient['is_bahan_basah']
                                                : false;
                                        @endphp

                                        <tr class="{{ !$isSufficient ? 'table-danger-subtle' : '' }}">
                                            <td>
                                                <div class="fw-medium">
                                                    {{ $ingredient['nama_bahan'] }}
                                                </div>
                                                <small class="text-muted">
                                                    {{ $ingredient['satuan'] }}
                                                    @if ($isBahanBasah)
                                                        <span class="text-info">
                                                            (Bahan Basah +7%)
                                                        </span>
                                                    @endif
                                                </small>
                                            </td>
                                            <td>
                                                {{ (float) number_format($neededAmount, 3) }}
                                            </td>
                                            <td>
                                                {{ (float) number_format($ingredient['available'], 3) }}

                                            </td>
                                            @if ($stockCheck['has_snapshots'] && isset($ingredient['current_available']))
                                                <td>
                                                    {{ (float) number_format($ingredient['current_available'], 3) }}
                                                </td>
                                            @endif

                                            <td>
                                                @if ($isSufficient)
                                                    <span class="badge bg-success">
                                                        Cukup
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        Kurang
                                                        {{ (float) number_format($neededAmount - $ingredient['available'], 3) }}
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

        <!-- Modal Detail Penerimaan -->
        <div class="modal fade" id="modalDetailPenerimaan" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header py-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded bg-label-success text-success"><i
                                        class="bx bx-check-double"></i></span>
                            </div>
                            <h5 class="modal-title fw-bold mb-0">Detail Konfirmasi Penerima MBG</h5>
                        </div>
                    </div>
                    <div class="modal-body p-4">
                        @if (isset($orderDist) && $orderDist)
                            <div class="row g-4">
                                <div class="col-md-7 border-end">
                                    <div class="d-flex justify-content-between align-items-start mb-4">
                                        <div>
                                            <small class="text-uppercase text-muted fw-bold d-block mb-1"
                                                style="font-size: 0.7rem; letter-spacing: 1px;">Status Konfirmasi</small>
                                            <span class="badge bg-label-success px-3 py-2 fs-6">
                                                <i class="bx bx-check-double me-1"></i> Selesai Dikonfirmasi
                                            </span>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-uppercase text-muted fw-bold d-block mb-1"
                                                style="font-size: 0.7rem; letter-spacing: 1px;">Update Terakhir</small>
                                            <span class="fw-semibold text-dark"><i
                                                    class="bx bx-calendar-check me-1"></i>{{ $orderDist->updated_at->format('d M Y, H:i') }}</span>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="fw-bold mb-3 d-flex align-items-center">
                                            <i class="bx bx-restaurant me-2 text-success"></i>Daftar Menu Diterima
                                        </h6>
                                        @php
                                            $porsiBesarMenus = $approval->transaksiDapur->detailTransaksiDapur->where(
                                                'tipe_porsi',
                                                'besar',
                                            );
                                            $porsiKecilMenus = $approval->transaksiDapur->detailTransaksiDapur->where(
                                                'tipe_porsi',
                                                'kecil',
                                            );
                                            $totalPorsiBesar = $approval->transaksiDapur->getTotalPorsiBesar();
                                            $totalPorsiKecil = $approval->transaksiDapur->getTotalPorsiKecil();
                                        @endphp

                                        @if ($porsiBesarMenus->count() > 0)
                                            <div class="mb-3">
                                                <div
                                                    class="p-2 bg-label-primary rounded-top border border-bottom-0 d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold small text-uppercase">Porsi Besar :
                                                        {{ $totalPorsiBesar }} Porsi</span>
                                                </div>
                                                <div class="table-responsive border rounded-bottom">
                                                    <table class="table table-sm table-hover mb-0">
                                                        <tbody>
                                                            @foreach ($porsiBesarMenus as $det)
                                                                <tr>
                                                                    <td class="py-2 px-3 fw-medium text-dark">
                                                                        {{ $det->menuMakanan->nama_menu }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($porsiKecilMenus->count() > 0)
                                            <div class="mb-3">
                                                <div
                                                    class="p-2 bg-label-warning rounded-top border border-bottom-0 d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold small text-uppercase">Porsi Kecil :
                                                        {{ $totalPorsiKecil }} Porsi</span>
                                                </div>
                                                <div class="table-responsive border rounded-bottom">
                                                    <table class="table table-sm table-hover mb-0">
                                                        <tbody>
                                                            @foreach ($porsiKecilMenus as $det)
                                                                <tr>
                                                                    <td class="py-2 px-3 fw-medium text-dark">
                                                                        {{ $det->menuMakanan->nama_menu }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="fw-bold mb-3 d-flex align-items-center">
                                            <i class="bx bx-check-shield me-2 text-success"></i>Status Konfirmasi per
                                            Penerima
                                        </h6>
                                        <div class="table-responsive border rounded">
                                            <table class="table table-sm table-hover mb-0">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th class="py-2 px-3">Penerima</th>
                                                        <th class="py-2 text-center">Status</th>
                                                        <th class="py-2 text-center">Diterima (B/K)</th>
                                                        <th class="py-2 px-3">Ulasan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($orderDist->details as $pDetail)
                                                        <tr>
                                                            <td class="py-2 px-3">
                                                                <div class="fw-medium text-dark small">
                                                                    {{ $pDetail->penerimaMbg->userRole->user->nama ?? 'Unknown' }}
                                                                </div>
                                                            </td>
                                                            <td class="py-2 text-center">
                                                                @if ($pDetail->status_penerimaan === 'diterima')
                                                                    <span class="badge bg-label-success small"
                                                                        style="font-size: 0.6rem;">Diterima</span>
                                                                @elseif ($pDetail->status_penerimaan === 'ditolak')
                                                                    <span class="badge bg-label-danger small"
                                                                        style="font-size: 0.6rem;">Ditolak</span>
                                                                @else
                                                                    <span class="badge bg-label-secondary small"
                                                                        style="font-size: 0.6rem;">Menunggu</span>
                                                                @endif
                                                            </td>
                                                            <td class="py-2 text-center">
                                                                @if ($pDetail->status_penerimaan !== 'menunggu')
                                                                    <div class="text-primary" style="font-size: 0.6rem;">
                                                                        {{ $pDetail->porsi_besar_diterima ?? 0 }}B /
                                                                        {{ $pDetail->porsi_kecil_diterima ?? 0 }}K</div>
                                                                @else
                                                                    <span class="text-muted small">-</span>
                                                                @endif
                                                            </td>
                                                            <td class="py-2 px-3">
                                                                <small class="text-muted fst-italic d-block mb-1"
                                                                    style="font-size: 0.65rem;">{{ Str::limit($pDetail->ulasan, 30) ?: '-' }}</small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- Kritik Highlight --}}
                                    @php 
                                        $detailsWithKritik = $orderDist->details->whereNotNull('kritik');
                                    @endphp
                                    @if($detailsWithKritik->count() > 0)
                                        <div class="mt-4">
                                            <h6 class="fw-bold mb-3 d-flex align-items-center text-danger">
                                                <i class="bx bx-message-error me-2"></i>Kritik & Masukan dari Penerima
                                            </h6>
                                            <div class="list-group list-group-flush border rounded overflow-hidden shadow-sm">
                                                @foreach($detailsWithKritik as $dk)
                                                    <div class="list-group-item bg-label-danger p-3 border-start border-danger border-3 mb-1">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <span class="fw-bold text-dark small">{{ $dk->penerimaMbg->userRole->user->nama }}</span>
                                                            <small class="text-muted" style="font-size: 0.6rem;">{{ $dk->updated_at->diffForHumans() }}</small>
                                                        </div>
                                                        <p class="mb-0 text-dark small fst-italic" style="line-height: 1.4;">"{{ $dk->kritik }}"</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-5">
                                    <h6 class="fw-bold mb-3 d-flex align-items-center">
                                        <i class="bx bx-camera me-2 text-success"></i>Bukti Foto Penerimaan
                                    </h6>
                                    <div class="row g-2">
                                        @php $hasAnyPhoto = false; @endphp
                                        @foreach ($orderDist->details as $pDetail)
                                            @if ($pDetail->penerimaanFoto && $pDetail->penerimaanFoto->count() > 0)
                                                @php $hasAnyPhoto = true; @endphp
                                                @foreach ($pDetail->penerimaanFoto as $foto)
                                                    <div class="col-6">
                                                        <a href="{{ Storage::url($foto->path_foto) }}" target="_blank"
                                                            class="d-block card-hover position-relative">
                                                            <img src="{{ Storage::url($foto->path_foto) }}"
                                                                class="img-fluid rounded border shadow-sm"
                                                                style="width: 100%; height: 140px; object-fit: cover;"
                                                                alt="Bukti">
                                                            <div class="position-absolute top-0 end-0 p-1">
                                                                <span class="badge bg-dark bg-opacity-50"><i
                                                                        class="bx bx-zoom-in"></i></span>
                                                            </div>
                                                            <div
                                                                class="position-absolute bottom-0 start-0 w-100 p-1 bg-dark bg-opacity-50">
                                                                <small class="text-white"
                                                                    style="font-size: 0.6rem;">{{ $pDetail->penerimaMbg->userRole->user->nama }}</small>
                                                            </div>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            @endif
                                        @endforeach
                                        @if (!$hasAnyPhoto)
                                            <div class="col-12">
                                                <div class="text-center py-5 border rounded bg-lighter">
                                                    <i class="bx bx-no-entry display-4 text-muted mb-2"></i>
                                                    <p class="text-muted mb-0 small">Belum ada foto bukti penerimaan</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @if ($hasAnyPhoto)
                                        <div class="mt-3">
                                            <p class="text-muted small">
                                                <i class="bx bx-info-circle me-1"></i>Klik gambar untuk melihat dalam
                                                ukuran penuh.
                                            </p>
                                        </div>
                                    @endif

                                    {{-- Dokumentasi Kritik --}}
                                    @php $hasAnyKritikPhoto = false; @endphp
                                    @foreach ($orderDist->details as $pDetail)
                                        @if ($pDetail->kritik_foto)
                                            @php $hasAnyKritikPhoto = true; @endphp
                                        @endif
                                    @endforeach

                                    @if ($hasAnyKritikPhoto)
                                        <div class="mt-4">
                                            <h6 class="fw-bold mb-3 d-flex align-items-center">
                                                <i class="bx bx-image-alt me-2 text-warning"></i>Dokumentasi Kritik
                                            </h6>
                                            <div class="row g-2">
                                                @foreach ($orderDist->details as $pDetail)
                                                    @if ($pDetail->kritik_foto)
                                                        <div class="col-6">
                                                            <a href="{{ asset('storage/' . $pDetail->kritik_foto) }}" target="_blank"
                                                                class="d-block card-hover position-relative">
                                                                <img src="{{ asset('storage/' . $pDetail->kritik_foto) }}"
                                                                    class="img-fluid rounded border shadow-sm"
                                                                    style="width: 100%; height: 140px; object-fit: cover;"
                                                                    alt="Foto Kritik">
                                                                <div class="position-absolute top-0 end-0 p-1">
                                                                    <span class="badge bg-dark bg-opacity-50"><i
                                                                            class="bx bx-zoom-in"></i></span>
                                                                </div>
                                                                <div
                                                                    class="position-absolute bottom-0 start-0 w-100 p-1 bg-dark bg-opacity-50">
                                                                    <small class="text-white"
                                                                        style="font-size: 0.6rem;">{{ $pDetail->penerimaMbg->userRole->user->nama }}</small>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bx bx-error display-1 text-warning mb-2"></i>
                                <h5 class="fw-bold">Data Konfirmasi Tidak Ditemukan</h5>
                                <p class="text-muted mb-0">Oops! Terjadi kesalahan saat memuat rincian konfirmasi.</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer border-top py-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup
                            Detail</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalDetailDistribusi" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header py-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded bg-label-warning text-warning"><i
                                        class="bx bx-car"></i></span>
                            </div>
                            <h5 class="modal-title fw-bold mb-0">Detail Proses Distribusi</h5>
                        </div>
                    </div>
                    <div class="modal-body p-4">
                        @if (isset($orderDist) && $orderDist)
                            <div class="row g-4">
                                <div class="col-md-7 border-end">
                                    <div class="d-flex justify-content-between align-items-start mb-4">
                                        <div>
                                            <small class="text-uppercase text-muted fw-bold d-block mb-1"
                                                style="font-size: 0.7rem; letter-spacing: 1px;">Status Distribusi
                                                Keseluruhan</small>
                                            @php
                                                $mapStatusDistModal = [
                                                    'belum_dikirim' => [
                                                        'badge' => 'bg-label-secondary',
                                                        'icon' => 'bx-time',
                                                        'text' => 'Belum Dikirim',
                                                    ],
                                                    'sedang_dikirim' => [
                                                        'badge' => 'bg-label-warning',
                                                        'icon' => 'bx-loader-circle',
                                                        'text' => 'Sedang Dikirim',
                                                    ],
                                                    'sudah_dikirim' => [
                                                        'badge' => 'bg-label-success',
                                                        'icon' => 'bx-check-double',
                                                        'text' => 'Sudah Dikirim',
                                                    ],
                                                ];
                                                $dD =
                                                    $mapStatusDistModal[$orderDist->status] ??
                                                    $mapStatusDistModal['belum_dikirim'];
                                            @endphp
                                            <span class="badge {{ $dD['badge'] }} px-3 py-2 fs-6">
                                                <i class="bx {{ $dD['icon'] }} me-1"></i> {{ $dD['text'] }}
                                            </span>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-uppercase text-muted fw-bold d-block mb-1"
                                                style="font-size: 0.7rem; letter-spacing: 1px;">Update Terakhir</small>
                                            <span class="fw-semibold text-dark"><i
                                                    class="bx bx-calendar-check me-1"></i>{{ $orderDist->updated_at->format('d M Y, H:i') }}</span>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="fw-bold mb-3 d-flex align-items-center">
                                            <i class="bx bx-restaurant me-2 text-warning"></i>Daftar Menu Dikirim
                                        </h6>
                                        @php
                                            $porsiBesarMenus = $approval->transaksiDapur->detailTransaksiDapur->where(
                                                'tipe_porsi',
                                                'besar',
                                            );
                                            $porsiKecilMenus = $approval->transaksiDapur->detailTransaksiDapur->where(
                                                'tipe_porsi',
                                                'kecil',
                                            );
                                            $totalPorsiBesar = $approval->transaksiDapur->getTotalPorsiBesar();
                                            $totalPorsiKecil = $approval->transaksiDapur->getTotalPorsiKecil();
                                        @endphp

                                        @if ($porsiBesarMenus->count() > 0)
                                            <div class="mb-3">
                                                <div
                                                    class="p-2 bg-label-primary rounded-top border border-bottom-0 d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold small text-uppercase">Porsi Besar :
                                                        {{ $totalPorsiBesar }} Porsi</span>
                                                </div>
                                                <div class="table-responsive border rounded-bottom">
                                                    <table class="table table-sm table-hover mb-0">
                                                        <tbody>
                                                            @foreach ($porsiBesarMenus as $det)
                                                                <tr>
                                                                    <td class="py-2 px-3 fw-medium text-dark">
                                                                        {{ $det->menuMakanan->nama_menu }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($porsiKecilMenus->count() > 0)
                                            <div class="mb-3">
                                                <div
                                                    class="p-2 bg-label-warning rounded-top border border-bottom-0 d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold small text-uppercase">Porsi Kecil :
                                                        {{ $totalPorsiKecil }} Porsi</span>
                                                </div>
                                                <div class="table-responsive border rounded-bottom">
                                                    <table class="table table-sm table-hover mb-0">
                                                        <tbody>
                                                            @foreach ($porsiKecilMenus as $det)
                                                                <tr>
                                                                    <td class="py-2 px-3 fw-medium text-dark">
                                                                        {{ $det->menuMakanan->nama_menu }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="fw-bold mb-3 d-flex align-items-center">
                                            <i class="bx bx-map-pin me-2 text-warning"></i>Rincian Pengiriman per Penerima
                                        </h6>
                                        <div class="table-responsive border rounded">
                                            <table class="table table-sm table-hover mb-0">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th class="py-2 px-3">Penerima</th>
                                                        <th class="py-2 text-center">Status</th>
                                                        <th class="py-2 text-center">Porsi (B/K)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if ($orderDist->details && $orderDist->details->count() > 0)
                                                        @foreach ($orderDist->details as $dDetail)
                                                            <tr>
                                                                <td class="py-2 px-3">
                                                                    <div class="fw-medium text-dark small">
                                                                        {{ $dDetail->penerimaMbg->userRole->user->nama ?? 'Unknown' }}
                                                                    </div>
                                                                </td>
                                                                <td class="py-2 text-center">
                                                                    @php $sD = $mapStatusDistModal[$dDetail->status] ?? $mapStatusDistModal['belum_dikirim']; @endphp
                                                                    <span class="badge {{ $sD['badge'] }} small"
                                                                        style="font-size: 0.65rem;">{{ $sD['text'] }}</span>
                                                                </td>
                                                                <td class="py-2 text-center">

                                                                    <div class="text-muted" style="font-size: 0.6rem;">
                                                                        {{ $dDetail->porsi_besar ?? 0 }}B /
                                                                        {{ $dDetail->porsi_kecil ?? 0 }}K</div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <h6 class="fw-bold mb-2 d-flex align-items-center">
                                            <i class="bx bx-note me-2 text-warning"></i>Catatan Distribusi
                                        </h6>
                                        <div class="p-3 bg-lighter rounded border" style="min-height: 80px;">
                                            <p class="mb-0 text-dark small fst-italic">
                                                {{ $orderDist->catatan ?: 'Tidak ada catatan dari distributor.' }}
                                            </p>
                                        </div>
                                    </div>

                                    @if($orderDist->ulasan)
                                        <div class="mb-0 mt-4">
                                            <h6 class="fw-bold mb-2 d-flex align-items-center">
                                                <i class="bx bx-message-square-detail me-2 text-success"></i>Ulasan Distribusi
                                            </h6>
                                            <div class="p-3 bg-lighter rounded border" style="min-height: 80px;">
                                                <p class="mb-0 text-dark small fst-italic">
                                                    "{{ $orderDist->ulasan }}"
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-5">
                                    <h6 class="fw-bold mb-3 d-flex align-items-center">
                                        <i class="bx bx-image-alt me-2 text-warning"></i>Dokumentasi Distribusi
                                    </h6>
                                    <div class="row g-2">
                                        @php $hasAnyDistDok = false; @endphp
                                        @forelse($orderDist->dokumentasi as $dok)
                                            @php $hasAnyDistDok = true; @endphp
                                            <div class="col-6">
                                                <a href="{{ $dok->url }}" target="_blank"
                                                    class="d-block card-hover">
                                                    <div class="position-relative">
                                                        <img src="{{ $dok->url }}"
                                                            class="img-fluid rounded border shadow-sm"
                                                            style="width: 100%; height: 140px; object-fit: cover;"
                                                            alt="Dokumentasi">
                                                        <div class="position-absolute top-0 end-0 p-1">
                                                            <span class="badge bg-dark bg-opacity-50"><i
                                                                    class="bx bx-zoom-in"></i></span>
                                                        </div>
                                                        <div
                                                            class="position-absolute bottom-0 start-0 w-100 p-1 bg-dark bg-opacity-50">
                                                            <small class="text-white"
                                                                style="font-size: 0.6rem;">{{ $headDistributor ? ($headDistributor->nama_lengkap ?? $headDistributor->userRole->user->nama) : $orderDist->dapur->nama_dapur }}</small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @empty
                                            @foreach ($orderDist->details as $dDetail)
                                                @foreach ($dDetail->dokumentasi as $dok)
                                                    @php $hasAnyDistDok = true; @endphp
                                                    <div class="col-6">
                                                        <a href="{{ $dok->url }}" target="_blank"
                                                            class="d-block card-hover">
                                                            <div class="position-relative">
                                                                <img src="{{ $dok->url }}"
                                                                    class="img-fluid rounded border shadow-sm"
                                                                    style="width: 100%; height: 140px; object-fit: cover;"
                                                                    alt="Dokumentasi">
                                                                <div class="position-absolute top-0 end-0 p-1">
                                                                    <span class="badge bg-dark bg-opacity-50"><i
                                                                            class="bx bx-zoom-in"></i></span>
                                                                </div>
                                                                <div
                                                                    class="position-absolute bottom-0 start-0 w-100 p-1 bg-dark bg-opacity-50">
                                                                    <small class="text-white"
                                                                        style="font-size: 0.6rem;">{{ $headDistributor ? ($headDistributor->nama_lengkap ?? $headDistributor->userRole->user->nama) : $orderDist->dapur->nama_dapur }}</small>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            @endforeach
                                        @endforelse
                                        @if (!$hasAnyDistDok)
                                            <div class="col-12">
                                                <div class="text-center py-5 border rounded bg-lighter">
                                                    <i class="bx bx-no-entry display-4 text-muted mb-2"></i>
                                                    <p class="text-muted mb-0 small">Belum ada foto dokumentasi</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @if ($hasAnyDistDok)
                                        <div class="mt-3">
                                            <p class="text-muted small">
                                                <i class="bx bx-info-circle me-1"></i>Klik gambar untuk melihat dalam
                                                ukuran penuh.
                                            </p>
                                        </div>
                                    @endif

                                    @if($orderDist->ulasan_foto)
                                        <div class="mt-4">
                                            <h6 class="fw-bold mb-3 d-flex align-items-center">
                                                <i class="bx bx-image me-2 text-success"></i>Dokumentasi Ulasan
                                            </h6>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <a href="{{ asset('storage/' . $orderDist->ulasan_foto) }}" target="_blank" class="d-block card-hover">
                                                        <div class="position-relative">
                                                            <img src="{{ asset('storage/' . $orderDist->ulasan_foto) }}" class="img-fluid rounded border shadow-sm" style="width: 100%; height: 140px; object-fit: cover;" alt="Foto Ulasan">
                                                            <div class="position-absolute top-0 end-0 p-1">
                                                                <span class="badge bg-dark bg-opacity-50"><i class="bx bx-zoom-in"></i></span>
                                                            </div>
                                                            <div class="position-absolute bottom-0 start-0 w-100 p-1 bg-dark bg-opacity-50">
                                                                <small class="text-white" style="font-size: 0.6rem;">{{ $headDistributor ? ($headDistributor->nama_lengkap ?? $headDistributor->userRole->user->nama) : $orderDist->dapur->nama_dapur }}</small>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bx bx-error display-1 text-warning mb-3"></i>
                                <h5 class="fw-bold">Data Distribusi Tidak Ditemukan</h5>
                                <p class="text-muted">Oops! Terjadi kesalahan saat memuat detail distribusi. Silakan
                                    hubungi admin.</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer border-top py-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup
                            Detail</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detail Produksi -->
        <div class="modal fade" id="modalDetailProduksi" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header py-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded bg-label-primary text-primary"><i
                                        class="bx bx-cog"></i></span>
                            </div>
                            <h5 class="modal-title fw-bold mb-0">Detail Proses Produksi</h5>
                        </div>
                    </div>
                    <div class="modal-body p-4">
                        @if (isset($orderProd) && $orderProd)
                            <div class="row g-4">
                                <div class="col-md-7 border-end">
                                    <div class="d-flex justify-content-between align-items-start mb-4">
                                        <div>
                                            <small class="text-uppercase text-muted fw-bold d-block mb-1"
                                                style="font-size: 0.7rem; letter-spacing: 1px;">Status Produksi</small>
                                            @php
                                                $mapStatusProdModal = [
                                                    'stok_kurang' => [
                                                        'badge' => 'bg-label-danger',
                                                        'icon' => 'bx-error-circle',
                                                        'text' => 'Stok Kurang',
                                                    ],
                                                    'belum_dibuat' => [
                                                        'badge' => 'bg-label-secondary',
                                                        'icon' => 'bx-time',
                                                        'text' => 'Belum Dibuat',
                                                    ],
                                                    'sedang_dibuat' => [
                                                        'badge' => 'bg-label-warning',
                                                        'icon' => 'bx-loader-circle',
                                                        'text' => 'Sedang Dibuat',
                                                    ],
                                                    'selesai' => [
                                                        'badge' => 'bg-label-success',
                                                        'icon' => 'bx-check-circle',
                                                        'text' => 'Selesai',
                                                    ],
                                                ];
                                                $pD =
                                                    $mapStatusProdModal[$orderProd->status] ??
                                                    $mapStatusProdModal['belum_dibuat'];
                                            @endphp
                                            <span class="badge {{ $pD['badge'] }} px-3 py-2 fs-6">
                                                <i class="bx {{ $pD['icon'] }} me-1"></i> {{ $pD['text'] }}
                                            </span>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-uppercase text-muted fw-bold d-block mb-1"
                                                style="font-size: 0.7rem; letter-spacing: 1px;">Update Terakhir</small>
                                            <span class="fw-semibold text-dark"><i
                                                    class="bx bx-calendar-check me-1"></i>{{ $orderProd->updated_at->format('d M Y, H:i') }}</span>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="fw-bold mb-3 d-flex align-items-center">
                                            <i class="bx bx-restaurant me-2 text-primary"></i>Daftar Menu Diproduksi
                                        </h6>
                                        @php
                                            $porsiBesarMenus = $approval->transaksiDapur->detailTransaksiDapur->where(
                                                'tipe_porsi',
                                                'besar',
                                            );
                                            $porsiKecilMenus = $approval->transaksiDapur->detailTransaksiDapur->where(
                                                'tipe_porsi',
                                                'kecil',
                                            );
                                            $totalPorsiBesar = $approval->transaksiDapur->getTotalPorsiBesar();
                                            $totalPorsiKecil = $approval->transaksiDapur->getTotalPorsiKecil();
                                        @endphp

                                        @if ($porsiBesarMenus->count() > 0)
                                            <div class="mb-3">
                                                <div
                                                    class="p-2 bg-label-primary rounded-top border border-bottom-0 d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold small text-uppercase">Porsi Besar :
                                                        {{ $totalPorsiBesar }} Porsi</span>
                                                </div>
                                                <div class="table-responsive border rounded-bottom">
                                                    <table class="table table-sm table-hover mb-0">
                                                        <tbody>
                                                            @foreach ($porsiBesarMenus as $det)
                                                                <tr>
                                                                    <td class="py-2 px-3 fw-medium text-dark">
                                                                        {{ $det->menuMakanan->nama_menu }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($porsiKecilMenus->count() > 0)
                                            <div class="mb-3">
                                                <div
                                                    class="p-2 bg-label-warning rounded-top border border-bottom-0 d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold small text-uppercase">Porsi Kecil :
                                                        {{ $totalPorsiKecil }} Porsi</span>
                                                </div>
                                                <div class="table-responsive border rounded-bottom">
                                                    <table class="table table-sm table-hover mb-0">
                                                        <tbody>
                                                            @foreach ($porsiKecilMenus as $det)
                                                                <tr>
                                                                    <td class="py-2 px-3 fw-medium text-dark">
                                                                        {{ $det->menuMakanan->nama_menu }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif


                                    </div>

                                    <div class="mb-0">
                                        <h6 class="fw-bold mb-2 d-flex align-items-center">
                                            <i class="bx bx-note me-2 text-primary"></i>Catatan Produksi
                                        </h6>
                                        <div class="p-3 bg-lighter rounded border" style="min-height: 80px;">
                                            <p class="mb-0 text-dark small fst-italic">
                                                {{ $orderProd->catatan ?: 'Tidak ada catatan dari staf produksi.' }}
                                            </p>
                                        </div>
                                    </div>

                                    @if($orderProd->ulasan)
                                        <div class="mb-0 mt-4">
                                            <h6 class="fw-bold mb-2 d-flex align-items-center">
                                                <i class="bx bx-message-square-detail me-2 text-success"></i>Ulasan Produksi
                                            </h6>
                                            <div class="p-3 bg-lighter rounded border" style="min-height: 80px;">
                                                <p class="mb-0 text-dark small fst-italic">
                                                    "{{ $orderProd->ulasan }}"
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-5">
                                    <h6 class="fw-bold mb-3 d-flex align-items-center">
                                        <i class="bx bx-image-alt me-2 text-primary"></i>Dokumentasi Produksi
                                    </h6>
                                    <div class="row g-2">
                                        @forelse($orderProd->dokumentasi as $dok)
                                            <div class="col-6">
                                                <a href="{{ $dok->url }}" target="_blank"
                                                    class="d-block card-hover">
                                                    <div class="position-relative">
                                                        <img src="{{ $dok->url }}"
                                                            class="img-fluid rounded border shadow-sm"
                                                            style="width: 100%; height: 140px; object-fit: cover;"
                                                            alt="Dokumentasi">
                                                        <div class="position-absolute top-0 end-0 p-1">
                                                            <span class="badge bg-dark bg-opacity-50"><i
                                                                    class="bx bx-zoom-in"></i></span>
                                                        </div>
                                                        <div
                                                            class="position-absolute bottom-0 start-0 w-100 p-1 bg-dark bg-opacity-50">
                                                            <small class="text-white"
                                                                style="font-size: 0.6rem;">{{ $headProduksi ? ($headProduksi->nama_lengkap ?? $headProduksi->userRole->user->nama) : $orderProd->dapur->nama_dapur }}</small>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <div class="text-center py-5 border rounded bg-lighter">
                                                    <i class="bx bx-no-entry display-4 text-muted mb-2"></i>
                                                    <p class="text-muted mb-0 small">Belum ada foto dokumentasi</p>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                    @if ($orderProd->dokumentasi && $orderProd->dokumentasi->count() > 0)
                                        <div class="mt-3">
                                            <p class="text-muted small">
                                                <i class="bx bx-info-circle me-1"></i>Klik gambar untuk melihat dalam
                                                ukuran penuh.
                                            </p>
                                        </div>
                                    @endif

                                    @if($orderProd->ulasan_foto)
                                        <div class="mt-4">
                                            <h6 class="fw-bold mb-3 d-flex align-items-center">
                                                <i class="bx bx-image me-2 text-success"></i>Dokumentasi Ulasan
                                            </h6>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <a href="{{ asset('storage/' . $orderProd->ulasan_foto) }}" target="_blank" class="d-block card-hover">
                                                        <div class="position-relative">
                                                            <img src="{{ asset('storage/' . $orderProd->ulasan_foto) }}" class="img-fluid rounded border shadow-sm" style="width: 100%; height: 140px; object-fit: cover;" alt="Foto Ulasan">
                                                            <div class="position-absolute top-0 end-0 p-1">
                                                                <span class="badge bg-dark bg-opacity-50"><i class="bx bx-zoom-in"></i></span>
                                                            </div>
                                                            <div class="position-absolute bottom-0 start-0 w-100 p-1 bg-dark bg-opacity-50">
                                                                <small class="text-white" style="font-size: 0.6rem;">{{ $headProduksi ? ($headProduksi->nama_lengkap ?? $headProduksi->userRole->user->nama) : $orderProd->dapur->nama_dapur }}</small>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bx bx-error display-1 text-warning mb-3"></i>
                                <h5 class="fw-bold">Data Produksi Tidak Ditemukan</h5>
                                <p class="text-muted">Oops! Terjadi kesalahan saat memuat detail produksi. Silakan hubungi
                                    admin.</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer border-top py-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup
                            Detail</button>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .avatar-initial {
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .avatar-sm .avatar-initial {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }

            .table-danger-subtle {
                background-color: rgba(220, 53, 69, 0.1) !important;
            }

            .timeline {
                position: relative;
                padding-left: 1.5rem;
            }

            .timeline::before {
                content: '';
                position: absolute;
                left: 0.5rem;
                top: 0;
                bottom: 0;
                width: 2px;
                background: #e3e3e3;
            }

            .timeline-item {
                position: relative;
                margin-bottom: 1.5rem;
            }

            .timeline-indicator {
                position: absolute;
                left: -2rem;
                top: 0.25rem;
                width: 2rem;
                height: 2rem;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.75rem;
                z-index: 1;
            }

            .timeline-indicator-success {
                background: #28a745;
                color: white;
            }

            .timeline-indicator-warning {
                background: #ffc107;
                color: #212529;
            }

            .timeline-indicator-danger {
                background: #dc3545;
                color: white;
            }

            .timeline-content {
                padding-left: 1rem;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const alerts = document.querySelectorAll('.alert-dismissible');
                alerts.forEach((alert) => {
                    setTimeout(() => {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }, 5000);
                });
            });
        </script>
    </div>
@endsection
