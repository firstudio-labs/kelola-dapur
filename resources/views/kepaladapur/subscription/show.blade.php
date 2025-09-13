@extends("template_kepala_dapur.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <nav class="d-flex align-items-center mb-2">
                    <a
                        href="{{ route("kepala-dapur.dashboard", $dapur) }}"
                        class="text-muted me-2"
                    >
                        <i class="bx bx-home-alt me-1"></i>
                        Dashboard
                    </a>
                    <i class="bx bx-chevron-right me-2"></i>
                    <a
                        href="{{ route("kepala-dapur.subscription.index", $dapur) }}"
                        class="text-muted me-2"
                    >
                        Subscription
                    </a>
                    <i class="bx bx-chevron-right me-2"></i>
                    <span class="text-dark">Detail Request</span>
                </nav>
                <h4 class="mb-1">Detail Request Subscription</h4>
                <p class="mb-0 text-muted">
                    Informasi request subscription Anda
                </p>
            </div>
        </div>

        <!-- Request Details -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5 class="mb-0">Informasi Request</h5>
                        <a
                            href="{{ route("kepala-dapur.subscription.index", $dapur) }}"
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
                                            {{ $dapur->nama_dapur }}
                                        </h6>
                                        <small class="text-muted">
                                            ID Dapur: {{ $dapur->id_dapur }}
                                        </small>
                                    </div>
                                </div>
                                <p class="mb-2">
                                    <strong>Tanggal Request:</strong>
                                    {{ $subscriptionRequest->tanggal_request->format("d M Y H:i") }}
                                </p>
                                @if ($subscriptionRequest->status !== "pending")
                                    <p class="mb-0">
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
                                        <p class="mb-0">
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
                                                {{ number_format($dapur->id_dapur, 0, ",", ".") }}
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
                                    <h6 class="mb-2">Bukti Transfer Anda</h6>
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

                        <!-- Catatan dari Admin -->
                        @if ($subscriptionRequest->catatan)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6 class="mb-2">Catatan dari Admin</h6>
                                    <div class="alert alert-info">
                                        {{ $subscriptionRequest->catatan }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Cancel Button (if pending) -->
                        @if ($subscriptionRequest->status === "pending")
                            <div class="row mt-4">
                                <div class="col-12">
                                    <form
                                        method="POST"
                                        action="{{ route("kepala-dapur.subscription.cancel", [$dapur, $subscriptionRequest]) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('Yakin ingin membatalkan request ini?')"
                                    >
                                        @csrf
                                        @method("DELETE")
                                        <button
                                            type="submit"
                                            class="btn btn-outline-danger"
                                        >
                                            <i class="bx bx-x me-1"></i>
                                            Batalkan Request
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="bx bx-info-circle me-1"></i>
                            Langkah Selanjutnya
                        </h6>
                        @if ($subscriptionRequest->status === "pending")
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    <i class="bx bx-time text-warning me-1"></i>
                                    Tunggu approval dari superadmin (1-2 hari
                                    kerja)
                                </li>
                                <li class="mb-0">
                                    <i
                                        class="bx bx-check text-success me-1"
                                    ></i>
                                    Jika approved, subscription aktif segera
                                </li>
                            </ul>
                        @elseif ($subscriptionRequest->status === "approved")
                            <ul class="list-unstyled small">
                                <li class="mb-0">
                                    <i
                                        class="bx bx-check text-success me-1"
                                    ></i>
                                    Subscription sudah aktif! Nikmati fitur
                                    premium.
                                </li>
                            </ul>
                        @else
                            <ul class="list-unstyled small">
                                <li class="mb-0">
                                    <i class="bx bx-x text-danger me-1"></i>
                                    Request ditolak. Silakan buat request baru.
                                </li>
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
