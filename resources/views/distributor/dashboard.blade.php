@extends('template_distributor.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card bg-primary text-white" style="background: linear-gradient(135deg, #696cff 0%, #8592a3 100%);">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="avatar avatar-xl me-3">
                        <img src="{{ (isset($distributor) && $distributor->foto_diri) ? asset('storage/' . $distributor->foto_diri) : asset('admin/assets/img/illustrations/man-with-laptop-light.png') }}" 
                             alt="Profile" class="rounded-circle border border-2 border-white" style="object-fit: cover; width: 64px; height: 64px;">
                    </div>
                    <div>
                        <h5 class="mb-1 text-white fw-bold">Halo, {{ explode(' ', trim($user->nama))[0] }}! 👋</h5>
                        <p class="mb-0 small" style="opacity: 0.85;">
                            Distributor - {{ $dapur->nama_dapur }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h6 class="text-muted fw-semibold mb-3">Statistik Hari Ini</h6>
    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body p-2 p-md-3">
                    <div class="avatar avatar-sm mx-auto mb-2">
                            <span class="avatar-initial rounded-circle bg-label-primary"><i class="bx bx-package"></i></span>
                    </div>
                    <h4 class="mb-0">{{ $totalOrdersToday }}</h4>
                    <small class="text-muted" style="font-size: 10px;">Total Order</small>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body p-2 p-md-3">
                    <div class="avatar avatar-sm mx-auto mb-2">
                        <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-check-double"></i></span>
                    </div>
                    <h4 class="mb-0 text-success">{{ $ordersCompletedToday }}</h4>
                    <small class="text-muted" style="font-size: 10px;">Selesai</small>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body p-2 p-md-3">
                    <div class="avatar avatar-sm mx-auto mb-2">
                        <span class="avatar-initial rounded-circle bg-label-warning"><i class="bx bx-time"></i></span>
                    </div>
                    <h4 class="mb-0 text-warning">{{ $ordersPendingToday }}</h4>
                    <small class="text-muted" style="font-size: 10px;">Belum/Proses</small>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-muted fw-semibold mb-0">Pengiriman Hari Ini</h6>
        <a href="{{ route('distributor.order.index') }}" class="text-primary small fw-semibold">Lihat Semua</a>
    </div>

    @if($todayDeliveries->isEmpty())
        <div class="card text-center border-0 shadow-sm border-top border-4 border-secondary">
            <div class="card-body py-5">
                <i class="bx bx-coffee bx-lg text-muted mb-2 opacity-50"></i>
                <h6>Belum Ada Order Hari Ini</h6>
                <p class="text-muted small mb-0">Order produksi yang masuk hari ini akan tampil di sini saat masuk tahap distribusi.</p>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($todayDeliveries as $order)
                @php
                    $transaksi = $order->orderProduksi->transaksiDapur;
                    $badgeClass = match($order->status) {
                        'sudah_dikirim'  => 'bg-label-success',
                        'sedang_dikirim' => 'bg-label-warning',
                        default          => 'bg-label-secondary',
                    };
                    $pctDelivered = 0;
                    $totalPenerima = $order->details->count();
                    if($totalPenerima > 0) {
                        $sudahTerkirim = $order->details->where('status','sudah_dikirim')->count();
                        $pctDelivered = round(($sudahTerkirim / $totalPenerima) * 100);
                    }
                @endphp
                <div class="col-12 col-md-6 mb-2">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <div class="fw-semibold text-primary">Order #{{ $order->id_distribusi }}</div>
                                    <small class="text-muted">{{ $transaksi->nama_paket ?? 'Paket Menu' }}</small>
                                </div>
                                <span class="badge {{ $badgeClass }}">{{ $order->status_label }}</span>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Progress Pengiriman ({{ $pctDelivered }}%)</small>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar {{ $pctDelivered == 100 ? 'bg-success' : 'bg-primary' }}" 
                                         role="progressbar" style="width: {{ $pctDelivered }}%" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <a href="{{ route('distributor.order.show', $order->id_distribusi) }}" class="btn btn-sm btn-outline-primary w-100">
                                <i class="bx bx-right-arrow-alt me-1"></i> Proses Pengiriman
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($totalPendingAllTime > $ordersPendingToday)
    <div class="alert alert-warning mt-4 d-flex align-items-center" role="alert">
        <i class="bx bx-error-circle me-2 fs-4"></i>
        <div class="small">
            Terdapat <strong>{{ $totalPendingAllTime - $ordersPendingToday }} order tertunda</strong> dari hari sebelumnya yang belum selesai dikirim. 
            <a href="{{ route('distributor.order.index') }}" class="alert-link text-decoration-underline">Cek Daftar Order</a>.
        </div>
    </div>
    @endif

</div>
@endsection
