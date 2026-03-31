@extends("template_kepala_dapur.layout")
@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a href="{{ route('dashboard') }}" class="text-muted me-2">
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <a href="{{ route('kepala-dapur.supplier.index', ['dapur' => $dapur->id_dapur]) }}" class="text-muted me-2">
                                Data Supplier
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Detail Supplier</span>
                        </nav>
                        <h4 class="mb-1">Detail & Riwayat Supplier</h4>
                        <p class="mb-0 text-muted">Informasi supplier dan histori stok yang dikirimkan</p>
                    </div>
                    <div>
                        <a href="{{ route('kepala-dapur.supplier.index', ['dapur' => $dapur->id_dapur]) }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-2">
                        <h5 class="card-title mb-0">Informasi Supplier</h5>
                    </div>
                    <div class="card-body mt-2">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3">
                                <span class="fw-semibold text-heading d-block">Nama Supplier:</span>
                                <span>{{ $supplier->nama_supplier }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-semibold text-heading d-block">Kontak:</span>
                                <span>{{ $supplier->kontak ?? '-' }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-semibold text-heading d-block">Alamat:</span>
                                <span>{{ $supplier->alamat ?? '-' }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-semibold text-heading d-block">Keterangan Tambahan:</span>
                                <span>{{ $supplier->keterangan ?? '-' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-4">
                <div class="card h-100">
                    <div class="card-header border-bottom mb-3">
                        <h5 class="card-title mb-0">Histori Penambahan Stok</h5>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead class="table-light">
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Nama Bahan</th>
                                        <th>Jumlah Disuplai</th>
                                        <th>Penerima (Admin)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($riwayatStok as $riwayat)
                                        <tr>
                                            <td>{{ $riwayat->created_at->format('d M Y, H:i') }}</td>
                                            <td>
                                                <strong>{{ optional($riwayat->stockItem->templateItem)->nama_bahan ?? 'Bahan Tidak Diketahui' }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">+{{ number_format($riwayat->jumlah, 2) }} {{ $riwayat->satuan }}</span>
                                            </td>
                                            <td>
                                                <small>{{ optional($riwayat->adminGudang->user)->nama ?? 'Sistem' }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">Belum ada riwayat stok masuk dari supplier ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($riwayatStok->hasPages())
                            <div class="card-footer">
                                {{ $riwayatStok->links("vendor.pagination.sneat") }}
                            </div>
                        @endif
                </div>
            </div>
        </div>
    </div>
@endsection
