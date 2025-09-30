@extends("template_kepala_dapur.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a
                                href="{{ route("kepala-dapur.dashboard", $dapur) }}"
                                class="text-muted me-2"
                            >
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Subscription</span>
                        </nav>
                        <h4 class="mb-1">Kelola Subscription</h4>
                        <p class="mb-0 text-muted">
                            Kelola subscription untuk {{ $dapur->nama_dapur }}
                        </p>
                    </div>
                    @if (! $pendingRequest && ! $currentSubscription)
                        <a
                            href="{{ route("kepala-dapur.subscription.choose-package", $dapur) }}"
                            class="btn btn-primary btn-sm"
                        >
                            <i class="bx bx-plus me-1"></i>
                            Berlangganan
                        </a>
                    @endif
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

        @if (session("warning"))
            <div
                class="alert alert-warning alert-dismissible mb-4"
                role="alert"
            >
                {{ session("warning") }}
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"
                ></button>
            </div>
        @endif

        <!-- Current Subscription Status -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Status Subscription</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span
                                    class="avatar-initial rounded bg-label-primary"
                                >
                                    <i class="bx bx-buildings"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $dapur->nama_dapur }}</h6>
                                <small class="text-muted">
                                    ID Dapur: {{ $dapur->id_dapur }}
                                </small>
                            </div>
                        </div>

                        @if ($dapur->subscription_end)
                            <p class="mb-2">
                                <strong>Status:</strong>
                                <span
                                    class="badge bg-label-{{ $dapur->getSubscriptionStatus() === "active" ? "success" : ($dapur->getSubscriptionStatus() === "expiring_soon" ? "warning" : "danger") }}"
                                >
                                    {{ $dapur->subscription_status_text }}
                                </span>
                            </p>
                            <p class="mb-2">
                                <strong>Berakhir:</strong>
                                {{ \Carbon\Carbon::parse($dapur->subscription_end)->format("d M Y") }}
                            </p>
                            @if ($dapur->getSubscriptionStatus() === "active")
                                <p class="mb-0">
                                    <strong>Sisa Hari:</strong>
                                    {{ \Carbon\Carbon::now()->diffInDays($dapur->subscription_end) }}
                                    hari
                                </p>
                            @endif
                        @else
                            <p class="mb-0">
                                <span class="badge bg-label-secondary">
                                    Belum Berlangganan
                                </span>
                            </p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        @if ($currentSubscription)
                            <div class="border-start ps-3">
                                <h6 class="mb-2">Subscription Aktif</h6>
                                <p class="mb-1">
                                    <strong>Paket:</strong>
                                    {{ $currentSubscription->package->nama_paket }}
                                </p>
                                <p class="mb-1">
                                    <strong>Harga:</strong>
                                    {{ $currentSubscription->formatted_harga_final }}
                                </p>
                                <p class="mb-0">
                                    <strong>Diaktivasi:</strong>
                                    {{ $currentSubscription->tanggal_approval->format("d M Y") }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Request Alert -->
        @if ($pendingRequest)
            <div class="alert alert-warning" role="alert">
                <div class="d-flex">
                    <div class="alert-icon rounded">
                        <i class="bx bx-time"></i>
                    </div>
                    <div class="ps-3">
                        <h6 class="alert-heading mb-1">
                            Request Subscription Pending
                        </h6>
                        <p class="mb-2">
                            Anda memiliki request subscription yang sedang
                            menunggu approval.
                        </p>
                        <div class="d-flex gap-2">
                            <a
                                href="{{ route("kepala-dapur.subscription.show", [$dapur, $pendingRequest]) }}"
                                class="btn btn-sm btn-outline-warning"
                            >
                                <i class="bx bx-show me-1"></i>
                                Lihat Detail
                            </a>
                            <form
                                method="POST"
                                action="{{ route("kepala-dapur.subscription.cancel", [$dapur, $pendingRequest]) }}"
                                class="d-inline"
                                onsubmit="return confirm('Yakin ingin membatalkan request subscription?')"
                            >
                                @csrf
                                @method("DELETE")
                                <button
                                    type="submit"
                                    class="btn btn-sm btn-outline-danger"
                                >
                                    <i class="bx bx-x me-1"></i>
                                    Batalkan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Subscription History -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Riwayat Subscription</h5>
            </div>
            <div class="card-body">
                @if ($dapur->subscriptionRequests->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal Request</th>
                                    <th>Paket</th>
                                    <th>Promo</th>
                                    <th>Harga Final</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dapur->subscriptionRequests as $request)
                                    <tr>
                                        <td>
                                            {{ $request->tanggal_request->format("d M Y H:i") }}
                                        </td>
                                        <td>
                                            <strong>
                                                {{ $request->package->nama_paket }}
                                            </strong>
                                            <br />
                                            <small class="text-muted">
                                                {{ $request->package->durasi_text }}
                                            </small>
                                        </td>
                                        <td>
                                            @if ($request->promoCode)
                                                <span
                                                    class="badge bg-label-info"
                                                >
                                                    {{ $request->promoCode->kode_promo }}
                                                </span>
                                                <br />
                                                <small class="text-muted">
                                                    -{{ $request->promoCode->persentase_diskon }}%
                                                </small>
                                            @else
                                                <span class="text-muted">
                                                    -
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>
                                                {{ $request->formatted_harga_final }}
                                            </strong>
                                            @if ($request->diskon > 0)
                                                <br />
                                                <small class="text-muted">
                                                    <del>
                                                        {{ $request->formatted_harga_asli }}
                                                    </del>
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-label-{{ $request->status_badge }}"
                                            >
                                                {{ $request->status_text }}
                                            </span>
                                            @if ($request->status !== "pending")
                                                <br />
                                                <small class="text-muted">
                                                    {{ $request->tanggal_approval->format("d M Y") }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a
                                                    href="{{ route("kepala-dapur.subscription.show", [$dapur, $request]) }}"
                                                    class="btn btn-sm btn-outline-primary"
                                                >
                                                    <i
                                                        class="bx bx-show me-1"
                                                    ></i>
                                                    Detail
                                                </a>
                                                @if ($request->status === "approved")
                                                    <a
                                                        href="{{ route("kepala-dapur.subscription.invoice", [$dapur, $request]) }}"
                                                        class="btn btn-sm btn-outline-success"
                                                        target="_blank"
                                                    >
                                                        <i
                                                            class="bx bx-printer me-1"
                                                        ></i>
                                                        Print
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bx bx-receipt bx-lg text-muted mb-3"></i>
                        <h6 class="mb-1">Belum ada riwayat subscription</h6>
                        <p class="text-muted mb-3">
                            Mulai dengan berlangganan paket pertama.
                        </p>
                        <a
                            href="{{ route("kepala-dapur.subscription.choose-package", $dapur) }}"
                            class="btn btn-primary"
                        >
                            <i class="bx bx-plus me-1"></i>
                            Pilih Paket
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Subscription Info -->
        <div class="row">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bx bx-info-circle me-1"></i>
                            Informasi Subscription
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="bx bx-check text-success me-2"></i>
                                Akses ke semua fitur sistem
                            </li>
                            <li class="mb-2">
                                <i class="bx bx-check text-success me-2"></i>
                                Support 24/7
                            </li>
                            <li class="mb-2">
                                <i class="bx bx-check text-success me-2"></i>
                                Update otomatis
                            </li>
                            <li class="mb-0">
                                <i class="bx bx-check text-success me-2"></i>
                                Backup data harian
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bx bx-help-circle me-1"></i>
                            Butuh Bantuan?
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            Tim support kami siap membantu Anda dengan segala
                            pertanyaan terkait subscription.
                        </p>
                        <div class="d-grid gap-2">
                            <a
                                href="mailto:support@dapur.com"
                                class="btn btn-outline-primary btn-sm"
                            >
                                <i class="bx bx-envelope me-1"></i>
                                Email Support
                            </a>
                            <a
                                href="tel:+62123456789"
                                class="btn btn-outline-success btn-sm"
                            >
                                <i class="bx bx-phone me-1"></i>
                                Telepon Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Check subscription expiry and show notification
            @if ($dapur->subscription_end && \Carbon\Carbon::now()->diffInDays($dapur->subscription_end) <= 7)
                // Show expiry warning if less than 7 days left
                setTimeout(function() {
                    if (!sessionStorage.getItem('subscription_warning_shown')) {
                        const toast = document.createElement('div');
                        toast.className = 'toast align-items-center text-white bg-warning border-0 position-fixed top-0 end-0 m-3';
                        toast.style.zIndex = '9999';
                        toast.innerHTML = `
                            <div class="d-flex">
                                <div class="toast-body">
                                    <i class="bx bx-alarm me-2"></i>
                                    Subscription Anda akan berakhir dalam {{ \Carbon\Carbon::now()->diffInDays($dapur->subscription_end) }} hari!
                                </div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                            </div>
                        `;
                        document.body.appendChild(toast);

                        const bsToast = new bootstrap.Toast(toast);
                        bsToast.show();

                        sessionStorage.setItem('subscription_warning_shown', 'true');
                    }
                }, 1000);
            @endif
        });
    </script>
@endsection
