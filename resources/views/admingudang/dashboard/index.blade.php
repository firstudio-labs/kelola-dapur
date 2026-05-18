@extends("template_admin_gudang.layout")
@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md me-3">
                                <span
                                    class="avatar-initial rounded-circle bg-label-primary"
                                >
                                    <i class="bx bx-user"></i>
                                </span>
                            </div>
                            <div>
                                <h4 class="mb-1">Dashboard Admin Gudang</h4>
                                <p class="mb-0 text-muted">
                                    <i class="bx bx-user me-1"></i>
                                    {{ $user->nama ?? "N/A" }} (Admin Gudang)
                                </p>
                                <p class="mb-0 text-muted">
                                    <i class="bx bx-building me-1"></i>
                                    Dapur: {{ $dapur->nama_dapur ?? "N/A" }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Akun</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">Nama</dt>
                            <dd class="col-sm-8">
                                {{ $user->nama ?? "N/A" }}
                            </dd>
                            <dt class="col-sm-4">Username</dt>
                            <dd class="col-sm-8">
                                {{ $user->username ?? "N/A" }}
                            </dd>
                            <dt class="col-sm-4">Email</dt>
                            <dd class="col-sm-8">
                                {{ $user->email ?? "N/A" }}
                            </dd>
                            <dt class="col-sm-4">Status</dt>
                            <dd class="col-sm-8">
                                <span
                                    class="badge bg-label-{{ $user->is_active ?? false ? "success" : "danger" }}"
                                >
                                    {{ $user->is_active ?? false ? "Aktif" : "Tidak Aktif" }}
                                </span>
                            </dd>
                            <dt class="col-sm-4">Role</dt>
                            <dd class="col-sm-8">Admin Gudang</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Dapur</h5>
                    </div>
                    <div class="card-body">
                        @if ($dapur)
                            <dl class="row">
                                <dt class="col-sm-4">Nama Dapur</dt>
                                <dd class="col-sm-8">
                                    {{ $dapur->nama_dapur ?? "N/A" }}
                                </dd>
                                <dt class="col-sm-4">Kepala Dapur</dt>
                                <dd class="col-sm-8">
                                    @if ($dapur->kepalaDapur && $dapur->kepalaDapur->isNotEmpty())
                                        {{ $dapur->kepalaDapur->first()->user->nama ?? "N/A" }}
                                    @else
                                            N/A
                                    @endif
                                </dd>
                                <dt class="col-sm-4">Provinsi</dt>
                                <dd class="col-sm-8">
                                    {{ $dapur->wilayah_hierarchy["province"]["name"] ?? "N/A" }}
                                </dd>
                                <dt class="col-sm-4">Kota/Kabupaten</dt>
                                <dd class="col-sm-8">
                                    {{ $dapur->wilayah_hierarchy["regency"]["name"] ?? "N/A" }}
                                </dd>
                                <dt class="col-sm-4">Kecamatan</dt>
                                <dd class="col-sm-8">
                                    {{ $dapur->wilayah_hierarchy["district"]["name"] ?? "N/A" }}
                                </dd>
                                <dt class="col-sm-4">Kelurahan</dt>
                                <dd class="col-sm-8">
                                    {{ $dapur->wilayah_hierarchy["village"]["name"] ?? "N/A" }}
                                </dd>
                                <dt class="col-sm-4">Alamat</dt>
                                <dd class="col-sm-8">
                                    {{ $dapur->alamat ?? "N/A" }}
                                </dd>
                                <dt class="col-sm-4">Telepon</dt>
                                <dd class="col-sm-8">
                                    {{ $dapur->telepon ?? "N/A" }}
                                </dd>
                                <dt class="col-sm-4">Status</dt>
                                <dd class="col-sm-8">
                                    <span
                                        class="badge bg-label-{{ $dapur->isActive() ? "success" : "danger" }}"
                                    >
                                        {{ $dapur->isActive() ? "Aktif" : "Tidak Aktif" }}
                                    </span>
                                </dd>
                                <dt class="col-sm-4">Akhir Berlangganan</dt>
                                <dd class="col-sm-8">
                                    @if ($dapur->subscription_end)
                                        {{ $dapur->subscription_end->format("d M Y") }}
                                        @if ($dapur->subscription_end->isBefore(now()->addDays(30)))
                                            <span
                                                class="badge bg-label-warning ms-2"
                                            >
                                                Segera Berakhir
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-muted">
                                            Tidak ada data
                                        </span>
                                    @endif
                                </dd>
                            </dl>
                        @else
                            <p class="text-muted">Data dapur tidak tersedia.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if(isset($adminGudang))
        
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Profil Spesifik Admin Gudang</h5>
                <a href="{{ route('admin-gudang.profile.edit', $dapur) }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-edit-alt me-1"></i> Edit Profil
                </a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        <img src="{{ $adminGudang->foto_diri ? Storage::url($adminGudang->foto_diri) : asset('assets/img/avatars/1.png') }}" 
                             alt="Foto Admin Gudang" 
                             class="img-fluid rounded border" 
                             style="max-height: 200px; object-fit: cover;">
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <p class="mb-1"><strong>NIK:</strong> {{ $adminGudang->nik_admin_gudang ?: '-' }}</p>
                                <p class="mb-1"><strong>Nama Lengkap:</strong> {{ $adminGudang->nama_lengkap ?: '-' }}</p>
                                <p class="mb-1"><strong>Jabatan:</strong> {{ $adminGudang->jabatan ?: '-' }}</p>
                                <p class="mb-1"><strong>Pendidikan:</strong> {{ $adminGudang->pendidikan_terakhir ?: '-' }}</p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p class="mb-1"><strong>Kontak WA:</strong> {{ $adminGudang->kontak_wa ?: '-' }}</p>
                                <p class="mb-1"><strong>Jenis Kelamin:</strong> {{ $adminGudang->jenis_kelamin ?: '-' }}</p>
                            </div>
                            <div class="col-12 mt-2">
                                <p class="mb-1"><strong>Alamat Lengkap:</strong></p>
                                <p class="text-muted mb-0">
                                    {{ $adminGudang->alamat_detail ?: '-' }}<br>
                                    @if($adminGudang->village_name || $adminGudang->district_name)
                                        Kel. {{ $adminGudang->village_name ?: '-' }}, Kec. {{ $adminGudang->district_name ?: '-' }}<br>
                                        {{ $adminGudang->regency_name ?: '-' }}, Prov. {{ $adminGudang->province_name ?: '-' }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-2">Total Item Bahan</h6>
                                <h4 class="mb-0">@formatNumber($totalStock ?? 0)</h4>
                            </div>
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-package"></i>
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
                                <h6 class="mb-2">Stok Menipis</h6>
                                <h4 class="mb-0">@formatNumber($lowStockItems ?? 0)</h4>
                            </div>
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-{{ ($lowStockItems ?? 0) > 0 ? 'warning' : 'success' }}">
                                    <i class="bx bx-trending-down"></i>
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
                                <h6 class="mb-2">Restok Pending</h6>
                                <h4 class="mb-0">@formatNumber($pendingRequestsCount ?? 0)</h4>
                            </div>
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="bx bx-loader-circle"></i>
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
                                <h6 class="mb-2">Kekurangan Stok</h6>
                                <h4 class="mb-0">@formatNumber($pendingShortagesCount ?? 0)</h4>
                            </div>
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-{{ ($pendingShortagesCount ?? 0) > 0 ? 'danger' : 'success' }}">
                                    <i class="bx bx-error-circle"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (isset($recentShortages) && $recentShortages->isNotEmpty())
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Kekurangan Terkini</h5>

                            @if (isset($dapur->id_dapur))
                                <a href="{{ route("admin-gudang.laporan-kekurangan.index", $dapur->id_dapur) }}" class="btn btn-sm btn-primary">
                                    <i class="bx bx-list-ul me-1"></i>
                                    Lihat Semua
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Item Bahan</th>
                                            <th>Kurang</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentShortages as $shortage)
                                            <tr>
                                                <td>{{ $shortage->templateItem->nama_bahan ?? "N/A" }}</td>
                                                <td>{{ $shortage->getFormattedJumlahKurang() }}</td>
                                                <td>
                                                    <span class="badge {{ $shortage->getStatusBadgeClass() }}">
                                                        {{ ucfirst($shortage->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $shortage->created_at ? $shortage->created_at->format('d M Y H:i') : '-' }}
                                                </td>
                                                <td>
                                                    @if (isset($dapur->id_dapur))
                                                        <a href="{{ route("admin-gudang.laporan-kekurangan.index", $dapur->id_dapur) }}" class="btn btn-sm btn-icon btn-outline-info" title="Lihat Detail">
                                                            <i class="bx bx-show"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
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
        @else
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <div class="avatar avatar-lg mx-auto mb-3">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="bx bx-check-shield" style="font-size: 2rem"></i>
                                </span>
                            </div>
                            <h5 class="mb-2">Tidak Ada Laporan Kekurangan</h5>
                            <p class="text-muted mb-0">
                                Saat ini tidak ada laporan kekurangan stok yang perlu ditangani.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
