@extends("template_admin_gudang.layout")
@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
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

            <!-- Informasi Dapur -->
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

        <!-- Statistik Utama -->
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div
                            class="d-flex justify-content-between align-items-center"
                        >
                            <div>
                                <h6 class="mb-2">Total Menu</h6>
                                <h4 class="mb-0">
                                    {{ number_format($totalMenus ?? 0) }}
                                </h4>
                            </div>
                            <div class="avatar flex-shrink-0 me-3">
                                <span
                                    class="avatar-initial rounded bg-label-primary"
                                >
                                    <i class="bx bx-food-menu"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div
                            class="d-flex justify-content-between align-items-center"
                        >
                            <div>
                                <h6 class="mb-2">Menu Aktif</h6>
                                <h4 class="mb-0">
                                    {{ number_format($activeMenus ?? 0) }}
                                </h4>
                            </div>
                            <div class="avatar flex-shrink-0 me-3">
                                <span
                                    class="avatar-initial rounded bg-label-success"
                                >
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
                        <div
                            class="d-flex justify-content-between align-items-center"
                        >
                            <div>
                                <h6 class="mb-2">Menu Tidak Aktif</h6>
                                <h4 class="mb-0">
                                    {{ number_format($inactiveMenus ?? 0) }}
                                </h4>
                            </div>
                            <div class="avatar flex-shrink-0 me-3">
                                <span
                                    class="avatar-initial rounded bg-label-danger"
                                >
                                    <i class="bx bx-x-circle"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div
                            class="d-flex justify-content-between align-items-center"
                        >
                            <div>
                                <h6 class="mb-2">Transaksi Bulanan</h6>
                                <h4 class="mb-0">
                                    {{ number_format($totalTransactions ?? 0) }}
                                </h4>
                            </div>
                            <div class="avatar flex-shrink-0 me-3">
                                <span
                                    class="avatar-initial rounded bg-label-info"
                                >
                                    <i class="bx bx-cart"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Menu Terbaru -->
        @if (isset($recentMenus) && $recentMenus->isNotEmpty())
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card">
                        <div
                            class="card-header d-flex justify-content-between align-items-center"
                        >
                            <h5 class="mb-0">Menu Terbaru</h5>

                            @if (isset($dapur->id_dapur))
                                <a
                                    href="{{ route("ahli-gizi.menu-makanan.create", $dapur->id_dapur) }}"
                                    class="btn btn-sm btn-primary"
                                >
                                    <i class="bx bx-plus me-1"></i>
                                    Tambah Menu
                                </a>
                            @else
                                <a
                                    href="{{ route("ahli-gizi.menu-makanan.create") }}"
                                    class="btn btn-sm btn-primary"
                                >
                                    <i class="bx bx-plus me-1"></i>
                                    Tambah Menu
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nama Menu</th>
                                            <th>Deskripsi</th>
                                            <th>Status</th>
                                            <th>Terakhir Diperbarui</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentMenus as $menu)
                                            <tr>
                                                <td>
                                                    {{ $menu->nama_menu ?? "N/A" }}
                                                </td>
                                                <td>
                                                    {{ Str::limit($menu->deskripsi ?? "Tidak ada deskripsi", 50) }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-label-{{ $menu->is_active ?? false ? "success" : "danger" }}"
                                                    >
                                                        {{ $menu->is_active ?? false ? "Aktif" : "Tidak Aktif" }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if (
                                                    isset($menu->updated_at) && $menu->updated_at                                                    )
                                                        {{ $menu->updated_at->format("d M Y H:i") }}
                                                    @else
                                                        <span
                                                            class="text-muted"
                                                        >
                                                            Tidak ada data
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (isset($menu->id_menu))
                                                        <a
                                                            href="{{ route("ahli-gizi.menu-makanan.show", $menu->id_menu) }}"
                                                            class="btn btn-sm btn-icon btn-outline-info me-1"
                                                            title="Lihat Detail"
                                                        >
                                                            <i
                                                                class="bx bx-show"
                                                            ></i>
                                                        </a>
                                                        <a
                                                            href="{{ route("ahli-gizi.menu-makanan.edit", $menu->id_menu) }}"
                                                            class="btn btn-sm btn-icon btn-outline-primary"
                                                            title="Edit"
                                                        >
                                                            <i
                                                                class="bx bx-edit"
                                                            ></i>
                                                        </a>
                                                    @else
                                                        <span
                                                            class="text-muted"
                                                        >
                                                            -
                                                        </span>
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
            <!-- Empty State -->
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <div class="avatar avatar-lg mx-auto mb-3">
                                <span
                                    class="avatar-initial rounded bg-label-primary"
                                >
                                    <i
                                        class="bx bx-food-menu"
                                        style="font-size: 2rem"
                                    ></i>
                                </span>
                            </div>
                            <h5 class="mb-2">Belum Ada Menu</h5>
                            <p class="text-muted mb-4">
                                Anda belum membuat menu makanan. Mulai dengan
                                membuat menu pertama Anda.
                            </p>

                            @if (isset($dapur->id_dapur))
                                <a
                                    href="{{ route("ahli-gizi.menu-makanan.create", $dapur->id_dapur) }}"
                                    class="btn btn-primary"
                                >
                                    <i class="bx bx-plus me-1"></i>
                                    Buat Menu Pertama
                                </a>
                            @else
                                <a
                                    href="{{ route("ahli-gizi.menu-makanan.create") }}"
                                    class="btn btn-primary"
                                >
                                    <i class="bx bx-plus me-1"></i>
                                    Buat Menu Pertama
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
