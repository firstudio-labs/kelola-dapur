@extends('template_mitra.layout')
@section('title', 'Profil Mitra')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a href="{{ route('mitra.dashboard') }}" class="text-muted me-2">
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

        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <form id="profileForm" action="{{ route('mitra.profile.update') }}" method="POST" class="row g-3">
                    @csrf
                    @method('PUT')

                    <div class="col-12 mb-2">
                        <h6 class="fw-semibold pb-2 border-bottom">Informasi Dasar & Akun</h6>
                    </div>

                    <div class="col-md-6">
                        <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror"
                            value="{{ old('nama', $user->nama) }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="nik_pemilik" class="form-label">NIK Pemilik <span class="text-danger">*</span></label>
                        <input type="text" name="nik_pemilik" id="nik_pemilik" class="form-control @error('nik_pemilik') is-invalid @enderror"
                            value="{{ old('nik_pemilik', $mitra->nik_pemilik) }}" required>
                        @error('nik_pemilik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="nama_pemilik" class="form-label">Nama Pemilik <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pemilik" id="nama_pemilik" class="form-control @error('nama_pemilik') is-invalid @enderror"
                            value="{{ old('nama_pemilik', $mitra->nama_pemilik) }}" required>
                        @error('nama_pemilik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror"
                            value="{{ old('username', $user->username) }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mt-4 mb-2">
                        <h6 class="fw-semibold pb-2 border-bottom">Informasi Alamat & Wilayah</h6>
                    </div>

                    <input type="hidden" name="province_code" id="province_code" value="{{ old('province_code', $mitra->province_code) }}">
                    <input type="hidden" name="provinsi" id="provinsi_name_hidden" value="{{ old('provinsi', $mitra->province_name) }}">
                    <input type="hidden" name="regency_code" id="regency_code" value="{{ old('regency_code', $mitra->regency_code) }}">
                    <input type="hidden" name="kabupaten_kota" id="kabupaten_kota_name_hidden" value="{{ old('kabupaten_kota', $mitra->regency_name) }}">
                    <input type="hidden" name="district_code" id="district_code" value="{{ old('district_code', $mitra->district_code) }}">
                    <input type="hidden" name="kecamatan" id="kecamatan_name_hidden" value="{{ old('kecamatan', $mitra->district_name) }}">
                    <input type="hidden" name="village_code" id="village_code" value="{{ old('village_code', $mitra->village_code) }}">
                    <input type="hidden" name="kelurahan" id="kelurahan_name_hidden" value="{{ old('kelurahan', $mitra->village_name) }}">

                    <div class="col-md-6">
                        <label for="select_provinsi" class="form-label">Provinsi</label>
                        <select id="select_provinsi" class="form-select @error('provinsi') is-invalid @enderror">
                            <option value="">Memuat data...</option>
                        </select>
                        @error('provinsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="select_kabupaten_kota" class="form-label">Kabupaten/Kota</label>
                        <select id="select_kabupaten_kota" class="form-select @error('kabupaten_kota') is-invalid @enderror" disabled>
                            <option value="">Pilih Provinsi terlebih dahulu</option>
                        </select>
                        @error('kabupaten_kota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="select_kecamatan" class="form-label">Kecamatan</label>
                        <select id="select_kecamatan" class="form-select @error('kecamatan') is-invalid @enderror" disabled>
                            <option value="">Pilih Kabupaten/Kota terlebih dahulu</option>
                        </select>
                        @error('kecamatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="select_kelurahan" class="form-label">Kelurahan/Desa</label>
                        <select id="select_kelurahan" class="form-select @error('kelurahan') is-invalid @enderror" disabled>
                            <option value="">Pilih Kecamatan terlebih dahulu</option>
                        </select>
                        @error('kelurahan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label for="alamat_detail" class="form-label">Detail Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="alamat_detail" id="alamat_detail" rows="3" class="form-control @error('alamat_detail') is-invalid @enderror" 
                                  required>{{ old('alamat_detail', $mitra->alamat_detail) }}</textarea>
                        @error('alamat_detail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan Profil</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('mitra.riwayat-password.update') }}" method="POST" class="row g-3">
                    @csrf
                    @method('PUT')
                    <div class="col-12 mb-2">
                        <h6 class="fw-semibold pb-2 border-bottom">Keamanan & Ubah Password</h6>
                    </div>
                    
                    <div class="col-md-12">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <div class="input-group input-group-merge">
                            <input type="password" name="current_password" id="current_password" class="form-control" required>
                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                        </div>
                        @error('current_password')<div class="small text-danger mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <div class="input-group input-group-merge">
                            <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Min. 8 Karakter" required>
                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                        </div>
                        @error('new_password')<div class="small text-danger mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <div class="input-group input-group-merge">
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required>
                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">Ganti Password</button>
                    </div>
                </form>
            </div>
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
                const provinsiNameInput = document.getElementById('provinsi_name_hidden');
                const regencyCodeInput = document.getElementById('regency_code');
                const regencyNameInput = document.getElementById('kabupaten_kota_name_hidden');
                const districtCodeInput = document.getElementById('district_code');
                const districtNameInput = document.getElementById('kecamatan_name_hidden');
                const villageCodeInput = document.getElementById('village_code');
                const villageNameInput = document.getElementById('kelurahan_name_hidden');

                const currentProvinsiCode = '{{ old('province_code', $mitra->province_code) }}';
                const currentProvinsiName = '{{ old('provinsi', $mitra->province_name) }}';
                const currentKabupatenCode = '{{ old('regency_code', $mitra->regency_code) }}';
                const currentKabupatenName = '{{ old('kabupaten_kota', $mitra->regency_name) }}';
                const currentKecamatanCode = '{{ old('district_code', $mitra->district_code) }}';
                const currentKecamatanName = '{{ old('kecamatan', $mitra->district_name) }}';
                const currentKelurahanCode = '{{ old('village_code', $mitra->village_code) }}';
                const currentKelurahanName = '{{ old('kelurahan', $mitra->village_name) }}';

                const LoadingState = {
                    show(select, message = 'Memuat...') {
                        select.innerHTML = `<option value="">${message}</option>`;
                        select.disabled = true;
                    },
                    hide(select, placeholder = 'Pilih...') {
                        select.disabled = false;
                    },
                    error(select, message = 'Gagal memuat') {
                        select.innerHTML = `<option value="">${message}</option>`;
                        select.disabled = true;
                    }
                };

                async function callAPI(url) {
                    const response = await fetch(url, { headers: { Accept: 'application/json' } });
                    if (!response.ok) throw new Error('API Error');
                    const result = await response.json();
                    return result.success ? result.data : [];
                }

                function populateSelect(select, data, selectedValue = '', nameInput) {
                    const options = data.map(item => {
                        const selected = (item.id == selectedValue || item.name == selectedValue) ? 'selected' : '';
                        return `<option value="${item.name}" data-code="${item.id}" ${selected}>${item.name}</option>`;
                    }).join('');
                    select.innerHTML = `<option value="">Pilih...</option>${options}`;
                }

                async function loadProvinsi() {
                    try {
                        LoadingState.show(provinsiSelect);
                        const data = await callAPI('/api/wilayah/provinces');
                        populateSelect(provinsiSelect, data, currentProvinsiName);
                        LoadingState.hide(provinsiSelect);
                        if (currentProvinsiCode) await loadKabupaten(currentProvinsiCode);
                    } catch (e) { LoadingState.error(provinsiSelect); }
                }

                async function loadKabupaten(provinceId) {
                    try {
                        LoadingState.show(kabupatenSelect);
                        const data = await callAPI(`/api/wilayah/regencies/${provinceId}`);
                        populateSelect(kabupatenSelect, data, currentKabupatenName);
                        LoadingState.hide(kabupatenSelect);
                        if (currentKabupatenCode) await loadKecamatan(currentKabupatenCode);
                    } catch (e) { LoadingState.error(kabupatenSelect); }
                }

                async function loadKecamatan(regencyId) {
                    try {
                        LoadingState.show(kecamatanSelect);
                        const data = await callAPI(`/api/wilayah/districts/${regencyId}`);
                        populateSelect(kecamatanSelect, data, currentKecamatanName);
                        LoadingState.hide(kecamatanSelect);
                        if (currentKecamatanCode) await loadKelurahan(currentKecamatanCode);
                    } catch (e) { LoadingState.error(kecamatanSelect); }
                }

                async function loadKelurahan(districtId) {
                    try {
                        LoadingState.show(kelurahanSelect);
                        const data = await callAPI(`/api/wilayah/villages/${districtId}`);
                        populateSelect(kelurahanSelect, data, currentKelurahanName);
                        LoadingState.hide(kelurahanSelect);
                    } catch (e) { LoadingState.error(kelurahanSelect); }
                }

                provinsiSelect.addEventListener('change', function() {
                    const opt = this.options[this.selectedIndex];
                    provinceCodeInput.value = opt.dataset.code || '';
                    provinsiNameInput.value = this.value;
                    resetSelects(['kabupaten', 'kecamatan', 'kelurahan']);
                    if (opt.dataset.code) loadKabupaten(opt.dataset.code);
                });

                kabupatenSelect.addEventListener('change', function() {
                    const opt = this.options[this.selectedIndex];
                    regencyCodeInput.value = opt.dataset.code || '';
                    regencyNameInput.value = this.value;
                    resetSelects(['kecamatan', 'kelurahan']);
                    if (opt.dataset.code) loadKecamatan(opt.dataset.code);
                });

                kecamatanSelect.addEventListener('change', function() {
                    const opt = this.options[this.selectedIndex];
                    districtCodeInput.value = opt.dataset.code || '';
                    districtNameInput.value = this.value;
                    resetSelects(['kelurahan']);
                    if (opt.dataset.code) loadKelurahan(opt.dataset.code);
                });

                kelurahanSelect.addEventListener('change', function() {
                    const opt = this.options[this.selectedIndex];
                    villageCodeInput.value = opt.dataset.code || '';
                    villageNameInput.value = this.value;
                });

                function resetSelects(levels) {
                    levels.forEach(lvl => {
                        if (lvl === 'kabupaten') { 
                            kabupatenSelect.innerHTML = '<option value="">Pilih...</option>'; kabupatenSelect.disabled = true; regencyCodeInput.value = ''; regencyNameInput.value = '';
                        }
                        if (lvl === 'kecamatan') { 
                            kecamatanSelect.innerHTML = '<option value="">Pilih...</option>'; kecamatanSelect.disabled = true; districtCodeInput.value = ''; districtNameInput.value = '';
                        }
                        if (lvl === 'kelurahan') { 
                            kelurahanSelect.innerHTML = '<option value="">Pilih...</option>'; kelurahanSelect.disabled = true; villageCodeInput.value = ''; villageNameInput.value = '';
                        }
                    });
                }

                loadProvinsi();
            });
        </script>
    @endpush
@endsection
