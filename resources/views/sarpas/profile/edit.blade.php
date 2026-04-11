@extends('template_sarpas.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a href="{{ route('sarpas.dashboard', $dapur) }}" class="text-muted me-2">
                                <i class="bx bx-home-alt me-1"></i> Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Profil Saya</span>
                        </nav>
                        <h4 class="mb-0">Pengaturan Akun</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-pills flex-column flex-md-row mb-3">
                    <li class="nav-item">
                        <a class="nav-link active" href="javascript:void(0);">
                            <i class="bx bx-user me-1"></i> Profil Saya
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('sarpas.profile.security.edit', $dapur) }}">
                            <i class="bx bx-lock-alt me-1"></i> Keamanan
                        </a>
                    </li>
                </ul>

                <div class="card mb-4">
                    <h5 class="card-header">Detail Profil</h5>
                    <form id="formAccountSettings" method="POST" action="{{ route('sarpas.profile.update', $dapur) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible mb-3" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-12 mb-3">
                                    <h6 class="fw-semibold">Informasi Dasar</h6>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="nama" class="form-label">Nama Akun (Login) <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control @error('nama') is-invalid @enderror" type="text"
                                        id="nama" name="nama" value="{{ old('nama', $user->nama) }}" autofocus
                                        required />
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="username" class="form-label">Username <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control @error('username') is-invalid @enderror" type="text"
                                        name="username" id="username" value="{{ old('username', $user->username) }}"
                                        required />
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">E-mail <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control @error('email') is-invalid @enderror" type="email"
                                        id="email" name="email" value="{{ old('email', $user->email) }}"
                                        placeholder="john.doe@example.com" required />
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <hr class="my-3">
                                    <h6 class="fw-semibold mb-3">Detail Personal Sarpas</h6>

                                    <label class="form-label d-block text-center mb-3">Foto Profil</label>
                                    <div class="d-flex align-items-start align-items-sm-center justify-content-center gap-4 mb-4">
                                        <div class="position-relative">
                                            <img id="uploadedAvatar"
                                                src="{{ $sarpas->foto_diri ? Storage::url($sarpas->foto_diri) : asset('admin/assets/img/avatars/1.png') }}"
                                                alt="user-avatar" class="rounded-3 border" height="100" width="100"
                                                style="object-fit: cover" />
                                            <label for="upload"
                                                class="btn btn-icon btn-primary rounded-circle position-absolute @error('foto_diri') border-danger @enderror"
                                                style="bottom: -10px; right: -10px; width: 32px; height: 32px; padding: 0; cursor: pointer;"
                                                title="Unggah Foto Baru">
                                                <i class="bx bx-camera fs-6"></i>
                                                <input type="file" id="upload" name="foto_diri"
                                                    class="account-file-input" hidden
                                                    accept="image/png, image/jpeg, image/jpg, image/webp" />
                                            </label>
                                        </div>
                                        <div class="button-wrapper text-start">
                                            <button type="button"
                                                class="btn btn-outline-secondary btn-sm mb-2 account-image-reset {{ !$sarpas->foto_diri ? 'd-none' : '' }}">
                                                <i class="bx bx-reset me-1"></i> Reset
                                            </button>
                                            <p class="text-muted mb-0 small">Format yang diizinkan JPG, GIF atau PNG.</p>
                                            <p class="text-muted mb-0 small">Ukuran maksimal 2MB.</p>
                                            @error('foto_diri')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row g-4">
                                        <div class="mb-3 col-md-6">
                                            <label for="nik_sarpas" class="form-label">NIK Sarpas</label>
                                            <input type="text"
                                                class="form-control @error('nik_sarpas') is-invalid @enderror"
                                                id="nik_sarpas" name="nik_sarpas"
                                                value="{{ old('nik_sarpas', $sarpas->nik_sarpas) }}" />
                                            @error('nik_sarpas')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="nama_lengkap" class="form-label">Nama Lengkap Sesuai KTP</label>
                                            <input class="form-control @error('nama_lengkap') is-invalid @enderror" type="text"
                                                name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap', $sarpas->nama_lengkap) }}"
                                                placeholder="Nama Lengkap" />
                                            @error('nama_lengkap')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="kontak_wa" class="form-label">Nomor WhatsApp / Telepon</label>
                                            <input class="form-control @error('kontak_wa') is-invalid @enderror"
                                                type="text" id="kontak_wa" name="kontak_wa"
                                                value="{{ old('kontak_wa', $sarpas->kontak_wa) }}" />
                                            @error('kontak_wa')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="jabatan" class="form-label">Jabatan</label>
                                            <select name="jabatan" id="jabatan"
                                                class="form-select @error('jabatan') is-invalid @enderror">
                                                <option value="">Pilih Jabatan</option>
                                                <option value="Penanggung jawab"
                                                    {{ old('jabatan', $sarpas->jabatan) === 'Penanggung jawab' ? 'selected' : '' }}>
                                                    Penanggung jawab</option>
                                                <option value="Anggota"
                                                    {{ old('jabatan', $sarpas->jabatan) === 'Anggota' ? 'selected' : '' }}>
                                                    Anggota
                                                </option>
                                            </select>
                                            @error('jabatan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="pendidikan" class="form-label">Pendidikan Terakhir</label>
                                            <select name="pendidikan" id="pendidikan"
                                                class="form-select @error('pendidikan') is-invalid @enderror">
                                                <option value="">Pilih Pendidikan</option>
                                                @foreach (['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'Sarjana'] as $edu)
                                                    <option value="{{ $edu }}"
                                                        {{ old('pendidikan', $sarpas->pendidikan) === $edu ? 'selected' : '' }}>
                                                        {{ $edu }}</option>
                                                @endforeach
                                            </select>
                                            @error('pendidikan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                            <select name="jenis_kelamin" id="jenis_kelamin"
                                                class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                                <option value="">Pilih Jenis Kelamin</option>
                                                <option value="Pria"
                                                    {{ old('jenis_kelamin', $sarpas->jenis_kelamin) === 'Pria' ? 'selected' : '' }}>
                                                    Pria</option>
                                                <option value="Wanita"
                                                    {{ old('jenis_kelamin', $sarpas->jenis_kelamin) === 'Wanita' ? 'selected' : '' }}>
                                                    Wanita</option>
                                            </select>
                                            @error('jenis_kelamin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-4 mb-3">
                                    <hr class="my-3">
                                    <h6 class="fw-semibold">Alamat Lengkap</h6>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="province_id" class="form-label">Provinsi</label>
                                    <select id="province_id" name="province_code" class="form-select select2">
                                        <option value="">Pilih Provinsi</option>
                                    </select>
                                    <input type="hidden" name="provinsi" id="province_name"
                                        value="{{ old('provinsi', $sarpas->province_name) }}">
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="regency_id" class="form-label">Kabupaten / Kota</label>
                                    <select id="regency_id" name="regency_code" class="form-select select2" disabled>
                                        <option value="">Pilih Kabupaten/Kota</option>
                                    </select>
                                    <input type="hidden" name="kabupaten_kota" id="regency_name"
                                        value="{{ old('kabupaten_kota', $sarpas->regency_name) }}">
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="district_id" class="form-label">Kecamatan</label>
                                    <select id="district_id" name="district_code" class="form-select select2" disabled>
                                        <option value="">Pilih Kecamatan</option>
                                    </select>
                                    <input type="hidden" name="kecamatan" id="district_name"
                                        value="{{ old('kecamatan', $sarpas->district_name) }}">
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="village_id" class="form-label">Desa / Kelurahan</label>
                                    <select id="village_id" name="village_code" class="form-select select2" disabled>
                                        <option value="">Pilih Desa/Kelurahan</option>
                                    </select>
                                    <input type="hidden" name="kelurahan" id="village_name"
                                        value="{{ old('kelurahan', $sarpas->village_name) }}">
                                </div>

                                <div class="mb-3 col-md-12">
                                    <label for="alamat_detail" class="form-label">Alamat Detail</label>
                                    <textarea class="form-control @error('alamat_detail') is-invalid @enderror" id="alamat_detail"
                                        name="alamat_detail" rows="3" placeholder="Nama jalan, gedung, no rumah, RT/RW, dsb.">{{ old('alamat_detail', $sarpas->alamat_detail) }}</textarea>
                                    @error('alamat_detail')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-2 text-end">
                                <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
                                <a href="{{ route('sarpas.dashboard') }}" class="btn btn-outline-secondary">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #d9dee3;
            border-radius: 0.375rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            padding-left: 12px;
            color: #697a8d;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #696cff;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image Preview
            const uploadInput = document.getElementById('upload');
            const uploadedAvatar = document.getElementById('uploadedAvatar');
            const accountImageReset = document.querySelector('.account-image-reset');

            if (uploadInput) {
                uploadInput.onchange = (e) => {
                    const [file] = uploadInput.files;
                    if (file) {
                        uploadedAvatar.src = URL.createObjectURL(file);
                    }
                };
            }

            if (accountImageReset) {
                accountImageReset.onclick = () => {
                    uploadInput.value = '';
                    uploadedAvatar.src =
                        "{{ $sarpas->foto_diri ? Storage::url($sarpas->foto_diri) : asset('admin/assets/img/avatars/1.png') }}";
                };
            }

            // Helper: init or reinit Select2 on an element
            function initSelect2(el) {
                if ($(el).data('select2')) { $(el).select2('destroy'); }
                $(el).select2({ theme: 'default', width: '100%' });
            }

            // Initialize all select2 elements
            $('.select2').each(function() { initSelect2(this); });

            // Wilayah API Logic
            const provinceSelect = $('#province_id');
            const regencySelect  = $('#regency_id');
            const districtSelect = $('#district_id');
            const villageSelect  = $('#village_id');

            const provinceNameInput = $('#province_name');
            const regencyNameInput  = $('#regency_name');
            const districtNameInput = $('#district_name');
            const villageNameInput  = $('#village_name');

            const currentProv = "{{ old('province_code', $sarpas->province_code) }}";
            const currentReg  = "{{ old('regency_code', $sarpas->regency_code) }}";
            const currentDist = "{{ old('district_code', $sarpas->district_code) }}";
            const currentVill = "{{ old('village_code', $sarpas->village_code) }}";

            function resetSelect(el, placeholder) {
                el.html('<option value="">' + placeholder + '</option>');
                el.prop('disabled', true);
                initSelect2(el[0]);
            }

            // Load Provinces
            $.get("{{ route('api.wilayah.provinces') }}", function(response) {
                (response.data || response).forEach(function(item) { provinceSelect.append(new Option(item.name, item.id)); });
                initSelect2(provinceSelect[0]);
                if (currentProv) { provinceSelect.val(currentProv).trigger('change'); }
            });

            provinceSelect.on('change', function() {
                const id = $(this).val();
                provinceNameInput.val($(this).find('option:selected').text());
                resetSelect(regencySelect, 'Pilih Kabupaten/Kota');
                resetSelect(districtSelect, 'Pilih Kecamatan');
                resetSelect(villageSelect, 'Pilih Desa/Kelurahan');
                if (id) {
                    $.get("{{ route('api.wilayah.regencies', '') }}/" + id, function(response) {
                        (response.data || response).forEach(function(item) { regencySelect.append(new Option(item.name, item.id)); });
                        regencySelect.prop('disabled', false);
                        initSelect2(regencySelect[0]);
                        if (currentReg && provinceSelect.val() === currentProv) {
                            regencySelect.val(currentReg).trigger('change');
                        }
                    });
                }
            });

            regencySelect.on('change', function() {
                const id = $(this).val();
                regencyNameInput.val($(this).find('option:selected').text());
                resetSelect(districtSelect, 'Pilih Kecamatan');
                resetSelect(villageSelect, 'Pilih Desa/Kelurahan');
                if (id) {
                    $.get("{{ route('api.wilayah.districts', '') }}/" + id, function(response) {
                        (response.data || response).forEach(function(item) { districtSelect.append(new Option(item.name, item.id)); });
                        districtSelect.prop('disabled', false);
                        initSelect2(districtSelect[0]);
                        if (currentDist && regencySelect.val() === currentReg) {
                            districtSelect.val(currentDist).trigger('change');
                        }
                    });
                }
            });

            districtSelect.on('change', function() {
                const id = $(this).val();
                districtNameInput.val($(this).find('option:selected').text());
                resetSelect(villageSelect, 'Pilih Desa/Kelurahan');
                if (id) {
                    $.get("{{ route('api.wilayah.villages', '') }}/" + id, function(response) {
                        (response.data || response).forEach(function(item) { villageSelect.append(new Option(item.name, item.id)); });
                        villageSelect.prop('disabled', false);
                        initSelect2(villageSelect[0]);
                        if (currentVill && districtSelect.val() === currentDist) {
                            villageSelect.val(currentVill).trigger('change');
                        }
                    });
                }
            });

            villageSelect.on('change', function() {
                villageNameInput.val($(this).find('option:selected').text());
            });
        });
    </script>
@endpush
