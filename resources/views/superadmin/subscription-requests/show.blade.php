{{-- resources/views/superadmin/subscription-requests/show.blade.php --}}
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
                        href="{{ route("superadmin.subscription-requests.index") }}"
                        class="text-muted me-2"
                    >
                        Request Subscription
                    </a>
                    <i class="bx bx-chevron-right me-2"></i>
                    <span class="text-dark">Detail Request</span>
                </nav>
                <h4 class="mb-1">Detail Request Subscription</h4>
                <p class="mb-0 text-muted">
                    Informasi lengkap request dari
                    {{ $subscriptionRequest->dapur->nama_dapur }}
                </p>
            </div>
        </div>

        <!-- Request Details -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5 class="mb-0">Informasi Request</h5>
                        <a
                            href="{{ route("superadmin.subscription-requests.index") }}"
                            class="btn btn-sm btn-outline-secondary"
                        >
                            <i class="bx bx-arrow-back me-1"></i>
                            Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span
                                            class="avatar-initial rounded bg-label-primary"
                                        >
                                            <i class="bx bx-building"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">
                                            {{ $subscriptionRequest->dapur->nama_dapur }}
                                        </h6>
                                        <small class="text-muted">
                                            ID Dapur:
                                            {{ $subscriptionRequest->dapur->id_dapur }}
                                        </small>
                                    </div>
                                </div>
                                <p class="mb-2">
                                    <strong>Tanggal Request:</strong>
                                    {{ $subscriptionRequest->tanggal_request->format("d M Y H:i") }}
                                </p>
                                @if ($subscriptionRequest->status !== "pending")
                                    <p class="mb-2">
                                        <strong>Tanggal Proses:</strong>
                                        {{ $subscriptionRequest->tanggal_approval->format("d M Y H:i") }}
                                    </p>
                                @endif

                                <p class="mb-0">
                                    <strong>Status:</strong>
                                    <span
                                        class="badge bg-label-{{ $subscriptionRequest->status_badge }}"
                                    >
                                        {{ $subscriptionRequest->status_text }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <div class="border-start ps-3">
                                    <h6 class="mb-2">Detail Paket</h6>
                                    <p class="mb-1">
                                        <strong>Paket:</strong>
                                        {{ $subscriptionRequest->package->nama_paket }}
                                    </p>
                                    <p class="mb-1">
                                        <strong>Durasi:</strong>
                                        {{ $subscriptionRequest->package->durasi_text }}
                                    </p>
                                    @if ($subscriptionRequest->promoCode)
                                        <p class="mb-1">
                                            <strong>Promo:</strong>
                                            <span class="badge bg-label-info">
                                                {{ $subscriptionRequest->promoCode->kode_promo }}
                                            </span>
                                            (-{{ $subscriptionRequest->promoCode->persentase_diskon }}%)
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Pricing Breakdown -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="mb-2">Rincian Harga</h6>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-muted">
                                                Harga Dasar Paket
                                            </td>
                                            <td class="text-end">
                                                Rp
                                                {{ number_format($subscriptionRequest->package->harga, 0, ",", ".") }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">
                                                Tambahan ID Dapur
                                            </td>
                                            <td class="text-end">
                                                Rp
                                                {{ number_format($subscriptionRequest->dapur->id_dapur, 0, ",", ".") }}
                                            </td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>Harga Asli</strong></td>
                                            <td class="text-end">
                                                <strong>
                                                    Rp
                                                    {{ number_format($subscriptionRequest->harga_asli, 0, ",", ".") }}
                                                </strong>
                                            </td>
                                        </tr>
                                        @if ($subscriptionRequest->diskon > 0)
                                            <tr>
                                                <td class="text-muted">
                                                    Diskon Promo
                                                </td>
                                                <td
                                                    class="text-end text-success"
                                                >
                                                    -Rp
                                                    {{ number_format($subscriptionRequest->diskon, 0, ",", ".") }}
                                                </td>
                                            </tr>
                                        @endif

                                        <tr class="border-top">
                                            <td>
                                                <strong>Harga Final</strong>
                                            </td>
                                            <td class="text-end">
                                                <strong>
                                                    Rp
                                                    {{ number_format($subscriptionRequest->harga_final, 0, ",", ".") }}
                                                </strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Bukti Transfer -->
                        @if ($subscriptionRequest->bukti_transfer)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6 class="mb-2">Bukti Transfer</h6>
                                    <div class="text-center">
                                        <img
                                            src="{{ Storage::url($subscriptionRequest->bukti_transfer) }}"
                                            alt="Bukti Transfer"
                                            class="img-fluid rounded border"
                                            style="
                                                max-width: 400px;
                                                max-height: 300px;
                                            "
                                        />
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Catatan Approval/Reject -->
                        @if ($subscriptionRequest->catatan)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6 class="mb-2">Catatan Proses</h6>
                                    <div class="alert alert-info">
                                        {{ $subscriptionRequest->catatan }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Approval/Reject Form -->
                        @if ($subscriptionRequest->status === "pending")
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6 class="mb-3">Proses Request</h6>
                                    <div class="d-flex gap-3">
                                        <form
                                            method="POST"
                                            action="{{ route("superadmin.subscription-requests.approve", $subscriptionRequest) }}"
                                            class="flex-fill"
                                        >
                                            @csrf
                                            <div class="mb-3">
                                                <label
                                                    for="catatan_approve"
                                                    class="form-label"
                                                >
                                                    Catatan Approval (Opsional)
                                                </label>
                                                <textarea
                                                    name="catatan"
                                                    id="catatan_approve"
                                                    class="form-control"
                                                    rows="2"
                                                    placeholder="Catatan untuk dapur..."
                                                ></textarea>
                                            </div>
                                            <button
                                                type="submit"
                                                class="btn btn-success"
                                            >
                                                <i class="bx bx-check me-1"></i>
                                                Setujui Request
                                            </button>
                                        </form>
                                        <form
                                            method="POST"
                                            action="{{ route("superadmin.subscription-requests.reject", $subscriptionRequest) }}"
                                            class="flex-fill"
                                        >
                                            @csrf
                                            <div class="mb-3">
                                                <label
                                                    for="catatan_reject"
                                                    class="form-label"
                                                >
                                                    Alasan Reject (Wajib)
                                                </label>
                                                <textarea
                                                    name="catatan"
                                                    id="catatan_reject"
                                                    class="form-control"
                                                    rows="2"
                                                    placeholder="Alasan penolakan..."
                                                    required
                                                ></textarea>
                                            </div>
                                            <button
                                                type="submit"
                                                class="btn btn-danger"
                                                onclick="return confirm('Yakin menolak request ini?')"
                                            >
                                                <i class="bx bx-x me-1"></i>
                                                Tolak Request
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Quick Stats -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Statistik Dapur</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex justify-content-between mb-2">
                                <span class="text-muted">
                                    Subscription Aktif:
                                </span>
                                <span>
                                    {{ $subscriptionRequest->dapur->subscription_end ? \Carbon\Carbon::parse($subscriptionRequest->dapur->subscription_end)->format("d M Y") : "Tidak Ada" }}
                                </span>
                            </li>
                            <li class="d-flex justify-content-between mb-2">
                                <span class="text-muted">
                                    Total Request Sebelumnya:
                                </span>
                                <span>
                                    {{ $subscriptionRequest->dapur->subscriptionRequests->count() - 1 }}
                                </span>
                            </li>
                            <li class="d-flex justify-content-between mb-0">
                                <span class="text-muted">
                                    Harga Final Request Ini:
                                </span>
                                <span class="text-success">
                                    Rp
                                    {{ number_format($subscriptionRequest->harga_final, 0, ",", ".") }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Help -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="bx bx-help-circle me-1"></i>
                            Panduan Proses
                        </h6>
                        <ul class="list-unstyled small">
                            <li class="mb-2">
                                <i class="bx bx-check text-success me-1"></i>
                                Approve: Setujui dan aktivasi subscription
                            </li>
                            <li class="mb-2">
                                <i class="bx bx-check text-success me-1"></i>
                                Reject: Tolak dengan alasan wajib
                            </li>
                            <li class="mb-0">
                                <i class="bx bx-check text-success me-1"></i>
                                Masa aktif mulai dari tanggal approve
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
