@extends("template_kepala_dapur.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a
                                href="{{ route("kepala-dapur.dashboard", $currentDapur->id_dapur ?? (auth()->user()->userRole->id_dapur ?? null)) }}"
                                class="text-muted me-2"
                            >
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <a
                                href="{{ route("kepala-dapur.laporan-kekurangan.index") }}"
                                class="text-muted me-2"
                            >
                                Laporan Kekurangan Stok
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Detail</span>
                        </nav>
                        <h4 class="mb-1">
                            Detail Laporan Kekurangan Stok -
                            {{ $transaksi->nama_paket }}
                        </h4>
                        <p class="mb-0 text-muted">
                            Transaksi ID: {{ $transaksi->id_transaksi }} |
                            Dapur: {{ $currentDapur->nama_dapur ?? "Dapur" }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if (session("success"))
            <div
                class="alert alert-success alert-dismissible mb-4"
                role="alert"
            >
                {{ session("success") }}
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"
                ></button>
            </div>
        @endif

        @if (session("error"))
            <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                {{ session("error") }}
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"
                ></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Aksi</h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2">
                    <a
                        href="{{ route("kepala-dapur.laporan-kekurangan.export-pdf", $transaksi) }}"
                        class="btn btn-outline-primary"
                    >
                        <i class="bx bx-file me-1"></i>
                        Export PDF
                    </a>
                    <a
                        href="{{ route("kepala-dapur.laporan-kekurangan.export-csv", $transaksi) }}"
                        class="btn btn-outline-primary"
                    >
                        <i class="bx bx-download me-1"></i>
                        Export CSV
                    </a>
                    @if ($laporan->where("status", "pending")->isNotEmpty())
                        <button
                            type="button"
                            class="btn btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#resolveModal"
                        >
                            <i class="bx bx-check-double me-1"></i>
                            Selesaikan
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Informasi Transaksi</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <strong>ID Transaksi:</strong>
                            {{ $transaksi->id_transaksi }}
                        </p>
                        
                        <p>
                            <strong>Tanggal Transaksi:</strong>
                            {{ $transaksi->tanggal_transaksi->format("d M Y") }}
                        </p>
                        <p>
                            <strong>Total Porsi:</strong>
                            @formatNumber($transaksi->total_porsi)
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <strong>Dibuat Oleh:</strong>
                            {{ $transaksi->createdBy->nama }}
                        </p>
                        <p>
                            <strong>Status:</strong>
                            @if ($laporan->where("status", "pending")->isNotEmpty())
                                <span class="badge bg-warning">Pending</span>
                            @else
                                <span class="badge bg-success">Resolved</span>
                            @endif
                            @if ($laporan->contains(function($item) { return $item->isPurchasedByAkuntan(); }))
                                <small class="text-muted d-block mt-1">
                                    <i class="bx bx-wallet-alt text-primary me-1"></i>Akuntan
                                </small>
                            @endif
                        </p>
                        <p>
                            <strong>Jumlah Kekurangan Bahan:</strong>
                            @formatNumber($laporan->count())
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @php
            $normalLaporan = $laporan->filter(fn($item) => is_null($item->id_handler));
            $handlerLaporan = $laporan->filter(fn($item) => !is_null($item->id_handler));
        @endphp

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bx bx-package me-1"></i>Detail Kekurangan Stok</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Bahan</th>
                                <th>Jumlah Dibutuhkan</th>
                                <th>Jumlah Tersedia</th>
                                <th>Jumlah Kurang</th>
                                <th>Satuan</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($normalLaporan as $item)
                                <tr
                                    class="{{ $item->status === "pending" ? "table-warning-subtle" : "" }}"
                                >
                                    <td>
                                        {{ $item->templateItem->nama_bahan }}
                                    </td>
                                    <td>@formatNumber($item->jumlah_dibutuhkan)</td>
                                    <td>@formatNumber($item->jumlah_tersedia)</td>
                                    <td>@formatNumber($item->jumlah_kurang)</td>
                                    <td>{{ $item->satuan }}</td>
                                    <td>
                                        @if ($item->status === "pending")
                                            <span class="badge bg-warning">
                                                Pending
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                Resolved
                                            </span>
                                        @endif
                                        @if ($item->isPurchasedByAkuntan())
                                            <small class="text-muted d-block mt-1">
                                                <i class="bx bx-wallet-alt me-1"></i>Akuntan
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $item->keterangan_resolve ?? "-" }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="7"
                                        class="text-center text-muted"
                                    >
                                        Tidak ada data kekurangan stok ditemukan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @php
                    $accountingTransactionsNormal = collect();
                    foreach ($normalLaporan as $item) {
                        foreach ($item->accountingTransactionShortages as $sh) {
                            if ($sh->transaction) {
                                $accountingTransactionsNormal->put($sh->transaction->id, $sh->transaction);
                            }
                        }
                    }
                @endphp

                @if ($accountingTransactionsNormal->isNotEmpty())
                    <hr class="my-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><i class="bx bx-wallet-alt me-1"></i> Realisasi Transaksi Oleh Akuntan</label>
                        <p class="text-muted small mb-0">Rincian data transaksi pembelian yang dicatat oleh Akuntan untuk menyelesaikan kekurangan stok ini:</p>
                    </div>
                    @foreach ($accountingTransactionsNormal as $tx)
                        <div class="p-3 border rounded mb-3 bg-light">
                            <div class="row g-3 mb-3">
                                <div class="col-6 col-md-3">
                                    <span class="text-muted small d-block">No. Bukti / Voucher</span>
                                    <span class="fw-bold text-dark">{{ $tx->no_bukti ?? 'Tanpa No. Bukti' }}</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-muted small d-block">Tanggal Transaksi</span>
                                    <span class="fw-bold text-dark">{{ $tx->date->format('d M Y') }}</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-muted small d-block">Metode Kas / Rekening</span>
                                    <span class="fw-bold text-dark">{{ $tx->cashAccount->name ?? '-' }}</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-muted small d-block">Total Nominal Transaksi</span>
                                    <span class="fw-bold text-danger">@rupiah($tx->credit)</span>
                                </div>
                                <div class="col-12">
                                    <span class="text-muted small d-block">Uraian / Deskripsi</span>
                                    <span class="text-dark">{{ $tx->description ?? '-' }}</span>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle bg-white mb-0" style="font-size: 0.85rem;">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th style="width: 20%;">Bahan Baku</th>
                                            <th style="width: 14%;">Kekurangan</th>
                                            <th style="width: 22%;">Harga Satuan</th>
                                            <th style="width: 22%;">Jumlah Dibeli</th>
                                            <th style="width: 22%;">Nominal Pembelian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tx->shortages as $sh)
                                            <tr>
                                                <td>{{ $sh->laporanKekurangan->templateItem->nama_bahan ?? '-' }}</td>
                                                <td class="text-center">{{ isset($sh->laporanKekurangan->jumlah_kurang) ? formatIndonesianNumber($sh->laporanKekurangan->jumlah_kurang) : '-' }} {{ $sh->laporanKekurangan->satuan ?? '' }}</td>
                                                <td class="text-end">@rupiah($sh->harga_satuan)</td>
                                                <td class="text-center">{{ formatIndonesianNumber($sh->qty_dibeli) }} {{ $sh->laporanKekurangan->satuan ?? '' }}</td>
                                                <td class="text-end fw-semibold">@rupiah($sh->nominal)</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        @if($handlerLaporan->isNotEmpty())
        <div class="card mt-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="bx bx-transfer me-1"></i>Detail Handler Kekurangan Stok</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Bahan</th>
                                <th>Jumlah Dibutuhkan</th>
                                <th>Jumlah Tersedia</th>
                                <th>Jumlah Kurang</th>
                                <th>Satuan</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($handlerLaporan as $item)
                                <tr
                                    class="{{ $item->status === "pending" ? "table-warning-subtle" : "" }}"
                                >
                                    <td>
                                        {{ $item->templateItem->nama_bahan }} - Handler
                                    </td>
                                    <td>@formatNumber($item->jumlah_dibutuhkan)</td>
                                    <td>@formatNumber($item->jumlah_tersedia)</td>
                                    <td>@formatNumber($item->jumlah_kurang)</td>
                                    <td>{{ $item->satuan }}</td>
                                    <td>
                                        @if ($item->status === "pending")
                                            <span class="badge bg-warning">
                                                Pending
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                Resolved
                                            </span>
                                        @endif
                                        @if ($item->isPurchasedByAkuntan())
                                            <small class="text-muted d-block mt-1">
                                                <i class="bx bx-wallet-alt me-1"></i>Akuntan
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $item->keterangan_resolve ?? "-" }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @php
                    $accountingTransactionsHandler = collect();
                    foreach ($handlerLaporan as $item) {
                        foreach ($item->accountingTransactionShortages as $sh) {
                            if ($sh->transaction) {
                                $accountingTransactionsHandler->put($sh->transaction->id, $sh->transaction);
                            }
                        }
                    }
                @endphp

                @if ($accountingTransactionsHandler->isNotEmpty())
                    <hr class="my-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><i class="bx bx-wallet-alt me-1"></i> Realisasi Transaksi Oleh Akuntan (Handler)</label>
                        <p class="text-muted small mb-0">Rincian data transaksi pembelian yang dicatat oleh Akuntan untuk menyelesaikan kekurangan stok ini:</p>
                    </div>
                    @foreach ($accountingTransactionsHandler as $tx)
                        <div class="p-3 border rounded mb-3 bg-light">
                            <div class="row g-3 mb-3">
                                <div class="col-6 col-md-3">
                                    <span class="text-muted small d-block">No. Bukti / Voucher</span>
                                    <span class="fw-bold text-dark">{{ $tx->no_bukti ?? 'Tanpa No. Bukti' }}</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-muted small d-block">Tanggal Transaksi</span>
                                    <span class="fw-bold text-dark">{{ $tx->date->format('d M Y') }}</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-muted small d-block">Metode Kas / Rekening</span>
                                    <span class="fw-bold text-dark">{{ $tx->cashAccount->name ?? '-' }}</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-muted small d-block">Total Nominal Transaksi</span>
                                    <span class="fw-bold text-danger">@rupiah($tx->credit)</span>
                                </div>
                                <div class="col-12">
                                    <span class="text-muted small d-block">Uraian / Deskripsi</span>
                                    <span class="text-dark">{{ $tx->description ?? '-' }}</span>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle bg-white mb-0" style="font-size: 0.85rem;">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th style="width: 20%;">Bahan Baku</th>
                                            <th style="width: 14%;">Kekurangan</th>
                                            <th style="width: 22%;">Harga Satuan</th>
                                            <th style="width: 22%;">Jumlah Dibeli</th>
                                            <th style="width: 22%;">Nominal Pembelian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tx->shortages as $sh)
                                            <tr>
                                                <td>{{ $sh->laporanKekurangan->templateItem->nama_bahan ?? '-' }}</td>
                                                <td class="text-center">{{ isset($sh->laporanKekurangan->jumlah_kurang) ? formatIndonesianNumber($sh->laporanKekurangan->jumlah_kurang) : '-' }} {{ $sh->laporanKekurangan->satuan ?? '' }}</td>
                                                <td class="text-end">@rupiah($sh->harga_satuan)</td>
                                                <td class="text-center">{{ formatIndonesianNumber($sh->qty_dibeli) }} {{ $sh->laporanKekurangan->satuan ?? '' }}</td>
                                                <td class="text-end fw-semibold">@rupiah($sh->nominal)</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        @endif

        <div
            class="modal fade"
            id="resolveModal"
            tabindex="-1"
            aria-labelledby="resolveModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <form
                        id="resolveForm"
                        method="POST"
                        action="{{ route("kepala-dapur.laporan-kekurangan.bulk-resolve") }}"
                    >
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="resolveModalLabel">
                                Selesaikan Laporan Kekurangan
                            </h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                
                            </div>
                            <div class="mb-3">
                                <label
                                    for="resolveCreatedBy"
                                    class="form-label"
                                >
                                    Dibuat Oleh
                                </label>
                                <input
                                    type="text"
                                    id="resolveCreatedBy"
                                    class="form-control"
                                    value="{{ $transaksi->createdBy->nama }}"
                                    readonly
                                />
                            </div>
                            <div class="mb-3">
                                <label
                                    for="keterangan_resolve"
                                    class="form-label"
                                >
                                    Keterangan (Opsional)
                                </label>
                                <textarea
                                    id="keterangan_resolve"
                                    name="catatan"
                                    class="form-control"
                                    rows="4"
                                ></textarea>
                            </div>
                            @foreach ($laporan->where("status", "pending") as $item)
                                <input
                                    type="hidden"
                                    name="laporan_ids[]"
                                    value="{{ $item->id_laporan }}"
                                />
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                class="btn btn-primary"
                                {{ $laporan->where("status", "pending")->isEmpty() ? "disabled" : "" }}
                            >
                                Selesaikan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css"
    />

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
            transition:
                transform 0.2s ease,
                opacity 0.2s ease;
        }
        .action-btn:hover:not(.disabled) {
            transform: scale(1.1);
            opacity: 0.9;
        }
        .table td {
            vertical-align: middle;
        }
        .table-warning-subtle {
            background-color: rgba(255, 243, 205, 0.3) !important;
        }
        .table-info-subtle {
            background-color: rgba(13, 202, 240, 0.08) !important;
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
    </style>

    <script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Choices.js
            const selects = document.querySelectorAll('.choices-select');
            selects.forEach((select) => {
                new Choices(select, {
                    searchEnabled: false,
                    itemSelectText: '',
                    shouldSort: false,
                });
            });

            // Initialize Bootstrap tooltips
            const tooltipTriggerList = document.querySelectorAll(
                '[data-bs-toggle="tooltip"]',
            );
            const tooltipList = [...tooltipTriggerList].map(
                (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl),
            );

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach((alert) => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
@endsection
