@extends("template_kepala_dapur.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div
                            class="d-flex align-items-center justify-content-between"
                        >
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-lg me-3">
                                    <span
                                        class="avatar-initial rounded-circle bg-label-primary"
                                    >
                                        <i class="bx bx-user-check fs-4"></i>
                                    </span>
                                </div>
                                <div>
                                    <h3 class="mb-1">
                                        Selamat Datang, {{ $user->nama }}
                                    </h3>
                                    <p class="mb-0 text-muted">
                                        <i class="bx bx-building me-1"></i>
                                        Dashboard Kepala Dapur -
                                        {{ $dapur->nama_dapur }}
                                    </p>
                                    <p class="mb-0">
                                        <small class="text-muted">
                                            <i class="bx bx-calendar me-1"></i>
                                            {{ now()->format("l, d F Y") }}
                                        </small>
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <!-- Subscription Status Badge -->
                                @if ($subscriptionStatus["is_expired"])
                                    <span class="badge bg-danger">
                                        <i class="bx bx-x-circle me-1"></i>
                                        Langganan Berakhir
                                    </span>
                                @elseif ($subscriptionStatus["is_expiring_soon"])
                                    <span class="badge bg-warning">
                                        <i class="bx bx-time me-1"></i>
                                        {{ $subscriptionStatus["days_left"] }}
                                        Hari Lagi
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="bx bx-check-circle me-1"></i>
                                        Aktif
                                    </span>
                                @endif

                                <!-- Quick Notification Bell -->
                                <div class="dropdown">
                                    <button
                                        class="btn btn-icon btn-outline-secondary"
                                        type="button"
                                        data-bs-toggle="dropdown"
                                    >
                                        <i class="bx bx-bell"></i>
                                        @if (count($systemAlerts) > 0)
                                            <span
                                                class="badge bg-danger badge-dot"
                                            ></span>
                                        @endif
                                    </button>
                                    <div
                                        class="dropdown-menu dropdown-menu-end"
                                        style="width: 300px"
                                    >
                                        <h6 class="dropdown-header">
                                            Notifikasi Sistem
                                        </h6>
                                        @forelse ($systemAlerts as $alert)
                                            <div class="dropdown-item">
                                                <div class="d-flex">
                                                    <div
                                                        class="avatar flex-shrink-0 me-2"
                                                    >
                                                        <span
                                                            class="avatar-initial rounded bg-label-{{ $alert["type"] === "critical" ? "danger" : "warning" }}"
                                                        >
                                                            <i
                                                                class="{{ $alert["icon"] }}"
                                                            ></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">
                                                            {{ $alert["title"] }}
                                                        </h6>
                                                        <p
                                                            class="mb-0 small text-muted"
                                                        >
                                                            {{ $alert["message"] }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            @if (! $loop->last)
                                                <div
                                                    class="dropdown-divider"
                                                ></div>
                                            @endif
                                        @empty
                                            <div
                                                class="dropdown-item text-center text-muted"
                                            >
                                                <i
                                                    class="bx bx-check-circle me-1"
                                                ></i>
                                                Tidak ada notifikasi
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Alerts -->
        @if (count($systemAlerts) > 0)
            <div class="row mb-4">
                <div class="col-12">
                    @foreach ($systemAlerts as $alert)
                        <div
                            class="alert alert-{{ $alert["type"] === "critical" ? "danger" : ($alert["type"] === "warning" ? "warning" : "info") }} alert-dismissible"
                            role="alert"
                        >
                            <div class="d-flex align-items-center">
                                <i class="{{ $alert["icon"] }} me-2"></i>
                                <div class="flex-grow-1">
                                    <h6 class="alert-heading mb-1">
                                        {{ $alert["title"] }}
                                    </h6>
                                    <div class="mb-0">
                                        {{ $alert["message"] }}
                                    </div>
                                </div>
                                {{-- <a
                                    href="{{ $alert["action_url"] }}"
                                    class="btn btn-sm btn-{{ $alert["type"] === "critical" ? "danger" : ($alert["type"] === "warning" ? "warning" : "info") }} me-2"
                                >
                                    {{ $alert["action_text"] }}
                                </a> --}}
                            </div>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                            ></button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Quick Actions Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5 class="mb-0">
                            <i class="bx bx-zap me-1"></i>
                            Quick Actions
                        </h5>
                        <small class="text-muted">
                            Aksi cepat berdasarkan kondisi saat ini
                        </small>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach (array_slice($quickActions, 0, 6) as $action)
                                <div class="col-md-6 col-lg-4">
                                    <a
                                        href="{{ $action["url"] }}"
                                        class="card border-0 shadow-sm h-100 text-decoration-none quick-action-card"
                                    >
                                        <div
                                            class="card-body d-flex align-items-center"
                                        >
                                            <div
                                                class="avatar flex-shrink-0 me-3"
                                            >
                                                <span
                                                    class="avatar-initial rounded bg-label-{{ $action["color"] }}"
                                                >
                                                    <i
                                                        class="{{ $action["icon"] }}"
                                                    ></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div
                                                    class="d-flex justify-content-between align-items-start"
                                                >
                                                    <div>
                                                        <h6 class="mb-1">
                                                            {{ $action["title"] }}
                                                        </h6>
                                                        <p
                                                            class="mb-0 text-muted small"
                                                        >
                                                            {{ $action["description"] }}
                                                        </p>
                                                    </div>
                                                    @if ($action["badge"])
                                                        <span
                                                            class="badge bg-{{ $action["color"] }}"
                                                        >
                                                            {{ $action["badge"] }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Core Statistics Cards -->
        <div class="row mb-4">
            <!-- Pending Approvals -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div
                            class="d-flex justify-content-between align-items-center"
                        >
                            <div>
                                <h6 class="mb-2">Approval Tertunda</h6>
                                <h3 class="mb-0 text-warning">
                                    {{ number_format($statistics["pending_approvals"] + $statistics["pending_transaction_approvals"]) }}
                                </h3>
                                <small class="text-muted">
                                    Stock:
                                    {{ $statistics["pending_approvals"] }} |
                                    Transaksi:
                                    {{ $statistics["pending_transaction_approvals"] }}
                                </small>
                            </div>
                            <div class="avatar flex-shrink-0">
                                <span
                                    class="avatar-initial rounded bg-label-warning"
                                >
                                    <i class="bx bx-time-five fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Overview -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div
                            class="d-flex justify-content-between align-items-center"
                        >
                            <div>
                                <h6 class="mb-2">Item Stock</h6>
                                <h3 class="mb-0 text-primary">
                                    {{ number_format($statistics["total_stock_items"]) }}
                                </h3>
                                <small class="text-muted">
                                    @if ($statistics["low_stock_alerts"] > 0)
                                        <span class="text-danger">
                                            {{ $statistics["low_stock_alerts"] }}
                                            rendah
                                        </span>
                                    @else
                                        <span class="text-success">
                                            Stock aman
                                        </span>
                                    @endif
                                </small>
                            </div>
                            <div class="avatar flex-shrink-0">
                                <span
                                    class="avatar-initial rounded bg-label-primary"
                                >
                                    <i class="bx bx-package fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Transactions -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div
                            class="d-flex justify-content-between align-items-center"
                        >
                            <div>
                                <h6 class="mb-2">Transaksi Bulan Ini</h6>
                                <h3 class="mb-0 text-success">
                                    {{ number_format($statistics["monthly_transactions"]) }}
                                </h3>
                                <small class="text-muted">
                                    {{ number_format($statistics["total_portions_month"]) }}
                                    total porsi
                                </small>
                            </div>
                            <div class="avatar flex-shrink-0">
                                <span
                                    class="avatar-initial rounded bg-label-success"
                                >
                                    <i class="bx bx-cart fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Members -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div
                            class="d-flex justify-content-between align-items-center"
                        >
                            <div>
                                <h6 class="mb-2">Anggota Tim</h6>
                                <h3 class="mb-0 text-info">
                                    {{ $statistics["team_members"] }}
                                </h3>
                                <small class="text-muted">
                                    KD:
                                    {{ $teamOverview["summary"]["kepala_dapur_count"] }}
                                    | AG:
                                    {{ $teamOverview["summary"]["admin_gudang_count"] }}
                                    | AhliGizi:
                                    {{ $teamOverview["summary"]["ahli_gizi_count"] }}
                                </small>
                            </div>
                            <div class="avatar flex-shrink-0">
                                <span
                                    class="avatar-initial rounded bg-label-info"
                                >
                                    <i class="bx bx-group fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="row">
            <!-- Stock Health Overview -->
<div class="col-lg-8 mb-4">
    <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bx bx-health me-1"></i>
                Kondisi Stock
            </h5>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-label-success">
                    {{ $stockHealth["overview"]["stock_health_percentage"] }}%
                    Stock
                </span>
                <a href="{{ route("kepala-dapur.stock.index", $dapur) }}" class="btn btn-sm btn-outline-primary">
                    Lihat Detail
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Stock Overview Stats -->
            <div class="row mb-4">
                <div class="col-3">
                    <div class="d-flex flex-column align-items-center">
                        <h4 class="text-success mb-1">
                            {{ $stockHealth["overview"]["available_items"] }}
                        </h4>
                        <small class="text-muted">Tersedia</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="d-flex flex-column align-items-center">
                        <h4 class="text-warning mb-1">
                            {{ $stockHealth["overview"]["low_stock"] }}
                        </h4>
                        <small class="text-muted">Stock Rendah</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="d-flex flex-column align-items-center">
                        <h4 class="text-danger mb-1">
                            {{ $stockHealth["overview"]["critical_stock"] }}
                        </h4>
                        <small class="text-muted">Kritis</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="d-flex flex-column align-items-center">
                        <h4 class="text-dark mb-1">
                            {{ $stockHealth["overview"]["out_of_stock"] }}
                        </h4>
                        <small class="text-muted">Habis</small>
                    </div>
                </div>
            </div>

            <!-- Low Stock Items Table -->
            @if (count($stockHealth["low_stock_items"]) > 0)
                <div>
                    <h6 class="mb-3">Item Stock Rendah & Habis</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Bahan</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Restock Terakhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (array_slice($stockHealth["low_stock_items"], 0, 10) as $item) <!-- Diperluas slice menjadi 10 untuk lebih banyak data -->
                                    <tr>
                                        <td>{{ $item["nama_bahan"] }}</td>
                                        <td>
                                            <span class="text-{{ $item["status"] === "critical" ? "danger" : ($item["status"] === "out_of_stock" ? "dark" : "warning") }}">
                                                {{ number_format($item["jumlah"], 2) }} {{ $item["satuan"] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-{{ $item["status"] === "critical" ? "danger" : ($item["status"] === "out_of_stock" ? "dark" : "warning") }}">
                                                {{ $item["status"] === "critical" ? "Kritis" : ($item["status"] === "out_of_stock" ? "Habis" : "Rendah") }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $item["last_restock"] ? \Carbon\Carbon::parse($item["last_restock"])->format("d M Y") : "Belum pernah" }}
                                            </small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="bx bx-package display-6"></i>
                    <p class="mt-2">Tidak ada item stock rendah atau habis saat ini</p>
                </div>
            @endif
        </div>
    </div>
</div>

            <!-- Recent Activities -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5 class="mb-0">
                            <i class="bx bx-time-five me-1"></i>
                            Aktivitas Terbaru
                        </h5>
                        <small class="text-muted">24 jam terakhir</small>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @forelse (array_slice($recentActivities, 0, 5) as $activity)
                                <div class="timeline-item">
                                    <div class="timeline-icon">
                                        <span class="avatar avatar-sm">
                                            <span
                                                class="avatar-initial rounded-circle bg-label-{{ $activity["color"] }}"
                                            >
                                                <i
                                                    class="{{ $activity["icon"] }} small"
                                                ></i>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="timeline-content">
                                        <div
                                            class="d-flex justify-content-between align-items-start"
                                        >
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    {{ $activity["title"] }}
                                                </h6>
                                                <p
                                                    class="mb-1 small text-muted"
                                                >
                                                    {{ $activity["description"] }}
                                                </p>
                                                <small class="text-muted">
                                                    {{ $activity["user"] ?? "" }}
                                                    •
                                                    {{ \Carbon\Carbon::parse($activity["created_at"])->diffForHumans() }}
                                                </small>
                                            </div>
                                            @if (isset($activity["url"]))
                                                <a
                                                    href="{{ $activity["url"] }}"
                                                    class="btn btn-sm btn-outline-secondary"
                                                >
                                                    <i
                                                        class="bx bx-right-arrow-alt"
                                                    ></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="bx bx-time-five display-6"></i>
                                    <p class="mt-2">
                                        Belum ada aktivitas hari ini
                                    </p>
                                </div>
                            @endforelse
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
                                    <span
                                        class="badge bg-label-{{ $user->is_active ? "success" : "danger" }}"
                                    >
                                        {{ $user->is_active ? "Aktif" : "Tidak Aktif" }}
                                    </span>
                                </dd>

                                <dt class="col-sm-4">Role</dt>
                                <dd class="col-sm-8">
                                    {{ ucfirst(str_replace("_", " ", $role)) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Informasi Dapur -->
                <div class="col-md-6">
                    <div class="card">
                        <div
                            class="card-header d-flex justify-content-between align-items-center"
                        >
                            <h5 class="mb-0">Informasi Dapur</h5>
                            {{-- <a
                                href="{{ route("superadmin.dapur.show", $dapur->id_dapur) }}"
                                class="btn btn-sm btn-primary"
                            >
                                <i class="bx bx-detail me-1"></i>
                                Lihat Detail
                            </a> --}}
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-4">Nama Dapur</dt>
                                <dd class="col-sm-8">
                                    {{ $dapur->nama_dapur }}
                                </dd>

                                <dt class="col-sm-4">Kepala Dapur</dt>
                                <dd class="col-sm-8">
                                    @if ($dapur->kepalaDapur->isNotEmpty())
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
                                <dd class="col-sm-8">{{ $dapur->alamat }}</dd>

                                <dt class="col-sm-4">Telepon</dt>
                                <dd class="col-sm-8">{{ $dapur->telepon }}</dd>

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
                                    {{ $dapur->subscription_end->format("d M Y") }}
                                    @if ($dapur->subscription_end->isBefore(now()->subDays(1)))
                                        <span
                                            class="badge bg-label-danger ms-2"
                                        >
                                            Langganan Berakhir
                                        </span>
                                    @elseif ($dapur->subscription_end->isBefore(now()->addDays(7)))
                                        <span
                                            class="badge bg-label-warning ms-2"
                                        >
                                            Segera Berakhir
                                        </span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .quick-action-card:hover {
            transform: translateY(-2px);
            transition: all 0.2s ease;
        }

        .timeline {
            position: relative;
            padding-left: 1.5rem;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }

        .timeline-item:not(:last-child):before {
            content: '';
            position: absolute;
            left: -1.125rem;
            top: 2rem;
            width: 1px;
            height: calc(100% - 1rem);
            background-color: #ddd;
        }

        .timeline-icon {
            position: absolute;
            left: -1.5rem;
            top: 0;
        }

        .timeline-content {
            padding-left: 0.5rem;
        }
    </style>
@endsection

@section("scripts")
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Auto-hide alerts after 10 seconds
            setTimeout(function () {
                const alerts = document.querySelectorAll('.alert-dismissible');
                alerts.forEach(function (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 10000);

            // Add click animation to quick action cards
            const quickActionCards =
                document.querySelectorAll('.quick-action-card');
            quickActionCards.forEach(function (card) {
                card.addEventListener('click', function (e) {
                    card.style.transform = 'scale(0.98)';
                    setTimeout(function () {
                        card.style.transform = '';
                    }, 150);
                });
            });
        });
    </script>
@endsection
