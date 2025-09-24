@extends("template_admin.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div
                            class="d-flex justify-content-between align-items-center"
                        >
                            <div>
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
                                        href="{{ route("superadmin.dapur.index") }}"
                                        class="text-muted me-2"
                                    >
                                        Kelola Dapur
                                    </a>
                                    <i class="bx bx-chevron-right me-2"></i>
                                    <span class="text-dark">Edit Dapur</span>
                                </nav>
                                <h4 class="mb-1">
                                    Edit Dapur: {{ $dapur->nama_dapur }}
                                </h4>
                                <p class="mb-0 text-muted">
                                    Perbarui informasi dapur
                                </p>
                            </div>
                        </div>
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

        <!-- Form -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <form
                            action="{{ route("superadmin.dapur.update", $dapur) }}"
                            method="POST"
                            class="row g-3"
                        >
                            @csrf
                            @method("PUT")

                            <!-- Hidden fields for wilayah codes -->
                            <input
                                type="hidden"
                                name="province_code"
                                id="province_code"
                                value="{{ old("province_code", $dapur->province_code) }}"
                            />
                            <input
                                type="hidden"
                                name="regency_code"
                                id="regency_code"
                                value="{{ old("regency_code", $dapur->regency_code) }}"
                            />
                            <input
                                type="hidden"
                                name="district_code"
                                id="district_code"
                                value="{{ old("district_code", $dapur->district_code) }}"
                            />
                            <input
                                type="hidden"
                                name="village_code"
                                id="village_code"
                                value="{{ old("village_code", $dapur->village_code) }}"
                            />

                            <!-- Nama Dapur -->
                            <div class="col-12">
                                <label for="nama_dapur" class="form-label">
                                    Nama Dapur
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="nama_dapur"
                                    id="nama_dapur"
                                    required
                                    class="form-control @error("nama_dapur") is-invalid @enderror"
                                    placeholder="Contoh: Dapur Utama Jakarta"
                                    value="{{ old("nama_dapur", $dapur->nama_dapur) }}"
                                />
                                @error("nama_dapur")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Wilayah -->
                            <div class="col-md-6">
                                <label for="provinsi" class="form-label">
                                    Provinsi
                                    <span class="text-danger">*</span>
                                </label>
                                <select
                                    name="provinsi"
                                    id="provinsi"
                                    required
                                    class="form-select @error("provinsi") is-invalid @enderror"
                                >
                                    <option value="">Pilih Provinsi</option>
                                </select>
                                @error("provinsi")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="kabupaten_kota" class="form-label">
                                    Kabupaten/Kota
                                    <span class="text-danger">*</span>
                                </label>
                                <select
                                    name="kabupaten_kota"
                                    id="kabupaten_kota"
                                    required
                                    disabled
                                    class="form-select @error("kabupaten_kota") is-invalid @enderror"
                                >
                                    <option value="">Pilih Kota</option>
                                </select>
                                @error("kabupaten_kota")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="kecamatan" class="form-label">
                                    Kecamatan
                                    <span class="text-danger">*</span>
                                </label>
                                <select
                                    name="kecamatan"
                                    id="kecamatan"
                                    required
                                    disabled
                                    class="form-select @error("kecamatan") is-invalid @enderror"
                                >
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                                @error("kecamatan")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="kelurahan" class="form-label">
                                    Kelurahan
                                    <span class="text-danger">*</span>
                                </label>
                                <select
                                    name="kelurahan"
                                    id="kelurahan"
                                    required
                                    disabled
                                    class="form-select @error("kelurahan") is-invalid @enderror"
                                >
                                    <option value="">Pilih Kelurahan</option>
                                </select>
                                @error("kelurahan")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Alamat -->
                            <div class="col-12">
                                <label for="alamat" class="form-label">
                                    Alamat Lengkap
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea
                                    name="alamat"
                                    id="alamat"
                                    rows="4"
                                    required
                                    class="form-control @error("alamat") is-invalid @enderror"
                                    placeholder="Masukkan alamat lengkap dapur"
                                >
{{ old("alamat", $dapur->alamat) }}</textarea
                                >
                                @error("alamat")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Telepon -->
                            <div class="col-md-6">
                                <label for="telepon" class="form-label">
                                    Telepon
                                </label>
                                <input
                                    type="text"
                                    name="telepon"
                                    id="telepon"
                                    class="form-control @error("telepon") is-invalid @enderror"
                                    placeholder="Contoh: 0211234567"
                                    value="{{ old("telepon", $dapur->telepon) }}"
                                />
                                @error("telepon")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <small class="text-muted">
                                    Nomor telepon dapur (opsional)
                                </small>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label for="status" class="form-label">
                                    Status
                                    <span class="text-danger">*</span>
                                </label>
                                <select
                                    name="status"
                                    id="status"
                                    required
                                    class="form-select @error("status") is-invalid @enderror"
                                >
                                    <option value="">Pilih Status</option>
                                    <option
                                        value="active"
                                        {{ old("status", $dapur->status) === "active" ? "selected" : "" }}
                                    >
                                        Aktif
                                    </option>
                                    <option
                                        value="inactive"
                                        {{ old("status", $dapur->status) === "inactive" ? "selected" : "" }}
                                    >
                                        Tidak Aktif
                                    </option>
                                </select>
                                @error("status")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <small class="text-muted">
                                    Status dapur dalam sistem
                                </small>
                            </div>

                            <!-- Preview Card -->
                            <div class="col-12">
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Preview</h5>
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="avatar flex-shrink-0 me-3"
                                            >
                                                <span
                                                    class="avatar-initial rounded bg-label-primary"
                                                >
                                                    <i
                                                        class="bx bx-buildings"
                                                    ></i>
                                                </span>
                                            </div>
                                            <div>
                                                <div
                                                    class="d-flex align-items-center"
                                                >
                                                    <h6
                                                        class="mb-0"
                                                        id="preview-nama"
                                                    >
                                                        {{ old("nama_dapur", $dapur->nama_dapur) }}
                                                    </h6>
                                                    <span
                                                        class="badge ms-2"
                                                        id="preview-status-badge"
                                                    >
                                                        @if (old("status", $dapur->status) === "active")
                                                            <span
                                                                class="badge bg-label-success"
                                                            >
                                                                Aktif
                                                            </span>
                                                        @elseif (old("status", $dapur->status) === "inactive")
                                                            <span
                                                                class="badge bg-label-danger"
                                                            >
                                                                Tidak Aktif
                                                            </span>
                                                        @else
                                                            <span
                                                                class="badge bg-label-secondary"
                                                            >
                                                                Status
                                                            </span>
                                                        @endif
                                                    </span>
                                                </div>
                                                <small
                                                    class="text-muted"
                                                    id="preview-wilayah"
                                                >
                                                    {{ $dapur->getFullWilayahAttribute() ?: "Wilayah akan ditampilkan di sini" }}
                                                </small>
                                                <br />
                                                <small
                                                    class="text-muted"
                                                    id="preview-alamat"
                                                >
                                                    {{ old("alamat", $dapur->alamat) ?: "Alamat akan ditampilkan di sini" }}
                                                </small>
                                                <br />
                                                <small
                                                    class="text-muted"
                                                    id="preview-telepon"
                                                    @if(!old('telepon', $dapur->telepon)) style="display: none;" @endif
                                                >
                                                    <i
                                                        class="bx bx-phone me-1"
                                                    ></i>
                                                    <span
                                                        id="preview-telepon-text"
                                                    >
                                                        {{ old("telepon", $dapur->telepon) }}
                                                    </span>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div
                                class="col-12 d-flex justify-content-end gap-2"
                            >
                                <a
                                    href="{{ route("superadmin.dapur.index") }}"
                                    class="btn btn-outline-secondary"
                                >
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Perbarui Dapur
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info alert-dismissible" role="alert">
                    <div class="alert-heading d-flex align-items-center">
                        <i class="bx bx-info-circle me-2"></i>
                        <h5 class="mb-0">Tips</h5>
                    </div>
                    <ul class="list-disc list-inside mt-2">
                        <li>
                            Ketik untuk mencari provinsi, kabupaten/kota,
                            kecamatan, atau kelurahan
                        </li>
                        <li>
                            Perubahan nama dapur harus tetap unik dalam sistem
                        </li>
                        <li>
                            Jika mengubah wilayah, pilih provinsi, kemudian
                            kabupaten/kota, kecamatan, dan kelurahan secara
                            berurutan
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push("scripts")
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get elements
            const provinsiSelect = document.getElementById('provinsi');
            const kabupatenSelect = document.getElementById('kabupaten_kota');
            const kecamatanSelect = document.getElementById('kecamatan');
            const kelurahanSelect = document.getElementById('kelurahan');

            // Hidden code fields
            const provinceCodeInput = document.getElementById('province_code');
            const regencyCodeInput = document.getElementById('regency_code');
            const districtCodeInput = document.getElementById('district_code');
            const villageCodeInput = document.getElementById('village_code');

            // Form inputs for preview
            const namaDapurInput = document.getElementById('nama_dapur');
            const alamatInput = document.getElementById('alamat');
            const teleponInput = document.getElementById('telepon');
            const statusSelect = document.getElementById('status');

            // Preview elements
            const previewNama = document.getElementById('preview-nama');
            const previewAlamat = document.getElementById('preview-alamat');
            const previewTelepon = document.getElementById('preview-telepon');
            const previewTeleponText = document.getElementById(
                'preview-telepon-text',
            );
            const previewStatusBadge = document.getElementById(
                'preview-status-badge',
            );
            const previewWilayah = document.getElementById('preview-wilayah');

            // Current values from database
            const currentProvinsi =
                '{{ old("provinsi", $dapur->province_name ?? "") }}';
            const currentKabupaten =
                '{{ old("kabupaten_kota", $dapur->regency_name ?? "") }}';
            const currentKecamatan =
                '{{ old("kecamatan", $dapur->district_name ?? "") }}';
            const currentKelurahan =
                '{{ old("kelurahan", $dapur->village_name ?? "") }}';

            // Loading state manager
            const LoadingState = {
                show(select, message = 'Memuat...') {
                    select.innerHTML = `<option value="">${message}</option>`;
                    select.disabled = true;
                },
                hide(select, placeholder = 'Pilih...') {
                    if (
                        select.children.length === 0 ||
                        (select.children.length === 1 &&
                            select.children[0].value === '')
                    ) {
                        select.innerHTML = `<option value="">${placeholder}</option>`;
                    }
                    select.disabled = false;
                },
                error(select, message = 'Error loading data') {
                    select.innerHTML = `<option value="">${message}</option>`;
                    select.disabled = true;
                },
            };

            // API caller with error handling
            async function callAPI(url, retries = 2) {
                for (let attempt = 0; attempt < retries; attempt++) {
                    try {
                        const response = await fetch(url, {
                            headers: { Accept: 'application/json' },
                            timeout: 10000,
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }

                        const result = await response.json();

                        if (!result.success || !Array.isArray(result.data)) {
                            throw new Error(
                                result.message || 'Invalid data format',
                            );
                        }

                        return result.data;
                    } catch (error) {
                        if (attempt === retries - 1) throw error;
                        await new Promise((resolve) =>
                            setTimeout(resolve, 1000 * (attempt + 1)),
                        );
                    }
                }
            }

            // Populate select with options
            function populateSelect(
                select,
                data,
                selectedValue = '',
                codeField = 'id',
            ) {
                const options = data
                    .map((item) => {
                        const selected =
                            item.name === selectedValue ? 'selected' : '';
                        return `<option value="${item.name}" data-code="${item[codeField]}" ${selected}>${item.name}</option>`;
                    })
                    .join('');

                const placeholder =
                    select.querySelector('option[value=""]')?.textContent ||
                    'Pilih...';
                select.innerHTML = `<option value="">${placeholder}</option>${options}`;

                // Update corresponding code field
                if (selectedValue) {
                    const selectedOption = select.querySelector(
                        `option[value="${selectedValue}"]`,
                    );
                    if (selectedOption) {
                        const codeInput = getCodeInput(select.id);
                        if (codeInput) {
                            codeInput.value = selectedOption.dataset.code;
                        }
                    }
                }
            }

            // Get corresponding code input for a select
            function getCodeInput(selectId) {
                const mapping = {
                    provinsi: provinceCodeInput,
                    kabupaten_kota: regencyCodeInput,
                    kecamatan: districtCodeInput,
                    kelurahan: villageCodeInput,
                };
                return mapping[selectId];
            }

            // Reset dependent selects
            function resetDependentSelects(fromLevel) {
                const selects = [
                    kabupatenSelect,
                    kecamatanSelect,
                    kelurahanSelect,
                ];
                const codes = [
                    regencyCodeInput,
                    districtCodeInput,
                    villageCodeInput,
                ];
                const placeholders = [
                    'Pilih Kabupaten/Kota',
                    'Pilih Kecamatan',
                    'Pilih Kelurahan',
                ];

                let startIndex = 0;
                if (fromLevel === 'kabupaten') startIndex = 1;
                if (fromLevel === 'kecamatan') startIndex = 2;

                for (let i = startIndex; i < selects.length; i++) {
                    selects[i].innerHTML =
                        `<option value="">${placeholders[i]}</option>`;
                    selects[i].disabled = true;
                    codes[i].value = '';
                }

                updateWilayahPreview();
            }

            // Load provinces
            async function loadProvinsi() {
                try {
                    LoadingState.show(provinsiSelect, 'Memuat provinsi...');

                    const provinces = await callAPI('/api/wilayah/provinces');
                    populateSelect(provinsiSelect, provinces, currentProvinsi);

                    LoadingState.hide(provinsiSelect, 'Pilih Provinsi');

                    // Load kabupaten if province is selected
                    if (currentProvinsi) {
                        const selectedOption = provinsiSelect.querySelector(
                            `option[value="${currentProvinsi}"]`,
                        );
                        if (selectedOption) {
                            await loadKabupaten(selectedOption.dataset.code);
                        }
                    }
                } catch (error) {
                    console.error('Error loading provinces:', error);
                    LoadingState.error(provinsiSelect, 'Gagal memuat provinsi');
                    showErrorMessage(
                        'Gagal memuat data provinsi. Silakan refresh halaman.',
                    );
                }
            }

            // Load regencies
            async function loadKabupaten(provinceId) {
                if (!provinceId) return;

                try {
                    LoadingState.show(kabupatenSelect, 'Memuat kabupaten...');
                    resetDependentSelects('kabupaten');

                    const regencies = await callAPI(
                        `/api/wilayah/regencies/${provinceId}`,
                    );
                    populateSelect(
                        kabupatenSelect,
                        regencies,
                        currentKabupaten,
                    );

                    LoadingState.hide(kabupatenSelect, 'Pilih Kabupaten/Kota');

                    // Load kecamatan if regency is selected
                    if (currentKabupaten) {
                        const selectedOption = kabupatenSelect.querySelector(
                            `option[value="${currentKabupaten}"]`,
                        );
                        if (selectedOption) {
                            await loadKecamatan(selectedOption.dataset.code);
                        }
                    }
                } catch (error) {
                    console.error('Error loading regencies:', error);
                    LoadingState.error(
                        kabupatenSelect,
                        'Gagal memuat kabupaten',
                    );
                    showErrorMessage('Gagal memuat data kabupaten/kota.');
                }
            }

            // Load districts
            async function loadKecamatan(regencyId) {
                if (!regencyId) return;

                try {
                    LoadingState.show(kecamatanSelect, 'Memuat kecamatan...');
                    resetDependentSelects('kecamatan');

                    const districts = await callAPI(
                        `/api/wilayah/districts/${regencyId}`,
                    );
                    populateSelect(
                        kecamatanSelect,
                        districts,
                        currentKecamatan,
                    );

                    LoadingState.hide(kecamatanSelect, 'Pilih Kecamatan');

                    // Load kelurahan if district is selected
                    if (currentKecamatan) {
                        const selectedOption = kecamatanSelect.querySelector(
                            `option[value="${currentKecamatan}"]`,
                        );
                        if (selectedOption) {
                            await loadKelurahan(selectedOption.dataset.code);
                        }
                    }
                } catch (error) {
                    console.error('Error loading districts:', error);
                    LoadingState.error(
                        kecamatanSelect,
                        'Gagal memuat kecamatan',
                    );
                    showErrorMessage('Gagal memuat data kecamatan.');
                }
            }

            // Load villages
            async function loadKelurahan(districtId) {
                if (!districtId) return;

                try {
                    LoadingState.show(kelurahanSelect, 'Memuat kelurahan...');

                    const villages = await callAPI(
                        `/api/wilayah/villages/${districtId}`,
                    );
                    populateSelect(kelurahanSelect, villages, currentKelurahan);

                    LoadingState.hide(kelurahanSelect, 'Pilih Kelurahan');
                } catch (error) {
                    console.error('Error loading villages:', error);
                    LoadingState.error(
                        kelurahanSelect,
                        'Gagal memuat kelurahan',
                    );
                    showErrorMessage('Gagal memuat data kelurahan.');
                }
            }

            // Show error message
            function showErrorMessage(message) {
                let errorDiv = document.getElementById('wilayah-error-message');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.id = 'wilayah-error-message';
                    errorDiv.className =
                        'alert alert-danger alert-dismissible mt-3';
                    errorDiv.innerHTML = `${message} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
                    provinsiSelect.parentNode.appendChild(errorDiv);
                } else {
                    errorDiv.innerHTML = `${message} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
                }

                setTimeout(() => {
                    if (errorDiv && errorDiv.parentNode) {
                        errorDiv.parentNode.removeChild(errorDiv);
                    }
                }, 5000);
            }

            // Update wilayah preview
            function updateWilayahPreview() {
                const parts = [
                    kelurahanSelect.value,
                    kecamatanSelect.value,
                    kabupatenSelect.value,
                    provinsiSelect.value,
                ].filter((part) => part);

                previewWilayah.textContent =
                    parts.length > 0
                        ? parts.join(', ')
                        : 'Wilayah akan ditampilkan di sini';
            }

            // Event listeners for cascading dropdowns
            provinsiSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const provinceCode = selectedOption.dataset.code || '';

                provinceCodeInput.value = provinceCode;
                resetDependentSelects('provinsi');

                if (provinceCode) {
                    loadKabupaten(provinceCode);
                }
                updateWilayahPreview();
            });

            kabupatenSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const regencyCode = selectedOption.dataset.code || '';

                regencyCodeInput.value = regencyCode;
                resetDependentSelects('kabupaten');

                if (regencyCode) {
                    loadKecamatan(regencyCode);
                }
                updateWilayahPreview();
            });

            kecamatanSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const districtCode = selectedOption.dataset.code || '';

                districtCodeInput.value = districtCode;
                resetDependentSelects('kecamatan');

                if (districtCode) {
                    loadKelurahan(districtCode);
                }
                updateWilayahPreview();
            });

            kelurahanSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const villageCode = selectedOption.dataset.code || '';

                villageCodeInput.value = villageCode;
                updateWilayahPreview();
            });

            // Live preview updates for other fields
            namaDapurInput.addEventListener('input', function () {
                previewNama.textContent = this.value || 'Nama Dapur';
            });

            alamatInput.addEventListener('input', function () {
                previewAlamat.textContent =
                    this.value || 'Alamat akan ditampilkan di sini';
            });

            teleponInput.addEventListener('input', function () {
                if (this.value) {
                    previewTeleponText.textContent = this.value;
                    previewTelepon.style.display = 'block';
                } else {
                    previewTelepon.style.display = 'none';
                }
            });

            statusSelect.addEventListener('change', function () {
                const badgeClasses = {
                    active: 'bg-label-success',
                    inactive: 'bg-label-danger',
                };

                const statusTexts = {
                    active: 'Aktif',
                    inactive: 'Tidak Aktif',
                };

                if (this.value && badgeClasses[this.value]) {
                    previewStatusBadge.innerHTML = `<span class="badge ${badgeClasses[this.value]}">${statusTexts[this.value]}</span>`;
                } else {
                    previewStatusBadge.innerHTML =
                        '<span class="badge bg-label-secondary">Status</span>';
                }
            });

            // Initialize everything
            async function initialize() {
                // Set initial preview values
                if (teleponInput.value) {
                    teleponInput.dispatchEvent(new Event('input'));
                }
                if (statusSelect.value) {
                    statusSelect.dispatchEvent(new Event('change'));
                }

                // Load provinces and cascade down
                await loadProvinsi();

                // Update initial preview
                updateWilayahPreview();

                // Apply error styling if validation errors exist
                [
                    'provinsi',
                    'kabupaten_kota',
                    'kecamatan',
                    'kelurahan',
                ].forEach((field) => {
                    const select = document.getElementById(field);
                    const errorDiv =
                        select.parentNode.querySelector('.invalid-feedback');
                    if (errorDiv) {
                        select.classList.add('is-invalid');
                    }
                });
            }

            // Start initialization
            initialize().catch((error) => {
                console.error('Initialization error:', error);
                showErrorMessage(
                    'Terjadi kesalahan saat memuat halaman. Silakan refresh halaman.',
                );
            });
        });
    </script>
@endsection
