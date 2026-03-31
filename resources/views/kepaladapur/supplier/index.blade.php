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
                        <span class="text-dark">Data Supplier</span>
                    </nav>
                    <h4 class="mb-1">Data Supplier</h4>
                    <p class="mb-0 text-muted">Kelola daftar kontak supplier penyedia bahan untuk dapur ini.</p>
                </div>
                <a href="{{ route('kepala-dapur.supplier.create', ['dapur' => $dapur->id_dapur]) }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah Supplier
                </a>
            </div>
        </div>
    </div>

        @if (session("success"))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session("success") }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="25%">Nama Supplier</th>
                                <th width="20%">Kontak</th>
                                <th width="35%">Keterangan</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($suppliers as $index => $supplier)
                                <tr>
                                    <td class="text-center">{{ $suppliers->firstItem() + $index }}</td>
                                    <td><strong>{{ $supplier->nama_supplier }}</strong></td>
                                    <td>{{ $supplier->kontak ?? '-' }}</td>
                                    <td><small class="text-muted">{{ Str::limit($supplier->keterangan ?? '-', 50) }}</small></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('kepala-dapur.supplier.show', ['dapur' => $dapur->id_dapur, 'supplier' => $supplier->id_supplier]) }}" 
                                               class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Riwayat">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            <a href="{{ route('kepala-dapur.supplier.edit', ['dapur' => $dapur->id_dapur, 'supplier' => $supplier->id_supplier]) }}" 
                                               class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Edit">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <form action="{{ route('kepala-dapur.supplier.destroy', ['dapur' => $dapur->id_dapur, 'supplier' => $supplier->id_supplier]) }}" 
                                                  method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus supplier {{ $supplier->nama_supplier }}?');">
                                                @csrf
                                                @method("DELETE")
                                                <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Hapus">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada data supplier.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

                @if ($suppliers->hasPages())
                    <div class="card-footer">
                        {{ $suppliers->links("vendor.pagination.sneat") }}
                    </div>
                @endif
        </div>
    </div>
@endsection
