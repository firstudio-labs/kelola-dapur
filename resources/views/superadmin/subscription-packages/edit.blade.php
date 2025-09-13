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
                    <span class="text-dark">Edit Paket</span>
                </nav>
                <h4 class="mb-1">Edit Paket Subscription</h4>
                <p class="mb-0 text-muted">
                    Perbarui informasi paket subscription
                </p>
            </div>
        </div>

        <!-- Form -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Paket</h5>
                    </div>
                    <div class="card-body">
                        <form
                            method="POST"
                            action="{{ route("superadmin.subscription-packages.update", $subscriptionPackage) }}"
                        >
                            @csrf
                            @method("PUT")

                            <!-- Nama Paket -->
                            <div class="mb-3">
                                <label for="nama_paket" class="form-label">
                                    Nama Paket
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="nama_paket"
                                    id="nama_paket"
                                    class="form-control @error("nama_paket") is-invalid @enderror"
                                    value="{{ old("nama_paket", $subscriptionPackage->nama_paket) }}"
                                    placeholder="Contoh: Paket Premium"
                                    required
                                />
                                @error("nama_paket")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">
                                    Deskripsi
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea
                                    name="deskripsi"
                                    id="deskripsi"
                                    rows="4"
                                    class="form-control @error("deskripsi") is-invalid @enderror"
                                    placeholder="Masukkan deskripsi lengkap paket subscription..."
                                    required
                                >
{{ old("deskripsi", $subscriptionPackage->deskripsi) }}</textarea
                                >
                                @error("deskripsi")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Harga -->
                            <div class="mb-3">
                                <label for="harga" class="form-label">
                                    Harga Dasar
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input
                                        type="number"
                                        name="harga"
                                        id="harga"
                                        class="form-control @error("harga") is-invalid @enderror"
                                        value="{{ old("harga", $subscriptionPackage->harga) }}"
                                        min="0"
                                        step="1000"
                                        placeholder="2000000"
                                        required
                                    />
                                </div>
                                <small class="form-text text-muted">
                                    Harga akan ditambah dengan ID dapur untuk
                                    perhitungan final
                                </small>
                                @error("harga")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Durasi -->
                            <div class="mb-3">
                                <label for="durasi_hari" class="form-label">
                                    Durasi (Hari)
                                    <span class="text-danger">*</span>
                                </label>
                                <select
                                    name="durasi_hari"
                                    id="durasi_hari"
                                    class="form-select @error("durasi_hari") is-invalid @enderror"
                                    required
                                >
                                    <option value="">Pilih Durasi</option>
                                    <option
                                        value="30"
                                        {{ old("durasi_hari", $subscriptionPackage->durasi_hari) == "30" ? "selected" : "" }}
                                    >
                                        30 Hari (1 Bulan)
                                    </option>
                                    <option
                                        value="90"
                                        {{ old("durasi_hari", $subscriptionPackage->durasi_hari) == "90" ? "selected" : "" }}
                                    >
                                        90 Hari (3 Bulan)
                                    </option>
                                    <option
                                        value="180"
                                        {{ old("durasi_hari", $subscriptionPackage->durasi_hari) == "180" ? "selected" : "" }}
                                    >
                                        180 Hari (6 Bulan)
                                    </option>
                                    <option
                                        value="365"
                                        {{ old("durasi_hari", $subscriptionPackage->durasi_hari) == "365" ? "selected" : "" }}
                                    >
                                        365 Hari (1 Tahun)
                                    </option>
                                </select>
                                @error("durasi_hari")
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
                                        {{ old("is_active", $subscriptionPackage->is_active) ? "checked" : "" }}
                                    />
                                    <label
                                        class="form-check-label"
                                        for="is_active"
                                    >
                                        Paket Aktif
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Hanya paket aktif yang bisa dipilih oleh
                                    dapur
                                </small>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i>
                                    Update Paket
                                </button>
                                <a
                                    href="{{ route("superadmin.subscription-packages.index") }}"
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
                        <h6 class="mb-0">Preview Paket</h6>
                    </div>
                    <div class="card-body">
                        <div class="package-preview">
                            <div class="text-center mb-3">
                                <div class="avatar mx-auto mb-2">
                                    <span
                                        class="avatar-initial rounded bg-label-primary"
                                    >
                                        <i class="bx bx-package bx-lg"></i>
                                    </span>
                                </div>
                                <h6 class="package-name mb-1">
                                    {{ $subscriptionPackage->nama_paket }}
                                </h6>
                                <p
                                    class="package-description text-muted small mb-0"
                                >
                                    {{ Str::limit($subscriptionPackage->deskripsi, 50) }}
                                </p>
                            </div>
                            <div class="package-details">
                                <div
                                    class="d-flex justify-content-between mb-2"
                                >
                                    <span class="text-muted">Harga Dasar:</span>
                                    <strong class="package-price">
                                        Rp
                                        {{ number_format($subscriptionPackage->harga, 0, ",", ".") }}
                                    </strong>
                                </div>
                                <div
                                    class="d-flex justify-content-between mb-2"
                                >
                                    <span class="text-muted">Durasi:</span>
                                    <strong class="package-duration">
                                        {{ $subscriptionPackage->durasi_hari }}
                                        Hari
                                    </strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Status:</span>
                                    <span
                                        class="badge package-status bg-label-{{ $subscriptionPackage->is_active ? "success" : "danger" }}"
                                    >
                                        {{ $subscriptionPackage->is_active ? "Aktif" : "Tidak Aktif" }}
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
                                Harga final = Harga dasar + ID dapur
                            </li>
                            <li class="mb-1">
                                <i class="bx bx-check text-success me-1"></i>
                                Durasi dalam hari akan menentukan masa aktif
                            </li>
                            <li class="mb-1">
                                <i class="bx bx-check text-success me-1"></i>
                                Paket tidak aktif tidak akan ditampilkan
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
            const namaInput = document.getElementById('nama_paket');
            const deskripsiInput = document.getElementById('deskripsi');
            const hargaInput = document.getElementById('harga');
            const durasiSelect = document.getElementById('durasi_hari');
            const statusCheck = document.getElementById('is_active');

            // Preview elements
            const previewName = document.querySelector('.package-name');
            const previewDescription = document.querySelector(
                '.package-description',
            );
            const previewPrice = document.querySelector('.package-price');
            const previewDuration = document.querySelector('.package-duration');
            const previewStatus = document.querySelector('.package-status');

            // Update preview functions
            function updatePreview() {
                // Update name
                previewName.textContent =
                    namaInput.value ||
                    '{{ $subscriptionPackage->nama_paket }}';

                // Update description
                const desc =
                    deskripsiInput.value ||
                    '{{ $subscriptionPackage->deskripsi }}';
                previewDescription.textContent =
                    desc.length > 50 ? desc.substring(0, 50) + '...' : desc;

                // Update price
                const harga =
                    parseFloat(hargaInput.value) ||
                    {{ $subscriptionPackage->harga }};
                previewPrice.textContent =
                    'Rp ' + new Intl.NumberFormat('id-ID').format(harga);

                // Update duration
                const durasi =
                    durasiSelect.value ||
                    {{ $subscriptionPackage->durasi_hari }};
                if (durasi) {
                    const durasiText =
                        durasiSelect.options[durasiSelect.selectedIndex].text;
                    previewDuration.textContent = durasiText.split(' (')[0];
                } else {
                    previewDuration.textContent = '- Hari';
                }

                // Update status
                if (statusCheck.checked) {
                    previewStatus.textContent = 'Aktif';
                    previewStatus.className =
                        'badge package-status bg-label-success';
                } else {
                    previewStatus.textContent = 'Tidak Aktif';
                    previewStatus.className =
                        'badge package-status bg-label-danger';
                }
            }

            // Add event listeners
            namaInput.addEventListener('input', updatePreview);
            deskripsiInput.addEventListener('input', updatePreview);
            hargaInput.addEventListener('input', updatePreview);
            durasiSelect.addEventListener('change', updatePreview);
            statusCheck.addEventListener('change', updatePreview);

            // Initial update
            updatePreview();
        });
    </script>
@endsection
