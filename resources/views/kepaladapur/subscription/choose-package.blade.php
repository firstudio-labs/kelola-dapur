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
                    <span class="text-dark">Pilih Paket</span>
                </nav>
                <h4 class="mb-1">Pilih Paket Subscription</h4>
                <p class="mb-0 text-muted">
                    Pilih paket subscription yang sesuai untuk
                    {{ $dapur->nama_dapur }}
                </p>
            </div>
        </div>

        <!-- Success/Error Messages -->
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

        <!-- Available Packages -->
        @if ($packages->count() > 0)
            <div class="row">
                @foreach ($packages as $package)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div
                            class="card h-100 package-card"
                            data-package="{{ $package->id_package }}"
                        >
                            <div class="card-header text-center">
                                <div class="avatar avatar-lg mb-3">
                                    <span
                                        class="avatar-initial rounded bg-label-primary"
                                    >
                                        <i class="bx bx-package bx-md"></i>
                                    </span>
                                </div>
                                <h5 class="mb-1">
                                    {{ $package->nama_paket }}
                                </h5>
                                <h3 class="text-primary mb-0">
                                    {{ $package->formatted_harga }}
                                </h3>
                                <small class="text-muted">
                                    per {{ $package->durasi_text }}
                                </small>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    {{ $package->deskripsi }}
                                </p>
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2">
                                        <i
                                            class="bx bx-check text-success me-2"
                                        ></i>
                                        Akses penuh fitur sistem
                                    </li>
                                    <li class="mb-2">
                                        <i
                                            class="bx bx-check text-success me-2"
                                        ></i>
                                        Support 24/7
                                    </li>
                                    <li class="mb-2">
                                        <i
                                            class="bx bx-check text-success me-2"
                                        ></i>
                                        Backup data otomatis
                                    </li>
                                    <li class="mb-2">
                                        <i
                                            class="bx bx-check text-success me-2"
                                        ></i>
                                        Update gratis
                                    </li>
                                </ul>
                            </div>
                            <div class="card-footer">
                                <button
                                    type="button"
                                    class="btn btn-primary w-100 select-package-btn"
                                    data-package-id="{{ $package->id_package }}"
                                    data-package-name="{{ $package->nama_paket }}"
                                    data-package-price="{{ $package->harga }}"
                                    data-package-duration="{{ $package->durasi_text }}"
                                >
                                    <i class="bx bx-credit-card me-1"></i>
                                    Pilih Paket Ini
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="card">
                <div class="card-body">
                    <div class="text-center py-6">
                        <i class="bx bx-package bx-lg text-muted mb-3"></i>
                        <h5 class="mb-1">Tidak ada paket tersedia</h5>
                        <p class="text-muted mb-3">
                            Saat ini tidak ada paket subscription yang tersedia.
                        </p>
                        <a
                            href="{{ route("kepala-dapur.subscription.index", $dapur) }}"
                            class="btn btn-outline-primary"
                        >
                            <i class="bx bx-arrow-back me-1"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Proses Pembayaran</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>
                </div>
                <form
                    method="POST"
                    action="{{ route("kepala-dapur.subscription.process-payment", $dapur) }}"
                    enctype="multipart/form-data"
                    id="paymentForm"
                >
                    @csrf
                    <input
                        type="hidden"
                        name="id_package"
                        id="selected_package_id"
                    />

                    <div class="modal-body">
                        <!-- Package Summary -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h6
                                            class="mb-1"
                                            id="summary-package-name"
                                        >
                                            -
                                        </h6>
                                        <small
                                            class="text-muted"
                                            id="summary-duration"
                                        >
                                            -
                                        </small>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <h5
                                            class="text-primary mb-0"
                                            id="summary-price"
                                        >
                                            Rp 0
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Promo Code -->
                        <div class="mb-4">
                            <label for="kode_promo" class="form-label">
                                Kode Promo (opsional)
                            </label>
                            <div class="input-group">
                                <input
                                    type="text"
                                    name="kode_promo"
                                    id="kode_promo"
                                    class="form-control"
                                    placeholder="Masukkan kode promo..."
                                />
                                <button
                                    type="button"
                                    class="btn btn-outline-primary"
                                    id="validate-promo"
                                >
                                    <i class="bx bx-check me-1"></i>
                                    Validasi
                                </button>
                            </div>
                            <div id="promo-message" class="mt-2"></div>
                        </div>

                        <!-- Price Calculation -->
                        <div class="card border mb-4">
                            <div class="card-body">
                                <div
                                    class="d-flex justify-content-between mb-2"
                                >
                                    <span>Harga Paket:</span>
                                    <span id="calc-original-price">Rp 0</span>
                                </div>
                                <div
                                    class="d-flex justify-content-between mb-2"
                                    id="discount-row"
                                    style="display: none !important"
                                >
                                    <span>Diskon:</span>
                                    <span
                                        class="text-success"
                                        id="calc-discount"
                                    >
                                        -Rp 0
                                    </span>
                                </div>
                                <div
                                    class="d-flex justify-content-between mb-2"
                                >
                                    <span>
                                        ID Dapur ({{ $dapur->id_dapur }}):
                                    </span>
                                    <span>
                                        +Rp
                                        {{ number_format($dapur->id_dapur, 0, ",", ".") }}
                                    </span>
                                </div>
                                <hr />
                                <div class="d-flex justify-content-between">
                                    <strong>Total Pembayaran:</strong>
                                    <strong
                                        class="text-primary"
                                        id="calc-final-price"
                                    >
                                        Rp 0
                                    </strong>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Instructions -->
                        <div class="alert alert-info" role="alert">
                            <h6 class="alert-heading mb-2">
                                <i class="bx bx-info-circle me-1"></i>
                                Instruksi Pembayaran
                            </h6>
                            <p class="mb-2">
                                Silakan transfer ke rekening berikut:
                            </p>
                            <ul class="mb-2">
                                <li>
                                    <strong>Bank BCA:</strong>
                                    1234567890 a.n. PT Dapur Sistem
                                </li>
                                <li>
                                    <strong>Bank Mandiri:</strong>
                                    9876543210 a.n. PT Dapur Sistem
                                </li>
                                <li>
                                    <strong>Bank BRI:</strong>
                                    5555666677 a.n. PT Dapur Sistem
                                </li>
                            </ul>
                            <p class="mb-0">
                                <small>
                                    Setelah transfer, upload bukti transfer dan
                                    tunggu approval dari admin.
                                </small>
                            </p>
                        </div>

                        <!-- Upload Bukti Transfer -->
                        <div class="mb-3">
                            <label for="bukti_transfer" class="form-label">
                                Upload Bukti Transfer (opsional)
                            </label>
                            <input
                                type="file"
                                name="bukti_transfer"
                                id="bukti_transfer"
                                class="form-control @error("bukti_transfer") is-invalid @enderror"
                                accept="image/*"
                            />
                            <div class="form-text">
                                Format: JPG, PNG, max 2MB
                            </div>
                            @error("bukti_transfer")
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Preview Upload -->
                        <div id="image-preview" style="display: none">
                            <label class="form-label">
                                Preview Bukti Transfer:
                            </label>
                            <div class="border rounded p-2 mb-3">
                                <img
                                    id="preview-img"
                                    src=""
                                    alt="Preview"
                                    class="img-fluid"
                                    style="max-height: 200px"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal"
                        >
                            <i class="bx bx-x me-1"></i>
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-send me-1"></i>
                            Kirim Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .package-card {
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
            cursor: pointer;
        }
        .package-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .package-card.selected {
            border: 2px solid #696cff;
            box-shadow: 0 8px 25px rgba(105, 108, 255, 0.15);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const packageCards = document.querySelectorAll('.package-card');
            const selectButtons = document.querySelectorAll(
                '.select-package-btn',
            );
            const paymentModal = new bootstrap.Modal(
                document.getElementById('paymentModal'),
            );

            let selectedPackage = null;
            let validPromo = null;

            // Handle package selection
            selectButtons.forEach((btn) => {
                btn.addEventListener('click', function () {
                    selectedPackage = {
                        id: this.dataset.packageId,
                        name: this.dataset.packageName,
                        price: parseInt(this.dataset.packagePrice),
                        duration: this.dataset.packageDuration,
                    };

                    // Update modal content
                    document.getElementById('selected_package_id').value =
                        selectedPackage.id;
                    document.getElementById(
                        'summary-package-name',
                    ).textContent = selectedPackage.name;
                    document.getElementById('summary-duration').textContent =
                        selectedPackage.duration;
                    document.getElementById('summary-price').textContent =
                        'Rp ' + selectedPackage.price.toLocaleString('id-ID');

                    // Reset calculations
                    validPromo = null;
                    document.getElementById('kode_promo').value = '';
                    document.getElementById('promo-message').innerHTML = '';
                    updatePriceCalculation();

                    // Show modal
                    paymentModal.show();
                });
            });

            // Handle promo validation
            document
                .getElementById('validate-promo')
                .addEventListener('click', function () {
                    const promoCode = document
                        .getElementById('kode_promo')
                        .value.trim();
                    const messageDiv = document.getElementById('promo-message');

                    if (!promoCode) {
                        messageDiv.innerHTML =
                            '<small class="text-warning">Masukkan kode promo terlebih dahulu</small>';
                        return;
                    }

                    // Show loading
                    this.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-1"></span>Validasi...';
                    this.disabled = true;

                    // Calculate price with current package
                    calculatePrice(selectedPackage.id, promoCode);
                });

            // Handle file upload preview
            document
                .getElementById('bukti_transfer')
                .addEventListener('change', function () {
                    const file = this.files[0];
                    const preview = document.getElementById('image-preview');
                    const previewImg = document.getElementById('preview-img');

                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            previewImg.src = e.target.result;
                            preview.style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    } else {
                        preview.style.display = 'none';
                    }
                });

            function calculatePrice(packageId, promoCode = '') {
                fetch(
                    '{{ route("kepala-dapur.subscription.calculate-price", $dapur) }}',
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                        },
                        body: JSON.stringify({
                            id_package: packageId,
                            kode_promo: promoCode,
                        }),
                    },
                )
                    .then((response) => response.json())
                    .then((data) => {
                        const messageDiv =
                            document.getElementById('promo-message');
                        const validateBtn =
                            document.getElementById('validate-promo');

                        if (data.success) {
                            if (data.data.promo_valid && promoCode) {
                                validPromo = {
                                    code: promoCode,
                                    discount: data.data.diskon,
                                };
                                messageDiv.innerHTML = `<small class="text-success">${data.data.promo_message}</small>`;
                            } else if (promoCode && !data.data.promo_valid) {
                                validPromo = null;
                                messageDiv.innerHTML = `<small class="text-danger">${data.data.promo_message || 'Kode promo tidak valid'}</small>`;
                            } else {
                                validPromo = null;
                                messageDiv.innerHTML = '';
                            }

                            updatePriceCalculation(data.data);
                        } else {
                            messageDiv.innerHTML =
                                '<small class="text-danger">Gagal memvalidasi kode promo</small>';
                            validPromo = null;
                            updatePriceCalculation();
                        }

                        // Reset validate button
                        validateBtn.innerHTML =
                            '<i class="bx bx-check me-1"></i>Validasi';
                        validateBtn.disabled = false;
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                        document.getElementById('promo-message').innerHTML =
                            '<small class="text-danger">Terjadi kesalahan</small>';

                        // Reset validate button
                        const validateBtn =
                            document.getElementById('validate-promo');
                        validateBtn.innerHTML =
                            '<i class="bx bx-check me-1"></i>Validasi';
                        validateBtn.disabled = false;
                    });
            }

            function updatePriceCalculation(data = null) {
                if (!selectedPackage) return;

                const originalPrice = selectedPackage.price;
                const discount = data ? data.diskon : 0;
                const finalPrice = data
                    ? data.harga_final
                    : originalPrice + {{ $dapur->id_dapur }};

                document.getElementById('calc-original-price').textContent =
                    'Rp ' + originalPrice.toLocaleString('id-ID');
                document.getElementById('calc-final-price').textContent =
                    'Rp ' + finalPrice.toLocaleString('id-ID');

                const discountRow = document.getElementById('discount-row');
                if (discount > 0) {
                    document.getElementById('calc-discount').textContent =
                        '-Rp ' + discount.toLocaleString('id-ID');
                    discountRow.style.display = 'flex';
                } else {
                    discountRow.style.display = 'none';
                }
            }

            // Reset promo when input changes
            document
                .getElementById('kode_promo')
                .addEventListener('input', function () {
                    if (this.value.trim() === '') {
                        validPromo = null;
                        document.getElementById('promo-message').innerHTML = '';
                        updatePriceCalculation();
                    }
                });
        });
    </script>
@endsection
