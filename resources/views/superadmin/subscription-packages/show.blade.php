@extends("template_admin.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <nav class="d-flex align-items-center mb-2">
                    <a
                        href="{{ route("superadmin.dashboard") }}"
                        class="text-muted me-2"
                    >
                        <i class="bx bx-home-alt me-1"></i>
                        Dashboard
                    </a>
                    <i class="bx bx-chevron-right me-2"></i>
                    <a
                        href="{{ route("superadmin.subscription-packages.index") }}"
                        class="text-muted me-2"
                    >
                        Paket Subscription
                    </a>
                    <i class="bx bx-chevron-right me-2"></i>
                    <span class="text-dark">
                        {{ $subscriptionPackage->nama_paket }}
                    </span>
                </nav>
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="mb-1">Detail Paket Subscription</h4>
                        <p class="mb-0 text-muted">
                            Informasi lengkap paket subscription
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a
                            href="{{ route("superadmin.subscription-packages.edit", $subscriptionPackage) }}"
                            class="btn btn-primary btn-sm"
                        >
                            <i class="bx bx-edit me-1"></i>
                            Edit Paket
                        </a>
                        <form
                            method="POST"
                            action="{{ route("superadmin.subscription-packages.toggle-status", $subscriptionPackage) }}"
                            class="d-inline"
                        >
                            @csrf
                            @method("PATCH")
                            <button
                                type="submit"
                                class="btn btn-{{ $subscriptionPackage->is_active ? "warning" : "success" }} btn-sm"
                            >
                                <i
                                    class="bx bx-{{ $subscriptionPackage->is_active ? "pause" : "play" }} me-1"
                                ></i>
                                {{ $subscriptionPackage->is_active ? "Nonaktifkan" : "Aktifkan" }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
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

        <div class="row">
            <!-- Package Details -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5 class="mb-0">Informasi Paket</h5>
                        <span
                            class="badge {{ $subscriptionPackage->is_active ? "bg-success" : "bg-danger" }}"
                        >
                            {{ $subscriptionPackage->is_active ? "Aktif" : "Tidak Aktif" }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="mb-3">Detail Paket</h6>
                                <dl class="row">
                                    <dt class="col-sm-4">Nama Paket:</dt>
                                    <dd class="col-sm-8">
                                        {{ $subscriptionPackage->nama_paket }}
                                    </dd>

                                    <dt class="col-sm-4">Harga Dasar:</dt>
                                    <dd class="col-sm-8">
                                        <strong class="text-primary">
                                            {{ $subscriptionPackage->formatted_harga }}
                                        </strong>
                                    </dd>

                                    <dt class="col-sm-4">Durasi:</dt>
                                    <dd class="col-sm-8">
                                        {{ $subscriptionPackage->durasi_text }}
                                    </dd>

                                    <dt class="col-sm-4">Status:</dt>
                                    <dd class="col-sm-8">
                                        <span
                                            class="badge {{ $subscriptionPackage->is_active ? "bg-label-success" : "bg-label-danger" }}"
                                        >
                                            {{ $subscriptionPackage->is_active ? "Aktif" : "Tidak Aktif" }}
                                        </span>
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-3">Informasi Tambahan</h6>
                                <dl class="row">
                                    <dt class="col-sm-5">Dibuat:</dt>
                                    <dd class="col-sm-7">
                                        {{ $subscriptionPackage->created_at->format("d/m/Y H:i") }}
                                    </dd>

                                    <dt class="col-sm-5">Diperbarui:</dt>
                                    <dd class="col-sm-7">
                                        {{ $subscriptionPackage->updated_at->format("d/m/Y H:i") }}
                                    </dd>

                                    <dt class="col-sm-5">Total Request:</dt>
                                    <dd class="col-sm-7">
                                        <span class="badge bg-label-primary">
                                            {{ $subscriptionPackage->subscriptionRequests->count() }}
                                        </span>
                                    </dd>
                                </dl>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h6 class="mb-2">Deskripsi</h6>
                            <p class="text-muted mb-0">
                                {{ $subscriptionPackage->deskripsi }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Subscription Requests -->
                @if ($subscriptionPackage->subscriptionRequests->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Riwayat Subscription Request</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Dapur</th>
                                            <th>Harga Final</th>
                                            <th>Promo</th>
                                            <th>Tanggal Request</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($subscriptionPackage->subscriptionRequests->take(10) as $request)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <div
                                                        class="d-flex align-items-center"
                                                    >
                                                        <div
                                                            class="avatar flex-shrink-0 me-2"
                                                        >
                                                            <span
                                                                class="avatar-initial rounded bg-label-info"
                                                            >
                                                                {{ strtoupper(substr($request->dapur->nama_dapur, 0, 1)) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <strong>
                                                                {{ $request->dapur->nama_dapur }}
                                                            </strong>
                                                            <small
                                                                class="text-muted d-block"
                                                            >
                                                                ID:
                                                                {{ $request->dapur->id_dapur }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong>
                                                        Rp
                                                        {{ number_format($request->harga_final, 0, ",", ".") }}
                                                    </strong>
                                                    @if ($request->diskon > 0)
                                                        <small
                                                            class="text-success d-block"
                                                        >
                                                            Diskon: -Rp
                                                            {{ number_format($request->diskon, 0, ",", ".") }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($request->promoCode)
                                                        <span
                                                            class="badge bg-label-warning"
                                                        >
                                                            {{ $request->promoCode->kode_promo }}
                                                        </span>
                                                    @else
                                                        <span
                                                            class="text-muted"
                                                        >
                                                            -
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $request->tanggal_request->format("d/m/Y") }}
                                                </td>
                                                <td>
                                                    @if ($request->status == "pending")
                                                        <span
                                                            class="badge bg-warning"
                                                        >
                                                            Menunggu
                                                        </span>
                                                    @elseif ($request->status == "approved")
                                                        <span
                                                            class="badge bg-success"
                                                        >
                                                            Disetujui
                                                        </span>
                                                    @else
                                                        <span
                                                            class="badge bg-danger"
                                                        >
                                                            Ditolak
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a
                                                        href="{{ route("superadmin.subscription-requests.show", $request) }}"
                                                        class="btn btn-sm btn-outline-primary"
                                                    >
                                                        <i
                                                            class="bx bx-show"
                                                        ></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if ($subscriptionPackage->subscriptionRequests->count() > 10)
                                <div class="text-center mt-3">
                                    <a
                                        href="{{ route("superadmin.subscription-requests.index", ["package" => $subscriptionPackage->id_package]) }}"
                                        class="btn btn-outline-primary"
                                    >
                                        Lihat Semua Request
                                        ({{ $subscriptionPackage->subscriptionRequests->count() }})
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Package Preview -->
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="avatar mx-auto mb-3">
                            <span
                                class="avatar-initial rounded bg-label-primary"
                            >
                                <i class="bx bx-package bx-lg"></i>
                            </span>
                        </div>
                        <h5 class="mb-2">
                            {{ $subscriptionPackage->nama_paket }}
                        </h5>
                        <div class="mb-3">
                            <h4 class="text-primary mb-1">
                                {{ $subscriptionPackage->formatted_harga }}
                            </h4>
                            <small class="text-muted">
                                untuk {{ $subscriptionPackage->durasi_text }}
                            </small>
                        </div>
                        <span
                            class="badge {{ $subscriptionPackage->is_active ? "bg-success" : "bg-danger" }} mb-3"
                        >
                            {{ $subscriptionPackage->is_active ? "Paket Aktif" : "Paket Tidak Aktif" }}
                        </span>
                        <p class="text-muted mb-0">
                            {{ Str::limit($subscriptionPackage->deskripsi, 100) }}
                        </p>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="bx bx-bar-chart-alt-2 me-1"></i>
                            Statistik Request
                        </h6>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="d-flex flex-column">
                                    <div class="card-title mb-auto">
                                        <div class="badge bg-label-primary p-2">
                                            <i class="bx bx-list-ul bx-sm"></i>
                                        </div>
                                    </div>
                                    <span class="fw-semibold d-block mb-1">
                                        {{ $subscriptionPackage->subscriptionRequests->count() }}
                                    </span>
                                    <small class="text-muted">Total</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="d-flex flex-column">
                                    <div class="card-title mb-auto">
                                        <div class="badge bg-label-success p-2">
                                            <i class="bx bx-check bx-sm"></i>
                                        </div>
                                    </div>
                                    <span class="fw-semibold d-block mb-1">
                                        {{ $subscriptionPackage->subscriptionRequests->where("status", "approved")->count() }}
                                    </span>
                                    <small class="text-muted">Disetujui</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="d-flex flex-column">
                                    <div class="card-title mb-auto">
                                        <div class="badge bg-label-warning p-2">
                                            <i class="bx bx-time bx-sm"></i>
                                        </div>
                                    </div>
                                    <span class="fw-semibold d-block mb-1">
                                        {{ $subscriptionPackage->subscriptionRequests->where("status", "pending")->count() }}
                                    </span>
                                    <small class="text-muted">Menunggu</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price Calculator -->
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="bx bx-calculator me-1"></i>
                            Kalkulator Harga
                        </h6>
                        <div class="mb-3">
                            <label for="dapur_id" class="form-label">
                                ID Dapur
                            </label>
                            <input
                                type="number"
                                id="dapur_id"
                                class="form-control"
                                placeholder="Masukkan ID Dapur"
                                min="1"
                            />
                        </div>
                        <div class="price-calculation" style="display: none">
                            <div class="border-top pt-3">
                                <div
                                    class="d-flex justify-content-between mb-2"
                                >
                                    <span>Harga Dasar:</span>
                                    <span>
                                        {{ $subscriptionPackage->formatted_harga }}
                                    </span>
                                </div>
                                <div
                                    class="d-flex justify-content-between mb-2"
                                >
                                    <span>ID Dapur:</span>
                                    <span class="dapur-id-display">-</span>
                                </div>
                                <div
                                    class="d-flex justify-content-between border-top pt-2"
                                >
                                    <strong>Harga Final:</strong>
                                    <strong class="text-primary final-price">
                                        -
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dapurIdInput = document.getElementById('dapur_id');
            const priceCalculation =
                document.querySelector('.price-calculation');
            const dapurIdDisplay = document.querySelector('.dapur-id-display');
            const finalPrice = document.querySelector('.final-price');
            const basePrice = {{ $subscriptionPackage->harga }};

            dapurIdInput.addEventListener('input', function () {
                const dapurId = parseInt(this.value);

                if (dapurId && dapurId > 0) {
                    const finalAmount = basePrice + dapurId;

                    dapurIdDisplay.textContent = dapurId;
                    finalPrice.textContent =
                        'Rp ' +
                        new Intl.NumberFormat('id-ID').format(finalAmount);
                    priceCalculation.style.display = 'block';
                } else {
                    priceCalculation.style.display = 'none';
                }
            });
        });
    </script>
@endsection
