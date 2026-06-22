@extends('template_produksi.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @php
            $totalPorsiBesar =
                $transaksi->detailTransaksiDapur->where('tipe_porsi', 'besar')->first()->jumlah_porsi ?? 0;
            $totalPorsiKecil =
                $transaksi->detailTransaksiDapur->where('tipe_porsi', 'kecil')->first()->jumlah_porsi ?? 0;
            $totalKeseluruhan = $totalPorsiBesar;

            $hasPendingShortage = $transaksi->laporanKekuranganStock->where('status', 'pending')->count() > 0;
            $handlerEnabled = $order->status !== 'stok_kurang' || !$hasPendingShortage;

            $mapStatusProduksi = [
                'stok_kurang' => [
                    'badge' => 'bg-danger',
                    'icon' => 'bx-error-circle',
                    'text' => 'Stok Kurang',
                    'pct' => 0,
                    'color' => 'danger',
                ],
                'belum_dibuat' => [
                    'badge' => 'bg-secondary',
                    'icon' => 'bx-time',
                    'text' => 'Belum Dibuat',
                    'pct' => 25,
                    'color' => 'secondary',
                ],
                'sedang_dibuat' => [
                    'badge' => 'bg-warning',
                    'icon' => 'bx-loader-circle',
                    'text' => 'Sedang Dibuat',
                    'pct' => 60,
                    'color' => 'warning',
                ],
                'selesai' => [
                    'badge' => 'bg-success',
                    'icon' => 'bx-check-circle',
                    'text' => 'Selesai',
                    'pct' => 100,
                    'color' => 'success',
                ],
            ];
            $prodStatus = $mapStatusProduksi[$order->status] ?? [
                'badge' => 'bg-secondary',
                'icon' => 'bx-help-circle',
                'text' => 'Unknown',
                'pct' => 0,
                'color' => 'secondary',
            ];
        @endphp

        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm border-top border-4 border-primary">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div class="mb-2 mb-md-0 text-center text-md-start">
                                <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-2">
                                    <a href="{{ route('produksi.order.index') }}"
                                        class="text-secondary fw-semibold text-decoration-none me-2">
                                        <i class="bx bx-arrow-back me-1"></i> Kembali
                                    </a>
                                    <span class="text-muted opacity-50">|</span>
                                    <span class="ms-2 text-muted fw-semibold small">Order #{{ $order->id_order }}</span>
                                </div>
                                <h5 class="mb-1 fw-bold text-dark">Detail Order Produksi</h5>
                                <p class="mb-0 text-muted small">
                                    <i
                                        class="bx bx-calendar text-primary me-1"></i>{{ $transaksi->tanggal_transaksi->format('d M Y') }}
                                    &nbsp;·&nbsp; {{ $transaksi->dapur->nama_dapur }}
                                </p>
                            </div>
                            <div class="d-flex justify-content-center gap-4 text-center">
                                <div>
                                    <h4 class="mb-0 fw-bold text-primary">@formatNumber($totalKeseluruhan)</h4>
                                    <small class="text-muted" style="font-size: 11px;">Total Porsi</small>
                                </div>
                                <div>
                                    <h4 class="mb-0 fw-bold text-success">@formatNumber($totalPorsiBesar)</h4>
                                    <small class="text-muted" style="font-size: 11px;">Besar</small>
                                </div>
                                <div>
                                    <h4 class="mb-0 fw-bold text-warning">@formatNumber($totalPorsiKecil)</h4>
                                    <small class="text-muted" style="font-size: 11px;">Kecil</small>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge {{ $prodStatus['badge'] }} rounded-pill">
                                    <i class="bx {{ $prodStatus['icon'] }} me-1"></i>{{ $prodStatus['text'] }}
                                </span>
                                <small class="fw-semibold text-{{ $prodStatus['color'] }}">{{ $prodStatus['pct'] }}%</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-{{ $prodStatus['color'] }}" role="progressbar"
                                    style="width: {{ $prodStatus['pct'] }}%" aria-valuenow="{{ $prodStatus['pct'] }}"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card h-100 shadow-none border">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="bx bx-barcode me-1"></i> Kebutuhan Bahan
                        </h6>
                        <span class="badge bg-label-info">{{ count($bahanKebutuhan) }} Item</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3 py-2" style="font-size: 0.7rem;">BAHAN</th>
                                        <th class="py-2 text-center" style="font-size: 0.7rem;">BUTUH</th>
                                        @if ($order->status !== 'stok_kurang')
                                            <th class="py-2 text-center" style="font-size: 0.7rem;">ESTIMASI STOK</th>
                                        @endif
                                        <th class="py-2 text-center" style="font-size: 0.7rem;">STOK</th>
                                        <th class="pe-3 py-2 text-center" style="font-size: 0.7rem;">HANDLER BAHAN</th>
                                        <th class="pe-3 py-2 text-end" style="font-size: 0.7rem;">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bahanKebutuhan as $idTemplate => $bahan)
                                        @php
                                            $bahanArray = is_object($bahan) ? (array) $bahan : $bahan;
                                            $satuan = $bahanArray['satuan'] ?? 'N/A';
                                            $namaBahan = $bahanArray['nama_bahan'] ?? 'Unknown';
                                            $totalButuh = isset($bahanArray['total_kebutuhan'])
                                                ? (float) $bahanArray['total_kebutuhan']
                                                : 0;

                                            $stockInfo = $stockData[$idTemplate] ?? [
                                                'stock_tersedia' => 0,
                                                'stock_aktual' => 0,
                                                'sufficient' => false,
                                            ];
                                            $stockTersedia = (float) $stockInfo['stock_tersedia'];

                                            $formatVal = function ($val) {
                                                return formatIndonesianNumber($val);
                                            };

                                            $handlers = isset($handlerBahanData[$idTemplate])
                                                ? $handlerBahanData[$idTemplate]
                                                : collect();
                                            $handler = $handlers->last();

                                            $hasKonversi =
                                                isset($stockInfo['konversi_nilai']) && $stockInfo['konversi_nilai'] > 0;
                                            $nilaiKonversi = $hasKonversi ? $stockInfo['konversi_nilai'] : 1;
                                            $satuanDisplay = $hasKonversi ? $stockInfo['konversi_satuan'] : $satuan;

                                            $isPreProduksi = $order->status === 'belum_dibuat';
                                            $isInProduksi = in_array($order->status, ['sedang_dibuat', 'selesai']);

                                            $approvedHandlers = $handlers->filter(
                                                fn($h) => in_array($h->status, ['approved', 'resolved']),
                                            );
                                            $pendingHandler = $handlers->where('status', 'pending')->last();

                                            $approvedKekurangan = $approvedHandlers
                                                ->where('jenis', 'kekurangan')
                                                ->sum('jumlah');
                                            $approvedKelebihan = $approvedHandlers
                                                ->where('jenis', 'kelebihan')
                                                ->sum('jumlah');

                                            $pendingKekurangan =
                                                $pendingHandler && $pendingHandler->jenis === 'kekurangan'
                                                    ? $pendingHandler->jumlah
                                                    : 0;
                                            $pendingKelebihan =
                                                $pendingHandler && $pendingHandler->jenis === 'kelebihan'
                                                    ? $pendingHandler->jumlah
                                                    : 0;

                                            $adjustedButuhApproved =
                                                $totalButuh + $approvedKekurangan - $approvedKelebihan;

                                            $estimasiApproved = $isInProduksi
                                                ? $stockTersedia
                                                : $stockTersedia - $adjustedButuhApproved;
                                            $estimasiWithPending =
                                                $estimasiApproved - $pendingKekurangan + $pendingKelebihan;

                                            $estimasiStok = $pendingHandler ? $estimasiWithPending : $estimasiApproved;
                                            $estimasiStokIsPending = $pendingHandler !== null;

                                            $kekuranganReport = null;
                                            foreach ($handlers as $h) {
                                                if (
                                                    $h->laporanKekuranganStock &&
                                                    $h->laporanKekuranganStock->status === 'pending'
                                                ) {
                                                    $kekuranganReport = $h->laporanKekuranganStock;
                                                    break;
                                                }
                                            }

                                            if ($kekuranganReport) {
                                                $stockTersediaDisplay = (float) $kekuranganReport->jumlah_kurang;
                                                $isSufficient = false;
                                            } else {
                                                $stockTersediaDisplay = $stockTersedia;
                                                $isSufficient = $isInProduksi
                                                    ? $stockTersedia >= 0
                                                    : $stockTersedia >= $adjustedButuhApproved;
                                            }

                                            $steps = [];
                                            $steps[] = [
                                                'value' => $totalButuh,
                                                'formatted' => $formatVal($totalButuh / $nilaiKonversi),
                                                'type' => 'original',
                                                'status' => 'approved',
                                            ];
                                            foreach ($approvedHandlers as $ah) {
                                                if ($ah->jenis === 'kelebihan') {
                                                    $adjustedButuhApproved -= 0;
                                                    $steps[] = [
                                                        'value' => $ah->jumlah,
                                                        'formatted' => '-' . $formatVal($ah->jumlah / $nilaiKonversi),
                                                        'type' => 'kelebihan',
                                                        'status' => 'approved',
                                                    ];
                                                } else {
                                                    $steps[] = [
                                                        'value' => $ah->jumlah,
                                                        'formatted' => '+' . $formatVal($ah->jumlah / $nilaiKonversi),
                                                        'type' => 'kekurangan',
                                                        'status' => 'approved',
                                                    ];
                                                }
                                            }
                                            if ($pendingHandler) {
                                                $steps[] = [
                                                    'value' => $pendingHandler->jumlah,
                                                    'formatted' =>
                                                        ($pendingHandler->jenis === 'kelebihan' ? '-' : '+') .
                                                        $formatVal($pendingHandler->jumlah / $nilaiKonversi),
                                                    'type' => $pendingHandler->jenis,
                                                    'status' => 'pending',
                                                ];
                                            }

                                            $runningButuh = $totalButuh;
                                            $historyArray = [];
                                            foreach ($handlers as $h) {
                                                $awal = $runningButuh;
                                                if (in_array($h->status, ['approved', 'resolved'])) {
                                                    $akhir =
                                                        $h->jenis === 'kelebihan'
                                                            ? $awal - $h->jumlah
                                                            : $awal + $h->jumlah;
                                                    $runningButuh = $akhir;
                                                } elseif ($h->status === 'pending') {
                                                    $akhir =
                                                        $h->jenis === 'kelebihan'
                                                            ? $awal - $h->jumlah
                                                            : $awal + $h->jumlah;
                                                } else {
                                                    $akhir = $awal;
                                                }
                                                $historyArray[] = [
                                                    'tanggal' => $h->created_at->format('d M Y, H:i'),
                                                    'jenis' => $h->jenis,
                                                    'jumlah' =>
                                                        $formatVal($h->jumlah / $nilaiKonversi) . ' ' . $satuanDisplay,
                                                    'butuh_awal' =>
                                                        $formatVal($awal / $nilaiKonversi) . ' ' . $satuanDisplay,
                                                    'butuh_akhir' =>
                                                        $formatVal($akhir / $nilaiKonversi) . ' ' . $satuanDisplay,
                                                    'stok' =>
                                                        $formatVal($stockTersedia / $nilaiKonversi) .
                                                        ' ' .
                                                        $satuanDisplay,
                                                    'catatan' => $h->catatan ?? '-',
                                                    'status' => $h->status,
                                                ];
                                            }
                                            $historyJson = json_encode($historyArray);

                                            $estimasiSteps = [];
                                            $estimasiRawString = '';

                                            $estimasiSteps[] = [
                                                'value' => $stockTersedia,
                                                'formatted' => $formatVal($stockTersedia / $nilaiKonversi),
                                                'type' => 'stock',
                                                'status' => 'approved',
                                            ];

                                            if (!$isInProduksi) {
                                                $estimasiSteps[] = [
                                                    'value' => $totalButuh,
                                                    'formatted' => '-' . $formatVal($totalButuh / $nilaiKonversi),
                                                    'type' => 'butuh_original',
                                                    'status' => 'approved',
                                                ];
                                                foreach ($approvedHandlers as $ah) {
                                                    $estimasiSteps[] = [
                                                        'value' => $ah->jumlah,
                                                        'formatted' =>
                                                            ($ah->jenis === 'kelebihan' ? '+' : '-') .
                                                            $formatVal($ah->jumlah / $nilaiKonversi),
                                                        'type' => $ah->jenis,
                                                        'status' => 'approved',
                                                    ];
                                                }
                                            }

                                            if ($pendingHandler) {
                                                $estimasiSteps[] = [
                                                    'value' => $pendingHandler->jumlah,
                                                    'formatted' =>
                                                        ($pendingHandler->jenis === 'kelebihan' ? '+' : '-') .
                                                        $formatVal($pendingHandler->jumlah / $nilaiKonversi),
                                                    'type' => $pendingHandler->jenis,
                                                    'status' => 'pending',
                                                ];
                                            }

                                            if ($isInProduksi) {
                                                $estimasiRawString = $formatVal($stockTersedia);
                                                if ($pendingHandler) {
                                                    $estimasiRawString .=
                                                        ($pendingHandler->jenis === 'kelebihan' ? ' + ' : ' - ') .
                                                        $formatVal($pendingHandler->jumlah) .
                                                        ' (Estimasi)';
                                                }
                                                $estimasiRawString .= ' = ' . $formatVal($estimasiStok) . ' ' . $satuan;
                                            } else {
                                                $estimasiRawString =
                                                    $formatVal($stockTersedia) . ' - ' . $formatVal($totalButuh);
                                                foreach ($approvedHandlers as $ah) {
                                                    $estimasiRawString .=
                                                        ($ah->jenis === 'kelebihan' ? ' + ' : ' - ') .
                                                        $formatVal($ah->jumlah);
                                                }
                                                if ($pendingHandler) {
                                                    $estimasiRawString .=
                                                        ($pendingHandler->jenis === 'kelebihan' ? ' + ' : ' - ') .
                                                        $formatVal($pendingHandler->jumlah) .
                                                        ' (Estimasi)';
                                                }
                                                $estimasiRawString .= ' = ' . $formatVal($estimasiStok) . ' ' . $satuan;
                                            }

                                            $isPending = $estimasiStokIsPending;
                                            $estimasiButuh =
                                                $adjustedButuhApproved + $pendingKekurangan - $pendingKelebihan;

                                            $handlerEnabled = $order->status !== 'stok_kurang';
                                        @endphp
                                        <tr>
                                            <td class="ps-3 py-2">
                                                <span class="fw-bold text-dark small">{{ $namaBahan }}</span>
                                            </td>
                                            <td class="py-2 text-center small">
                                                <div class="fw-bold">
                                                    @foreach ($steps as $step)
                                                        @if ($step['type'] === 'original')
                                                            <span
                                                                class="text-muted small fw-normal">{{ $step['formatted'] }}</span>
                                                        @else
                                                            @if ($step['status'] === 'pending')
                                                                <span
                                                                    class="text-warning text-xs fw-semibold position-relative"
                                                                    style="cursor: help;" title="Estimasi Perhitungan">
                                                                    {{ $step['formatted'] }}
                                                                    <i class="bx bx-time-five text-warning"
                                                                        style="font-size: 11px;"></i>
                                                                </span>
                                                            @else
                                                                <span
                                                                    class="{{ $step['type'] === 'kelebihan' ? 'text-success' : 'text-danger' }} text-xs fw-semibold">
                                                                    {{ $step['formatted'] }}
                                                                </span>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                    =
                                                    @if ($isPending)
                                                        <span class="text-primary fw-bold" style="cursor: help;"
                                                            title="Estimasi Perhitungan">
                                                            {{ $formatVal($estimasiButuh / $nilaiKonversi) }}
                                                            <i class="bx bx-time-five text-warning"
                                                                style="font-size: 12px; margin-left: 2px;"></i>
                                                        </span>
                                                    @else
                                                        <span class="text-primary fw-bold">
                                                            {{ $formatVal($adjustedButuhApproved / $nilaiKonversi) }}
                                                        </span>
                                                    @endif
                                                    {{ $satuanDisplay }}
                                                </div>
                                                @php
                                                    $rawString = $formatVal($totalButuh);
                                                    foreach ($approvedHandlers as $ah) {
                                                        $rawString .=
                                                            ($ah->jenis === 'kelebihan' ? ' - ' : ' + ') .
                                                            $formatVal($ah->jumlah);
                                                    }
                                                    if ($pendingHandler) {
                                                        $rawString .=
                                                            ($pendingHandler->jenis === 'kelebihan' ? ' - ' : ' + ') .
                                                            $formatVal($pendingHandler->jumlah) .
                                                            ' (Estimasi)';
                                                    }
                                                    $rawString .=
                                                        ' = ' .
                                                        $formatVal(
                                                            $isPending ? $estimasiButuh : $adjustedButuhApproved,
                                                        ) .
                                                        ' ' .
                                                        $satuan;
                                                @endphp
                                                <div class="text-muted fw-normal" style="font-size: 10px;">
                                                    ({{ $rawString }})
                                                </div>
                                            </td>

                                            {{-- ── KOLOM ESTIMASI STOK ── --}}
                                            @if ($order->status !== 'stok_kurang')
                                                <td class="py-2 text-center small">
                                                    @if ($isPreProduksi || $isInProduksi)
                                                        <div class="fw-bold">
                                                            @foreach ($estimasiSteps as $step)
                                                                @if ($step['type'] === 'stock')
                                                                    <span
                                                                        class="text-muted small fw-normal">{{ $step['formatted'] }}</span>
                                                                @else
                                                                    @if ($step['status'] === 'pending')
                                                                        <span
                                                                            class="text-warning text-xs fw-semibold position-relative"
                                                                            style="cursor: help;"
                                                                            title="Estimasi Perhitungan">
                                                                            {{ $step['formatted'] }}
                                                                            <i class="bx bx-time-five text-warning"
                                                                                style="font-size: 11px;"></i>
                                                                        </span>
                                                                    @else
                                                                        <span
                                                                            class="text-{{ in_array($step['type'], ['kelebihan', 'stock']) ? 'success' : 'danger' }} text-xs fw-semibold">
                                                                            {{ $step['formatted'] }}
                                                                        </span>
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                            =
                                                            @if ($estimasiStokIsPending)
                                                                <span
                                                                    class="fw-bold {{ $estimasiStok >= 0 ? 'text-success' : 'text-danger' }}"
                                                                    style="cursor: help;" title="Estimasi Perhitungan">
                                                                    @if ($estimasiStok < 0)
                                                                        -
                                                                    @endif
                                                                    {{ $formatVal(abs($estimasiStok) / $nilaiKonversi) }}
                                                                    <i class="bx bx-time-five text-warning"
                                                                        style="font-size: 12px; margin-left: 2px;"></i>
                                                                </span>
                                                            @else
                                                                <span
                                                                    class="fw-bold {{ $estimasiStok >= 0 ? 'text-success' : 'text-danger' }}">
                                                                    @if ($estimasiStok < 0)
                                                                        -
                                                                    @endif
                                                                    {{ $formatVal(abs($estimasiStok) / $nilaiKonversi) }}
                                                                </span>
                                                            @endif
                                                            {{ $satuanDisplay }}
                                                        </div>
                                                        <div class="text-muted fw-normal" style="font-size: 10px;">
                                                            ({{ $estimasiRawString }})
                                                        </div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            @endif

                                            <td
                                                class="py-2 text-center small {{ $isSufficient ? 'text-success' : 'text-danger' }}">
                                                @if ($hasKonversi)
                                                    @php
                                                        $nilaiKonversiStok =
                                                            $stockTersediaDisplay / $stockInfo['konversi_nilai'];
                                                    @endphp
                                                    <div class="fw-bold">
                                                        {{ $formatVal($nilaiKonversiStok) }}
                                                        {{ $stockInfo['konversi_satuan'] }}
                                                        @if (!$isSufficient)
                                                            <i class="bx bx-cart text-warning ms-1"
                                                                style="font-size: 1.1rem; vertical-align: middle;"></i>
                                                        @endif
                                                    </div>
                                                    <div class="text-muted fw-normal" style="font-size: 10px;">
                                                        ({{ $formatVal($stockTersediaDisplay) }} {{ $satuan }})
                                                    </div>
                                                @else
                                                    <div>
                                                        {{ $formatVal($stockTersediaDisplay) }} <small
                                                            class="text-muted">{{ $satuan }}</small>
                                                        @if (!$isSufficient)
                                                            <i class="bx bx-cart text-warning ms-1"
                                                                style="font-size: 1.1rem; vertical-align: middle;"></i>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="pe-3 py-2 text-center">
                                                @if ($handlerEnabled)
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        @if ($handler)
                                                            @php
                                                                $jumlahDisplay = $handler->jumlah / $nilaiKonversi;
                                                            @endphp
                                                            @if ($handler->jenis === 'kelebihan')
                                                                <i class="bx bx-plus-circle text-success"
                                                                    style="font-size:15px;" title="Kelebihan"></i>
                                                                <span class="text-success fw-bold"
                                                                    style="font-size:14px;">{{ $formatVal($jumlahDisplay) }}
                                                                    <small class="text-muted fw-normal"
                                                                        style="font-size:11px;">{{ $satuanDisplay }}</small></span>
                                                            @else
                                                                <i class="bx bx-minus-circle text-danger"
                                                                    style="font-size:15px;" title="Kekurangan"></i>
                                                                <span class="text-danger fw-bold"
                                                                    style="font-size:14px;">{{ $formatVal($jumlahDisplay) }}
                                                                    <small class="text-muted fw-normal"
                                                                        style="font-size:11px;">{{ $satuanDisplay }}</small></span>
                                                            @endif

                                                            @if ($handler->status === 'pending')
                                                                <i class="bx bx-time-five text-warning"
                                                                    style="font-size:15px;" title="Menunggu Approval"></i>
                                                                <button type="button"
                                                                    class="btn btn-xs btn-icon btn-outline-secondary rounded-circle p-0 ms-1"
                                                                    style="width:20px;height:20px;font-size:10px;"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#handlerBahanModal"
                                                                    data-id="{{ $idTemplate }}"
                                                                    data-nama="{{ $namaBahan }}"
                                                                    data-jenis="{{ $handler->jenis }}"
                                                                    data-satuan="{{ $satuanDisplay }}"
                                                                    data-jumlah="{{ $jumlahDisplay }}"
                                                                    data-catatan="{{ $handler->catatan }}"
                                                                    data-handler-id="{{ $handler->id_handler }}"
                                                                    data-konversi="{{ $nilaiKonversi }}"
                                                                    data-history="{{ $historyJson }}"
                                                                    title="Edit Handler">
                                                                    <i class="bx bx-edit-alt"
                                                                        style="font-size:10px; pointer-events: none;"></i>
                                                                </button>
                                                            @elseif($handler->status === 'approved')
                                                                <i class="bx bx-check-circle text-success"
                                                                    style="font-size:15px;" title="Disetujui"></i>
                                                            @else
                                                                <i class="bx bx-x-circle text-danger"
                                                                    style="font-size:15px;" title="Ditolak"></i>
                                                            @endif

                                                            <button type="button"
                                                                class="btn btn-xs btn-icon btn-outline-info rounded-circle ms-1 p-0"
                                                                style="width:20px;height:20px;font-size:10px;"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#handlerHistoryModal"
                                                                data-id="{{ $idTemplate }}"
                                                                data-nama="{{ $namaBahan }}"
                                                                data-history="{{ $historyJson }}"
                                                                title="Detail Riwayat Handler">
                                                                <i class="bx bx-info-circle"
                                                                    style="font-size:12px; pointer-events: none;"></i>
                                                            </button>
                                                        @endif

                                                        @if (!$handler || $handler->status !== 'pending')
                                                            <div class="d-flex align-items-center justify-content-center gap-1 {{ $handler ? 'ms-1 border-start ps-2' : '' }}"
                                                                role="group">
                                                                <button type="button"
                                                                    class="btn btn-xs btn-icon btn-outline-success rounded-circle"
                                                                    style="width:22px; height:22px;"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#handlerBahanModal"
                                                                    data-id="{{ $idTemplate }}"
                                                                    data-nama="{{ $namaBahan }}"
                                                                    data-jenis="kelebihan"
                                                                    data-satuan="{{ $satuanDisplay }}"
                                                                    data-konversi="{{ $nilaiKonversi }}"
                                                                    data-history="{{ $historyJson }}"
                                                                    title="Ajukan Kelebihan">
                                                                    <i class="bx bx-plus" style="font-size:12px;"></i>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-xs btn-icon btn-outline-danger rounded-circle"
                                                                    style="width:22px; height:22px;"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#handlerBahanModal"
                                                                    data-id="{{ $idTemplate }}"
                                                                    data-nama="{{ $namaBahan }}"
                                                                    data-jenis="kekurangan"
                                                                    data-satuan="{{ $satuanDisplay }}"
                                                                    data-konversi="{{ $nilaiKonversi }}"
                                                                    data-history="{{ $historyJson }}"
                                                                    title="Ajukan Kekurangan">
                                                                    <i class="bx bx-minus" style="font-size:12px;"></i>
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="badge bg-label-warning" style="font-size: 0.7rem;"
                                                        title="Handler tersedia setelah laporan kekurangan stok diselesaikan">
                                                        <i class="bx bx-lock-alt me-1"></i>Menunggu Update Stok
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="pe-3 py-2 text-end">
                                                @if ($isSufficient)
                                                    <i class="bx bx-check-circle text-success" title="Cukup"></i>
                                                @else
                                                    <i class="bx bx-error-circle text-danger" title="Kurang"></i>
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

        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card h-100 shadow-none border">
                    <div class="card-header border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="bx bx-list-ul me-1"></i> Kebutuhan Bahan per Menu
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-4">
                            @php
                                $porsiBesarDetails = $transaksi->detailTransaksiDapur->where('tipe_porsi', 'besar');
                                $porsiKecilDetails = $transaksi->detailTransaksiDapur->where('tipe_porsi', 'kecil');
                            @endphp

                            @if ($porsiBesarDetails->count() > 0)
                                <div class="col-12">
                                    <h6 class="text-success mb-0 fw-bold"><i class="bx bx-chevron-right"></i> Porsi Besar
                                        : @formatNumber($totalPorsiBesar) Porsi</h6>
                                </div>
                                @foreach ($porsiBesarDetails as $detail)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="border rounded p-3 h-100 bg-light-success">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar avatar-sm me-2">
                                                    @if ($detail->menuMakanan->gambar_url)
                                                        <img src="{{ $detail->menuMakanan->gambar_url }}" class="rounded"
                                                            style="object-fit: cover;">
                                                    @else
                                                        <span class="avatar-initial rounded bg-label-success"><i
                                                                class="bx bx-dish"></i></span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold small">{{ $detail->menuMakanan->nama_menu }}
                                                    </h6>
                                                    <span class="badge bg-label-success" style="font-size: 0.6rem;">
                                                        Besar
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tbody>
                                                        @foreach ($detail->menuMakanan->bahanMenu as $bahanMenu)
                                                            @php
                                                                $idTemplate = $bahanMenu->id_template_item;
                                                                $namaBahan = $bahanMenu->templateItem->nama_bahan;
                                                                $satuan = $bahanMenu->templateItem->satuan;
                                                                $kebutuhanPerPorsi = $bahanMenu->jumlah_per_porsi;
                                                                $totalButuhMenu =
                                                                    $kebutuhanPerPorsi * $detail->jumlah_porsi;

                                                                $stockInfo = $stockData[$idTemplate] ?? null;
                                                            @endphp
                                                            <tr>
                                                                <td class="ps-0 py-1" style="font-size: 0.75rem;">
                                                                    <span class="text-dark">{{ $namaBahan }}</span>
                                                                </td>
                                                                <td class="pe-0 py-1 text-end"
                                                                    style="font-size: 0.75rem;">
                                                                    @if (isset($stockInfo['konversi_nilai']) && $stockInfo['konversi_nilai'] > 0)
                                                                        @php
                                                                            $butuhKonversiMenu =
                                                                                $totalButuhMenu /
                                                                                $stockInfo['konversi_nilai'];
                                                                        @endphp
                                                                        <span
                                                                            class="fw-bold">{{ $formatVal($butuhKonversiMenu) }}
                                                                            {{ $stockInfo['konversi_satuan'] }}</span>
                                                                        <div class="text-muted mt-1"
                                                                            style="font-size: 0.65rem;">
                                                                            ({{ $formatVal($totalButuhMenu) }}
                                                                            {{ $satuan }})
                                                                        </div>
                                                                    @else
                                                                        <span
                                                                            class="fw-bold">{{ $formatVal($totalButuhMenu) }}</span>
                                                                        <span
                                                                            class="text-muted">{{ $satuan }}</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if ($porsiKecilDetails->count() > 0)
                                <div class="col-12 mt-4">
                                    <h6 class="text-warning mb-0 fw-bold"><i class="bx bx-chevron-right"></i> Porsi Kecil
                                        : @formatNumber($totalPorsiKecil) Porsi</h6>
                                </div>
                                @foreach ($porsiKecilDetails as $detail)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="border rounded p-3 h-100 bg-light-warning">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar avatar-sm me-2">
                                                    @if ($detail->menuMakanan->gambar_url)
                                                        <img src="{{ $detail->menuMakanan->gambar_url }}" class="rounded"
                                                            style="object-fit: cover;">
                                                    @else
                                                        <span class="avatar-initial rounded bg-label-warning"><i
                                                                class="bx bx-dish"></i></span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold small">{{ $detail->menuMakanan->nama_menu }}
                                                    </h6>
                                                    <span class="badge bg-label-warning" style="font-size: 0.6rem;">
                                                        Kecil
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tbody>
                                                        @foreach ($detail->menuMakanan->bahanMenu as $bahanMenu)
                                                            @php
                                                                $idTemplate = $bahanMenu->id_template_item;
                                                                $namaBahan = $bahanMenu->templateItem->nama_bahan;
                                                                $satuan = $bahanMenu->templateItem->satuan;
                                                                $kebutuhanPerPorsi = $bahanMenu->jumlah_per_porsi;
                                                                $totalButuhMenu =
                                                                    $kebutuhanPerPorsi * $detail->jumlah_porsi;

                                                                $stockInfo = $stockData[$idTemplate] ?? null;
                                                            @endphp
                                                            <tr>
                                                                <td class="ps-0 py-1" style="font-size: 0.75rem;">
                                                                    <span class="text-dark">{{ $namaBahan }}</span>
                                                                </td>
                                                                <td class="pe-0 py-1 text-end"
                                                                    style="font-size: 0.75rem;">
                                                                    @if (isset($stockInfo['konversi_nilai']) && $stockInfo['konversi_nilai'] > 0)
                                                                        @php
                                                                            $butuhKonversiMenu =
                                                                                $totalButuhMenu /
                                                                                $stockInfo['konversi_nilai'];
                                                                        @endphp
                                                                        <span
                                                                            class="fw-bold">{{ $formatVal($butuhKonversiMenu) }}
                                                                            {{ $stockInfo['konversi_satuan'] }}</span>
                                                                        <div class="text-muted mt-1"
                                                                            style="font-size: 0.65rem;">
                                                                            ({{ $formatVal($totalButuhMenu) }}
                                                                            {{ $satuan }})
                                                                        </div>
                                                                    @else
                                                                        <span
                                                                            class="fw-bold">{{ $formatVal($totalButuhMenu) }}</span>
                                                                        <span
                                                                            class="text-muted">{{ $satuan }}</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card h-100 shadow-none border">
                    <div class="card-header border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="bx bx-git-branch me-1"></i> Alur Proses (Status Terkini)
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div
                            class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 position-relative">

                            <div class="d-none d-md-block position-absolute top-50 start-0 end-0 translate-middle-y"
                                style="height: 2px; background: #e9ecef; z-index: 0;"></div>

                            @php
                                $isDoneProd = $order->status === 'selesai';
                                $isDoneDist =
                                    $order->distribusiOrder && $order->distribusiOrder->status === 'sudah_dikirim';
                            @endphp

                            <div class="text-center position-relative" style="z-index: 1;">
                                <div class="avatar avatar-md mx-auto mb-2">
                                    <span
                                        class="avatar-initial rounded-circle {{ $isDoneProd ? 'bg-success' : 'bg-primary' }} text-white border border-4 border-white shadow-lg">
                                        <i class="bx bx-package fs-4 fw-bold"></i>
                                    </span>
                                </div>
                                <h6 class="mb-0 small fw-bold">PRODUKSI</h6>
                                <span class="badge {{ $prodStatus['badge'] }} text-white mt-1 fw-bold shadow-sm"
                                    style="font-size: 0.65rem;">{{ $prodStatus['text'] }}</span>
                            </div>

                            <div class="text-center position-relative" style="z-index: 1;">
                                @php
                                    $distStatus = $order->distribusiOrder
                                        ? $order->distribusiOrder->status
                                        : 'belum_dikirim';
                                    $mapDist = [
                                        'belum_dikirim' => [
                                            'bg' => 'bg-secondary',
                                            'icon' => 'bx-time-five',
                                            'text' => 'Belum Dikirim',
                                        ],
                                        'sedang_dikirim' => [
                                            'bg' => 'bg-warning',
                                            'icon' => 'bx-cycling',
                                            'text' => 'Proses',
                                        ],
                                        'sudah_dikirim' => [
                                            'bg' => 'bg-success',
                                            'icon' => 'bx-check-double',
                                            'text' => 'Selesai',
                                        ],
                                    ];
                                    $dData = $mapDist[$distStatus] ?? $mapDist['belum_dikirim'];
                                @endphp
                                <div class="avatar avatar-md mx-auto mb-2">
                                    <span
                                        class="avatar-initial rounded-circle {{ $dData['bg'] }} text-white border border-4 border-white shadow-lg">
                                        <i class="bx {{ $dData['icon'] }} fs-4 fw-bold"></i>
                                    </span>
                                </div>
                                <h6 class="mb-0 small fw-bold">DISTRIBUSI</h6>
                                <span class="badge {{ $dData['bg'] }} text-white mt-1 fw-bold shadow-sm"
                                    style="font-size: 0.65rem;">{{ $dData['text'] }}</span>
                            </div>

                            <div class="text-center position-relative" style="z-index: 1;">
                                <div class="avatar avatar-md mx-auto mb-2">
                                    <span
                                        class="avatar-initial rounded-circle {{ $isDoneDist ? 'bg-success' : 'bg-secondary' }} text-white border border-4 border-white shadow-lg">
                                        <i class="bx bx-badge-check fs-4 fw-bold"></i>
                                    </span>
                                </div>
                                <h6 class="mb-0 small fw-bold">SELESAI</h6>
                                <span
                                    class="badge {{ $isDoneDist ? 'bg-label-success' : 'bg-label-secondary' }} mt-1 fw-bold text-dark"
                                    style="font-size: 0.65rem;">{{ $isDoneDist ? 'Terverifikasi' : 'Pending' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($order->status === 'selesai' && $order->dokumentasi->count() > 0)
            <div class="card mb-4 border shadow-none">
                <div class="card-header border-bottom py-2">
                    <small class="text-muted fw-bold"><i class="bx bx-image me-1"></i>DOKUMENTASI PRODUKSI</small>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($order->dokumentasi as $dok)
                            <a href="{{ $dok->url }}" target="_blank" class="shadow-sm">
                                <img src="{{ $dok->url }}" alt="Dokumentasi" class="rounded border"
                                    style="width: 80px; height: 80px; object-fit: cover;">
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        @if ($order->status === 'selesai')
            <div class="card mb-4 border shadow-none">
                <div class="card-header border-bottom py-2">
                    <small class="text-muted fw-bold"><i class="bx bx-message-square-detail me-1"></i>ULASAN
                        PRODUKSI</small>
                </div>
                <div class="card-body p-3">
                    @if ($order->ulasan)
                        <div class="row g-3">
                            <div class="col-md-{{ $order->ulasan_foto ? '8' : '12' }}">
                                <div class="p-3 bg-lighter rounded border" style="min-height: 80px;">
                                    <p class="mb-0 text-dark small fst-italic">"{{ $order->ulasan }}"</p>
                                </div>
                            </div>
                            @if ($order->ulasan_foto)
                                <div class="col-md-4">
                                    <a href="{{ asset('storage/' . $order->ulasan_foto) }}" target="_blank"
                                        class="shadow-sm d-block">
                                        <img src="{{ asset('storage/' . $order->ulasan_foto) }}" alt="Foto Ulasan"
                                            class="rounded border w-100" style="height: 120px; object-fit: cover;">
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <form action="{{ route('produksi.order.ulasan', $order->id_order) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Bagaimana proses produksi ini berjalan?</label>
                                <textarea name="ulasan" class="form-control" rows="3"
                                    placeholder="Tuliskan ulasan atau kendala yang dihadapi..." required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Upload Foto Pendukung (Opsional)</label>
                                <input type="file" name="ulasan_foto" class="form-control form-control-sm"
                                    accept="image/*">
                                <small class="text-muted d-block mt-1" style="font-size: 0.65rem;">Maksimal ukuran 5MB.
                                    Foto akan otomatis dikompres.</small>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-send me-1"></i> Kirim
                                Ulasan</button>
                        </form>
                    @endif
                </div>
            </div>
        @endif

        <div class="row mb-5 mb-md-2">
            <div class="col-12">
                <div class="card shadow-none border">
                    <div
                        class="card-body p-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                        <a href="{{ route('produksi.order.index') }}" class="btn btn-outline-secondary w-100 w-md-auto">
                            <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar
                        </a>

                        @if ($order->status !== 'stok_kurang' && $order->status !== 'selesai')
                            <button type="button" class="btn btn-primary w-100 w-md-auto" data-bs-toggle="modal"
                                data-bs-target="#updateStatusModal" data-id="{{ $order->id_order }}"
                                data-status="{{ $order->status }}" data-catatan="{{ $order->catatan }}">
                                <i class="bx bx-edit me-1"></i> Update Status
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Update Status Order</h5>
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
                            <label class="form-label fw-semibold">Foto Dokumentasi <span
                                    class="text-danger">*</span></label>
                            <input type="file" name="dokumentasi[]" class="form-control" multiple accept="image/*"
                                id="inputDokumentasi">
                            <small class="text-muted">Wajib untuk status Selesai. Minimal 1 foto.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Catatan (opsional)</label>
                            <textarea name="catatan" class="form-control" id="modalCatatan" rows="3"
                                placeholder="Tambahkan catatan jika ada..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="handlerBahanModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold" id="handlerBahanModalTitle">Input Handler Bahan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="handlerBahanForm" method="POST"
                    action="{{ route('produksi.order.handler-bahan', $order->id_order) }}">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id_template_item" id="handlerBahanId">
                        <input type="hidden" name="id_handler" id="handlerBahanIdHandler">
                        <input type="hidden" name="jenis" id="handlerBahanJenis">
                        {{-- Hidden actual numeric value sent to server --}}
                        <input type="hidden" name="jumlah" id="handlerBahanJumlahReal">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Bahan</label>
                            <input type="text" class="form-control" id="handlerBahanNama" readonly
                                style="background-color:#f8f8f8;cursor:default;color:#555;">
                        </div>

                        <div class="mb-3" id="handlerBahanHistoryContainer" style="display: none;">
                            <label class="form-label fw-semibold">Riwayat Pengajuan Sebelumnya</label>
                            <div class="table-responsive border rounded bg-light"
                                style="max-height: 150px; overflow-y: auto;">
                                <table class="table table-sm table-borderless mb-0">
                                    <tbody id="handlerBahanHistoryList">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jumlah <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" inputmode="decimal" class="form-control"
                                    id="handlerBahanJumlahDisplay" placeholder="Masukkan jumlah..." autocomplete="off"
                                    required>
                                <span class="input-group-text fw-semibold" id="handlerBahanSatuan"
                                    style="min-width:60px;">—</span>
                            </div>
                            <small class="text-muted">Gunakan koma (,) untuk desimal. Contoh: 1.500,50</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Catatan (opsional)</label>
                            <textarea name="catatan" class="form-control" id="handlerBahanCatatan" rows="3"
                                placeholder="Tambahkan alasan/catatan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4" id="handlerBahanSubmitBtn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="handlerHistoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom bg-white py-3">
                    <div>
                        <h6 class="modal-title fw-bold mb-0">Detail Riwayat Handler Bahan</h6>
                        <small class="text-muted" id="historyModalNamaBahan">Nama Bahan</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    {{-- Summary Bar --}}
                    <div class="d-flex border-bottom" id="historyModalSummary">
                        <div class="flex-fill text-center py-3 border-end">
                            <div class="text-muted small mb-1">BUTUH Awal</div>
                            <div class="fw-bold text-primary" id="historyModalButuhAwal">-</div>
                        </div>
                        <div class="flex-fill text-center py-3 border-end">
                            <div class="text-muted small mb-1">STOK</div>
                            <div class="fw-bold" id="historyModalStok">-</div>
                        </div>
                        <div class="flex-fill text-center py-3">
                            <div class="text-muted small mb-1">BUTUH Akhir</div>
                            <div class="fw-bold text-dark" id="historyModalButuhAkhir">-</div>
                        </div>
                    </div>
                    {{-- Timeline Steps --}}
                    <div class="px-3 py-3" style="max-height: 400px; overflow-y: auto;" id="historyModalTimeline">
                        <!-- Steps injected via JS -->
                    </div>
                </div>
                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
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

            function toIndonesianFormat(num) {
                if (isNaN(num)) return '';
                let parts = parseFloat(num).toFixed(2).split('.');
                let decimal = parts[1].replace(/0+$/, '');
                let intPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                return decimal ? intPart + ',' + decimal : intPart;
            }

            function fromIndonesianFormat(str) {
                return str.replace(/\./g, '').replace(',', '.');
            }

            const handlerBahanModal = document.getElementById('handlerBahanModal');
            const handlerJumlahDisplay = document.getElementById('handlerBahanJumlahDisplay');
            const handlerJumlahReal = document.getElementById('handlerBahanJumlahReal');
            const handlerSatuan = document.getElementById('handlerBahanSatuan');
            let currentKonversi = 1;

            if (handlerBahanModal) {
                handlerJumlahDisplay.addEventListener('input', function() {
                    let cursorPos = this.selectionStart;
                    let raw = this.value;
                    let oldLen = raw.length;
                    raw = raw.replace(/[^\d,]/g, '');
                    let parts = raw.split(',');
                    if (parts.length > 2) {
                        raw = parts[0] + ',' + parts.slice(1).join('');
                        parts = raw.split(',');
                    }
                    let intPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    let formatted = parts.length > 1 ? intPart + ',' + parts[1] : intPart;

                    this.value = formatted;
                    let newLen = formatted.length;
                    let diff = newLen - oldLen;
                    this.setSelectionRange(cursorPos + diff, cursorPos + diff);
                    let floatVal = parseFloat(fromIndonesianFormat(formatted));
                    if (!isNaN(floatVal)) {
                        handlerJumlahReal.value = floatVal * currentKonversi;
                    } else {
                        handlerJumlahReal.value = '';
                    }
                });

                document.getElementById('handlerBahanForm').addEventListener('submit', function(e) {
                    const realVal = parseFloat(handlerJumlahReal.value);
                    if (!handlerJumlahReal.value || isNaN(realVal) || realVal <= 0) {
                        e.preventDefault();
                        handlerJumlahDisplay.classList.add('is-invalid');
                        handlerJumlahDisplay.focus();
                        return;
                    }
                    handlerJumlahDisplay.classList.remove('is-invalid');
                });

                handlerBahanModal.addEventListener('show.bs.modal', function(event) {
                    let button = event.relatedTarget;
                    if (!button) return;
                    if (!button.hasAttribute('data-id') && button.parentElement) {
                        button = button.closest('[data-bs-toggle="modal"]');
                    }
                    if (!button) return;

                    const idTemplate = button.getAttribute('data-id');
                    const namaBahan = button.getAttribute('data-nama');
                    const jenis = button.getAttribute('data-jenis');
                    const satuan = button.getAttribute('data-satuan') || '—';
                    const existingJumlah = button.getAttribute('data-jumlah') || '';
                    const existingCatatan = button.getAttribute('data-catatan') || '';
                    const handlerId = button.getAttribute('data-handler-id') || '';
                    const konversiStr = button.getAttribute('data-konversi');
                    const historyStr = button.getAttribute('data-history');

                    currentKonversi = (konversiStr && parseFloat(konversiStr) > 0) ? parseFloat(
                        konversiStr) : 1;

                    document.getElementById('handlerBahanId').value = idTemplate;
                    document.getElementById('handlerBahanIdHandler').value = handlerId;
                    document.getElementById('handlerBahanNama').value = namaBahan;
                    document.getElementById('handlerBahanJenis').value = jenis;
                    document.getElementById('handlerBahanCatatan').value = existingCatatan;

                    handlerSatuan.textContent = satuan;

                    if (existingJumlah && parseFloat(existingJumlah) > 0) {
                        handlerJumlahDisplay.value = toIndonesianFormat(existingJumlah);
                        handlerJumlahReal.value = parseFloat(existingJumlah) * currentKonversi;
                    } else {
                        handlerJumlahDisplay.value = '';
                        handlerJumlahReal.value = '';
                    }
                    handlerJumlahDisplay.classList.remove('is-invalid');

                    const isKelebihan = jenis === 'kelebihan';
                    const title = isKelebihan ? 'Input Kelebihan Bahan' : 'Input Kekurangan Bahan';
                    document.getElementById('handlerBahanModalTitle').innerText = title;
                    const submitBtn = document.getElementById('handlerBahanSubmitBtn');
                    submitBtn.className = isKelebihan ?
                        'btn btn-success px-4' :
                        'btn btn-danger px-4';

                    const historyContainer = document.getElementById('handlerBahanHistoryContainer');
                    const historyList = document.getElementById('handlerBahanHistoryList');
                    historyList.innerHTML = '';

                    if (historyStr) {
                        try {
                            const historyData = JSON.parse(historyStr);
                            if (historyData.length > 0) {
                                historyData.slice().reverse().forEach(item => {
                                    let statusBadge = '';
                                    if (item.status === 'pending') statusBadge =
                                        '<span class="badge bg-warning text-xs">Pending</span>';
                                    else if (item.status === 'approved' || item.status ===
                                        'resolved') statusBadge =
                                        '<span class="badge bg-success text-xs">Disetujui</span>';
                                    else statusBadge =
                                        '<span class="badge bg-danger text-xs">Ditolak</span>';

                                    let sign = item.jenis === 'kelebihan' ?
                                        '<span class="text-success fw-bold">+</span>' :
                                        '<span class="text-danger fw-bold">-</span>';

                                    historyList.innerHTML += `
                                    <tr class="border-bottom">
                                        <td class="py-2" style="font-size: 0.75rem;">
                                            <div class="fw-semibold text-dark">${sign} ${item.jumlah}</div>
                                            <div class="text-muted" style="font-size: 0.65rem;">${item.tanggal}</div>
                                        </td>
                                        <td class="py-2 text-end" style="font-size: 0.75rem;">
                                            ${statusBadge}
                                        </td>
                                    </tr>
                                `;
                                });
                                historyContainer.style.display = 'block';
                            } else {
                                historyContainer.style.display = 'none';
                            }
                        } catch (e) {
                            historyContainer.style.display = 'none';
                        }
                    } else {
                        historyContainer.style.display = 'none';
                    }
                });

                handlerBahanModal.addEventListener('hidden.bs.modal', function() {
                    handlerJumlahDisplay.value = '';
                    handlerJumlahReal.value = '';
                    document.getElementById('handlerBahanIdHandler').value = '';
                    handlerJumlahDisplay.classList.remove('is-invalid');
                    document.getElementById('handlerBahanHistoryContainer').style.display = 'none';
                });
            }

            const handlerHistoryModal = document.getElementById('handlerHistoryModal');
            if (handlerHistoryModal) {
                handlerHistoryModal.addEventListener('show.bs.modal', function(event) {
                    let button = event.relatedTarget;
                    if (!button) return;
                    if (!button.hasAttribute('data-id') && button.parentElement) {
                        button = button.closest('[data-bs-toggle="modal"]');
                    }
                    if (!button) return;

                    const namaBahan = button.getAttribute('data-nama');
                    const historyStr = button.getAttribute('data-history');

                    document.getElementById('historyModalNamaBahan').innerText = namaBahan;
                    const timeline = document.getElementById('historyModalTimeline');
                    timeline.innerHTML = '';

                    document.getElementById('historyModalButuhAwal').textContent = '-';
                    document.getElementById('historyModalStok').textContent = '-';
                    document.getElementById('historyModalButuhAkhir').textContent = '-';
                    document.getElementById('historyModalStok').className = 'fw-bold';

                    if (historyStr) {
                        try {
                            const historyData = JSON.parse(historyStr);
                            if (historyData.length > 0) {
                                const firstItem = historyData[0];
                                const lastItem = historyData[historyData.length - 1];
                                document.getElementById('historyModalButuhAwal').textContent = firstItem
                                    .butuh_awal;
                                document.getElementById('historyModalStok').textContent = firstItem.stok;
                                document.getElementById('historyModalButuhAkhir').textContent = lastItem
                                    .butuh_akhir;

                                const stokEl = document.getElementById('historyModalStok');
                                stokEl.className = 'fw-bold text-success';

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

                                    let stepHtml = `
                                    <div class="mb-2 bg-white rounded shadow-sm border p-3" style="font-size: 0.82rem;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold text-dark">
                                                <span class="badge bg-label-primary rounded-pill me-1" style="font-size: 0.65rem;">Pengajuan ${historyData.length - index}</span>
                                                <span class="text-${signColor} fw-bold">${isKelebihan ? 'Kelebihan' : 'Kekurangan'}</span>
                                            </span>
                                            <span class="badge ${statusBadge} rounded-pill" style="font-size: 0.65rem;">${statusLabel}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 mb-2 py-2 px-2 bg-light rounded" style="font-size: 0.8rem;">
                                            <span class="text-muted">${item.butuh_awal}</span>
                                            <span class="text-${signColor} fw-bold">${sign} ${item.jumlah}</span>
                                            <i class="bx bx-right-arrow-alt text-muted"></i>
                                            <span class="fw-bold text-dark">${item.butuh_akhir}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted" style="font-size: 0.72rem;">
                                                <i class="bx bx-message-detail me-1"></i>${item.catatan}
                                            </span>
                                            <span class="text-muted" style="font-size: 0.68rem;">
                                                <i class="bx bx-time me-1"></i>${item.tanggal}
                                            </span>
                                        </div>
                                    </div>`;
                                    timeline.innerHTML += stepHtml;
                                });
                            } else {
                                timeline.innerHTML =
                                    '<div class="text-center text-muted py-4"><i class="bx bx-info-circle me-1"></i>Belum ada riwayat handler.</div>';
                            }
                        } catch (e) {
                            timeline.innerHTML =
                                '<div class="text-center text-danger py-4"><i class="bx bx-error-circle me-1"></i>Gagal memuat riwayat.</div>';
                        }
                    }
                });
            }
        });
    </script>
@endpush
