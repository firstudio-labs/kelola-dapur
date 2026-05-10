@extends('template_mitra.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a href="{{ route('mitra.dashboard') }}" class="text-muted me-2">
                                <i class="bx bx-home-alt me-1"></i> Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Laporan Aduan</span>
                        </nav>
                        <h4 class="mb-1">Laporan Aduan & Ulasan</h4>
                        <p class="mb-0 text-muted">
                            Daftar ulasan dari Produksi, Distribusi, dan Kritik dari Penerima MBG
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

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('mitra.laporan-aduan.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="dapur-filter" class="form-label">Filter Dapur</label>
                        <select name="dapur" id="dapur-filter" class="form-select">
                            <option value="">Semua Dapur</option>
                            @foreach($dapurApproved as $dapur)
                                <option value="{{ $dapur->id_dapur }}" {{ request('dapur') == $dapur->id_dapur ? 'selected' : '' }}>
                                    {{ $dapur->nama_dapur }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="search-input" class="form-label">Cari Ulasan / Kritik</label>
                        <input type="text" name="search" id="search-input" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Dari Tanggal</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Sampai Tanggal</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    
                    <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                        @if (request()->hasAny(['dapur', 'search', 'date_from', 'date_to']))
                            <a href="{{ route('mitra.laporan-aduan.index') }}" class="btn btn-outline-secondary">
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

        <div class="card">
            <div class="card-body">
                @if ($aduans->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th width="150">Tanggal</th>
                                    <th width="200">Dapur / Transaksi</th>
                                    <th>Ulasan Produksi</th>
                                    <th>Ulasan Distribusi</th>
                                    <th>Kritik Penerima</th>
                                    <th width="80">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($aduans as $index => $orderProd)
                                    @php
                                        $transaksi = $orderProd->transaksiDapur;
                                        $orderDist = $orderProd->distribusiOrder;
                                        $approval  = $transaksi ? $transaksi->approvalTransaksi : null;
                                    @endphp
                                    <tr>
                                        <td>
                                            {{ $aduans->firstItem() + $index }}
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-dark">
                                                    {{ $orderProd->updated_at->format('d M Y') }}
                                                </span>
                                                <small class="text-muted">
                                                    {{ $orderProd->updated_at->format('H:i') }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-dark">
                                                {{ $transaksi->dapur->nama_dapur ?? '-' }}
                                            </div>
                                            <div class="text-primary small">
                                                {{ $transaksi->id_transaksi ?? '-' }}
                                            </div>
                                            <small class="text-muted">
                                                {{ $transaksi->tanggal_transaksi ? $transaksi->tanggal_transaksi->format('d/m/Y') : '' }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($orderProd->ulasan)
                                                @php $prodHead = $productionHeads->get($orderProd->id_dapur); @endphp
                                                <div class="small text-primary">{{ $prodHead ? ($prodHead->nama_lengkap ?: $prodHead->userRole->user->nama) : 'Tim Produksi' }}</div>
                                                <div class="text-wrap fst-italic text-muted small" title="{{ $orderProd->ulasan }}">
                                                    "{{ Str::limit($orderProd->ulasan, 80) }}"
                                                </div>
                                            @else
                                                <span class="text-light">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($orderDist && $orderDist->ulasan)
                                                @php $distHead = $distributionHeads->get($orderProd->id_dapur); @endphp
                                                <div class="small text-warning">{{ $distHead ? ($distHead->nama_lengkap ?: $distHead->userRole->user->nama) : 'Tim Distribusi' }}</div>
                                                <div class="text-wrap fst-italic text-muted small" title="{{ $orderDist->ulasan }}">
                                                    "{{ Str::limit($orderDist->ulasan, 80) }}"
                                                </div>
                                            @else
                                                <span class="text-light">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($orderDist)
                                                @php $critiques = $orderDist->details->whereNotNull('kritik'); @endphp
                                                @forelse($critiques as $critique)
                                                    <div class="mb-2 pb-2 {{ !$loop->last ? 'border-bottom border-light' : '' }}">
                                                        <div class="small text-danger">{{ $critique->penerimaMbg->userRole->user->nama }}</div>
                                                        <div class="text-wrap fst-italic text-muted small" title="{{ $critique->kritik }}">
                                                            "{{ Str::limit($critique->kritik, 80) }}"
                                                        </div>
                                                    </div>
                                                @empty
                                                    <span class="text-light">-</span>
                                                @endforelse
                                            @else
                                                <span class="text-light">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                @if($approval)
                                                    <a href="{{ route('mitra.laporan-transaksi.show', $approval->id_approval_transaksi) }}" 
                                                       class="btn btn-sm btn-outline-primary action-btn"
                                                       data-bs-toggle="tooltip" 
                                                       title="Lihat Detail Transaksi">
                                                        <i class="bx bx-show"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted small">N/A</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($aduans->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $aduans->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-6">
                        <i class="bx bx-message-error bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">Tidak ada aduan atau ulasan</h5>
                        <p class="text-muted mb-3">
                            Belum ada ulasan dari Produksi, Distribusi, maupun Kritik dari Penerima MBG untuk dapur Anda.
                        </p>
                        @if (request()->hasAny(['dapur', 'search', 'date_from', 'date_to']))
                            <a href="{{ route('mitra.laporan-aduan.index') }}" class="btn btn-outline-primary">
                                Reset Filter
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <style>
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
            .table td {
                vertical-align: middle;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            });
        </script>
    </div>
@endsection
