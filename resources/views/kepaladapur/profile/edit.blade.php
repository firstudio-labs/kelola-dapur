@extends('template_kepala_dapur.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a href="{{ route('kepala-dapur.dashboard', $dapur) }}" class="text-muted me-2">
                                <i class="bx bx-home-alt me-1"></i> Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Profil Saya</span>
                        </nav>
                        <h4 class="mb-0">Edit Profil</h4>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <form id="profileForm" action="{{ route('kepala-dapur.profile.update', $dapur) }}" method="POST" class="row g-3" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="col-12 mb-2">
                        <h6 class="fw-semibold pb-2 border-bottom">Informasi Dasar</h6>
                    </div>

                    <div class="col-md-6">
                        <label for="nama" class="form-label">
                            Nama Akun (Login) <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="nama" 
                               id="nama"
                               class="form-control @error('nama') is-invalid @enderror"
                               placeholder="Nama Akun"
                               value="{{ old('nama', $user->nama) }}" 
                               required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="nama_lengkap" class="form-label">
                            Nama Lengkap Sesuai KTP
                        </label>
                        <input type="text" 
                               name="nama_lengkap" 
                               id="nama_lengkap"
                               class="form-control @error('nama_lengkap') is-invalid @enderror"
                               placeholder="Nama Lengkap"
                               value="{{ old('nama_lengkap', $kepalaDapur->nama_lengkap) }}">
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="nik_kepala_sppg" class="form-label">
                            NIK 
                        </label>
                        <input type="text" 
                               name="nik_kepala_sppg" 
                               id="nik_kepala_sppg"
                               class="form-control @error('nik_kepala_sppg') is-invalid @enderror"
                               placeholder="Contoh: 3171234567890001"
                               value="{{ old('nik_kepala_sppg', $kepalaDapur->nik_kepala_sppg) }}">
                        @error('nik_kepala_sppg')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="kontak_wa" class="form-label">
                            Nomor WhatsApp / Telepon
                        </label>
                        <input type="text" 
                               name="kontak_wa" 
                               id="kontak_wa"
                               class="form-control @error('kontak_wa') is-invalid @enderror"
                               placeholder="Contoh: 08123456789"
                               value="{{ old('kontak_wa', $kepalaDapur->kontak_wa) }}">
                        @error('kontak_wa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="jenis_kelamin" class="form-label">
                            Jenis Kelamin
                        </label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Pria" {{ old('jenis_kelamin', $kepalaDapur->jenis_kelamin) === 'Pria' ? 'selected' : '' }}>Pria</option>
                            <option value="Wanita" {{ old('jenis_kelamin', $kepalaDapur->jenis_kelamin) === 'Wanita' ? 'selected' : '' }}>Wanita</option>
                        </select>
                        @error('jenis_kelamin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="pendidikan_terakhir" class="form-label">
                            Pendidikan Terakhir
                        </label>
                        <select name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-select @error('pendidikan_terakhir') is-invalid @enderror">
                            <option value="">Pilih Pendidikan</option>
                            <option value="SD" {{ old('pendidikan_terakhir', $kepalaDapur->pendidikan_terakhir) === 'SD' ? 'selected' : '' }}>SD</option>
                            <option value="SMP" {{ old('pendidikan_terakhir', $kepalaDapur->pendidikan_terakhir) === 'SMP' ? 'selected' : '' }}>SMP</option>
                            <option value="SMA" {{ old('pendidikan_terakhir', $kepalaDapur->pendidikan_terakhir) === 'SMA' ? 'selected' : '' }}>SMA</option>
                            <option value="D1" {{ old('pendidikan_terakhir', $kepalaDapur->pendidikan_terakhir) === 'D1' ? 'selected' : '' }}>D1</option>
                            <option value="D2" {{ old('pendidikan_terakhir', $kepalaDapur->pendidikan_terakhir) === 'D2' ? 'selected' : '' }}>D2</option>
                            <option value="D3" {{ old('pendidikan_terakhir', $kepalaDapur->pendidikan_terakhir) === 'D3' ? 'selected' : '' }}>D3</option>
                            <option value="Sarjana" {{ old('pendidikan_terakhir', $kepalaDapur->pendidikan_terakhir) === 'Sarjana' ? 'selected' : '' }}>Sarjana (S1/S2/S3)</option>
                        </select>
                        @error('pendidikan_terakhir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <hr class="my-3">
                        <label class="form-label d-block text-center mb-3">Foto Profil</label>
                        <div class="d-flex align-items-start align-items-sm-center justify-content-center gap-4">
                            <div class="position-relative">
                                <img id="uploadedAvatar"
                                    src="{{ $kepalaDapur->foto_diri ? Storage::url($kepalaDapur->foto_diri) : asset('admin/assets/img/avatars/1.png') }}"
                                    alt="user-avatar" class="rounded-3 border" height="100" width="100"
                                    style="object-fit: cover" />
                                <label for="upload"
                                    class="btn btn-icon btn-primary rounded-circle position-absolute @error('foto_diri') border-danger @enderror"
                                    style="bottom: -10px; right: -10px; width: 32px; height: 32px; padding: 0; cursor: pointer;"
                                    title="Unggah Foto Baru">
                                    <i class="bx bx-camera fs-6"></i>
                                    <input type="file" id="upload" name="foto_diri" class="account-file-input"
                                        hidden accept="image/png, image/jpeg, image/jpg, image/webp" />
                                </label>
                            </div>
                            <div class="button-wrapper text-start">
                                <button type="button"
                                    class="btn btn-outline-secondary btn-sm mb-2 account-image-reset {{ !$kepalaDapur->foto_diri ? 'd-none' : '' }}">
                                    <i class="bx bx-reset me-1"></i> Reset
                                </button>
                                <p class="text-muted mb-0 small">Format yang diizinkan JPG, GIF atau PNG.</p>
                                <p class="text-muted mb-0 small">Ukuran maksimal 2MB.</p>
                                @error('foto_diri')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4 mb-2">
                        <h6 class="fw-semibold pb-2 border-bottom">Informasi Alamat Pribadi</h6>
                    </div>

                    <input type="hidden" name="province_code" id="province_code" value="{{ old('province_code', $kepalaDapur->province_code) }}">
                    <input type="hidden" name="provinsi" id="provinsi_name_hidden" value="{{ old('provinsi', $kepalaDapur->province_name) }}">
                    <input type="hidden" name="regency_code" id="regency_code" value="{{ old('regency_code', $kepalaDapur->regency_code) }}">
                    <input type="hidden" name="kabupaten_kota" id="kabupaten_kota_name_hidden" value="{{ old('kabupaten_kota', $kepalaDapur->regency_name) }}">
                    <input type="hidden" name="district_code" id="district_code" value="{{ old('district_code', $kepalaDapur->district_code) }}">
                    <input type="hidden" name="kecamatan" id="kecamatan_name_hidden" value="{{ old('kecamatan', $kepalaDapur->district_name) }}">
                    <input type="hidden" name="village_code" id="village_code" value="{{ old('village_code', $kepalaDapur->village_code) }}">
                    <input type="hidden" name="kelurahan" id="kelurahan_name_hidden" value="{{ old('kelurahan', $kepalaDapur->village_name) }}">

                    <div class="col-md-6">
                        <label for="select_provinsi" class="form-label">
                            Provinsi
                        </label>
                        <select id="select_provinsi" class="form-select @error('provinsi') is-invalid @enderror">
                            <option value="">Memuat data...</option>
                        </select>
                        @error('provinsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="select_kabupaten_kota" class="form-label">
                            Kabupaten/Kota
                        </label>
                        <select id="select_kabupaten_kota" class="form-select @error('kabupaten_kota') is-invalid @enderror" disabled>
                            <option value="">Pilih Provinsi terlebih dahulu</option>
                        </select>
                        @error('kabupaten_kota')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="select_kecamatan" class="form-label">
                            Kecamatan
                        </label>
                        <select id="select_kecamatan" class="form-select @error('kecamatan') is-invalid @enderror" disabled>
                            <option value="">Pilih Kabupaten/Kota terlebih dahulu</option>
                        </select>
                        @error('kecamatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="select_kelurahan" class="form-label">
                            Kelurahan/Desa
                        </label>
                        <select id="select_kelurahan" class="form-select @error('kelurahan') is-invalid @enderror" disabled>
                            <option value="">Pilih Kecamatan terlebih dahulu</option>
                        </select>
                        @error('kelurahan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="alamat_detail" class="form-label">
                            Detail Alamat Lengkap
                        </label>
                        <textarea name="alamat_detail" 
                                  id="alamat_detail"
                                  rows="3" 
                                  class="form-control @error('alamat_detail') is-invalid @enderror"
                                  placeholder="Contoh: Jl. Mawar No 12, RT 01/02...">{{ old('alamat_detail', $kepalaDapur->alamat_detail) }}</textarea>
                        @error('alamat_detail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('kepala-dapur.dashboard', $dapur) }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Perbarui Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>

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
                    Jika mengubah wilayah, pilih provinsi, kemudian
                    kabupaten/kota, kecamatan, dan kelurahan secara
                    berurutan
                </li>
            </ul>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const provinsiSelect = document.getElementById('select_provinsi');
                const kabupatenSelect = document.getElementById('select_kabupaten_kota');
                const kecamatanSelect = document.getElementById('select_kecamatan');
                const kelurahanSelect = document.getElementById('select_kelurahan');

                const provinceCodeInput = document.getElementById('province_code');
                const regencyCodeInput = document.getElementById('regency_code');
                const districtCodeInput = document.getElementById('district_code');
                const villageCodeInput = document.getElementById('village_code');

                const currentProvinsi =
                    '{{ old('province_code', $kepalaDapur->province_code ?? '') }}';
                const currentKabupaten =
                    '{{ old('regency_code', $kepalaDapur->regency_code ?? '') }}';
                const currentKecamatan =
                    '{{ old('district_code', $kepalaDapur->district_code ?? '') }}';
                const currentKelurahan =
                    '{{ old('village_code', $kepalaDapur->village_code ?? '') }}';

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

                async function callAPI(url, retries = 2) {
                    for (let attempt = 0; attempt < retries; attempt++) {
                        try {
                            const response = await fetch(url, {
                                headers: {
                                    Accept: 'application/json'
                                },
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

                function populateSelect(
                    select,
                    data,
                    selectedValue = '',
                    codeField = 'id',
                ) {
                    const options = data
                        .map((item) => {
                            const selected =
                                String(item[codeField]) === String(selectedValue) ? 'selected' : '';
                            return `<option value="${item.name}" data-code="${item[codeField]}" ${selected}>${item.name}</option>`;
                        })
                        .join('');

                    const placeholder =
                        select.querySelector('option[value=""]')?.textContent ||
                        'Pilih...';
                    select.innerHTML = `<option value="">${placeholder}</option>${options}`;

                    if (selectedValue) {
                        const selectedOption = select.querySelector(
                            `option[data-code="${selectedValue}"]`,
                        );
                        if (selectedOption) {
                            const codeInput = getCodeInput(select.id);
                            if (codeInput) {
                                codeInput.value = selectedOption.dataset.code;
                            }
                            
                            // Update the hidden name input as well
                            const nameInput = document.getElementById(`${select.id === 'select_provinsi' ? 'provinsi' : select.id.replace('select_', '')}_name_hidden`);
                            if (nameInput) {
                                nameInput.value = selectedOption.value;
                            }
                        }
                    }
                }

                function getCodeInput(selectId) {
                    const mapping = {
                        provinsi: provinceCodeInput,
                        kabupaten_kota: regencyCodeInput,
                        kecamatan: districtCodeInput,
                        kelurahan: villageCodeInput,
                    };
                    return mapping[selectId];
                }

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

                async function loadProvinsi() {
                    try {
                        LoadingState.show(provinsiSelect, 'Memuat provinsi...');

                        const provinces = await callAPI('/api/wilayah/provinces');
                        populateSelect(provinsiSelect, provinces, currentProvinsi);

                        LoadingState.hide(provinsiSelect, 'Pilih Provinsi');

                        if (currentProvinsi) {
                            const selectedOption = provinsiSelect.querySelector(
                                `option[data-code="${currentProvinsi}"]`,
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

                        if (currentKabupaten) {
                            const selectedOption = kabupatenSelect.querySelector(
                                `option[data-code="${currentKabupaten}"]`,
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

                        if (currentKecamatan) {
                            const selectedOption = kecamatanSelect.querySelector(
                                `option[data-code="${currentKecamatan}"]`,
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

                function showErrorMessage(message) {
                    let errorDiv = document.getElementById('wilayah-error-message');
                    if (!errorDiv) {
                        errorDiv = document.createElement('div');
                        errorDiv.id = 'wilayah-error-message';
                        errorDiv.className =
                            'alert alert-danger alert-dismissible mt-3';
                        errorDiv.innerHTML =
                            `${message} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
                        provinsiSelect.parentNode.appendChild(errorDiv);
                    } else {
                        errorDiv.innerHTML =
                            `${message} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
                    }

                    setTimeout(() => {
                        if (errorDiv && errorDiv.parentNode) {
                            errorDiv.parentNode.removeChild(errorDiv);
                        }
                    }, 5000);
                }

                function updateWilayahPreview() {
                    const parts = [
                        kelurahanSelect.value,
                        kecamatanSelect.value,
                        kabupatenSelect.value,
                        provinsiSelect.value,
                    ].filter((part) => part);

                    // Also update hidden name inputs when user manually changes values
                    if (provinsiSelect.value) document.getElementById('provinsi_name_hidden').value = provinsiSelect.value;
                    if (kabupatenSelect.value) document.getElementById('kabupaten_kota_name_hidden').value = kabupatenSelect.value;
                    if (kecamatanSelect.value) document.getElementById('kecamatan_name_hidden').value = kecamatanSelect.value;
                    if (kelurahanSelect.value) document.getElementById('kelurahan_name_hidden').value = kelurahanSelect.value;
                }

                provinsiSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const provinceCode = selectedOption.dataset.code || '';

                    provinceCodeInput.value = provinceCode;
                    resetDependentSelects('provinsi');

                    if (provinceCode) {
                        loadKabupaten(provinceCode);
                    }
                    updateWilayahPreview();
                });

                kabupatenSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const regencyCode = selectedOption.dataset.code || '';

                    regencyCodeInput.value = regencyCode;
                    resetDependentSelects('kabupaten');

                    if (regencyCode) {
                        loadKecamatan(regencyCode);
                    }
                    updateWilayahPreview();
                });

                kecamatanSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const districtCode = selectedOption.dataset.code || '';

                    districtCodeInput.value = districtCode;
                    resetDependentSelects('kecamatan');

                    if (districtCode) {
                        loadKelurahan(districtCode);
                    }
                    updateWilayahPreview();
                });

                kelurahanSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const villageCode = selectedOption.dataset.code || '';

                    villageCodeInput.value = villageCode;
                    updateWilayahPreview();
                });

                async function initialize() {

                    await loadProvinsi();

                    updateWilayahPreview();

                    [
                        'select_provinsi',
                        'select_kabupaten_kota',
                        'select_kecamatan',
                        'select_kelurahan',
                    ].forEach((field) => {
                        const select = document.getElementById(field);
                        if (select) {
                            const errorDiv =
                                select.parentNode.querySelector('.invalid-feedback');
                            if (errorDiv) {
                                select.classList.add('is-invalid');
                            }
                        }
                    });
                }

                initialize().catch((error) => {
                    console.error('Initialization error:', error);
                    showErrorMessage(
                        'Terjadi kesalahan saat memuat halaman. Silakan refresh halaman.',
                    );
                });

                document.getElementById('profileForm').addEventListener('submit', function(e) {
                    const fileInput = document.getElementById('foto_diri');
                    if (fileInput.files.length > 0) {
                        const fileSize = fileInput.files[0].size / 1024 / 1024;
                        if (fileSize > 2) {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'error',
                                title: 'Ukuran Gambar Terlalu Besar',
                                text: 'Maksimal ukuran gambar yang diperbolehkan adalah 2MB. Silakan pilih gambar dengan ukuran yang lebih kecil.',
                                confirmButtonText: 'Mengerti',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false
                            });
                        }
                    }
                });

                // Image Preview Logic
                const uploadInput = document.getElementById('upload');
                const uploadedAvatar = document.getElementById('uploadedAvatar');
                const accountImageReset = document.querySelector('.account-image-reset');

                if (uploadInput) {
                    uploadInput.onchange = (e) => {
                        const [file] = uploadInput.files;
                        if (file) {
                            uploadedAvatar.src = URL.createObjectURL(file);
                            accountImageReset.classList.remove('d-none');
                        }
                    };
                }

                if (accountImageReset) {
                    accountImageReset.onclick = () => {
                        uploadInput.value = '';
                        uploadedAvatar.src = "{{ $kepalaDapur->foto_diri ? Storage::url($kepalaDapur->foto_diri) : asset('admin/assets/img/avatars/1.png') }}";
                        if (!{{ $kepalaDapur->foto_diri ? 'true' : 'false' }}) {
                            accountImageReset.classList.add('d-none');
                        }
                    };
                }
            });
        </script>
    @endpush
@endsection
