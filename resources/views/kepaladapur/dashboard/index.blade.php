@extends('template_kepala_dapur.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    <i class="bx bx-user"></i>
                                </span>
                            </div>
                            <div>
                                <h4 class="mb-1">Dashboard Kepala Dapur</h4>
                                <p class="mb-0 text-muted">
                                    <i class="bx bx-user me-1"></i>
                                    {{ $user->nama }} ({{ $role }})
                                </p>
                                <p class="mb-0 text-muted">
                                    <i class="bx bx-building me-1"></i>
                                    Dapur: {{ $dapur->nama_dapur }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Detail Akun dan Dapur -->
        <div class="row mb-4">
            <!-- Informasi Akun -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Akun</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">Nama</dt>
                            <dd class="col-sm-8">{{ $user->nama }}</dd>

                            <dt class="col-sm-4">Username</dt>
                            <dd class="col-sm-8">{{ $user->username }}</dd>

                            <dt class="col-sm-4">Email</dt>
                            <dd class="col-sm-8">{{ $user->email }}</dd>

                            <dt class="col-sm-4">Status</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-label-{{ $user->is_active ? 'success' : 'danger' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </dd>

                            <dt class="col-sm-4">Role</dt>
                            <dd class="col-sm-8">{{ ucfirst(str_replace('_', ' ', $role)) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Informasi Dapur -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Informasi Dapur</h5>
                        <a href="{{ route('superadmin.dapur.show', $dapur->id_dapur) }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-detail me-1"></i> Lihat Detail
                        </a>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">Nama Dapur</dt>
                            <dd class="col-sm-8">{{ $dapur->nama_dapur }}</dd>

                            <dt class="col-sm-4">Kepala Dapur</dt>
                            <dd class="col-sm-8">
                                @if($dapur->kepalaDapur->isNotEmpty())
                                    {{ $dapur->kepalaDapur->first()->user->nama ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </dd>
                            
                            <dt class="col-sm-4">Provinsi</dt>
                            <dd class="col-sm-8">{{ $dapur->wilayah_hierarchy['province']['name'] ?? 'N/A' }}</dd>

                            <dt class="col-sm-4">Kota/Kabupaten</dt>
                            <dd class="col-sm-8">{{ $dapur->wilayah_hierarchy['regency']['name'] ?? 'N/A' }}</dd>

                            <dt class="col-sm-4">Kecamatan</dt>
                            <dd class="col-sm-8">{{ $dapur->wilayah_hierarchy['district']['name'] ?? 'N/A' }}</dd>

                            <dt class="col-sm-4">Kelurahan</dt>
                            <dd class="col-sm-8">{{ $dapur->wilayah_hierarchy['village']['name'] ?? 'N/A' }}</dd>

                            <dt class="col-sm-4">Alamat</dt>
                            <dd class="col-sm-8">{{ $dapur->alamat }}</dd>

                            <dt class="col-sm-4">Telepon</dt>
                            <dd class="col-sm-8">{{ $dapur->telepon }}</dd>

                            <dt class="col-sm-4">Status</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-label-{{ $dapur->isActive() ? 'success' : 'danger' }}">
                                    {{ $dapur->isActive() ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </dd>

                            <dt class="col-sm-4">Akhir Berlangganan</dt>
                            <dd class="col-sm-8">
                                {{ $dapur->subscription_end->format('d M Y') }}
                                @if($dapur->subscription_end->isBefore(now()->addDays(30)))
                                    <span class="badge bg-label-warning ms-2">Segera Berakhir</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Utama -->
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-2">Persetujuan Tertunda</h6>
                                <h4 class="mb-0">{{ number_format($pendingApprovals) }}</h4>
                            </div>
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-check-circle"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-2">Total Stok</h6>
                                <h4 class="mb-0">{{ number_format($totalStock) }}</h4>
                            </div>
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-box"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-2">Item Stok Rendah</h6>
                                <h4 class="mb-0">{{ number_format($lowStockItems->count()) }}</h4>
                            </div>
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="bx bx-down-arrow-alt"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-2">Transaksi Bulanan</h6>
                                <h4 class="mb-0">{{ number_format($monthlyTransactions) }}</h4>
                            </div>
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="bx bx-cart"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Tim -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Statistik Tim Dapur</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class="bx bx-group"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Total Anggota Tim</h6>
                                        <small class="text-muted">{{ $teamMembers['total'] }} Anggota</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-success">
                                            <i class="bx bx-user-check"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Kepala Dapur</h6>
                                        <small class="text-muted">{{ $teamMembers['kepala_dapur']->count() }} Anggota</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-warning">
                                            <i class="bx bx-warehouse"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Admin Gudang</h6>
                                        <small class="text-muted">{{ $teamMembers['admin_gudang']->count() }} Anggota</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-info">
                                            <i class="bx bx-heart"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Ahli Gizi</h6>
                                        <small class="text-muted">{{ $teamMembers['ahli_gizi']->count() }} Anggota</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Item Stok Rendah -->
        @if($lowStockItems->isNotEmpty())
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Item Stok Rendah</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Item</th>
                                        <th>Jumlah</th>
                                        <th>Satuan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockItems as $item)
                                    <tr>
                                        <td>{{ $item->nama_item ?? 'N/A' }}</td>
                                        <td>{{ $item->jumlah ?? 0 }}</td>
                                        <td>{{ $item->satuan ?? 'N/A' }}</td>
                                        <td><span class="badge bg-label-danger">Stok Rendah</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection