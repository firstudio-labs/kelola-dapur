@extends('template_produksi.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav class="d-flex align-items-center mb-2">
                        <a href="{{ route('produksi.dashboard') }}" class="text-muted me-2">
                            <i class="bx bx-home-alt me-1"></i>
                            Dashboard
                        </a>
                        <i class="bx bx-chevron-right me-2"></i>
                        <span class="text-dark">Order Produksi</span>
                    </nav>
                    <h4 class="mb-1">Order Produksi</h4>
                    <p class="mb-0 text-muted">Daftar transaksi yang perlu diproduksi</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
            <i class="bx bx-x-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('produksi.order.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="status-filter" class="form-label">Filter Status</label>
                    <select name="status" id="status-filter" class="choices-select form-select">
                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="stok_kurang" {{ $statusFilter === 'stok_kurang' ? 'selected' : '' }}>Stok Kurang</option>
                        <option value="belum_dibuat" {{ $statusFilter === 'belum_dibuat' ? 'selected' : '' }}>Belum Dibuat</option>
                        <option value="sedang_dibuat" {{ $statusFilter === 'sedang_dibuat' ? 'selected' : '' }}>Sedang Dibuat</option>
                        <option value="selesai" {{ $statusFilter === 'selesai' ? 'selected' : '' }}>Selesai</option>
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
                <div class="col-md-3">
                    <label for="search-input" class="form-label">Pencarian Cepat</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" id="search-input" class="form-control" placeholder="Cari nama, menu..." />
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    @if (request()->hasAny(['search', 'status', 'date_from', 'date_to']) && (request('status') !== 'all' || request('date_from')))
                        <a href="{{ route('produksi.order.index') }}" class="btn btn-outline-secondary">
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    @if($orders->total() > 0)
        <div class="card mb-4">
            <div class="card-body py-2 px-4">
                <div class="row justify-content-center g-3">
                    <div class="col-md-3 text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="badge bg-label-primary me-2">
                                <i class="bx bx-list-check"></i>
                            </span>
                            <div>
                                <small class="text-muted">Total Order</small>
                                <h6 class="mb-0">{{ $orders->total() }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="badge bg-label-secondary me-2">
                                <i class="bx bx-time-five"></i>
                            </span>
                            <div>
                                <small class="text-muted">Belum Dibuat</small>
                                <h6 class="mb-0">{{ $stats['belum_dibuat'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="badge bg-label-warning me-2">
                                <i class="bx bx-loader"></i>
                            </span>
                            <div>
                                <small class="text-muted">Sedang Dibuat</small>
                                <h6 class="mb-0">{{ $stats['sedang_dibuat'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="badge bg-label-success me-2">
                                <i class="bx bx-check-double"></i>
                            </span>
                            <div>
                                <small class="text-muted">Selesai</small>
                                <h6 class="mb-0">{{ $stats['selesai'] }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if($orders->count() > 0)
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover">
                        <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Transaksi</th>
                                    <th>Ahli Gizi</th>
                                    <th>Total Porsi</th>
                                    <th>Status Transaksi</th>
                                    <th>Status Stock</th>
                                    <th>Status Produksi</th>
                                    <th>Status Distribusi</th>
                                    <th>Aksi</th>
                                </tr>
                        </thead>
                        <tbody id="transaksi-table-body">
                            @foreach($orders as $order)
                                @php
                                    $transaksi  = $order->transaksiDapur;
                                    $totalPorsi     = $transaksi->detailTransaksiDapur->sum('jumlah_porsi');
                                    $menuList       = $transaksi->detailTransaksiDapur->map(fn($d) => $d->menuMakanan->nama_menu ?? '-')->unique()->implode(', ');
                                    $isStokKurang   = $order->status === 'stok_kurang';
                                    
                                    // Status Stock logic
                                    $pendingShortages = $transaksi->laporanKekuranganStock->where('status', 'pending');
                                    $hasPendingShortage = $pendingShortages->isNotEmpty();
                                    $hasResolvedShortage = $transaksi->laporanKekuranganStock->where('status', 'resolved')->isNotEmpty() && !$hasPendingShortage;

                                    // Status Transaksi
                                    $statusClasses = [
                                        "draft" => "bg-label-warning",
                                        "pending_approval" => "bg-label-info",
                                        "completed" => "bg-label-success",
                                        "rejected" => "bg-label-danger",
                                    ];

                                    $mapStatusProduksi = [
                                        'stok_kurang' => ['badge' => 'bg-danger', 'text' => 'Stok Kurang'],
                                        'belum_dibuat' => ['badge' => 'bg-secondary', 'text' => 'Belum Dibuat'],
                                        'sedang_dibuat' => ['badge' => 'bg-warning', 'text' => 'Sedang Dibuat'],
                                        'selesai' => ['badge' => 'bg-success', 'text' => 'Selesai']
                                    ];
                                    $prodData = $mapStatusProduksi[$order->status] ?? ['badge' => 'bg-label-secondary', 'text' => 'Unknown'];
                                @endphp

                                <tr class="{{ $isStokKurang ? 'table-danger' : '' }}" 
                                    data-search="{{ strtolower($transaksi->nama_paket ?? '') }} {{ strtolower($transaksi->createdBy->nama ?? '') }} {{ strtolower($menuList) }}" 
                                    data-status="{{ $order->status }}">
                                    <td>{{ $loop->iteration + ($orders->currentPage() - 1) * $orders->perPage() }}</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $transaksi->tanggal_transaksi->format("d M Y") }}<br>
                                            {{ $transaksi->tanggal_transaksi->format("H:i") }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="fw-semibold">{{ $transaksi->createdBy->nama ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bx bx-bowl-hot me-1 text-primary"></i>
                                            <span class="fw-semibold">{{ $totalPorsi }} Porsi</span>
                                        </div>
                                        <small class="text-muted d-block text-truncate" style="max-width: 150px;" title="{{ $menuList }}">{{ $menuList }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $statusClasses[$transaksi->status] ?? "bg-label-secondary" }}">
                                            {{ ucfirst(str_replace("_", " ", $transaksi->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($hasPendingShortage)
                                            <span class="badge bg-label-danger">
                                                <i class="bx bx-error me-1"></i>
                                                {{ $pendingShortages->count() }} Item
                                            </span>
                                        @elseif ($hasResolvedShortage)
                                            <span class="badge bg-label-success">
                                                <i class="bx bx-check me-1"></i>
                                                Diselesaikan
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $prodData['badge'] }} text-white shadow-sm">
                                            {{ $prodData['text'] }}
                                        </span>
                                        @if($isStokKurang)
                                            <div class="mt-1">
                                                <small class="text-danger">
                                                    <i class="bx bx-info-circle me-1"></i> Menunggu stok
                                                </small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->distribusiOrder)
                                            @php
                                                $orderDistribusi = $order->distribusiOrder;
                                                $mapStatusDistribusi = [
                                                    'belum_dikirim' => ['badge' => 'bg-secondary', 'text' => 'Belum Dikirim'],
                                                    'sedang_dikirim' => ['badge' => 'bg-warning', 'text' => 'Sedang Dikirim'],
                                                    'sudah_dikirim' => ['badge' => 'bg-success', 'text' => 'Sudah Dikirim']
                                                ];
                                                $distData = $mapStatusDistribusi[$orderDistribusi->status] ?? ['badge' => 'bg-label-secondary', 'text' => 'Unknown'];
                                            @endphp
                                            <span class="badge {{ $distData['badge'] }} text-white shadow-sm">
                                                {{ $distData['text'] }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('produksi.order.show', $order->id_order) }}" 
                                               class="btn btn-sm btn-outline-primary action-btn" data-bs-toggle="tooltip" title="Lihat Detail">
                                                <i class="bx bx-show px-1"></i> Detail
                                            </a>
                                            @if(!$isStokKurang && $order->status !== 'selesai')
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-info action-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#updateStatusModal"
                                                        data-id="{{ $order->id_order }}"
                                                        data-status="{{ $order->status }}"
                                                        data-catatan="{{ $order->catatan }}"
                                                        title="Update Status">
                                                    <i class="bx bx-edit px-1"></i>
                                                </button>
                                            @endif
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
                            $transaksi = $order->transaksiDapur;
                            $totalPorsi = $transaksi->detailTransaksiDapur->sum('jumlah_porsi');
                            $menuList = $transaksi->detailTransaksiDapur->map(fn($d) => $d->menuMakanan->nama_menu ?? '-')->unique()->implode(', ');
                            $isStokKurang = $order->status === 'stok_kurang';
                            
                            $mapStatusProduksi = [
                                'stok_kurang' => ['badge' => 'bg-danger', 'text' => 'Stok Kurang'],
                                'belum_dibuat' => ['badge' => 'bg-secondary', 'text' => 'Belum Dibuat'],
                                'sedang_dibuat' => ['badge' => 'bg-warning', 'text' => 'Sedang Dibuat'],
                                'selesai' => ['badge' => 'bg-success', 'text' => 'Selesai']
                            ];
                            $prodData = $mapStatusProduksi[$order->status] ?? ['badge' => 'bg-label-secondary', 'text' => 'Unknown'];
                            
                            $distData = ['badge' => 'bg-secondary', 'text' => '-'];
                            if ($order->distribusiOrder) {
                                $mapStatusDistribusi = [
                                    'belum_dikirim' => ['badge' => 'bg-secondary', 'text' => 'Belum'],
                                    'sedang_dikirim' => ['badge' => 'bg-warning', 'text' => 'Proses'],
                                    'sudah_dikirim' => ['badge' => 'bg-success', 'text' => 'Selesai']
                                ];
                                $distData = $mapStatusDistribusi[$order->distribusiOrder->status] ?? $distData;
                            }
                        @endphp
                        
                        <div class="card mb-3 border shadow-none mobile-card-item {{ $isStokKurang ? 'border-danger' : '' }}" 
                             data-search="{{ strtolower($transaksi->nama_paket ?? '') }} {{ strtolower($transaksi->createdBy->nama ?? '') }} {{ strtolower($menuList) }}">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="fw-semibold text-primary mb-1">Order #{{ $order->id_order }}</div>
                                        <small class="text-muted"><i class="bx bx-calendar me-1"></i>{{ $transaksi->tanggal_transaksi->format("d M Y") }}</small>
                                    </div>
                                    <span class="badge {{ $prodData['badge'] }} text-white shadow-sm">{{ $prodData['text'] }}</span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-bowl-hot"></i></span>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Porsi</small>
                                            <span class="fw-semibold">{{ $totalPorsi }}</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block mb-1">Distribusi</small>
                                        <span class="badge {{ $distData['badge'] }}" style="font-size: 0.7rem;">{{ $distData['text'] }}</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1">Menu:</small>
                                    <div class="text-dark small text-truncate" title="{{ $menuList }}">
                                        {{ $menuList }}
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('produksi.order.show', $order->id_order) }}" class="btn btn-sm btn-outline-primary">
                                            Detail
                                        </a>
                                        @if(!$isStokKurang && $order->status !== 'selesai')
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-info"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#updateStatusModal"
                                                    data-id="{{ $order->id_order }}"
                                                    data-status="{{ $order->status }}"
                                                    data-catatan="{{ $order->catatan }}">
                                                Update
                                            </button>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">Dibuat Oleh:</small>
                                        <span class="fw-semibold small">{{ $transaksi->createdBy->nama ?? 'N/A' }}</span>
                                    </div>
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
                    @if (request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                        <i class="bx bx-search bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">Tidak ada hasil</h5>
                        <p class="text-muted mb-3">Tidak ada order produksi yang sesuai dengan filter.</p>
                        <a href="{{ route('produksi.order.index') }}" class="btn btn-outline-primary">
                            Reset Filter
                        </a>
                    @else
                        <i class="bx bx-package bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">Belum ada order produksi</h5>
                        <p class="text-muted mb-3">Order produksi akan muncul di sini secara otomatis ketika Ahli Gizi membuat transaksi.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status Order Produksi</h5>
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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css" />

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
    .action-btn {
        min-width: 40px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease, opacity 0.2s ease;
    }
    .action-btn:hover:not(.disabled) {
        transform: scale(1.1);
        opacity: 0.9;
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusFilter = document.getElementById('status-filter');
        const searchInput = document.getElementById('search-input');
        const tableBody = document.getElementById('transaksi-table-body');
        const rows = tableBody ? tableBody.getElementsByTagName('tr') : [];
        const mobileCardsContainer = document.getElementById('mobile-cards-container');
        const mobileCards = mobileCardsContainer ? mobileCardsContainer.getElementsByClassName('mobile-card-item') : [];
        
        // Initialize Choices.js
        if(statusFilter) {
            new Choices(statusFilter, {
                searchEnabled: false,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'Semua Status',
            });
        }

        // Fast Searching Logic (Client-side)
        if(searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                
                // Filter Table Rows
                Array.from(rows).forEach(row => {
                    const text = row.getAttribute('data-search').toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });

                // Filter Mobile Cards
                Array.from(mobileCards).forEach(card => {
                    const text = card.getAttribute('data-search').toLowerCase();
                    card.style.display = text.includes(query) ? '' : 'none';
                });
            });
        }

        // Initialize Bootstrap tooltips
        const tooltipTriggerList = document.querySelectorAll(
            '[data-bs-toggle="tooltip"]',
        );
        const tooltipList = [...tooltipTriggerList].map(
            (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl),
        );
        
        // Update Status Modal Logic
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
@endsection
