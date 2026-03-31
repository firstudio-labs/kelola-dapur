@extends('template_distributor.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    @foreach(['success' => 'check-circle', 'warning' => 'error', 'error' => 'x-circle'] as $type => $icon)
        @if(session($type))
            <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible mb-4" role="alert">
                <i class="bx bx-{{ $icon }} me-2"></i>{{ session($type) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    @php
        $porsiBesarTotal    = $order->details->sum('porsi_besar');
        $porsiKecilTotal    = $order->details->sum('porsi_kecil');
        $qtyTotalPorsi      = $porsiBesarTotal + $porsiKecilTotal;

        $porsiBesarSelesai  = $order->details->where('status','sudah_dikirim')->sum('porsi_besar');
        $porsiKecilSelesai  = $order->details->where('status','sudah_dikirim')->sum('porsi_kecil');
        $qtySelesaiPorsi    = $porsiBesarSelesai + $porsiKecilSelesai;
        
        $porsiBesarSedang   = $order->details->where('status','sedang_dikirim')->sum('porsi_besar');
        $porsiKecilSedang   = $order->details->where('status','sedang_dikirim')->sum('porsi_kecil');
        $qtySedangPorsi     = $porsiBesarSedang + $porsiKecilSedang;

        $porsiBesarBelum    = $order->details->where('status','belum_dikirim')->sum('porsi_besar');
        $porsiKecilBelum    = $order->details->where('status','belum_dikirim')->sum('porsi_kecil');
        $qtyBelumPorsi      = $porsiBesarBelum + $porsiKecilBelum;

        $jumlahPenerima     = $order->details->count();
        $penerimaSelesai    = $order->details->where('status','sudah_dikirim')->count();
        $penerimaSedang     = $order->details->where('status','sedang_dikirim')->count();
        $penerimaBelum      = $order->details->where('status','belum_dikirim')->count();
        
        $pctDelivered       = $qtyTotalPorsi > 0 ? round(($qtySelesaiPorsi / $qtyTotalPorsi) * 100) : 0;

        $mapStatusDist = [
            'belum_dikirim'  => ['badge' => 'bg-label-secondary', 'btn' => 'btn-outline-secondary', 'icon' => 'bx-time',         'text' => 'Belum Dikirim'],
            'sedang_dikirim' => ['badge' => 'bg-label-warning',   'btn' => 'btn-outline-warning',   'icon' => 'bx-car',          'text' => 'Sedang Dikirim'],
            'sudah_dikirim'  => ['badge' => 'bg-label-success',   'btn' => 'btn-outline-success',   'icon' => 'bx-check-double', 'text' => 'Sudah Dikirim'],
        ];
        $distStatusData = $mapStatusDist[$order->status] ?? $mapStatusDist['belum_dikirim'];

        $mapStatusProd = [
            'stok_kurang'  => ['badge' => 'bg-label-danger',   'icon' => 'bx-error-circle',   'text' => 'Stok Kurang'],
            'belum_dibuat' => ['badge' => 'bg-label-secondary','icon' => 'bx-time',            'text' => 'Belum Dibuat'],
            'sedang_dibuat'=> ['badge' => 'bg-label-warning',  'icon' => 'bx-loader-circle',   'text' => 'Sedang Dibuat'],
            'selesai'      => ['badge' => 'bg-label-success',  'icon' => 'bx-check-circle',    'text' => 'Selesai'],
        ];
        $prodStatusData = $mapStatusProd[$order->orderProduksi->status] ?? ['badge'=>'bg-label-secondary','icon'=>'bx-help-circle','text'=>'Unknown'];
    @endphp

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm border-top border-4 border-primary">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="mb-2 mb-md-0 text-center text-md-start">
                            <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-2">
                                <a href="{{ route('distributor.order.index') }}" class="text-secondary fw-semibold text-decoration-none me-2">
                                    <i class="bx bx-arrow-back me-1"></i> Kembali
                                </a>
                                <span class="text-muted opacity-50">|</span>
                                <span class="ms-2 text-muted fw-semibold small">Order #{{ $order->id_distribusi }}</span>
                            </div>
                            <h5 class="mb-1 fw-bold text-dark">Detail Distribusi Paket</h5>
                            <p class="mb-0 text-muted small text-truncate">
                                <i class="bx bx-calendar text-primary me-1"></i>{{ $transaksi->tanggal_transaksi->format('d M Y') }} &nbsp;·&nbsp; {{ $transaksi->dapur->nama_dapur }}
                            </p>
                        </div>
                        <div class="d-flex justify-content-center gap-4 text-center">
                            <div>
                                <h4 class="mb-0 fw-bold text-primary">{{ $qtySelesaiPorsi }}<span class="fs-6 text-muted fw-normal">/{{ $qtyTotalPorsi }}</span></h4>
                                <small class="text-muted" style="font-size: 11px;">Porsi Terkirim</small>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold text-info">{{ $penerimaSelesai }}<span class="fs-6 text-muted fw-normal">/{{ $jumlahPenerima }}</span></h4>
                                <small class="text-muted" style="font-size: 11px;">Penerima</small>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold text-success">{{ $pctDelivered }}%</h4>
                                <small class="text-muted" style="font-size: 11px;">Progres</small>
                            </div>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $pctDelivered }}%" aria-valuenow="{{ $pctDelivered }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-2 mb-4">
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm text-center">
                <div class="card-body p-3">
                    <div class="text-primary mb-1"><i class="bx bx-group fs-3"></i></div>
                    <h4 class="mb-0 fw-bold">{{ $jumlahPenerima }}</h4>
                    <small class="text-muted" style="font-size: 10px;">Total Penerima</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm text-center border-bottom border-success border-3">
                <div class="card-body p-3">
                    <div class="text-success mb-1"><i class="bx bx-check-double fs-3"></i></div>
                    <h4 class="mb-0 fw-bold text-success">{{ $qtySelesaiPorsi }}</h4>
                    <small class="text-muted" style="font-size: 10px;">Porsi Selesai</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm text-center border-bottom border-warning border-3">
                <div class="card-body p-3">
                    <div class="text-warning mb-1"><i class="bx bx-car fs-3"></i></div>
                    <h4 class="mb-0 fw-bold text-warning">{{ $qtySedangPorsi }}</h4>
                    <small class="text-muted" style="font-size: 10px;">Porsi Dikirim</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm text-center border-bottom border-secondary border-3">
                <div class="card-body p-3">
                    <div class="text-secondary mb-1"><i class="bx bx-time fs-3"></i></div>
                    <h4 class="mb-0 fw-bold text-secondary">{{ $qtyBelumPorsi }}</h4>
                    <small class="text-muted" style="font-size: 10px;">Porsi Belum</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        
        <div class="col-md-5 mb-4 mb-md-0">
            <div class="card h-100">
                <div class="card-header border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bx bx-info-circle me-1"></i> Informasi Paket Menu</h6>
                </div>
                <div class="card-body p-3">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted" style="width:45%">Tanggal Transaksi</td>
                            <td class="fw-semibold">{{ $transaksi->tanggal_transaksi->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dapur</td>
                            <td class="fw-semibold">{{ $transaksi->dapur->nama_dapur }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Ahli Gizi</td>
                            <td class="fw-semibold">{{ $transaksi->createdBy->nama ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Porsi Besar</td>
                            <td><span class="badge bg-label-success">{{ $porsiBesarSelesai }} / {{ $porsiBesarTotal }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Porsi Kecil</td>
                            <td><span class="badge bg-label-warning">{{ $porsiKecilSelesai }} / {{ $porsiKecilTotal }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Porsi</td>
                            <td><span class="badge bg-label-primary">{{ $qtySelesaiPorsi }} / {{ $qtyTotalPorsi }} Terkirim</span></td>
                        </tr>
                        @if($transaksi->keterangan)
                        <tr>
                            <td class="text-muted">Keterangan</td>
                            <td>{{ $transaksi->keterangan }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card h-100">
                <div class="card-header border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bx bx-git-branch me-1"></i> Alur Proses Distribusi</h6>
                </div>
                <div class="card-body p-3">
                    
                    <div class="timeline-wrapper">
                        
                        <div class="d-flex mb-3">
                            <div class="me-3 d-flex flex-column align-items-center">
                                <div class="avatar avatar-sm">
                                    <span class="avatar-initial rounded-circle {{ $prodStatusData['badge'] }}">
                                        <i class="bx {{ $prodStatusData['icon'] }}"></i>
                                    </span>
                                </div>
                                <div style="width:2px;flex:1;background:#dee2e6;margin-top:4px"></div>
                            </div>
                            <div class="flex-grow-1 pb-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="fw-semibold">Status Produksi</span>
                                    <span class="badge {{ $prodStatusData['badge'] }}">{{ $prodStatusData['text'] }}</span>
                                </div>
                                @if($order->orderProduksi->dokumentasi->count() > 0)
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        @foreach($order->orderProduksi->dokumentasi->take(3) as $dok)
                                            <a href="{{ $dok->url }}" target="_blank">
                                                <img src="{{ $dok->url }}" class="rounded border" style="width:48px;height:48px;object-fit:cover;">
                                            </a>
                                        @endforeach
                                        @if($order->orderProduksi->dokumentasi->count() > 3)
                                            <div class="rounded border d-flex align-items-center justify-content-center bg-label-secondary" style="width:48px;height:48px;">
                                                <small>+{{ $order->orderProduksi->dokumentasi->count() - 3 }}</small>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <small class="text-muted">Belum ada dokumentasi produksi</small>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex mb-3">
                            <div class="me-3 d-flex flex-column align-items-center">
                                <div class="avatar avatar-sm">
                                    <span class="avatar-initial rounded-circle {{ $distStatusData['badge'] }}">
                                        <i class="bx {{ $distStatusData['icon'] }}"></i>
                                    </span>
                                </div>
                                <div style="width:2px;flex:1;background:#dee2e6;margin-top:4px"></div>
                            </div>
                            <div class="flex-grow-1 pb-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="fw-semibold">Status Distribusi (Keseluruhan)</span>
                                    <span class="badge {{ $distStatusData['badge'] }}">{{ $distStatusData['text'] }}</span>
                                </div>
                                @if($order->catatan)
                                    <small class="text-muted"><i class="bx bx-note me-1"></i>{{ $order->catatan }}</small>
                                @endif
                                @if($order->dokumentasi->count() > 0)
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        @foreach($order->dokumentasi->take(3) as $dok)
                                            <a href="{{ $dok->url }}" target="_blank">
                                                <img src="{{ $dok->url }}" class="rounded border" style="width:48px;height:48px;object-fit:cover;">
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex">
                            <div class="me-3">
                                <div class="avatar avatar-sm">
                                    <span class="avatar-initial rounded-circle {{ $pctDelivered == 100 ? 'bg-label-success' : 'bg-label-primary' }}">
                                        <i class="bx bx-package"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fw-semibold">Progress Pengiriman ke Penerima</span>
                                    <small class="fw-semibold text-primary">{{ $penerimaSelesai }}/{{ $jumlahPenerima }} penerima</small>
                                </div>
                                <div class="progress mb-1" style="height:8px">
                                    <div class="progress-bar {{ $pctDelivered == 100 ? 'bg-success' : 'bg-primary' }}"
                                         style="width:{{ $pctDelivered }}%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">{{ $pctDelivered }}% pengiriman selesai</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">
                    <h6 class="text-muted small mb-2">Menu yang Dikirim:</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($transaksi->detailTransaksiDapur as $det)
                            <span class="badge {{ $det->tipe_porsi === 'besar' ? 'bg-label-success' : 'bg-label-warning' }}">
                                {{ $det->menuMakanan->nama_menu }} ({{ $det->jumlah_porsi }} porsi)
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom py-3">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="bx bx-list-check me-1"></i> Checklist Pengiriman
                        </h6>
                        <div class="d-flex flex-wrap align-items-center gap-1" id="recipientFilters">
                            <button type="button" class="btn btn-xs btn-primary filter-btn active" data-filter="all">Semua</button>
                            <button type="button" class="btn btn-xs btn-outline-success filter-btn" data-filter="sudah_dikirim">
                                <i class="bx bx-check me-1"></i>{{ $penerimaSelesai }} Selesai
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-warning filter-btn" data-filter="sedang_dikirim">
                                <i class="bx bx-car me-1"></i>{{ $penerimaSedang }} Dikirim
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-secondary filter-btn" data-filter="belum_dikirim">
                                <i class="bx bx-time me-1"></i>{{ $penerimaBelum }} Belum
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($order->details->isNotEmpty())
                        <div class="row g-3">
                            @foreach($order->details as $idx => $detail)
                                @php
                                    $sd = $mapStatusDist[$detail->status] ?? $mapStatusDist['belum_dikirim'];
                                    $penerima = $detail->penerimaMbg;
                                @endphp
                                <div class="col-12 col-md-6 col-lg-4 recipient-card-wrapper" data-status="{{ $detail->status }}">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body p-3">
                                            
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark">{{ $idx + 1 }}. {{ $penerima->userRole->user->nama ?? '-' }}</h6>
                                                    <small class="text-muted d-block fw-semibold" style="font-size: 11px;">PJ: {{ $penerima->penanggung_jawab ?? '-' }}</small>
                                                    <small class="text-muted d-block" style="font-size: 10px;">{{ $penerima->id_type_label ?? 'ID' }}: {{ $penerima->id_number ?? '-' }}</small>
                                                </div>
                                                <span class="badge {{ $sd['badge'] }}"><i class="bx {{ $sd['icon'] }} me-1"></i>{{ $sd['text'] }}</span>
                                            </div>

                                            <div class="d-flex align-items-center mb-3">
                                                <i class="bx bx-map text-muted me-2"></i>
                                                <small class="text-muted text-truncate me-2" style="max-width: 65%;">
                                                    {{ implode(', ', array_filter([$penerima->village_name, $penerima->district_name, $penerima->regency_name])) ?: '-' }}
                                                </small>
                                                @if($penerima->link_gmaps)
                                                    <a href="{{ $penerima->link_gmaps }}" target="_blank" class="btn btn-xs btn-outline-primary ms-auto" style="padding: 2px 6px;">
                                                        <i class="bx bx-map-alt me-1"></i>Maps
                                                    </a>
                                                @endif
                                            </div>

                                            <div class="mt-3 pt-3 border-top">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <small class="fw-semibold text-muted">Detail Porsi:</small>
                                                    <span class="fw-bold text-dark">{{ $detail->jumlah_diterima }} Porsi</span>
                                                </div>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="p-1 bg-light rounded text-center border">
                                                            <div style="font-size: 10px;" class="text-muted">Besar</div>
                                                            <div class="fw-bold small text-primary">{{ $detail->porsi_besar }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="p-1 bg-light rounded text-center border">
                                                            <div style="font-size: 10px;" class="text-muted">Kecil</div>
                                                            <div class="fw-bold small text-success">{{ $detail->porsi_kecil }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($detail->catatan)
                                                <div class="mb-2 p-2 rounded bg-label-secondary small text-dark">
                                                    <i class="bx bx-note me-1 opacity-50"></i>{{ $detail->catatan }}
                                                </div>
                                            @endif

                                            @if($detail->dokumentasi->isNotEmpty())
                                                <div class="d-flex flex-wrap gap-1 mb-2 mt-1">
                                                    @foreach($detail->dokumentasi->take(4) as $dok)
                                                        <a href="{{ $dok->url }}" target="_blank" class="position-relative">
                                                            <img src="{{ $dok->url }}" class="rounded border" style="width:48px;height:48px;object-fit:cover;">
                                                        </a>
                                                    @endforeach
                                                    @if($detail->dokumentasi->count() > 4)
                                                        <div class="rounded border d-flex align-items-center justify-content-center bg-label-secondary" style="width:48px;height:48px;">
                                                            <small class="fw-bold">+{{ $detail->dokumentasi->count() - 4 }}</small>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            @if($detail->status !== 'sudah_dikirim')
                                                <button type="button"
                                                        class="btn btn-sm w-100 btn-primary mt-2 btn-update-detail"
                                                        data-id-detail="{{ $detail->id_detail }}"
                                                        data-id-order="{{ $order->id_distribusi }}"
                                                        data-status="{{ $detail->status }}"
                                                        data-porsi-besar="{{ $detail->porsi_besar }}"
                                                        data-porsi-kecil="{{ $detail->porsi_kecil }}"
                                                        data-max-porsi="{{ $penerima->jumlah_porsi }}"
                                                        data-catatan="{{ $detail->catatan }}"
                                                        data-nama="{{ $penerima->penanggung_jawab ?? 'Penerima' }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#updateDetailModal">
                                                    <i class="bx bx-edit-alt me-1"></i>Update Pengiriman
                                                </button>
                                            @else
                                                <div class="text-center mt-2 pt-2 border-top border-dashed border-light">
                                                    <span class="text-success small fw-semibold"><i class="bx bx-check-circle me-1"></i>Pengiriman Selesai</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="bx bx-info-circle me-2"></i>
                            Belum ada data penerima MBG untuk pengiriman ini.
                            Pastikan Penerima MBG sudah disetujui oleh Kepala Dapur.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5 mb-md-2">
        <div class="col-12">
            <a href="{{ route('distributor.order.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar Order
            </a>
        </div>
    </div>

</div>

<div class="modal fade" id="updateDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title"><i class="bx bx-user-check me-2 text-primary"></i>Update Pengiriman</h5>
                    <small class="text-muted" id="detailNamaPenerima"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateDetailForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Pengiriman <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" id="detailStatus" required>
                            <option value="belum_dikirim">⏱ Belum Dikirim</option>
                            <option value="sedang_dikirim">🚗 Sedang Dikirim</option>
                            <option value="sudah_dikirim">✅ Sudah Dikirim</option>
                        </select>
                    </div>
                    <div class="row g-3 mb-1">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-primary">Porsi Besar</label>
                            <div class="input-group">
                                <input type="number" name="porsi_besar" class="form-control portion-input" id="detailPorsiBesar" min="0">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-success">Porsi Kecil</label>
                            <div class="input-group">
                                <input type="number" name="porsi_kecil" class="form-control portion-input" id="detailPorsiKecil" min="0">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 bg-lighter rounded border" id="porsiDisplayWrapper">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold">Total Diinput:</span>
                                    <div>
                                        <span class="fs-5 fw-bold text-dark" id="detailTotalPorsi">0</span>
                                        <span class="text-muted small">/ <span id="detailMaxPorsi">0</span></span>
                                    </div>
                                </div>
                            </div>
                            <div id="portionErrorMessage" class="text-danger small mt-1 d-none">
                                <i class="bx bx-error-circle me-1"></i> Jumlah tidak boleh melebihi batas maksimal!
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 d-none" id="detailDokumentasiWrapper">
                        <label class="form-label fw-semibold">Foto Bukti Pengiriman <span class="text-danger">*</span></label>
                        <input type="file" name="dokumentasi[]" class="form-control" multiple accept="image/*" id="detailInputDokumentasi">
                        <small class="text-muted">Wajib untuk status Sudah Dikirim. Dapat memilih lebih dari 1 foto.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan (opsional)</label>
                        <textarea name="catatan" class="form-control" id="detailCatatan" rows="3" placeholder="Tambahkan catatan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i>Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const updateDetailModal = document.getElementById('updateDetailModal');
        const detailStatus      = document.getElementById('detailStatus');
        const detailDokWrapper  = document.getElementById('detailDokumentasiWrapper');
        const detailInputDok    = document.getElementById('detailInputDokumentasi');
        const baseDetailUrl     = '{{ url("/distributor/order-distribusi") }}';

        function toggleDetailDok(status) {
            if (status === 'sudah_dikirim') {
                detailDokWrapper.classList.remove('d-none');
                detailInputDok.setAttribute('required', 'required');
            } else {
                detailDokWrapper.classList.add('d-none');
                detailInputDok.removeAttribute('required');
            }
        }

        if (updateDetailModal) {
            updateDetailModal.addEventListener('show.bs.modal', function(event) {
                const btn       = event.relatedTarget;
                const idDetail  = btn.getAttribute('data-id-detail');
                const idOrder   = btn.getAttribute('data-id-order');
                const status    = btn.getAttribute('data-status');
                const catatan   = btn.getAttribute('data-catatan');
                const pBesar    = parseInt(btn.getAttribute('data-porsi-besar')) || 0;
                const pKecil    = parseInt(btn.getAttribute('data-porsi-kecil')) || 0;
                const maxP      = parseInt(btn.getAttribute('data-max-porsi')) || 0;
                const nama      = btn.getAttribute('data-nama');

                detailStatus.value = status;
                document.getElementById('detailPorsiBesar').value = pBesar;
                document.getElementById('detailPorsiBesar').setAttribute('max', maxP);
                document.getElementById('detailPorsiKecil').value = pKecil;
                document.getElementById('detailPorsiKecil').setAttribute('max', maxP);
                document.getElementById('detailMaxPorsi').textContent = maxP;
                document.getElementById('detailTotalPorsi').textContent = pBesar + pKecil;
                document.getElementById('detailCatatan').value = (catatan && catatan !== 'null') ? catatan : '';
                document.getElementById('detailNamaPenerima').textContent = nama ? 'Untuk: ' + nama : '';
                toggleDetailDok(status);

                // Reset error state
                document.getElementById('portionErrorMessage').classList.add('d-none');
                document.getElementById('porsiDisplayWrapper').classList.remove('border-danger', 'bg-label-danger');
                document.querySelector('#updateDetailForm button[type="submit"]').disabled = false;

                document.getElementById('updateDetailForm').action =
                    baseDetailUrl + '/' + idOrder + '/detail/' + idDetail + '/update-status';
            });

            detailStatus.addEventListener('change', function() {
                toggleDetailDok(this.value);
            });

            // Validation & Auto-calculate total in modal
            const inpBesar = document.getElementById('detailPorsiBesar');
            const inpKecil = document.getElementById('detailPorsiKecil');
            const totalText = document.getElementById('detailTotalPorsi');
            const maxText = document.getElementById('detailMaxPorsi');
            const submitBtn = document.querySelector('#updateDetailForm button[type="submit"]');
            const errBox = document.getElementById('portionErrorMessage');
            const wrapBox = document.getElementById('porsiDisplayWrapper');

            function validatePortions() {
                const valB = parseInt(inpBesar.value) || 0;
                const valK = parseInt(inpKecil.value) || 0;
                const max = parseInt(maxText.textContent) || 0;
                const total = valB + valK;
                
                totalText.textContent = total;

                if (valB > max || valK > max) {
                    errBox.classList.remove('d-none');
                    wrapBox.classList.add('border-danger', 'bg-label-danger');
                    submitBtn.disabled = true;
                } else {
                    errBox.classList.add('d-none');
                    wrapBox.classList.remove('border-danger', 'bg-label-danger');
                    submitBtn.disabled = false;
                }
            }

            [inpBesar, inpKecil].forEach(inp => {
                inp.addEventListener('input', validatePortions);
            });
        }

        // --- Logic Filter Penerima MBG ---
        const filterBtns = document.querySelectorAll('.filter-btn');
        const recipients = document.querySelectorAll('.recipient-card-wrapper');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                
                // Update active state
                filterBtns.forEach(b => {
                    b.classList.remove('active');
                    if (b.classList.contains('btn-primary')) {
                        b.classList.replace('btn-primary', 'btn-outline-primary');
                    }
                    // Restore original outline classes if needed
                    const f = b.getAttribute('data-filter');
                    if (f === 'sudah_dikirim') b.classList.add('btn-outline-success');
                    if (f === 'sedang_dikirim') b.classList.add('btn-outline-warning');
                    if (f === 'belum_dikirim') b.classList.add('btn-outline-secondary');
                });

                this.classList.add('active');
                if (filter === 'all') {
                    this.classList.replace('btn-outline-primary', 'btn-primary');
                } else {
                    // For status buttons, we handle active state by keeping their specific colors
                    // but they are already btn-outline-X. 
                    // Let's make it more obvious by swapping outline to solid if active
                    if (filter === 'sudah_dikirim') this.classList.replace('btn-outline-success', 'btn-success');
                    if (filter === 'sedang_dikirim') this.classList.replace('btn-outline-warning', 'btn-warning');
                    if (filter === 'belum_dikirim') this.classList.replace('btn-outline-secondary', 'btn-secondary');
                }

                // Filtering logic
                recipients.forEach(card => {
                    if (filter === 'all' || card.getAttribute('data-status') === filter) {
                        card.classList.remove('d-none');
                    } else {
                        card.classList.add('d-none');
                    }
                });
            });
        });

        // Helper function to restore original btn classes for non-active buttons
        function resetBtnClasses() {
            filterBtns.forEach(b => {
                const f = b.getAttribute('data-filter');
                if (!b.classList.contains('active')) {
                    if (f === 'all') {
                        b.className = 'btn btn-xs btn-outline-primary filter-btn';
                    } else if (f === 'sudah_dikirim') {
                        b.className = 'btn btn-xs btn-outline-success filter-btn';
                    } else if (f === 'sedang_dikirim') {
                        b.className = 'btn btn-xs btn-outline-warning filter-btn';
                    } else if (f === 'belum_dikirim') {
                        b.className = 'btn btn-xs btn-outline-secondary filter-btn';
                    }
                }
            });
        }

        // Re-inject reset log into event listeners for cleaner UX
        filterBtns.forEach(btn => {
            btn.addEventListener('click', resetBtnClasses);
        });
    });
</script>
@endpush
