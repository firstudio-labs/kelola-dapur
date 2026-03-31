@extends('template_penerima_mbg.layout')
@section('title', 'History')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav class="d-flex align-items-center mb-2">
                        <a href="{{ route('penerima-mbg.dashboard') }}" class="text-muted me-2 small">
                            <i class="bx bx-home-alt me-1"></i> Dashboard
                        </a>
                        <i class="bx bx-chevron-right me-2 text-muted small"></i>
                        <span class="text-dark small">History</span>
                    </nav>
                    <h4 class="mb-1 fw-bold">History Kiriman</h4>
                    <p class="mb-0 text-muted small">
                        Daftar riwayat kiriman makanan yang telah Anda terima
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="bx bx-filter-alt me-1 text-primary"></i> Filter Pencarian</h6>
            <form method="GET" action="{{ route('penerima-mbg.history.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label small fw-bold text-muted">Cari Menu / Dapur</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text bg-light border-end-0"><i class="bx bx-search fs-5"></i></span>
                        <input type="text" name="search" id="search" class="form-control border-start-0 ps-0" placeholder="Ketik nama menu..." value="{{ request('search') }}" />
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label small fw-bold text-muted">Status</label>
                    <select name="status" id="status" class="form-select bg-light">
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="diterima" {{ request('status') === 'diterima' ? 'selected' : '' }}>Dikonfirmasi</option>
                        <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label small fw-bold text-muted">Mulai</label>
                    <input type="date" name="date_from" id="date_from" class="form-control bg-light" value="{{ request('date_from') }}" />
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label small fw-bold text-muted">Selesai</label>
                    <input type="date" name="date_to" id="date_to" class="form-control bg-light" value="{{ request('date_to') }}" />
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bx bx-filter-alt me-1"></i> Terapkan Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                        <a href="{{ route('penerima-mbg.history.index') }}" class="btn btn-label-secondary" data-bs-toggle="tooltip" title="Reset Filter">
                            <i class="bx bx-refresh"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3 text-center">
                    <div class="avatar avatar-sm bg-label-primary rounded-circle mx-auto mb-2">
                        <i class="bx bx-package"></i>
                    </div>
                    <small class="text-muted d-block">Total Kiriman</small>
                    <h6 class="mb-0 fw-bold">{{ $stats['total'] }}</h6>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3 text-center">
                    <div class="avatar avatar-sm bg-label-warning rounded-circle mx-auto mb-2">
                        <i class="bx bx-time"></i>
                    </div>
                    <small class="text-muted d-block">Menunggu</small>
                    <h6 class="mb-0 fw-bold">{{ $stats['menunggu'] }}</h6>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3 text-center">
                    <div class="avatar avatar-sm bg-label-success rounded-circle mx-auto mb-2">
                        <i class="bx bx-check-double"></i>
                    </div>
                    <small class="text-muted d-block">Diterima</small>
                    <h6 class="mb-0 fw-bold">{{ $stats['diterima'] }}</h6>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3 text-center">
                    <div class="avatar avatar-sm bg-label-danger rounded-circle mx-auto mb-2">
                        <i class="bx bx-x-circle"></i>
                    </div>
                    <small class="text-muted d-block">Ditolak</small>
                    <h6 class="mb-0 fw-bold">{{ $stats['ditolak'] }}</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th>Menu Makanan</th>
                        <th>Dapur SPPG</th>
                        <th>Tanggal Kirim</th>
                        <th class="text-center">Porsi (B / K) / Total</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kiriman as $item)
                        @php
                            $statusPenerimaan = $item->status_penerimaan ?? 'menunggu';
                            $badgeColor = match($statusPenerimaan) {
                                'diterima' => 'success',
                                'ditolak'  => 'danger',
                                default    => 'warning',
                            };
                            $badgeLabel = match($statusPenerimaan) {
                                'diterima' => 'Sudah Dikonfirmasi',
                                'ditolak'  => 'Ditolak',
                                default    => 'Menunggu Konfirmasi',
                            };
                            $transaksi = $item->orderDistribusi?->orderProduksi?->transaksiDapur;
                            $dapur     = $item->orderDistribusi?->dapur;
                            $menus     = $transaksi?->detailTransaksiDapur ?? collect();
                            $porsiBesar = $item->porsi_besar ?? 0;
                            $porsiKecil = $item->porsi_kecil ?? 0;
                            $totalPorsi = $porsiBesar + $porsiKecil;
                        @endphp
                        <tr>
                            <td class="text-center">{{ ($kiriman->currentPage() - 1) * $kiriman->perPage() + $loop->iteration }}</td>
                            <td>
                                <div class="text-wrap" style="max-width: 250px;">
                                    @if($menus->count())
                                        <span class="fw-semibold text-dark">{{ $menus->map(fn($d) => $d->menuMakanan?->nama_menu)->filter()->implode(', ') }}</span>
                                    @else
                                        <span class="text-muted small">No Menu Data</span>
                                    @endif
                                </div>
                            </td>
                            <td><small class="fw-semibold">{{ $dapur?->nama_dapur ?? '—' }}</small></td>
                            <td>
                                <small class="text-muted">
                                    {{ $transaksi?->tanggal_transaksi ? \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->translatedFormat('d M Y') : '—' }}
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="fw-bold text-dark">{{ $totalPorsi }}</div>
                                <span class="badge bg-label-secondary small" style="font-size: 0.65rem;">
                                    {{ $porsiBesar }} / {{ $porsiKecil }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-{{ $badgeColor }} small">{{ $badgeLabel }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('penerima-mbg.history.show', $item->id_detail) }}"
                                   class="btn btn-sm btn-{{ $statusPenerimaan === 'menunggu' ? 'primary' : 'outline-primary' }} px-3">
                                   <i class="bx bx-{{ $statusPenerimaan === 'menunggu' ? 'check-double' : 'show' }} me-1"></i>
                                   {{ $statusPenerimaan === 'menunggu' ? 'Konfirmasi' : 'Detail' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bx bx-package mb-2 d-block opacity-25" style="font-size:3rem;"></i>
                                <p class="mb-0">Belum ada riwayat kiriman.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($kiriman->hasPages())
            <div class="card-footer border-top bg-transparent">
                {{ $kiriman->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush

