@extends("template_admin_gudang.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a
                                href="{{ route("admin-gudang.dashboard", $dapur->id_dapur ?? (auth()->user()->userRole->id_dapur ?? null)) }}"
                                class="text-muted me-2"
                            >
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <a
                                href="{{ route('admin-gudang.laporan-kekurangan.index', ['dapur' => $dapur->id_dapur]) }}"
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
                            Dapur: {{ $dapur->nama_dapur ?? 'Dapur' }}
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
                        href="{{ route('admin-gudang.laporan-kekurangan.export-pdf', ['dapur' => $dapur->id_dapur, 'transaksi' => $transaksi->id_transaksi]) }}"
                        class="btn btn-outline-primary"
                    >
                        <i class="bx bx-file me-1"></i>
                        Export PDF
                    </a>
                    <a
                        href="{{ route('admin-gudang.laporan-kekurangan.export-csv', ['dapur' => $dapur->id_dapur, 'transaksi' => $transaksi->id_transaksi]) }}"
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
                            {{ $transaksi->total_porsi }}
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
                        </p>
                        <p>
                            <strong>Jumlah Kekurangan Bahan:</strong>
                            {{ $laporan->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Detail Kekurangan Stok</h5>
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
                                <th>Satuan Asli</th>
                                <th>Konversi (Kekurangan)</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($laporan as $item)
                                <tr
                                    class="{{ $item->status === "pending" ? "table-warning-subtle" : "" }}"
                                >
                                    @php
                                        $stockItem = \App\Models\StockItem::where('id_dapur', $dapur->id_dapur)->where('id_template_item', $item->id_template_item)->first();
                                    @endphp
                                    <td>
                                        {{ $item->templateItem->nama_bahan }}
                                    </td>
                                    <td>{{ rtrim(rtrim(number_format((float) $item->jumlah_dibutuhkan, 3, ',', '.'), '0'), ',') }}</td>
                                    <td>{{ rtrim(rtrim(number_format((float) $item->jumlah_tersedia, 3, ',', '.'), '0'), ',') }}</td>
                                    <td>{{ rtrim(rtrim(number_format((float) $item->jumlah_kurang, 3, ',', '.'), '0'), ',') }}</td>
                                    <td>{{ $item->satuan }}</td>
                                    <td>
                                        @if($stockItem && $stockItem->konversi_nilai > 0)
                                            {{ rtrim(rtrim(number_format((float) ($item->jumlah_kurang / $stockItem->konversi_nilai), 3, ',', '.'), '0'), ',') }} {{ $stockItem->konversi_satuan }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
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
                                    </td>
                                    <td>
                                        {{ $item->keterangan_resolve ?? "-" }}
                                    </td>
                                    <td class="text-center">
                                        @if ($item->status === "pending")
                                            @if($stockItem)
                                            <button 
                                                type="button" 
                                                class="btn btn-icon text-success action-btn pulse" 
                                                onclick="window.updateRequestStockModal({{ $stockItem->id_stock_item }}, '{{ $item->templateItem->nama_bahan }}', {{ $stockItem->jumlah }}, '{{ $item->satuan }}', {{ $item->jumlah_kurang }}, {{ $stockItem->konversi_nilai ?? 0 }}, '{{ $stockItem->konversi_satuan ?? '' }}')"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#requestStockModal"
                                                data-bs-placement="top" 
                                                title="Tambah Stok"
                                            >
                                                <i class="bx bx-plus-circle fs-4"></i>
                                            </button>
                                            @else
                                            <span class="text-muted fst-italic">Master Stok Tidak Ditemukan</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="8"
                                        class="text-center text-muted"
                                    >
                                        Tidak ada data kekurangan stok ditemukan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

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
                        action="{{ route('admin-gudang.laporan-kekurangan.bulk-resolve', ['dapur' => $dapur->id_dapur]) }}"
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

        @include('partials.request-stock-modal')
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
