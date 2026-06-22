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

        @if (session('success'))
            <div class="alert alert-success alert-dismissible mb-4" role="alert">
                <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
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
                            <option value="stok_kurang" {{ $statusFilter === 'stok_kurang' ? 'selected' : '' }}>Stok Kurang
                            </option>
                            <option value="belum_dibuat" {{ $statusFilter === 'belum_dibuat' ? 'selected' : '' }}>Belum
                                Dibuat</option>
                            <option value="sedang_dibuat" {{ $statusFilter === 'sedang_dibuat' ? 'selected' : '' }}>Sedang
                                Dibuat</option>
                            <option value="selesai" {{ $statusFilter === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="date-from" class="form-label">Dari Tanggal</label>
                        <input type="date" name="date_from" id="date-from" value="{{ request('date_from') }}"
                            class="form-control" />
                    </div>
                    <div class="col-md-3">
                        <label for="date-to" class="form-label">Sampai Tanggal</label>
                        <input type="date" name="date_to" id="date-to" value="{{ request('date_to') }}"
                            class="form-control" />
                    </div>
                    <div class="col-md-3">
                        <label for="search-input" class="form-label">Pencarian Cepat</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" id="search-input" class="form-control" placeholder="Cari nama, menu..." />
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                        @if (request()->hasAny(['search', 'status', 'date_from', 'date_to']) &&
                                (request('status') !== 'all' || request('date_from')))
                            <a href="{{ route('produksi.order.index') }}" class="btn btn-outline-secondary">
                                Reset Filter
                            </a>
                        @endif
                        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
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
                                    <i class="bx bx-list-check"></i>
                                </span>
                                <div>
                                    <small class="text-muted">Total Order</small>
                                    <h6 class="mb-0">@formatNumber($orders->total())</h6>
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
                                    <h6 class="mb-0">@formatNumber($stats['belum_dibuat'])</h6>
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
                                    <h6 class="mb-0">@formatNumber($stats['sedang_dibuat'])</h6>
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
                                    <h6 class="mb-0">@formatNumber($stats['selesai'])</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                @if ($orders->count() > 0)
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Transaksi</th>
                                    <th>Ahli Gizi</th>
                                    <th>Total Porsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="transaksi-table-body">
                                @foreach ($orders as $order)
                                    @php
                                        $transaksi = $order->transaksiDapur;
                                        $totalPorsiBesar =
                                            $transaksi->detailTransaksiDapur->where('tipe_porsi', 'besar')->first()
                                                ->jumlah_porsi ?? 0;
                                        $totalPorsiKecil =
                                            $transaksi->detailTransaksiDapur->where('tipe_porsi', 'kecil')->first()
                                                ->jumlah_porsi ?? 0;
                                        $totalPorsi = $totalPorsiBesar + $totalPorsiKecil;
                                        $menuList = $transaksi->detailTransaksiDapur
                                            ->map(fn($d) => $d->menuMakanan->nama_menu ?? '-')
                                            ->unique()
                                            ->implode(', ');
                                        $isStokKurang = $order->status === 'stok_kurang';

                                        $pendingShortages = $transaksi->laporanKekuranganStock->where(
                                            'status',
                                            'pending',
                                        );
                                        $hasPendingShortage = $pendingShortages->isNotEmpty();
                                        $hasResolvedShortage =
                                            $transaksi->laporanKekuranganStock
                                                ->where('status', 'resolved')
                                                ->isNotEmpty() && !$hasPendingShortage;

                                        if ($order->distribusiOrder && $order->distribusiOrder->details->isNotEmpty()) {
                                            $details = $order->distribusiOrder->details;
                                            $totalDetails = $details->count();
                                            $diterimaCount = $details
                                                ->where(
                                                    'status_penerimaan',
                                                    \App\Models\OrderDistribusiDetail::STATUS_PENERIMAAN_DITERIMA,
                                                )
                                                ->count();
                                            $ditolakCount = $details
                                                ->where(
                                                    'status_penerimaan',
                                                    \App\Models\OrderDistribusiDetail::STATUS_PENERIMAAN_DITOLAK,
                                                )
                                                ->count();

                                            if ($ditolakCount > 0) {
                                                $latestStatusText = 'Penerimaan Ditolak';
                                                $latestStatusClass = 'bg-label-danger';
                                            } elseif ($diterimaCount === $totalDetails) {
                                                $latestStatusText = 'Diterima Penerima';
                                                $latestStatusClass = 'bg-label-success';
                                            } else {
                                                $latestStatusText = 'Sebagian Diterima';
                                                $latestStatusClass = 'bg-label-info';
                                            }
                                        } elseif ($order->distribusiOrder) {
                                            $distStatus = $order->distribusiOrder->status;
                                            if ($distStatus === 'sudah_dikirim') {
                                                $latestStatusText = 'Sudah Dikirim';
                                                $latestStatusClass = 'bg-label-success';
                                            } elseif ($distStatus === 'sedang_dikirim') {
                                                $latestStatusText = 'Sedang Dikirim';
                                                $latestStatusClass = 'bg-label-warning';
                                            } else {
                                                $latestStatusText = 'Belum Dikirim';
                                                $latestStatusClass = 'bg-label-secondary';
                                            }
                                        } elseif ($order->status === 'selesai') {
                                            $latestStatusText = 'Selesai';
                                            $latestStatusClass = 'bg-label-success';
                                        } elseif ($order->status === 'sedang_dibuat') {
                                            $latestStatusText = 'Sedang Dibuat';
                                            $latestStatusClass = 'bg-label-warning';
                                        } elseif ($order->status === 'belum_dibuat') {
                                            $latestStatusText = 'Belum Dibuat';
                                            $latestStatusClass = 'bg-label-secondary';
                                        } elseif ($order->status === 'stok_kurang' || $hasPendingShortage) {
                                            $latestStatusText = 'Stok Kurang';
                                            $latestStatusClass = 'bg-label-danger';
                                        } else {
                                            if ($transaksi->status === 'rejected') {
                                                $latestStatusText = 'Transaksi Ditolak';
                                                $latestStatusClass = 'bg-label-danger';
                                            } elseif ($transaksi->status === 'pending_approval') {
                                                $latestStatusText = 'Menunggu Approval';
                                                $latestStatusClass = 'bg-label-info';
                                            } else {
                                                $latestStatusText = 'Draft Transaksi';
                                                $latestStatusClass = 'bg-label-warning';
                                            }
                                        }

                                        if ($transaksi->status === 'draft') {
                                            $txIconColor = 'text-warning';
                                            $txTooltip = 'Transaksi: Draft';
                                        } elseif ($transaksi->status === 'pending_approval') {
                                            $txIconColor = 'text-info';
                                            $txTooltip = 'Transaksi: Menunggu Approval';
                                        } elseif ($transaksi->status === 'completed') {
                                            $txIconColor = 'text-success';
                                            $txTooltip = 'Transaksi: Disetujui';
                                        } else {
                                            $txIconColor = 'text-danger';
                                            $txTooltip = 'Transaksi: Ditolak';
                                        }

                                        if ($hasPendingShortage) {
                                            $stIconColor = 'text-danger';
                                            $stTooltip =
                                                'Stok: Kurang (' . $pendingShortages->count() . ' item pending)';
                                        } elseif ($hasResolvedShortage) {
                                            $stIconColor = 'text-info';
                                            $stTooltip = 'Stok: Kurang (Telah Diselesaikan)';
                                        } else {
                                            $stIconColor = 'text-success';
                                            $stTooltip = 'Stok: Cukup';
                                        }

                                        if ($order->status === 'stok_kurang') {
                                            $prIconColor = 'text-danger';
                                            $prTooltip = 'Produksi: Stok Kurang';
                                        } elseif ($order->status === 'belum_dibuat') {
                                            $prIconColor = 'text-secondary';
                                            $prTooltip = 'Produksi: Belum Dibuat';
                                        } elseif ($order->status === 'sedang_dibuat') {
                                            $prIconColor = 'text-warning';
                                            $prTooltip = 'Produksi: Sedang Dibuat';
                                        } else {
                                            $prIconColor = 'text-success';
                                            $prTooltip = 'Produksi: Selesai';
                                        }

                                        if (!$order->distribusiOrder) {
                                            $dsIconColor = 'text-muted';
                                            $dsTooltip = 'Distribusi: Belum Ada';
                                        } else {
                                            $distStatus = $order->distribusiOrder->status;
                                            if ($distStatus === 'belum_dikirim') {
                                                $dsIconColor = 'text-secondary';
                                                $dsTooltip = 'Distribusi: Belum Dikirim';
                                            } elseif ($distStatus === 'sedang_dikirim') {
                                                $dsIconColor = 'text-warning';
                                                $dsTooltip = 'Distribusi: Sedang Dikirim';
                                            } else {
                                                $dsIconColor = 'text-success';
                                                $dsTooltip = 'Distribusi: Sudah Dikirim';
                                            }
                                        }

                                        if (!$order->distribusiOrder) {
                                            $rcIconColor = 'text-muted';
                                            $rcTooltip = 'Penerimaan: Belum Ada Pengiriman';
                                        } else {
                                            $details = $order->distribusiOrder->details;
                                            if ($details->isEmpty()) {
                                                $rcIconColor = 'text-muted';
                                                $rcTooltip = 'Penerimaan: Menunggu Konfirmasi';
                                            } else {
                                                $totalDetails = $details->count();
                                                $diterimaCount = $details
                                                    ->where(
                                                        'status_penerimaan',
                                                        \App\Models\OrderDistribusiDetail::STATUS_PENERIMAAN_DITERIMA,
                                                    )
                                                    ->count();
                                                $ditolakCount = $details
                                                    ->where(
                                                        'status_penerimaan',
                                                        \App\Models\OrderDistribusiDetail::STATUS_PENERIMAAN_DITOLAK,
                                                    )
                                                    ->count();

                                                if ($ditolakCount > 0) {
                                                    $rcIconColor = 'text-danger';
                                                    $rcTooltip = 'Penerimaan: Ditolak (' . $ditolakCount . ' Sekolah)';
                                                } elseif ($diterimaCount === $totalDetails) {
                                                    $rcIconColor = 'text-success';
                                                    $rcTooltip = 'Penerimaan: Diterima oleh Semua Sekolah';
                                                } else {
                                                    $rcIconColor = 'text-warning';
                                                    $rcTooltip =
                                                        'Penerimaan: Sebagian (' .
                                                        $diterimaCount .
                                                        '/' .
                                                        $totalDetails .
                                                        ' Diterima)';
                                                }
                                            }
                                        }
                                    @endphp

                                    <tr class="{{ $isStokKurang ? 'table-danger' : '' }}"
                                        data-search="{{ strtolower($transaksi->nama_paket ?? '') }} {{ strtolower($transaksi->createdBy->nama ?? '') }} {{ strtolower($menuList) }}"
                                        data-status="{{ $order->status }}">
                                        <td>{{ $loop->iteration + ($orders->currentPage() - 1) * $orders->perPage() }}</td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $transaksi->tanggal_transaksi->format('d M Y') }}<br>
                                                {{ $transaksi->created_at->format('H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span
                                                    class="fw-semibold">{{ $transaksi->createdBy->nama ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-restaurant me-1 text-primary"></i>
                                                <span class="fw-semibold">@formatNumber($totalPorsi) Porsi</span>
                                            </div>
                                            <small class="text-muted d-block text-truncate" style="max-width: 150px;"
                                                title="{{ $menuList }}">{{ $menuList }}</small>
                                        </td>
                                        <td>
                                            <div class="mb-1">
                                                <span class="badge {{ $latestStatusClass }} shadow-sm">
                                                    {{ $latestStatusText }}
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center gap-1 mt-1 fs-5">
                                                <i class="bx bx-receipt {{ $txIconColor }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="{{ $txTooltip }}"></i>
                                                <i class="bx bx-chevron-right text-muted small"
                                                    style="font-size: 0.75rem;"></i>
                                                <i class="bx bx-package {{ $stIconColor }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="{{ $stTooltip }}"></i>
                                                <i class="bx bx-chevron-right text-muted small"
                                                    style="font-size: 0.75rem;"></i>
                                                <i class="bx bx-restaurant {{ $prIconColor }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="{{ $prTooltip }}"></i>
                                                <i class="bx bx-chevron-right text-muted small"
                                                    style="font-size: 0.75rem;"></i>
                                                <i class="bx bx-car {{ $dsIconColor }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="{{ $dsTooltip }}"></i>
                                                <i class="bx bx-chevron-right text-muted small"
                                                    style="font-size: 0.75rem;"></i>
                                                <i class="bx bx-smile {{ $rcIconColor }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="{{ $rcTooltip }}"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('produksi.order.show', $order->id_order) }}"
                                                    class="btn btn-sm btn-outline-primary action-btn"
                                                    data-bs-toggle="tooltip" title="Lihat Detail">
                                                    <i class="bx bx-show px-1"></i> Detail
                                                </a>
                                                @if (!$isStokKurang && $order->status !== 'selesai')
                                                    <button type="button" class="btn btn-sm btn-outline-info action-btn"
                                                        data-bs-toggle="modal" data-bs-target="#updateStatusModal"
                                                        data-id="{{ $order->id_order }}"
                                                        data-status="{{ $order->status }}"
                                                        data-catatan="{{ $order->catatan }}" title="Update Status">
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
                                $totalPorsiBesar =
                                    $transaksi->detailTransaksiDapur->where('tipe_porsi', 'besar')->first()
                                        ->jumlah_porsi ?? 0;
                                $totalPorsiKecil =
                                    $transaksi->detailTransaksiDapur->where('tipe_porsi', 'kecil')->first()
                                        ->jumlah_porsi ?? 0;
                                $totalPorsi = $totalPorsiBesar + $totalPorsiKecil;
                                $menuList = $transaksi->detailTransaksiDapur
                                    ->map(fn($d) => $d->menuMakanan->nama_menu ?? '-')
                                    ->unique()
                                    ->implode(', ');
                                $isStokKurang = $order->status === 'stok_kurang';

                                $pendingShortages = $transaksi->laporanKekuranganStock->where('status', 'pending');
                                $hasPendingShortage = $pendingShortages->isNotEmpty();
                                $hasResolvedShortage =
                                    $transaksi->laporanKekuranganStock->where('status', 'resolved')->isNotEmpty() &&
                                    !$hasPendingShortage;

                                $latestStatusText = '';
                                $latestStatusClass = '';

                                if ($order->distribusiOrder && $order->distribusiOrder->details->isNotEmpty()) {
                                    $mbgDetails = $order->distribusiOrder->details;
                                    $mbgTotal = $mbgDetails->count();
                                    $mbgDiterima = $mbgDetails
                                        ->where(
                                            'status_penerimaan',
                                            \App\Models\OrderDistribusiDetail::STATUS_PENERIMAAN_DITERIMA,
                                        )
                                        ->count();
                                    $mbgDitolak = $mbgDetails
                                        ->where(
                                            'status_penerimaan',
                                            \App\Models\OrderDistribusiDetail::STATUS_PENERIMAAN_DITOLAK,
                                        )
                                        ->count();
                                    if ($mbgDitolak > 0) {
                                        $latestStatusText = 'Penerimaan Ditolak';
                                        $latestStatusClass = 'bg-label-danger';
                                    } elseif ($mbgDiterima === $mbgTotal) {
                                        $latestStatusText = 'Diterima Penerima';
                                        $latestStatusClass = 'bg-label-success';
                                    } else {
                                        $latestStatusText = 'Sebagian Diterima';
                                        $latestStatusClass = 'bg-label-info';
                                    }
                                } elseif ($order->distribusiOrder) {
                                    $distStatus = $order->distribusiOrder->status;
                                    if ($distStatus === 'sudah_dikirim') {
                                        $latestStatusText = 'Sudah Dikirim';
                                        $latestStatusClass = 'bg-label-success';
                                    } elseif ($distStatus === 'sedang_dikirim') {
                                        $latestStatusText = 'Sedang Dikirim';
                                        $latestStatusClass = 'bg-label-warning';
                                    } else {
                                        $latestStatusText = 'Belum Dikirim';
                                        $latestStatusClass = 'bg-label-secondary';
                                    }
                                } elseif ($order->status === 'selesai') {
                                    $latestStatusText = 'Selesai';
                                    $latestStatusClass = 'bg-label-success';
                                } elseif ($order->status === 'sedang_dibuat') {
                                    $latestStatusText = 'Sedang Dibuat';
                                    $latestStatusClass = 'bg-label-warning';
                                } elseif ($order->status === 'belum_dibuat') {
                                    $latestStatusText = 'Belum Dibuat';
                                    $latestStatusClass = 'bg-label-secondary';
                                } elseif ($order->status === 'stok_kurang' || $hasPendingShortage) {
                                    $latestStatusText = 'Stok Kurang';
                                    $latestStatusClass = 'bg-label-danger';
                                } else {
                                    if ($transaksi->status === 'rejected') {
                                        $latestStatusText = 'Transaksi Ditolak';
                                        $latestStatusClass = 'bg-label-danger';
                                    } elseif ($transaksi->status === 'pending_approval') {
                                        $latestStatusText = 'Menunggu Approval';
                                        $latestStatusClass = 'bg-label-info';
                                    } else {
                                        $latestStatusText = 'Draft Transaksi';
                                        $latestStatusClass = 'bg-label-warning';
                                    }
                                }

                                if ($transaksi->status === 'draft') {
                                    $txIconColor = 'text-warning';
                                    $txTooltip = 'Transaksi: Draft';
                                } elseif ($transaksi->status === 'pending_approval') {
                                    $txIconColor = 'text-info';
                                    $txTooltip = 'Transaksi: Menunggu Approval';
                                } elseif ($transaksi->status === 'completed') {
                                    $txIconColor = 'text-success';
                                    $txTooltip = 'Transaksi: Disetujui';
                                } else {
                                    $txIconColor = 'text-danger';
                                    $txTooltip = 'Transaksi: Ditolak';
                                }

                                if ($hasPendingShortage) {
                                    $stIconColor = 'text-danger';
                                    $stTooltip = 'Stok: Kurang (' . $pendingShortages->count() . ' item pending)';
                                } elseif ($hasResolvedShortage) {
                                    $stIconColor = 'text-info';
                                    $stTooltip = 'Stok: Kurang (Telah Diselesaikan)';
                                } else {
                                    $stIconColor = 'text-success';
                                    $stTooltip = 'Stok: Cukup';
                                }

                                if ($order->status === 'stok_kurang') {
                                    $prIconColor = 'text-danger';
                                    $prTooltip = 'Produksi: Stok Kurang';
                                } elseif ($order->status === 'belum_dibuat') {
                                    $prIconColor = 'text-secondary';
                                    $prTooltip = 'Produksi: Belum Dibuat';
                                } elseif ($order->status === 'sedang_dibuat') {
                                    $prIconColor = 'text-warning';
                                    $prTooltip = 'Produksi: Sedang Dibuat';
                                } else {
                                    $prIconColor = 'text-success';
                                    $prTooltip = 'Produksi: Selesai';
                                }

                                if (!$order->distribusiOrder) {
                                    $dsIconColor = 'text-muted';
                                    $dsTooltip = 'Distribusi: Belum Ada';
                                } else {
                                    $distStatus = $order->distribusiOrder->status;
                                    if ($distStatus === 'belum_dikirim') {
                                        $dsIconColor = 'text-secondary';
                                        $dsTooltip = 'Distribusi: Belum Dikirim';
                                    } elseif ($distStatus === 'sedang_dikirim') {
                                        $dsIconColor = 'text-warning';
                                        $dsTooltip = 'Distribusi: Sedang Dikirim';
                                    } else {
                                        $dsIconColor = 'text-success';
                                        $dsTooltip = 'Distribusi: Sudah Dikirim';
                                    }
                                }

                                if (!$order->distribusiOrder) {
                                    $rcIconColor = 'text-muted';
                                    $rcTooltip = 'Penerimaan: Belum Ada Pengiriman';
                                } else {
                                    $rcDetails = $order->distribusiOrder->details;
                                    if ($rcDetails->isEmpty()) {
                                        $rcIconColor = 'text-muted';
                                        $rcTooltip = 'Penerimaan: Menunggu Konfirmasi';
                                    } else {
                                        $rcTotal = $rcDetails->count();
                                        $rcDiterima = $rcDetails
                                            ->where(
                                                'status_penerimaan',
                                                \App\Models\OrderDistribusiDetail::STATUS_PENERIMAAN_DITERIMA,
                                            )
                                            ->count();
                                        $rcDitolak = $rcDetails
                                            ->where(
                                                'status_penerimaan',
                                                \App\Models\OrderDistribusiDetail::STATUS_PENERIMAAN_DITOLAK,
                                            )
                                            ->count();
                                        if ($rcDitolak > 0) {
                                            $rcIconColor = 'text-danger';
                                            $rcTooltip = 'Penerimaan: Ditolak (' . $rcDitolak . ' Sekolah)';
                                        } elseif ($rcDiterima === $rcTotal) {
                                            $rcIconColor = 'text-success';
                                            $rcTooltip = 'Penerimaan: Diterima oleh Semua Sekolah';
                                        } else {
                                            $rcIconColor = 'text-warning';
                                            $rcTooltip =
                                                'Penerimaan: Sebagian (' . $rcDiterima . '/' . $rcTotal . ' Diterima)';
                                        }
                                    }
                                }
                            @endphp

                            <div class="card mb-3 border shadow-none mobile-card-item {{ $isStokKurang ? 'border-danger' : '' }}"
                                data-search="{{ strtolower($transaksi->nama_paket ?? '') }} {{ strtolower($transaksi->createdBy->nama ?? '') }} {{ strtolower($menuList) }}">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <div class="fw-semibold text-primary mb-1">Order #{{ $order->id_order }}</div>
                                            <small class="text-muted"><i
                                                    class="bx bx-calendar me-1"></i>{{ $transaksi->tanggal_transaksi->format('d M Y') }}</small>
                                        </div>
                                        <span
                                            class="badge {{ $latestStatusClass }} shadow-sm">{{ $latestStatusText }}</span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded bg-label-primary"><i
                                                        class="bx bx-restaurant"></i></span>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Porsi</small>
                                                <span class="fw-semibold">@formatNumber($totalPorsi)</span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted d-block mb-1">Alur Proses</small>
                                            <div class="d-flex align-items-center gap-1 fs-5">
                                                <i class="bx bx-receipt {{ $txIconColor }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="{{ $txTooltip }}"></i>
                                                <i class="bx bx-chevron-right text-muted small"
                                                    style="font-size: 0.60rem;"></i>
                                                <i class="bx bx-package {{ $stIconColor }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="{{ $stTooltip }}"></i>
                                                <i class="bx bx-chevron-right text-muted small"
                                                    style="font-size: 0.60rem;"></i>
                                                <i class="bx bx-restaurant {{ $prIconColor }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="{{ $prTooltip }}"></i>
                                                <i class="bx bx-chevron-right text-muted small"
                                                    style="font-size: 0.60rem;"></i>
                                                <i class="bx bx-car {{ $dsIconColor }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="{{ $dsTooltip }}"></i>
                                                <i class="bx bx-chevron-right text-muted small"
                                                    style="font-size: 0.60rem;"></i>
                                                <i class="bx bx-smile {{ $rcIconColor }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="{{ $rcTooltip }}"></i>
                                            </div>
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
                                            <a href="{{ route('produksi.order.show', $order->id_order) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                Detail
                                            </a>
                                            @if (!$isStokKurang && $order->status !== 'selesai')
                                                <button type="button" class="btn btn-sm btn-outline-info"
                                                    data-bs-toggle="modal" data-bs-target="#updateStatusModal"
                                                    data-id="{{ $order->id_order }}" data-status="{{ $order->status }}"
                                                    data-catatan="{{ $order->catatan }}">
                                                    Update
                                                </button>
                                            @endif
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted d-block" style="font-size: 0.7rem;">Dibuat
                                                Oleh:</small>
                                            <span
                                                class="fw-semibold small">{{ $transaksi->createdBy->nama ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>


                    @if ($orders->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $orders->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
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
                            <p class="text-muted mb-3">Order produksi akan muncul di sini secara otomatis ketika Ahli Gizi
                                membuat transaksi.</p>
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
                            <label class="form-label fw-semibold">Langkah Berikutnya:</label>
                            <div id="statusInfo" class="alert alert-info py-2 px-3 mb-0 d-flex align-items-center">
                                <i class="bx bx-info-circle me-2"></i>
                                <span id="statusText"></span>
                            </div>
                            <input type="hidden" name="status" id="modalStatus">
                        </div>

                        <div class="mb-3 d-none" id="dokumentasiWrapper">
                            <label class="form-label">Foto Dokumentasi (Wajib untuk Selesai, Minimal 1)</label>
                            <input type="file" name="dokumentasi[]" class="form-control" multiple accept="image/*"
                                id="inputDokumentasi">
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
        document.addEventListener('DOMContentLoaded', function() {
            const statusFilter = document.getElementById('status-filter');
            const searchInput = document.getElementById('search-input');
            const tableBody = document.getElementById('transaksi-table-body');
            const rows = tableBody ? tableBody.getElementsByTagName('tr') : [];
            const mobileCardsContainer = document.getElementById('mobile-cards-container');
            const mobileCards = mobileCardsContainer ? mobileCardsContainer.getElementsByClassName(
                'mobile-card-item') : [];

            if (statusFilter) {
                new Choices(statusFilter, {
                    searchEnabled: false,
                    itemSelectText: '',
                    placeholder: true,
                    placeholderValue: 'Semua Status',
                });
            }

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase();

                    Array.from(rows).forEach(row => {
                        const text = row.getAttribute('data-search').toLowerCase();
                        row.style.display = text.includes(query) ? '' : 'none';
                    });
                    Array.from(mobileCards).forEach(card => {
                        const text = card.getAttribute('data-search').toLowerCase();
                        card.style.display = text.includes(query) ? '' : 'none';
                    });
                });
            }

            const tooltipTriggerList = document.querySelectorAll(
                '[data-bs-toggle="tooltip"]',
            );
            const tooltipList = [...tooltipTriggerList].map(
                (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl),
            );

            const updateStatusModal = document.getElementById('updateStatusModal');
            const modalStatus = document.getElementById('modalStatus');
            const dokumentasiWrapper = document.getElementById('dokumentasiWrapper');
            const inputDokumentasi = document.getElementById('inputDokumentasi');

            if (updateStatusModal) {
                updateStatusModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const orderId = button.getAttribute('data-id');
                    const currentStatus = button.getAttribute('data-status');
                    const catatan = button.getAttribute('data-catatan');

                    const statusText = document.getElementById('statusText');
                    const statusInput = document.getElementById('modalStatus');
                    const statusInfo = document.getElementById('statusInfo');

                    let nextStatus = '';
                    let nextStatusLabel = '';
                    let alertClass = 'alert-info';

                    if (currentStatus === 'belum_dibuat') {
                        nextStatus = 'sedang_dibuat';
                        nextStatusLabel =
                            'Ubah status menjadi <strong>Sedang Dibuat</strong> (Stok akan otomatis dikurangi)';
                        alertClass = 'alert-warning';
                    } else if (currentStatus === 'sedang_dibuat') {
                        nextStatus = 'selesai';
                        nextStatusLabel =
                            'Ubah status menjadi <strong>Selesai</strong> (Pastikan dokumentasi sudah lengkap)';
                        alertClass = 'alert-success';
                    }

                    statusInput.value = nextStatus;
                    statusText.innerHTML = nextStatusLabel;
                    statusInfo.className = `alert ${alertClass} py-2 px-3 mb-0 d-flex align-items-center`;

                    document.getElementById('modalCatatan').value = (catatan && catatan !== 'null') ?
                        catatan : '';

                    toggleDokumentasi(nextStatus);

                    const baseUrl = '{{ route('produksi.order.update-status', ':id') }}';
                    document.getElementById('updateStatusForm').action = baseUrl.replace(':id', orderId);
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
