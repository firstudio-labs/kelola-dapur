@extends('template_kepala_dapur.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Template Bahan</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Template Bahan</h4>
            <p class="text-muted mb-0">Kelola template bahan untuk menu makanan</p>
        </div>
        <a href="{{ route('kepala-dapur.template-items.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i>Tambah Bahan
        </a>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form action="{{ route('kepala-dapur.template-items.index') }}" method="GET" class="row align-items-center">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama bahan..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bx bx-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Daftar Template Bahan</h6>
            <small class="text-muted">
                @if(request('search'))
                    Hasil pencarian: <strong>{{ $templateItems->total() }}</strong> dari "{{ request('search') }}"
                @else
                    Total: <strong>{{ $templateItems->total() }}</strong> template bahan
                @endif
            </small>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="30%">Nama Bahan</th>
                            <th width="15%" class="text-center">Satuan</th>
                            <th width="30%">Keterangan</th>
                            <th width="20%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($templateItems as $index => $item)
                        <tr>
                            <td class="text-center">{{ $templateItems->firstItem() + $index }}</td>
                            <td class="fw-semibold">{{ $item->nama_bahan }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $item->satuan }}</span>
                            </td>
                            <td class="text-muted">{{ $item->keterangan ?: '-' }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('kepala-dapur.template-items.show', $item) }}" 
                                       class="btn btn-sm btn-outline-info">
                                        <i class="bx bx-show"></i>
                                    </a>
                                    <a href="{{ route('kepala-dapur.template-items.edit', $item) }}" 
                                       class="btn btn-sm btn-outline-warning">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    <form action="{{ route('kepala-dapur.template-items.destroy', $item) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus {{ $item->nama_bahan }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="text-muted">
                                    @if(request('search'))
                                        <p>Tidak ditemukan hasil untuk "<strong>{{ request('search') }}</strong>"</p>
                                        <a href="{{ route('kepala-dapur.template-items.index') }}" class="btn btn-sm btn-outline-primary">
                                            Tampilkan Semua
                                        </a>
                                    @else
                                        <p>Belum ada template bahan</p>
                                        <a href="{{ route('kepala-dapur.template-items.create') }}" class="btn btn-sm btn-primary">
                                            Tambah Template Bahan
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($templateItems->hasPages())
            <div class="mt-3">
                {{ $templateItems->appends(['search' => request('search')])->links('vendor.pagination.sneat') }}
            </div>
            @endif
        </div>
        
        <!-- Table Info Footer -->
        @if($templateItems->count() > 0)
        <div class="card-footer">
            <small class="text-muted">
                Menampilkan {{ $templateItems->firstItem() }} - {{ $templateItems->lastItem() }} dari {{ $templateItems->total() }} data
                @if(request('search'))
                    untuk pencarian "{{ request('search') }}"
                @endif
            </small>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Auto hide alerts
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 4000);
</script>
@endpush
@endsection