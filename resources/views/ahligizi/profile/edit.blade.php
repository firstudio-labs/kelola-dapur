@extends('template_ahli_gizi.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a href="{{ route('ahli-gizi.dashboard', $dapur) }}" class="text-muted me-2">
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
                <form id="profileForm" action="{{ route('ahli-gizi.profile.update', $dapur) }}" method="POST" class="row g-3" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="col-12 mb-2">
                        <h6 class="fw-semibold pb-2 border-bottom">Informasi Dasar</h6>
                    </div>

                    <div class="col-md-6">
                        <label for="nama" class="form-label">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="nama" 
                               id="nama"
                               class="form-control @error('nama') is-invalid @enderror"
                               placeholder="Nama Lengkap"
                               value="{{ old('nama', $user->nama) }}" 
                               required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="nik_ahli_gizi" class="form-label">
                            NIK Ahli Gizi
                        </label>
                        <input type="text" 
                               name="nik_ahli_gizi" 
                               id="nik_ahli_gizi"
                               class="form-control @error('nik_ahli_gizi') is-invalid @enderror"
                               placeholder="Contoh: 3171234567890001"
                               value="{{ old('nik_ahli_gizi', $ahliGizi->nik_ahli_gizi) }}">
                        @error('nik_ahli_gizi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="jabatan" class="form-label">
                            Jabatan
                        </label>
                        <select name="jabatan" id="jabatan" class="form-select @error('jabatan') is-invalid @enderror">
                            <option value="">Pilih Jabatan</option>
                            <option value="Penanggung jawab" {{ old('jabatan', $ahliGizi->jabatan) === 'Penanggung jawab' ? 'selected' : '' }}>Penanggung jawab</option>
                            <option value="Anggota" {{ old('jabatan', $ahliGizi->jabatan) === 'Anggota' ? 'selected' : '' }}>Anggota</option>
                        </select>
                        @error('jabatan')
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
                               value="{{ old('kontak_wa', $ahliGizi->kontak_wa) }}">
                        @error('kontak_wa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="jenis_kelamin" class="form-label">
                            Jenis Kelamin
                        </label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Pria" {{ old('jenis_kelamin', $ahliGizi->jenis_kelamin) === 'Pria' ? 'selected' : '' }}>Pria</option>
                            <option value="Wanita" {{ old('jenis_kelamin', $ahliGizi->jenis_kelamin) === 'Wanita' ? 'selected' : '' }}>Wanita</option>
                        </select>
                        @error('jenis_kelamin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="pendidikan_terakhir" class="form-label">
                            Pendidikan Terakhir
                        </label>
                        <select name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-select @error('pendidikan_terakhir') is-invalid @enderror">
                            <option value="">Pilih Pendidikan</option>
                            <option value="SD" {{ old('pendidikan_terakhir', $ahliGizi->pendidikan_terakhir) === 'SD' ? 'selected' : '' }}>SD</option>
                            <option value="SMP" {{ old('pendidikan_terakhir', $ahliGizi->pendidikan_terakhir) === 'SMP' ? 'selected' : '' }}>SMP</option>
                            <option value="SMA" {{ old('pendidikan_terakhir', $ahliGizi->pendidikan_terakhir) === 'SMA' ? 'selected' : '' }}>SMA</option>
                            <option value="D1" {{ old('pendidikan_terakhir', $ahliGizi->pendidikan_terakhir) === 'D1' ? 'selected' : '' }}>D1</option>
                            <option value="D2" {{ old('pendidikan_terakhir', $ahliGizi->pendidikan_terakhir) === 'D2' ? 'selected' : '' }}>D2</option>
                            <option value="D3" {{ old('pendidikan_terakhir', $ahliGizi->pendidikan_terakhir) === 'D3' ? 'selected' : '' }}>D3</option>
                            <option value="Sarjana" {{ old('pendidikan_terakhir', $ahliGizi->pendidikan_terakhir) === 'Sarjana' ? 'selected' : '' }}>Sarjana (S1/S2/S3)</option>
                        </select>
                        @error('pendidikan_terakhir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="foto_diri" class="form-label">
                            Foto Diri
                        </label>
                        <input type="file" 
                               name="foto_diri" 
                               id="foto_diri"
                               accept="image/*"
                               class="form-control @error('foto_diri') is-invalid @enderror">
                        @error('foto_diri')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <div class="mt-3">
                            <img id="image_preview" 
                                 src="{{ $ahliGizi->foto_diri ? Storage::url($ahliGizi->foto_diri) : '#' }}" 
                                 alt="Preview Foto Diri" 
                                 class="img-fluid rounded border" 
                                 style="max-height: 200px; object-fit: cover; {{ $ahliGizi->foto_diri ? 'display: block;' : 'display: none;' }}">
                        </div>
                    </div>

                    <div class="col-12 mt-4 mb-2">
                        <h6 class="fw-semibold pb-2 border-bottom">Informasi Alamat Pribadi</h6>
                    </div>

                    <input type="hidden" name="province_code" id="province_code" value="{{ old('province_code', $ahliGizi->province_code) }}">
                    <input type="hidden" name="regency_code" id="regency_code" value="{{ old('regency_code', $ahliGizi->regency_code) }}">
                    <input type="hidden" name="district_code" id="district_code" value="{{ old('district_code', $ahliGizi->district_code) }}">
                    <input type="hidden" name="village_code" id="village_code" value="{{ old('village_code', $ahliGizi->village_code) }}">

                    <input type="hidden" name="provinsi" id="provinsi_name" value="{{ old('provinsi', $ahliGizi->province_name) }}">
                    <input type="hidden" name="kabupaten_kota" id="kabupaten_name" value="{{ old('kabupaten_kota', $ahliGizi->regency_name) }}">
                    <input type="hidden" name="kecamatan" id="kecamatan_name" value="{{ old('kecamatan', $ahliGizi->district_name) }}">
                    <input type="hidden" name="kelurahan" id="kelurahan_name" value="{{ old('kelurahan', $ahliGizi->village_name) }}">

                    <div class="col-md-6 border-end pe-3">
                        <div class="mb-3">
                            <label for="provinsi_select" class="form-label">Provinsi</label>
                            <select id="provinsi_select" class="form-select select2">
                                <option value="">Pilih Provinsi</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="kabupaten_select" class="form-label">Kabupaten / Kota</label>
                            <select id="kabupaten_select" class="form-select select2" disabled>
                                <option value="">Pilih Kabupaten/Kota</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="kecamatan_select" class="form-label">Kecamatan</label>
                            <select id="kecamatan_select" class="form-select select2" disabled>
                                <option value="">Pilih Kecamatan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="kelurahan_select" class="form-label">Desa / Kelurahan</label>
                            <select id="kelurahan_select" class="form-select select2" disabled>
                                <option value="">Pilih Desa/Kelurahan</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 ps-3">
                        <label for="alamat_detail" class="form-label">Alamat Lengkap</label>
                        <textarea name="alamat_detail" 
                                  id="alamat_detail" 
                                  class="form-control @error('alamat_detail') is-invalid @enderror" 
                                  rows="6" 
                                  placeholder="Nama jalan, gedung, no rumah, RT/RW, patokan">{{ old('alamat_detail', $ahliGizi->alamat_detail) }}</textarea>
                        @error('alamat_detail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mt-4 text-end">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bx bx-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #d9dee3;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        color: #697a8d;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Foto Preview Logic
    const fotoInput = document.getElementById('foto_diri');
    const imagePreview = document.getElementById('image_preview');

    fotoInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            // Revert back to original image if no file selected
            const defaultSrc = "{{ $ahliGizi->foto_diri ? Storage::url($ahliGizi->foto_diri) : '#' }}";
            if (defaultSrc !== '#') {
                imagePreview.src = defaultSrc;
            } else {
                imagePreview.style.display = 'none';
                imagePreview.src = '#';
            }
        }
    });

    // Initialize Select2
    $('.select2').select2({
        theme: 'default',
        width: '100%'
    });

    // DOM Elements for API Wilayah
    const provinsiSelect = $('#provinsi_select');
    const kabupatenSelect = $('#kabupaten_select');
    const kecamatanSelect = $('#kecamatan_select');
    const kelurahanSelect = $('#kelurahan_select');

    // Hidden Inputs Reference
    const codeProv = document.getElementById('province_code');
    const nameProv = document.getElementById('provinsi_name');
    const codeReg = document.getElementById('regency_code');
    const nameReg = document.getElementById('kabupaten_name');
    const codeDist = document.getElementById('district_code');
    const nameDist = document.getElementById('kecamatan_name');
    const codeVill = document.getElementById('village_code');
    const nameVill = document.getElementById('kelurahan_name');

    // Submit button logic to ensure data is loaded
    const submitBtn = document.getElementById('submitBtn');

    // Pre-filled data
    const savedProvCode = codeProv.value;
    const savedRegencyCode = codeReg.value;
    const savedDistrictCode = codeDist.value;
    const savedVillageCode = codeVill.value;

    function setLoading(selectElement, isLoading) {
        if (isLoading) {
            selectElement.prop('disabled', true);
            selectElement.html('<option value="">Sedang memuat data...</option>');
        }
    }

    function resetSelect(selectElement, defaultText) {
        selectElement.html(`<option value="">${defaultText}</option>`);
        selectElement.prop('disabled', true);
    }

    // LOAD PROVINCES
    fetch('https://ibnux.github.io/data-indonesia/provinsi.json')
        .then(response => response.json())
        .then(data => {
            let options = '<option value="">Pilih Provinsi</option>';
            data.forEach(prov => {
                const selected = savedProvCode === prov.id ? 'selected' : '';
                options += `<option value="${prov.id}" data-name="${prov.nama}" ${selected}>${prov.nama}</option>`;
            });
            provinsiSelect.html(options);

            // Pre-load regency if province exists
            if (savedProvCode) {
                loadKabupaten(savedProvCode, savedRegencyCode);
            }
        })
        .catch(err => console.error("Error loading provinces:", err));

    provinsiSelect.on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const id = selectedOption.val();
        const nama = selectedOption.data('name');

        codeProv.value = id;
        nameProv.value = nama;

        // Reset descendants
        resetSelect(kabupatenSelect, 'Pilih Kabupaten/Kota');
        resetSelect(kecamatanSelect, 'Pilih Kecamatan');
        resetSelect(kelurahanSelect, 'Pilih Desa/Kelurahan');
        
        codeReg.value = ''; nameReg.value = '';
        codeDist.value = ''; nameDist.value = '';
        codeVill.value = ''; nameVill.value = '';

        if (id) {
            loadKabupaten(id);
        }
    });

    function loadKabupaten(provId, preSelectedId = null) {
        setLoading(kabupatenSelect, true);
        fetch(`https://ibnux.github.io/data-indonesia/kabupaten/${provId}.json`)
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">Pilih Kabupaten/Kota</option>';
                data.forEach(kab => {
                    const selected = preSelectedId === kab.id ? 'selected' : '';
                    options += `<option value="${kab.id}" data-name="${kab.nama}" ${selected}>${kab.nama}</option>`;
                });
                kabupatenSelect.html(options);
                kabupatenSelect.prop('disabled', false);

                if (preSelectedId && savedDistrictCode) {
                    loadKecamatan(preSelectedId, savedDistrictCode);
                }
            });
    }

    kabupatenSelect.on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const id = selectedOption.val();
        const nama = selectedOption.data('name');

        codeReg.value = id;
        nameReg.value = nama;

        // Reset descendants
        resetSelect(kecamatanSelect, 'Pilih Kecamatan');
        resetSelect(kelurahanSelect, 'Pilih Desa/Kelurahan');
        
        codeDist.value = ''; nameDist.value = '';
        codeVill.value = ''; nameVill.value = '';

        if (id) {
            loadKecamatan(id);
        }
    });

    function loadKecamatan(kabId, preSelectedId = null) {
        setLoading(kecamatanSelect, true);
        fetch(`https://ibnux.github.io/data-indonesia/kecamatan/${kabId}.json`)
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">Pilih Kecamatan</option>';
                data.forEach(kec => {
                    const selected = preSelectedId === kec.id ? 'selected' : '';
                    options += `<option value="${kec.id}" data-name="${kec.nama}" ${selected}>${kec.nama}</option>`;
                });
                kecamatanSelect.html(options);
                kecamatanSelect.prop('disabled', false);

                if (preSelectedId && savedVillageCode) {
                    loadKelurahan(preSelectedId, savedVillageCode);
                }
            });
    }

    kecamatanSelect.on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const id = selectedOption.val();
        const nama = selectedOption.data('name');

        codeDist.value = id;
        nameDist.value = nama;

        // Reset descendants
        resetSelect(kelurahanSelect, 'Pilih Desa/Kelurahan');
        codeVill.value = ''; nameVill.value = '';

        if (id) {
            loadKelurahan(id);
        }
    });

    function loadKelurahan(kecId, preSelectedId = null) {
        setLoading(kelurahanSelect, true);
        fetch(`https://ibnux.github.io/data-indonesia/kelurahan/${kecId}.json`)
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">Pilih Desa/Kelurahan</option>';
                data.forEach(kel => {
                    const selected = preSelectedId === kel.id ? 'selected' : '';
                    options += `<option value="${kel.id}" data-name="${kel.nama}" ${selected}>${kel.nama}</option>`;
                });
                kelurahanSelect.html(options);
                kelurahanSelect.prop('disabled', false);
            });
    }

    kelurahanSelect.on('change', function() {
        const selectedOption = $(this).find('option:selected');
        codeVill.value = selectedOption.val();
        nameVill.value = selectedOption.data('name');
    });

});
</script>
@endpush
