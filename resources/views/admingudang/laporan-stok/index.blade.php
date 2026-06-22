@extends('template_admin_gudang.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a href="{{ route('admin-gudang.dashboard', $currentDapur->id_dapur ?? (auth()->user()->userRole->id_dapur ?? null)) }}"
                                class="text-muted me-2">
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Laporan Stok</span>
                        </nav>
                        <h4 class="mb-1">
                            Laporan Stok (Kelebihan & Kekurangan) - {{ $dapur->nama_dapur ?? 'Dapur' }}
                        </h4>
                        <p class="mb-0 text-muted">
                            Kelola laporan handler stok bahan dari bagian Produksi (Ahli Gizi).
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

        <div class="row mb-4">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-receipt"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">
                                    Total Laporan
                                </small>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-1">
                                        @formatNumber($stats['total'])
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-time"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">
                                    Menunggu Persetujuan
                                </small>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-1">
                                        @formatNumber($stats['pending'])
                                    </h6>
                                    @if ($stats['pending'] > 0)
                                        <span class="badge bg-warning ms-1 pulse">
                                            Baru
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="bx bx-check-circle"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">
                                    Diselesaikan
                                </small>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-1">
                                        @formatNumber($stats['resolved'])
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin-gudang.laporan-stok.index', ['dapur' => $dapur->id_dapur]) }}"
                    class="row g-3">
                    <div class="col-md-3">
                        <label for="status-filter" class="form-label">
                            Status
                        </label>
                        <select name="status" id="status-filter" class="form-select">
                            <option value="" {{ request('status') === '' ? 'selected' : '' }}>
                                Semua Status
                            </option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                Menunggu
                            </option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>
                                Disetujui (Resolved)
                            </option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>
                                Ditolak
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="jenis-filter" class="form-label">
                            Jenis Handler
                        </label>
                        <select name="jenis" id="jenis-filter" class="form-select">
                            <option value="" {{ request('jenis') === '' ? 'selected' : '' }}>
                                Semua Jenis
                            </option>
                            <option value="kelebihan" {{ request('jenis') === 'kelebihan' ? 'selected' : '' }}>
                                Kelebihan Bahan (+)
                            </option>
                            <option value="kekurangan" {{ request('jenis') === 'kekurangan' ? 'selected' : '' }}>
                                Kekurangan Bahan (-)
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date-from" class="form-label">
                            Dari Tanggal
                        </label>
                        <input type="date" name="date_from" id="date-from" value="{{ request('date_from') }}"
                            class="form-control" />
                    </div>
                    <div class="col-md-3">
                        <label for="date-to" class="form-label">
                            Sampai Tanggal
                        </label>
                        <input type="date" name="date_to" id="date-to" value="{{ request('date_to') }}"
                            class="form-control" />
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                        @if (request()->hasAny(['status', 'jenis', 'date_from', 'date_to']))
                            <a href="{{ route('admin-gudang.laporan-stok.index', ['dapur' => $dapur->id_dapur]) }}"
                                class="btn btn-outline-secondary">
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
                @if ($laporan->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>ID Transaksi</th>
                                    <th>Bahan</th>
                                    <th>Jenis</th>
                                    <th class="text-center">Jumlah</th>
                                    <th>Catatan Produksi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($laporan as $index => $item)
                                    <tr class="{{ $item->status === 'pending' ? 'table-warning-subtle' : '' }}">
                                        <td>{{ $laporan->firstItem() + $index }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-medium">{{ $item->created_at->format('d M Y') }}</span>
                                                <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <h6 class="mb-0">
                                                        TRX-{{ $item->orderProduksi->transaksiDapur->id_transaksi }}</h6>
                                                    <small class="text-muted">Order:
                                                        #{{ $item->orderProduksi->id_order }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="fw-medium">{{ $item->templateItem->nama_bahan ?? 'Unknown' }}</span>
                                        </td>
                                        <td>
                                            @if ($item->jenis === 'kelebihan')
                                                <span class="badge bg-label-success"><i class="bx bx-plus"></i>
                                                    Kelebihan</span>
                                            @else
                                                <span class="badge bg-label-danger"><i class="bx bx-minus"></i>
                                                    Kekurangan</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $stockItem = $stockItems[$item->id_template_item] ?? null;
                                                $hasKonversi =
                                                    $stockItem &&
                                                    isset($stockItem->konversi_nilai) &&
                                                    $stockItem->konversi_nilai > 0;
                                                $nilaiKonversi = $hasKonversi ? $stockItem->konversi_nilai : 1;
                                                $satuanKonversi = $hasKonversi ? $stockItem->konversi_satuan : '';
                                            @endphp
                                            <div class="fw-bold fs-6">
                                                {{ formatIndonesianNumber($item->jumlah) }} <small
                                                    class="text-muted fw-normal">{{ $item->templateItem->satuan ?? '' }}</small>
                                            </div>
                                            @if ($hasKonversi)
                                                <div class="text-muted" style="font-size: 11px;">
                                                    ({{ formatIndonesianNumber($item->jumlah / $nilaiKonversi) }}
                                                    {{ $satuanKonversi }})
                                                </div>
                                            @endif
                                        </td>
                                        <td style="max-width: 200px; white-space: normal;">
                                            <small>{{ $item->catatan ?? '-' }}</small>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = 'bg-label-secondary';
                                                $statusText = 'Unknown';
                                                if ($item->status === 'pending') {
                                                    $statusClass = 'bg-label-warning';
                                                    $statusText = 'Menunggu';
                                                } elseif ($item->status === 'resolved') {
                                                    $statusClass = 'bg-label-success';
                                                    $statusText = 'Disetujui';
                                                } elseif ($item->status === 'rejected') {
                                                    $statusClass = 'bg-label-danger';
                                                    $statusText = 'Ditolak';
                                                }

                                                $key = $item->id_order . '_' . $item->id_template_item;
                                                $itemHistory = $histories[$key] ?? collect();

                                                $bahanKebutuhan = $item->orderProduksi->transaksiDapur->calculateIngredientNeeds();
                                                $totalButuh =
                                                    (float) ($bahanKebutuhan[$item->id_template_item][
                                                        'total_kebutuhan'
                                                    ] ?? 0);

                                                $runningButuh = $totalButuh;
                                                $historyArray = [];
                                                foreach ($itemHistory as $h) {
                                                    $hKonv =
                                                        $stockItem &&
                                                        isset($stockItem->konversi_nilai) &&
                                                        $stockItem->konversi_nilai > 0;
                                                    $nk = $hKonv ? $stockItem->konversi_nilai : 1;
                                                    $sk = $hKonv ? $stockItem->konversi_satuan : '';
                                                    $satAsli = $h->templateItem->satuan ?? '';

                                                    $awal = $runningButuh;
                                                    if ($h->status === 'approved' || $h->status === 'resolved') {
                                                        if ($h->jenis === 'kelebihan') {
                                                            $akhir = $awal - $h->jumlah;
                                                        } else {
                                                            $akhir = $awal + $h->jumlah;
                                                        }
                                                        $runningButuh = $akhir;
                                                    } elseif ($h->status === 'pending') {
                                                        if ($h->jenis === 'kelebihan') {
                                                            $akhir = $awal - $h->jumlah;
                                                        } else {
                                                            $akhir = $awal + $h->jumlah;
                                                        }
                                                    } else {
                                                        $akhir = $awal;
                                                    }

                                                    $historyArray[] = [
                                                        'tanggal' => $h->created_at->format('d M Y, H:i'),
                                                        'jenis' => $h->jenis,
                                                        'jumlah' => $hKonv
                                                            ? formatIndonesianNumber($h->jumlah / $nk) .
                                                                ' ' .
                                                                $sk .
                                                                ' (' .
                                                                formatIndonesianNumber($h->jumlah) .
                                                                ' ' .
                                                                $satAsli .
                                                                ')'
                                                            : formatIndonesianNumber($h->jumlah) . ' ' . $satAsli,
                                                        'butuh_awal' => $hKonv
                                                            ? formatIndonesianNumber($awal / $nk) .
                                                                ' ' .
                                                                $sk .
                                                                ' (' .
                                                                formatIndonesianNumber($awal) .
                                                                ' ' .
                                                                $satAsli .
                                                                ')'
                                                            : formatIndonesianNumber($awal) . ' ' . $satAsli,
                                                        'butuh_akhir' => $hKonv
                                                            ? formatIndonesianNumber($akhir / $nk) .
                                                                ' ' .
                                                                $sk .
                                                                ' (' .
                                                                formatIndonesianNumber($akhir) .
                                                                ' ' .
                                                                $satAsli .
                                                                ')'
                                                            : formatIndonesianNumber($akhir) . ' ' . $satAsli,
                                                        'catatan' => $h->catatan ?? '-',
                                                        'status' => $h->status,
                                                        'is_current' => $h->id_handler === $item->id_handler,
                                                    ];
                                                }
                                                $historyJson = json_encode($historyArray);

                                                $detailJumlahText =
                                                    formatIndonesianNumber($item->jumlah) .
                                                    ' ' .
                                                    ($item->templateItem->satuan ?? '');
                                                if ($hasKonversi) {
                                                    $detailJumlahText =
                                                        formatIndonesianNumber($item->jumlah / $nilaiKonversi) .
                                                        ' ' .
                                                        $satuanKonversi .
                                                        ' (' .
                                                        $detailJumlahText .
                                                        ')';
                                                }
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                        </td>
                                        <td>
                                            @if ($item->status === 'pending')
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal" data-bs-target="#resolveModal"
                                                    data-id="{{ $item->id_handler }}"
                                                    data-bahan="{{ $item->templateItem->nama_bahan }}"
                                                    data-jenis="{{ $item->jenis === 'kelebihan' ? 'Kelebihan (+)' : 'Kekurangan (-)' }}"
                                                    data-jumlah="{{ $detailJumlahText }}"
                                                    data-catatan="{{ $item->catatan }}"
                                                    data-history="{{ $historyJson }}" title="Proses">
                                                    Proses
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-icon btn-outline-info rounded-circle"
                                                    data-bs-toggle="modal" data-bs-target="#resolveModal"
                                                    data-id="{{ $item->id_handler }}"
                                                    data-bahan="{{ $item->templateItem->nama_bahan }}"
                                                    data-jenis="{{ $item->jenis === 'kelebihan' ? 'Kelebihan (+)' : 'Kekurangan (-)' }}"
                                                    data-jumlah="{{ $detailJumlahText }}"
                                                    data-catatan="{{ $item->catatan }}"
                                                    data-history="{{ $historyJson }}" data-readonly="true"
                                                    title="Detail Riwayat">
                                                    <i class="bx bx-info-circle"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($laporan->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $laporan->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-6">
                        <i class="bx bx-receipt bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">Tidak ada laporan stok</h5>
                        <p class="text-muted mb-3">
                            Belum ada laporan dari Produksi yang sesuai dengan filter.
                        </p>
                        @if (request()->hasAny(['status', 'jenis', 'date_from', 'date_to']))
                            <a href="{{ route('admin-gudang.laporan-stok.index', ['dapur' => $dapur->id_dapur]) }}"
                                class="btn btn-outline-primary">
                                Reset Filter
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="modal fade" id="resolveModal" tabindex="-1" aria-labelledby="resolveModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="resolveForm" method="POST" action="">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="resolveModalLabel">
                                Proses Laporan Handler Stok
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body bg-light pb-2">
                            <div class="mb-3 row">
                                <div class="col-4 fw-semibold text-muted">Bahan</div>
                                <div class="col-8 fw-bold" id="detailBahan"></div>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-4 fw-semibold text-muted">Jenis</div>
                                <div class="col-8 fw-bold" id="detailJenis"></div>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-4 fw-semibold text-muted">Jumlah</div>
                                <div class="col-8 fw-bold" id="detailJumlah"></div>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-4 fw-semibold text-muted">Catatan Produksi</div>
                                <div class="col-8" id="detailCatatan" style="white-space: pre-line;"></div>
                            </div>

                            <div class="mt-4 border-top pt-3" id="historyContainerWrapper" style="display: none;">
                                <h6 class="fw-bold text-primary mb-2"><i class="bx bx-history me-1"></i>Riwayat Pengajuan
                                    Bahan Ini</h6>
                                <div class="d-flex border rounded mb-3 bg-white" id="modalHistorySummary">
                                    <div class="flex-fill text-center py-2 border-end">
                                        <div class="text-muted" style="font-size: 0.7rem;">BUTUH Awal</div>
                                        <div class="fw-bold text-primary small" id="summaryButuhAwal">-</div>
                                    </div>
                                    <div class="flex-fill text-center py-2">
                                        <div class="text-muted" style="font-size: 0.7rem;">BUTUH Akhir</div>
                                        <div class="fw-bold text-dark small" id="summaryButuhAkhir">-</div>
                                    </div>
                                </div>
                                <div class="bg-white rounded border" style="max-height: 250px; overflow-y: auto;"
                                    id="detailHistoryTimeline">
                                </div>
                            </div>
                        </div>
                        <div class="modal-body border-top" id="actionSection">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Aksi <span class="text-danger">*</span></label>
                                <select name="action" class="form-select" id="resolveActionSelect" required>
                                    <option value="">-- Pilih Aksi --</option>
                                    <option value="approve">Setujui (Stok Gudang Akan Disesuaikan)</option>
                                    <option value="reject">Tolak (Tidak Ada Perubahan Stok)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="catatan_admin" class="form-label fw-bold">
                                    Catatan Admin Gudang (Opsional)
                                </label>
                                <textarea id="catatan_admin" name="catatan_admin" class="form-control" rows="3"
                                    placeholder="Masukkan pesan balasan..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer" id="modalFooterSection">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-primary px-4" id="btnSubmitResolve">
                                Simpan Proses
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <style>
            .table-warning-subtle {
                background-color: rgba(255, 243, 205, 0.3) !important;
            }

            .pulse {
                animation: pulse 2s infinite;
            }

            @keyframes pulse {
                0% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.5;
                }

                100% {
                    opacity: 1;
                }
            }

            /* Timeline styles */
            .timeline-item {
                position: relative;
                padding-left: 1.5rem;
                border-left: 2px solid #696cff;
                padding-bottom: 1rem;
            }

            .timeline-item::before {
                content: '';
                position: absolute;
                left: -6px;
                top: 0;
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background-color: #696cff;
            }

            .timeline-item:last-child {
                border-left: 2px solid transparent;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const resolveModal = document.getElementById('resolveModal');
                if (resolveModal) {
                    resolveModal.addEventListener('show.bs.modal', function(event) {
                        const button = event.relatedTarget;
                        const id = button.getAttribute('data-id');
                        const bahan = button.getAttribute('data-bahan');
                        const jenis = button.getAttribute('data-jenis');
                        const jumlah = button.getAttribute('data-jumlah');
                        const catatan = button.getAttribute('data-catatan');
                        const historyStr = button.getAttribute('data-history');
                        const isReadonly = button.getAttribute('data-readonly') === 'true';

                        document.getElementById('detailBahan').innerHTML = bahan;
                        document.getElementById('detailJenis').innerHTML = jenis || '-';
                        document.getElementById('detailJumlah').innerHTML = jumlah || '-';
                        document.getElementById('detailCatatan').innerHTML = catatan || '-';

                        const actionSection = document.getElementById('actionSection');
                        const btnSubmitResolve = document.getElementById('btnSubmitResolve');
                        const resolveActionSelect = document.getElementById('resolveActionSelect');

                        if (isReadonly) {
                            actionSection.style.display = 'none';
                            btnSubmitResolve.style.display = 'none';
                            resolveActionSelect.removeAttribute('required');
                            document.getElementById('resolveModalLabel').innerText =
                                'Detail Riwayat Handler Stok';
                        } else {
                            actionSection.style.display = 'block';
                            btnSubmitResolve.style.display = 'inline-block';
                            resolveActionSelect.setAttribute('required', 'required');
                            document.getElementById('resolveModalLabel').innerText =
                                'Proses Laporan Handler Stok';

                            const baseUrl =
                                '{{ route('admin-gudang.laporan-stok.resolve', ['dapur' => $dapur->id_dapur, 'laporan' => 999999]) }}';
                            document.getElementById('resolveForm').action = baseUrl.replace('999999', id);
                        }

                        const historyWrapper = document.getElementById('historyContainerWrapper');
                        const historyTimeline = document.getElementById('detailHistoryTimeline');
                        historyTimeline.innerHTML = '';

                        document.getElementById('summaryButuhAwal').textContent = '-';
                        document.getElementById('summaryButuhAkhir').textContent = '-';

                        if (historyStr) {
                            try {
                                const historyData = JSON.parse(historyStr);
                                if (historyData.length > 0) {
                                    const firstItem = historyData[0];
                                    const lastItem = historyData[historyData.length - 1];
                                    document.getElementById('summaryButuhAwal').textContent = firstItem
                                        .butuh_awal;
                                    document.getElementById('summaryButuhAkhir').textContent = lastItem
                                        .butuh_akhir;

                                    historyData.slice().reverse().forEach((item, index) => {
                                        const isKelebihan = item.jenis === 'kelebihan';
                                        const sign = isKelebihan ? '−' : '+';
                                        const signColor = isKelebihan ? 'success' : 'danger';

                                        let statusBadge = '';
                                        let statusLabel = '';
                                        if (item.status === 'pending') {
                                            statusBadge = 'bg-warning';
                                            statusLabel = 'Menunggu';
                                        } else if (item.status === 'approved' || item.status ===
                                            'resolved') {
                                            statusBadge = 'bg-success';
                                            statusLabel = 'Disetujui';
                                        } else {
                                            statusBadge = 'bg-danger';
                                            statusLabel = 'Ditolak';
                                        }

                                        let currentHighlight = item.is_current && !isReadonly ?
                                            'bg-light' : '';

                                        historyTimeline.innerHTML += `
                                            <div class="border p-2 px-3 ${currentHighlight} ${index < historyData.length - 1 ? 'border-bottom' : ''}" style="font-size: 0.82rem;">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-bold text-dark">
                                                        <span class="badge bg-label-primary rounded-pill me-1" style="font-size: 0.6rem;">Pengajuan ${historyData.length - index}</span>
                                                        <span class="text-${signColor} fw-bold">${isKelebihan ? 'Kelebihan' : 'Kekurangan'}</span>
                                                        ${item.is_current && !isReadonly ? '<span class="badge bg-primary ms-1" style="font-size:0.55rem;">Diproses</span>' : ''}
                                                    </span>
                                                    <span class="badge ${statusBadge} rounded-pill" style="font-size: 0.6rem;">${statusLabel}</span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 mb-2 py-1 px-2 bg-light rounded" style="font-size: 0.78rem;">
                                                    <span class="text-muted">${item.butuh_awal}</span>
                                                    <span class="text-${signColor} fw-bold">${sign} ${item.jumlah}</span>
                                                    <i class="bx bx-right-arrow-alt text-muted"></i>
                                                    <span class="fw-bold text-dark">${item.butuh_akhir}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-muted fst-italic" style="font-size: 0.7rem;">
                                                        <i class="bx bx-message-detail me-1"></i>${item.catatan}
                                                    </span>
                                                    <span class="text-muted" style="font-size: 0.65rem;">
                                                        <i class="bx bx-time me-1"></i>${item.tanggal}
                                                    </span>
                                                </div>
                                            </div>`;
                                    });
                                    historyWrapper.style.display = 'block';
                                } else {
                                    historyWrapper.style.display = 'none';
                                }
                            } catch (e) {
                                historyWrapper.style.display = 'none';
                            }
                        } else {
                            historyWrapper.style.display = 'none';
                        }

                    });
                }
            });
        </script>
    @endsection
