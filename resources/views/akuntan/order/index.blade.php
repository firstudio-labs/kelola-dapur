@extends('template_akuntan.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav class="d-flex align-items-center mb-2">
                        <a href="{{ route('akuntan.dashboard') }}" class="text-muted me-2">
                            <i class="bx bx-home-alt me-1"></i>
                            Dashboard
                        </a>
                        <i class="bx bx-chevron-right me-2"></i>
                        <span class="text-dark">Order Akuntan</span>
                    </nav>
                    <h4 class="mb-1">Order Akuntan</h4>
                    <p class="mb-0 text-muted">Daftar transaksi yang perlu diproses</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('akuntan.transaksi.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="status-filter" class="form-label">Filter Status</label>
                    <select name="status" id="status-filter" class="choices-select form-select">
                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="proses" {{ $statusFilter === 'proses' ? 'selected' : '' }}>Proses</option>
                        <option value="selesai" {{ $statusFilter === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="date-from" class="form-label">Dari Tanggal</label>
                    <input type="date" name="date_from" id="date-from" value="{{ request('date_from') }}" class="form-control" />
                </div>
                <div class="col-md-3">
                    <label for="date-to" class="form-label">Sampai Tanggal</label>
                    <input type="date" name="date_to" id="date-to" value="{{ request('date_to') }}" class="form-control" />
                </div>
                <div class="col-md-3">
                    <label for="search-input" class="form-label">Pencarian Cepat</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" id="search-input" class="form-control" placeholder="Cari nama, menu..." />
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    @if (request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                        <a href="{{ route('akuntan.transaksi.index') }}" class="btn btn-outline-secondary">
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    @if($orders->total() > 0)
        <div class="card mb-4">
            <div class="card-body py-2 px-4">
                <div class="row justify-content-center g-3">
                    <div class="col-md-4 text-center border-end">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="badge bg-label-primary me-2">
                                <i class="bx bx-list-check"></i>
                            </span>
                            <div>
                                <small class="text-muted text-uppercase" style="font-size: 0.65rem;">Total Order</small>
                                <h6 class="mb-0">{{ $orders->total() }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center border-end">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="badge bg-label-warning me-2">
                                <i class="bx bx-time"></i>
                            </span>
                            <div>
                                <small class="text-muted text-uppercase" style="font-size: 0.65rem;">Pending</small>
                                <h6 class="mb-0">0</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="badge bg-label-success me-2">
                                <i class="bx bx-check-double"></i>
                            </span>
                            <div>
                                <small class="text-muted text-uppercase" style="font-size: 0.65rem;">Selesai</small>
                                <h6 class="mb-0">0</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Deskripsi</th>
                                <th>Minal/Stok</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                {{-- Loop logic here when data is implemented --}}
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <img src="{{ asset('admin/assets/img/illustrations/empty-box.png') }}" alt="No Data" class="img-fluid mb-3" style="max-height: 150px;">
                    <h5 class="mb-1 text-dark fw-bold">Belum Ada Data Order</h5>
                    <p class="text-muted px-3">Saat ini belum ada data order atau transaksi untuk role Akuntan.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusFilter = document.getElementById('status-filter');
        if(statusFilter) {
            new Choices(statusFilter, {
                searchEnabled: false,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'Semua Status',
            });
        }
    });
</script>
@endsection
