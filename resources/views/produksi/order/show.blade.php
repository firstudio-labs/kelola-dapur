@extends('template_produksi.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    @php
        $totalPorsiBesar = $transaksi->detailTransaksiDapur->where('tipe_porsi', 'besar')->first()->jumlah_porsi ?? 0;
        $totalPorsiKecil = $transaksi->detailTransaksiDapur->where('tipe_porsi', 'kecil')->first()->jumlah_porsi ?? 0;
        $totalKeseluruhan = $totalPorsiBesar + $totalPorsiKecil;

        $mapStatusProduksi = [
            'stok_kurang' => ['badge' => 'bg-danger', 'icon' => 'bx-error-circle', 'text' => 'Stok Kurang', 'pct' => 0, 'color' => 'danger'],
            'belum_dibuat' => ['badge' => 'bg-secondary', 'icon' => 'bx-time', 'text' => 'Belum Dibuat', 'pct' => 25, 'color' => 'secondary'],
            'sedang_dibuat' => ['badge' => 'bg-warning', 'icon' => 'bx-loader-circle', 'text' => 'Sedang Dibuat', 'pct' => 60, 'color' => 'warning'],
            'selesai' => ['badge' => 'bg-success', 'icon' => 'bx-check-circle', 'text' => 'Selesai', 'pct' => 100, 'color' => 'success']
        ];
        $prodStatus = $mapStatusProduksi[$order->status] ?? ['badge' => 'bg-secondary', 'icon' => 'bx-help-circle', 'text' => 'Unknown', 'pct' => 0, 'color' => 'secondary'];
    @endphp

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm border-top border-4 border-primary">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="mb-2 mb-md-0 text-center text-md-start">
                            <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-2">
                                <a href="{{ route('produksi.order.index') }}" class="text-secondary fw-semibold text-decoration-none me-2">
                                    <i class="bx bx-arrow-back me-1"></i> Kembali
                                </a>
                                <span class="text-muted opacity-50">|</span>
                                <span class="ms-2 text-muted fw-semibold small">Order #{{ $order->id_order }}</span>
                            </div>
                            <h5 class="mb-1 fw-bold text-dark">Detail Order Produksi</h5>
                            <p class="mb-0 text-muted small">
                                <i class="bx bx-calendar text-primary me-1"></i>{{ $transaksi->tanggal_transaksi->format('d M Y') }} &nbsp;·&nbsp; {{ $transaksi->dapur->nama_dapur }}
                            </p>
                        </div>
                        <div class="d-flex justify-content-center gap-4 text-center">
                            <div>
                                <h4 class="mb-0 fw-bold text-primary">{{ number_format($totalKeseluruhan, 0, ',', '.') }}</h4>
                                <small class="text-muted" style="font-size: 11px;">Total Porsi</small>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold text-success">{{ number_format($totalPorsiBesar, 0, ',', '.') }}</h4>
                                <small class="text-muted" style="font-size: 11px;">Besar</small>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold text-warning">{{ number_format($totalPorsiKecil, 0, ',', '.') }}</h4>
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
                            <div class="progress-bar bg-{{ $prodStatus['color'] }}" role="progressbar" style="width: {{ $prodStatus['pct'] }}%" aria-valuenow="{{ $prodStatus['pct'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row g-4 mb-4">
        <div class="col-md-5">
            <div class="card h-100 shadow-none border">
                <div class="card-header border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="bx bx-bowl-hot me-1"></i> Detail Menu
                    </h6>
                </div>
                <div class="card-body p-3">
                    @php
                        $porsiBesar = $transaksi->detailTransaksiDapur->where('tipe_porsi', 'besar');
                        $porsiKecil = $transaksi->detailTransaksiDapur->where('tipe_porsi', 'kecil');
                    @endphp

                    @if($porsiBesar->count() > 0)
                        <small class="text-success fw-bold d-block mb-2">PORSI BESAR</small>
                        <div class="list-group list-group-flush mb-3">
                            @foreach($porsiBesar as $detail)
                                <div class="list-group-item px-0 py-2 border-0 d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        @if($detail->menuMakanan->gambar_url)
                                            <img src="{{ $detail->menuMakanan->gambar_url }}" class="rounded" style="object-fit: cover;">
                                        @else
                                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-dish"></i></span>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 small fw-bold">{{ $detail->menuMakanan->nama_menu }}</h6>
                                        <span class="badge bg-label-success" style="font-size: 0.6rem;">{{ $detail->jumlah_porsi }} Porsi</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($porsiKecil->count() > 0)
                        <small class="text-warning fw-bold d-block mb-2">PORSI KECIL</small>
                        <div class="list-group list-group-flush">
                            @foreach($porsiKecil as $detail)
                                <div class="list-group-item px-0 py-2 border-0 d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        @if($detail->menuMakanan->gambar_url)
                                            <img src="{{ $detail->menuMakanan->gambar_url }}" class="rounded" style="object-fit: cover;">
                                        @else
                                            <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-dish"></i></span>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 small fw-bold">{{ $detail->menuMakanan->nama_menu }}</h6>
                                        <span class="badge bg-label-warning" style="font-size: 0.6rem;">{{ $detail->jumlah_porsi }} Porsi</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-7">
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
                                    <th class="py-2 text-center" style="font-size: 0.7rem;">STOK</th>
                                    <th class="pe-3 py-2 text-end" style="font-size: 0.7rem;">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bahanKebutuhan as $idTemplate => $bahan)
                                    @php
                                        $bahanArray = is_object($bahan) ? (array) $bahan : $bahan;
                                        $satuan = $bahanArray['satuan'] ?? 'N/A';
                                        $namaBahan = $bahanArray['nama_bahan'] ?? 'Unknown';
                                        $totalButuh = isset($bahanArray['total_kebutuhan']) ? (float)$bahanArray['total_kebutuhan'] : 0;
                                        
                                        $stockInfo = $stockData[$idTemplate] ?? ['stock_tersedia' => 0, 'stock_aktual' => 0, 'sufficient' => false];
                                        $stockTersedia = (float)$stockInfo['stock_tersedia'];
                                        
                                        $isSufficient = $stockTersedia >= $totalButuh;

                                        // Helper to format numbers cleanly
                                        $formatVal = function($val) {
                                            if (floor($val) == $val) return number_format($val, 0, ',', '.');
                                            return rtrim(rtrim(number_format($val, 3, ',', '.'), '0'), ',');
                                        };
                                    @endphp
                                    <tr>
                                        <td class="ps-3 py-2">
                                            <span class="fw-bold text-dark small">{{ $namaBahan }}</span>
                                        </td>
                                        <td class="py-2 text-center small">
                                            @if(isset($stockInfo['konversi_nilai']) && $stockInfo['konversi_nilai'] > 0)
                                                @php
                                                    $butuhKonversi = $totalButuh / $stockInfo['konversi_nilai'];
                                                @endphp
                                                <div class="fw-bold">
                                                    {{ $formatVal($butuhKonversi) }} {{ $stockInfo['konversi_satuan'] }}
                                                </div>
                                                <div class="text-muted fw-normal" style="font-size: 10px;">
                                                    ({{ $formatVal($totalButuh) }} {{ $satuan }})
                                                </div>
                                            @else
                                                <div>
                                                    {{ $formatVal($totalButuh) }} <small class="text-muted">{{ $satuan }}</small>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-2 text-center small {{ $isSufficient ? 'text-success' : 'text-danger' }}">
                                            @if(isset($stockInfo['konversi_nilai']) && $stockInfo['konversi_nilai'] > 0)
                                                @php
                                                    $nilaiKonversi = $stockTersedia / $stockInfo['konversi_nilai'];
                                                @endphp
                                                <div class="fw-bold">
                                                    {{ $formatVal($nilaiKonversi) }} {{ $stockInfo['konversi_satuan'] }}
                                                </div>
                                                <div class="text-muted fw-normal" style="font-size: 10px;">
                                                    ({{ $formatVal($stockTersedia) }} {{ $satuan }})
                                                </div>
                                            @else
                                                <div>
                                                    {{ $formatVal($stockTersedia) }} <small class="text-muted">{{ $satuan }}</small>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="pe-3 py-2 text-end">
                                            @if($isSufficient)
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
                        @foreach($transaksi->detailTransaksiDapur as $detail)
                            <div class="col-md-6 col-lg-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar avatar-sm me-2">
                                            @if($detail->menuMakanan->gambar_url)
                                                <img src="{{ $detail->menuMakanan->gambar_url }}" class="rounded" style="object-fit: cover;">
                                            @else
                                                <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-dish"></i></span>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold small">{{ $detail->menuMakanan->nama_menu }}</h6>
                                            <span class="badge {{ $detail->tipe_porsi == 'besar' ? 'bg-label-success' : 'bg-label-warning' }}" style="font-size: 0.6rem;">
                                                {{ ucfirst($detail->tipe_porsi) }} - {{ $detail->jumlah_porsi }} Porsi
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tbody>
                                                @foreach($detail->menuMakanan->bahanMenu as $bahanMenu)
                                                    @php
                                                        $idTemplate = $bahanMenu->id_template_item;
                                                        $namaBahan = $bahanMenu->templateItem->nama_bahan;
                                                        $satuan = $bahanMenu->templateItem->satuan;
                                                        $kebutuhanPerPorsi = $bahanMenu->jumlah_per_porsi;
                                                        $totalButuhMenu = $kebutuhanPerPorsi * $detail->jumlah_porsi;
                                                        
                                                        $stockInfo = $stockData[$idTemplate] ?? null;
                                                    @endphp
                                                    <tr>
                                                        <td class="ps-0 py-1" style="font-size: 0.75rem;">
                                                            <span class="text-dark">{{ $namaBahan }}</span>
                                                        </td>
                                                        <td class="pe-0 py-1 text-end" style="font-size: 0.75rem;">
                                                            @if(isset($stockInfo['konversi_nilai']) && $stockInfo['konversi_nilai'] > 0)
                                                                @php
                                                                    $butuhKonversiMenu = $totalButuhMenu / $stockInfo['konversi_nilai'];
                                                                @endphp
                                                                <span class="fw-bold">{{ $formatVal($butuhKonversiMenu) }} {{ $stockInfo['konversi_satuan'] }}</span>
                                                                <div class="text-muted mt-1" style="font-size: 0.65rem;">({{ $formatVal($totalButuhMenu) }} {{ $satuan }})</div>
                                                            @else
                                                                <span class="fw-bold">{{ $formatVal($totalButuhMenu) }}</span> <span class="text-muted">{{ $satuan }}</span>
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
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 position-relative">
                        
                        <div class="d-none d-md-block position-absolute top-50 start-0 end-0 translate-middle-y" style="height: 2px; background: #e9ecef; z-index: 0;"></div>

                        @php
                            $isDoneProd = $order->status === 'selesai';
                            $isDoneDist = $order->distribusiOrder && $order->distribusiOrder->status === 'sudah_dikirim';
                        @endphp

                        <div class="text-center position-relative" style="z-index: 1;">
                            <div class="avatar avatar-md mx-auto mb-2">
                                <span class="avatar-initial rounded-circle {{ $isDoneProd ? 'bg-success' : 'bg-primary' }} text-white border border-4 border-white shadow-lg">
                                    <i class="bx bx-package fs-4 fw-bold"></i>
                                </span>
                            </div>
                            <h6 class="mb-0 small fw-bold">PRODUKSI</h6>
                            <span class="badge {{ $prodStatus['badge'] }} text-white mt-1 fw-bold shadow-sm" style="font-size: 0.65rem;">{{ $prodStatus['text'] }}</span>
                        </div>

                        <div class="text-center position-relative" style="z-index: 1;">
                            @php
                                $distStatus = $order->distribusiOrder ? $order->distribusiOrder->status : 'belum_dikirim';
                                $mapDist = [
                                    'belum_dikirim' => ['bg' => 'bg-secondary', 'icon' => 'bx-time-five', 'text' => 'Belum Dikirim'],
                                    'sedang_dikirim' => ['bg' => 'bg-warning', 'icon' => 'bx-cycling', 'text' => 'Proses'],
                                    'sudah_dikirim' => ['bg' => 'bg-success', 'icon' => 'bx-check-double', 'text' => 'Selesai']
                                ];
                                $dData = $mapDist[$distStatus] ?? $mapDist['belum_dikirim'];
                            @endphp
                            <div class="avatar avatar-md mx-auto mb-2">
                                <span class="avatar-initial rounded-circle {{ $dData['bg'] }} text-white border border-4 border-white shadow-lg">
                                    <i class="bx {{ $dData['icon'] }} fs-4 fw-bold"></i>
                                </span>
                            </div>
                            <h6 class="mb-0 small fw-bold">DISTRIBUSI</h6>
                            <span class="badge {{ $dData['bg'] }} text-white mt-1 fw-bold shadow-sm" style="font-size: 0.65rem;">{{ $dData['text'] }}</span>
                        </div>

                        <div class="text-center position-relative" style="z-index: 1;">
                            <div class="avatar avatar-md mx-auto mb-2">
                                <span class="avatar-initial rounded-circle {{ $isDoneDist ? 'bg-success' : 'bg-secondary' }} text-white border border-4 border-white shadow-lg">
                                    <i class="bx bx-badge-check fs-4 fw-bold"></i>
                                </span>
                            </div>
                            <h6 class="mb-0 small fw-bold">SELESAI</h6>
                            <span class="badge {{ $isDoneDist ? 'bg-label-success' : 'bg-label-secondary' }} mt-1 fw-bold text-dark" style="font-size: 0.65rem;">{{ $isDoneDist ? 'Terverifikasi' : 'Pending' }}</span>
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
                            <img src="{{ $dok->url }}" alt="Dokumentasi" class="rounded border" style="width: 80px; height: 80px; object-fit: cover;">
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="row mb-5 mb-md-2">
        <div class="col-12">
            <div class="card shadow-none border">
                <div class="card-body p-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                    <a href="{{ route('produksi.order.index') }}" class="btn btn-outline-secondary w-100 w-md-auto">
                        <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar
                    </a>
                    
                    @if($order->status !== 'stok_kurang' && $order->status !== 'selesai')
                        <button type="button"
                            class="btn btn-primary w-100 w-md-auto"
                            data-bs-toggle="modal"
                            data-bs-target="#updateStatusModal"
                            data-id="{{ $order->id_order }}"
                            data-status="{{ $order->status }}"
                            data-catatan="{{ $order->catatan }}">
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
                        <label class="form-label fw-semibold">Foto Dokumentasi <span class="text-danger">*</span></label>
                        <input type="file" name="dokumentasi[]" class="form-control" multiple accept="image/*" id="inputDokumentasi">
                        <small class="text-muted">Wajib untuk status Selesai. Minimal 1 foto.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan (opsional)</label>
                        <textarea name="catatan" class="form-control" id="modalCatatan" rows="3" placeholder="Tambahkan catatan jika ada..."></textarea>
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const updateStatusModal = document.getElementById('updateStatusModal');
        const modalStatus = document.getElementById('modalStatus');
        const dokumentasiWrapper = document.getElementById('dokumentasiWrapper');
        const inputDokumentasi = document.getElementById('inputDokumentasi');

        if (updateStatusModal) {
            updateStatusModal.addEventListener('show.bs.modal', function (event) {
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
                    nextStatusLabel = 'Ubah status menjadi <strong>Sedang Dibuat</strong> (Stok akan otomatis dikurangi)';
                    alertClass = 'alert-warning';
                } else if (currentStatus === 'sedang_dibuat') {
                    nextStatus = 'selesai';
                    nextStatusLabel = 'Ubah status menjadi <strong>Selesai</strong> (Pastikan dokumentasi sudah lengkap)';
                    alertClass = 'alert-success';
                }

                statusInput.value = nextStatus;
                statusText.innerHTML = nextStatusLabel;
                statusInfo.className = `alert ${alertClass} py-2 px-3 mb-0 d-flex align-items-center`;

                document.getElementById('modalCatatan').value = (catatan && catatan !== 'null') ? catatan : '';
                
                toggleDokumentasi(nextStatus);

                const baseUrl = '{{ route("produksi.order.update-status", ":id") }}';
                document.getElementById('updateStatusForm').action = baseUrl.replace(':id', orderId);
            });

            // No longer needed as it's not a select, but we keep the function for toggleDokumentasi
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
@endpush

