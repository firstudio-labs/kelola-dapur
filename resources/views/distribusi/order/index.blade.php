@extends('template_distributor.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav class="d-flex align-items-center mb-2">
                        <a href="{{ route('distributor.dashboard') }}" class="text-muted me-2">
                            <i class="bx bx-home-alt me-1"></i>
                            Dashboard
                        </a>
                        <i class="bx bx-chevron-right me-2"></i>
                        <span class="text-dark">
                            Daftar Input Paket Menu
                        </span>
                    </nav>
                    <h4 class="mb-1">Daftar Input Paket Menu</h4>
                    <p class="mb-0 text-muted">
                        Kelola semua pesanan pengiriman untuk dapur mitra
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
            <i class="bx bx-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
            <i class="bx bx-x-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('distributor.order.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="status-filter" class="form-label">Filter Status Pengiriman</label>
                    <select name="status" id="status-filter" class="choices-select form-select">
                        <option value="all" {{ request("status") === "all" ? "selected" : "" }}>Semua Status</option>
                        <option value="belum_dikirim" {{ request("status") === "belum_dikirim" ? "selected" : "" }}>Belum Dikirim</option>
                        <option value="sedang_dikirim" {{ request("status") === "sedang_dikirim" ? "selected" : "" }}>Sedang Dikirim</option>
                        <option value="sudah_dikirim" {{ request("status") === "sudah_dikirim" ? "selected" : "" }}>Sudah Dikirim</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date-from" class="form-label">Dari Tanggal</label>
                    <input type="date" name="date_from" id="date-from" value="{{ request('date_from') }}" class="form-control" />
                </div>
                <div class="col-md-3">
                    <label for="date-to" class="form-label">Sampai Tanggal</label>
                    <input type="date" name="date_to" id="date-to" value="{{ request('date_to') }}" class="form-control" />
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    @if (request()->hasAny(["search", "status", "date_from", "date_to"]))
                        <a href="{{ route('distributor.order.index') }}" class="btn btn-outline-secondary">
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit" class="btn btn-primary">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($orders->total() > 0)
        <div class="card mb-4">
            <div class="card-body py-2 px-4">
                <div class="row justify-content-center g-3">
                    <div class="col-md-3 text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="badge bg-label-primary me-2">
                                <i class="bx bx-package"></i>
                            </span>
                            <div>
                                <small class="text-muted">Total Order Pengiriman</small>
                                <h6 class="mb-0">{{ $orders->total() }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="badge bg-label-secondary me-2">
                                <i class="bx bx-time"></i>
                            </span>
                            <div>
                                <small class="text-muted">Belum Dikirim</small>
                                <h6 class="mb-0">{{ \App\Models\OrderDistribusi::where('status', 'belum_dikirim')->count() }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="badge bg-label-warning me-2">
                                <i class="bx bx-car"></i>
                            </span>
                            <div>
                                <small class="text-muted">Sedang Dikirim</small>
                                <h6 class="mb-0">{{ \App\Models\OrderDistribusi::where('status', 'sedang_dikirim')->count() }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="badge bg-label-success me-2">
                                <i class="bx bx-check-double"></i>
                            </span>
                            <div>
                                <small class="text-muted">Sudah Dikirim</small>
                                <h6 class="mb-0">{{ \App\Models\OrderDistribusi::where('status', 'sudah_dikirim')->count() }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if ($orders->isNotEmpty())
                
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Transaksi</th>
                                <th>Total Porsi</th>
                                <th>Status Transaksi</th>
                                <th>Status Produksi</th>
                                <th>Status Distribusi</th>
                                <th>Waktu Dikirim</th>
                                <th>Catatan</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="transaksi-table-body">
                            @foreach ($orders as $order)
                                @php
                                    $produksiOrder = $order->orderProduksi;
                                    $transaksi = $produksiOrder->transaksiDapur;
                                    $menus = $transaksi->detailTransaksiDapur->map(fn($d) => $d->menuMakanan->nama_menu)->unique()->join(', ');
                                    $totalPorsiBesar = $transaksi->detailTransaksiDapur->where('tipe_porsi', 'besar')->first()->jumlah_porsi ?? 0;
                                    $totalPorsiKecil = $transaksi->detailTransaksiDapur->where('tipe_porsi', 'kecil')->first()->jumlah_porsi ?? 0;
                                    $totalPorsi = $totalPorsiBesar;
                                    $badgeClass = match($order->status) {
                                        'sudah_dikirim'  => 'bg-label-success',
                                        'sedang_dikirim' => 'bg-label-warning',
                                        default          => 'bg-label-secondary',
                                    };
                                @endphp

                                <tr data-search="{{ strtolower($transaksi->nama_paket ?? '') }}" data-status="{{ $order->status }}">
                                    <td>{{ $loop->iteration + ($orders->currentPage() - 1) * $orders->perPage() }}</td>
                                    <td>
                                        <small class="text-muted">{{ $transaksi->tanggal_transaksi->format("d M Y") }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bx bx-bowl-hot me-1"></i>
                                            <span class="fw-semibold">{{ $totalPorsi }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                "draft" => "bg-label-warning",
                                                "pending_approval" => "bg-label-info",
                                                "completed" => "bg-label-success",
                                                "rejected" => "bg-label-danger",
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusClasses[$transaksi->status] ?? "bg-label-secondary" }}">
                                            {{ ucfirst(str_replace("_", " ", $transaksi->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $mapStatusProduksi = [
                                                'stok_kurang' => ['badge' => 'bg-label-danger', 'text' => 'Stok Kurang'],
                                                'belum_dibuat' => ['badge' => 'bg-label-secondary', 'text' => 'Belum Dibuat'],
                                                'sedang_dibuat' => ['badge' => 'bg-label-warning', 'text' => 'Sedang Dibuat'],
                                                'selesai' => ['badge' => 'bg-label-success', 'text' => 'Selesai']
                                            ];
                                            $prodData = $mapStatusProduksi[$produksiOrder->status] ?? ['badge' => 'bg-label-secondary', 'text' => 'Unknown'];
                                        @endphp
                                        <span class="badge {{ $prodData['badge'] }}">
                                            {{ $prodData['text'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $badgeClass }}">{{ $order->status_label }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $order->tanggal_dikirim ? $order->tanggal_dikirim->format('d M Y H:i') : '—' }}
                                        </small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $order->catatan ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $order->created_at->format("d M Y") }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('distributor.order.show', $order->id_distribusi) }}" class="btn btn-sm btn-outline-info">
                                                <i class="bx bx-show px-1"></i> Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-block d-md-none mt-3" id="mobile-cards-container">
                    @foreach ($orders as $order)
                        @php
                            $produksiOrder = $order->orderProduksi;
                            $transaksi = $produksiOrder->transaksiDapur;
                            $totalPorsiBesar = $transaksi->detailTransaksiDapur->where('tipe_porsi', 'besar')->first()->jumlah_porsi ?? 0;
                            $totalPorsiKecil = $transaksi->detailTransaksiDapur->where('tipe_porsi', 'kecil')->first()->jumlah_porsi ?? 0;
                            $totalPorsi = $totalPorsiBesar;
                            $badgeClass = match($order->status) {
                                'sudah_dikirim'  => 'bg-label-success',
                                'sedang_dikirim' => 'bg-label-warning',
                                default          => 'bg-label-secondary',
                            };
                            $mapStatusProduksi = [
                                'stok_kurang' => ['badge' => 'bg-label-danger', 'text' => 'Stok Kurang'],
                                'belum_dibuat' => ['badge' => 'bg-label-secondary', 'text' => 'Belum Dibuat'],
                                'sedang_dibuat' => ['badge' => 'bg-label-warning', 'text' => 'Sedang Dibuat'],
                                'selesai' => ['badge' => 'bg-label-success', 'text' => 'Selesai']
                            ];
                            $prodData = $mapStatusProduksi[$produksiOrder->status] ?? ['badge' => 'bg-label-secondary', 'text' => 'Unknown'];
                        @endphp
                        
                        <div class="card mb-3 border shadow-none mobile-card-item" data-search="{{ strtolower($transaksi->nama_paket ?? '') }}">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="fw-semibold text-primary mb-1">Order #{{ $order->id_distribusi }}</div>
                                        <small class="text-muted"><i class="bx bx-calendar me-1"></i>{{ $transaksi->tanggal_transaksi->format("d M Y") }}</small>
                                    </div>
                                    <span class="badge {{ $badgeClass }}">{{ $order->status_label }}</span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-bowl-hot"></i></span>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Total Porsi</small>
                                            <span class="fw-semibold">{{ $totalPorsi }}</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block mb-1">Status Produksi</small>
                                        <span class="badge {{ $prodData['badge'] }}" style="font-size: 0.7rem;">{{ $prodData['text'] }}</span>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small text-truncate pe-2" style="max-width: 70%;">
                                        <i class="bx bx-note me-1"></i>{{ $order->catatan ?? 'Tidak ada catatan' }}
                                    </div>
                                    <a href="{{ route('distributor.order.show', $order->id_distribusi) }}" class="btn btn-sm btn-primary">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($orders->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $orders->appends(request()->query())->links("vendor.pagination.bootstrap-5") }}
                    </div>
                @endif
            @else
                
                <div class="text-center py-6">
                    @if (request()->hasAny(["search", "status", "date_from", "date_to"]))
                        <i class="bx bx-search bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">Tidak ada hasil</h5>
                        <p class="text-muted mb-3">Tidak ada order pengiriman yang sesuai dengan filter.</p>
                        <a href="{{ route('distributor.order.index') }}" class="btn btn-outline-primary">Reset Filter</a>
                    @else
                        <i class="bx bx-package bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">Belum ada order pengiriman</h5>
                        <p class="text-muted mb-3">Order pengiriman akan muncul di sini secara otomatis.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css"/>

<style>
    .choices__inner {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 0.5rem;
        font-size: 0.875rem;
    }
    .choices__list--dropdown {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .choices__list--dropdown .choices__item--selectable.is-highlighted {
        background-color: #f8f9fa;
    }
    .choices[data-type*='select-one'] .choices__inner {
        padding-bottom: 0;
    }
    .choices.is-disabled .choices__inner {
        background-color: #f8f9fa;
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-top: none;
        white-space: nowrap;
    }
    .badge {
        font-size: 0.75rem;
    }
    .badge i {
        font-size: 0.7rem;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusFilter = document.getElementById('status-filter');
        const searchInput = document.getElementById('search-input');
    const tableBody = document.getElementById('transaksi-table-body');
    const rows = tableBody ? tableBody.getElementsByTagName('tr') : [];
    const mobileCardsContainer = document.getElementById('mobile-cards-container');
    const mobileCards = mobileCardsContainer ? mobileCardsContainer.getElementsByClassName('mobile-card-item') : [];

    if (statusFilter) {
            new Choices(statusFilter, {
                searchEnabled: false,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'Semua Status',
            });
        }

    function filterTable() {
        const searchText = searchInput ? searchInput.value.toLowerCase() : '';
        
        // Filter desktop table rows
        Array.from(rows).forEach((row) => {
            const searchData = row.getAttribute('data-search') || '';
            const matchesSearch = searchText ? searchData.includes(searchText) : true;
            row.style.display = matchesSearch ? '' : 'none';
        });

        // Filter mobile cards
        Array.from(mobileCards).forEach((card) => {
            const searchData = card.getAttribute('data-search') || '';
            const matchesSearch = searchText ? searchData.includes(searchText) : true;
            card.style.display = matchesSearch ? '' : 'none';
        });
    }

        if (searchInput) searchInput.addEventListener('input', filterTable);
    });
</script>
@endpush
