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
                        href="{{ route("superadmin.promo-codes.index") }}"
                        class="text-muted me-2"
                    >
                        Kode Promo
                    </a>
                    <i class="bx bx-chevron-right me-2"></i>
                    <span class="text-dark">Tambah Promo</span>
                </nav>
                <h4 class="mb-1">Tambah Kode Promo</h4>
                <p class="mb-0 text-muted">
                    Buat kode promo baru untuk subscription
                </p>
            </div>
        </div>

        <!-- Form -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Promo</h5>
                    </div>
                    <div class="card-body">
                        <form
                            method="POST"
                            action="{{ route("superadmin.promo-codes.store") }}"
                        >
                            @csrf

                            <!-- Kode Promo -->
                            <div class="mb-3">
                                <label for="kode_promo" class="form-label">
                                    Kode Promo
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="kode_promo"
                                    id="kode_promo"
                                    class="form-control @error("kode_promo") is-invalid @enderror"
                                    value="{{ old("kode_promo") }}"
                                    placeholder="Contoh: PREMIUM20"
                                    required
                                />
                                @error("kode_promo")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Persentase Diskon -->
                            <div class="mb-3">
                                <label
                                    for="persentase_diskon"
                                    class="form-label"
                                >
                                    Persentase Diskon
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input
                                        type="number"
                                        name="persentase_diskon"
                                        id="persentase_diskon"
                                        class="form-control @error("persentase_diskon") is-invalid @enderror"
                                        value="{{ old("persentase_diskon") }}"
                                        min="1"
                                        max="100"
                                        placeholder="20"
                                        required
                                    />
                                    <span class="input-group-text">%</span>
                                </div>
                                @error("persentase_diskon")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Tanggal Mulai -->
                            <div class="mb-3">
                                <label for="tanggal_mulai" class="form-label">
                                    Tanggal Mulai
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="date"
                                    name="tanggal_mulai"
                                    id="tanggal_mulai"
                                    class="form-control @error("tanggal_mulai") is-invalid @enderror"
                                    value="{{ old("tanggal_mulai") }}"
                                    min="{{ now()->format("Y-m-d") }}"
                                    required
                                />
                                @error("tanggal_mulai")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Tanggal Berakhir -->
                            <div class="mb-3">
                                <label
                                    for="tanggal_berakhir"
                                    class="form-label"
                                >
                                    Tanggal Berakhir
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="date"
                                    name="tanggal_berakhir"
                                    id="tanggal_berakhir"
                                    class="form-control @error("tanggal_berakhir") is-invalid @enderror"
                                    value="{{ old("tanggal_berakhir") }}"
                                    required
                                />
                                @error("tanggal_berakhir")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input
                                        type="checkbox"
                                        name="is_active"
                                        id="is_active"
                                        class="form-check-input"
                                        value="1"
                                        {{ old("is_active", true) ? "checked" : "" }}
                                    />
                                    <label
                                        class="form-check-label"
                                        for="is_active"
                                    >
                                        Promo Aktif
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Hanya promo aktif yang bisa digunakan
                                </small>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i>
                                    Simpan Promo
                                </button>
                                <a
                                    href="{{ route("superadmin.promo-codes.index") }}"
                                    class="btn btn-outline-secondary"
                                >
                                    <i class="bx bx-arrow-back me-1"></i>
                                    Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Preview Promo</h6>
                    </div>
                    <div class="card-body">
                        <div class="promo-preview text-center">
                            <div class="mb-3">
                                <span
                                    class="badge bg-label-info fs-4 px-3 py-2 mb-2 d-inline-block"
                                >
                                    KODEPROMO
                                </span>
                                <h6 class="promo-code mb-1">KODEPROMO</h6>
                                <p class="text-muted small mb-0">
                                    Masukkan kode di halaman pembayaran
                                </p>
                            </div>
                            <div class="promo-details">
                                <div
                                    class="d-flex justify-content-between mb-2"
                                >
                                    <span class="text-muted">Diskon:</span>
                                    <strong class="promo-discount">0%</strong>
                                </div>
                                <div
                                    class="d-flex justify-content-between mb-2"
                                >
                                    <span class="text-muted">Berlaku:</span>
                                    <strong class="promo-period">-</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Status:</span>
                                    <span
                                        class="badge promo-status bg-label-success"
                                    >
                                        Aktif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bx bx-help-circle me-1"></i>
                            Bantuan
                        </h6>
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-1">
                                <i class="bx bx-check text-success me-1"></i>
                                Kode promo otomatis uppercase
                            </li>
                            <li class="mb-1">
                                <i class="bx bx-check text-success me-1"></i>
                                Periode mulai hari ini atau setelahnya
                            </li>
                            <li class="mb-1">
                                <i class="bx bx-check text-success me-1"></i>
                                Diskon berlaku untuk harga final subscription
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Form elements
            const kodeInput = document.getElementById('kode_promo');
            const diskonInput = document.getElementById('persentase_diskon');
            const mulaiInput = document.getElementById('tanggal_mulai');
            const berakhirInput = document.getElementById('tanggal_berakhir');
            const statusCheck = document.getElementById('is_active');

            // Preview elements
            const previewCode = document.querySelector('.promo-code');
            const previewDiscount = document.querySelector('.promo-discount');
            const previewPeriod = document.querySelector('.promo-period');
            const previewStatus = document.querySelector('.promo-status');

            // Update preview functions
            function updatePreview() {
                // Update code
                const kode = kodeInput.value.toUpperCase() || 'KODEPROMO';
                previewCode.textContent = kode;
                document.querySelector('.badge').textContent = kode;

                // Update discount
                const diskon = parseInt(diskonInput.value) || 0;
                previewDiscount.textContent = diskon + '%';

                // Update period
                const mulai = mulaiInput.value
                    ? new Date(mulaiInput.value).toLocaleDateString('id-ID', {
                          day: 'numeric',
                          month: 'short',
                          year: 'numeric',
                      })
                    : ' - ';
                const berakhir = berakhirInput.value
                    ? new Date(berakhirInput.value).toLocaleDateString(
                          'id-ID',
                          { day: 'numeric', month: 'short', year: 'numeric' },
                      )
                    : ' - ';
                previewPeriod.textContent = mulai + ' - ' + berakhir;

                // Update status
                if (statusCheck.checked) {
                    previewStatus.textContent = 'Aktif';
                    previewStatus.className =
                        'badge promo-status bg-label-success';
                } else {
                    previewStatus.textContent = 'Tidak Aktif';
                    previewStatus.className =
                        'badge promo-status bg-label-danger';
                }
            }

            // Add event listeners
            kodeInput.addEventListener('input', updatePreview);
            diskonInput.addEventListener('input', updatePreview);
            mulaiInput.addEventListener('change', updatePreview);
            berakhirInput.addEventListener('change', updatePreview);
            statusCheck.addEventListener('change', updatePreview);

            // Initial update
            updatePreview();
        });
    </script>
@endsection
