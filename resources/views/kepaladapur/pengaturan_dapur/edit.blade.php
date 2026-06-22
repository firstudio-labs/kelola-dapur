@extends('template_kepala_dapur.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav class="d-flex align-items-center mb-2">
                            <a href="{{ route('kepala-dapur.dashboard', $dapur->id_dapur) }}" class="text-muted me-2">
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Pengaturan Dapur</span>
                        </nav>
                        <h4 class="mb-1">
                            Pengaturan Dapur: {{ $dapur->nama_dapur }}
                        </h4>
                        <p class="mb-0 text-muted">
                            Perbarui informasi profil cabang dapur Anda
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('kepala-dapur.pengaturan-dapur.update', $dapur->id_dapur) }}" method="POST" class="row g-3" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="province_code" id="province_code"
                        value="{{ old('province_code', $dapur->province_code) }}" />
                    <input type="hidden" name="regency_code" id="regency_code"
                        value="{{ old('regency_code', $dapur->regency_code) }}" />
                    <input type="hidden" name="district_code" id="district_code"
                        value="{{ old('district_code', $dapur->district_code) }}" />
                    <input type="hidden" name="village_code" id="village_code"
                        value="{{ old('village_code', $dapur->village_code) }}" />

                    <div class="col-12 mb-4">
                        <label class="form-label d-block">Foto Bangunan Dapur</label>
                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                            @if ($dapur->foto_bangunan && Storage::exists('public/' . $dapur->foto_bangunan))
                                <img src="{{ asset('storage/' . $dapur->foto_bangunan) }}" alt="Foto Bangunan"
                                    class="d-block rounded img-fluid" id="uploadedFoto" style="max-height: 200px; object-fit: cover;" />
                            @else
                                <div class="bg-label-secondary border border-dashed rounded d-flex align-items-center justify-content-center p-4"
                                    id="placeholderFoto" style="min-height: 200px; width: 100%; max-width: 400px;">
                                    <div class="text-center">
                                        <i class="bx bx-image text-muted mb-2" style="font-size: 3rem;"></i>
                                        <p class="mb-0 text-muted">Belum ada foto</p>
                                    </div>
                                </div>
                                <img src="" alt="Preview Foto" class="d-none rounded img-fluid" id="uploadedFoto" style="max-height: 200px; object-fit: cover;" />
                            @endif

                            <div class="button-wrapper">
                                <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                    <span class="d-none d-sm-block">Ganti Foto</span>
                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                    <input type="file" id="upload" name="foto_bangunan" class="account-file-input" hidden
                                        accept="image/png, image/jpeg, image/jpg, image/webp" />
                                </label>
                                <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                                    <i class="bx bx-reset d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Reset</span>
                                </button>
                                <p class="text-muted mb-0">Format: JPG, PNG, WEBP. Maks: 10MB</p>
                            </div>
                        </div>
                        @error('foto_bangunan')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <div class="col-md-6">
                        <label for="nama_dapur" class="form-label">Nama Dapur <span class="text-danger">*</span></label>
                        <input type="text" name="nama_dapur" id="nama_dapur" required
                            class="form-control @error('nama_dapur') is-invalid @enderror"
                            value="{{ old('nama_dapur', $dapur->nama_dapur) }}" />
                        @error('nama_dapur')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="telepon" class="form-label">Nomor Telepon</label>
                        <input type="text" name="telepon" id="telepon"
                            class="form-control @error('telepon') is-invalid @enderror"
                            value="{{ old('telepon', $dapur->telepon) }}" />
                        @error('telepon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="no_registrasi_sppg" class="form-label">No Registrasi SPPG</label>
                        <input type="text" name="no_registrasi_sppg" id="no_registrasi_sppg"
                            class="form-control @error('no_registrasi_sppg') is-invalid @enderror"
                            value="{{ old('no_registrasi_sppg', $dapur->no_registrasi_sppg) }}" />
                        @error('no_registrasi_sppg')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="nik_pemilik" class="form-label">NIK Pemilik</label>
                        <input type="text" name="nik_pemilik" id="nik_pemilik"
                            class="form-control @error('nik_pemilik') is-invalid @enderror"
                            value="{{ old('nik_pemilik', $dapur->nik_pemilik) }}" />
                        @error('nik_pemilik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="tag_lokasi" class="form-label">Link Maps / Tag Lokasi</label>
                        <input type="text" name="tag_lokasi" id="tag_lokasi"
                            class="form-control @error('tag_lokasi') is-invalid @enderror"
                            placeholder="Contoh: https://maps.app.goo.gl/xxx"
                            value="{{ old('tag_lokasi', $dapur->tag_lokasi) }}" />
                        @error('tag_lokasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="provinsi" class="form-label">Provinsi <span class="text-danger">*</span></label>
                        <select name="provinsi" id="provinsi" required class="form-select @error('provinsi') is-invalid @enderror">
                            <option value="">Pilih Provinsi</option>
                        </select>
                        @error('provinsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="kabupaten_kota" class="form-label">Kabupaten/Kota <span class="text-danger">*</span></label>
                        <select name="kabupaten_kota" id="kabupaten_kota" required disabled
                            class="form-select @error('kabupaten_kota') is-invalid @enderror">
                            <option value="">Pilih Kota</option>
                        </select>
                        @error('kabupaten_kota')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="kecamatan" class="form-label">Kecamatan <span class="text-danger">*</span></label>
                        <select name="kecamatan" id="kecamatan" required disabled
                            class="form-select @error('kecamatan') is-invalid @enderror">
                            <option value="">Pilih Kecamatan</option>
                        </select>
                        @error('kecamatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="kelurahan" class="form-label">Kelurahan <span class="text-danger">*</span></label>
                        <select name="kelurahan" id="kelurahan" required disabled
                            class="form-select @error('kelurahan') is-invalid @enderror">
                            <option value="">Pilih Kelurahan</option>
                        </select>
                        @error('kelurahan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="alamat" id="alamat" rows="3" required
                            class="form-control @error('alamat') is-invalid @enderror"
                            placeholder="Detail jalan, patokan, rt/rw">{{ old('alamat', $dapur->alamat) }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 text-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let fileInput = document.querySelector('.account-file-input');
            let resetBtn = document.querySelector('.account-image-reset');
            let foto = document.getElementById('uploadedFoto');
            let placeholder = document.getElementById('placeholderFoto');

            if (fileInput) {
                const originalSrc = foto ? foto.src : '';
                
                fileInput.addEventListener('change', function(e) {
                    if (e.target.files[0]) {
                        var file = e.target.files[0];
                        if (file.type.match(/^image\//)) {
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                if(foto) {
                                    foto.src = e.target.result;
                                    foto.classList.remove('d-none');
                                }
                                if(placeholder) placeholder.classList.add('d-none');
                            }
                            reader.readAsDataURL(file);
                        } else {
                            alert('Tolong unggah file gambar (JPG, PNG, WEBP).');
                            this.value = '';
                        }
                    }
                });

                resetBtn.addEventListener('click', function() {
                    fileInput.value = '';
                    if (originalSrc) {
                        if(foto) {
                            foto.src = originalSrc;
                            foto.classList.remove('d-none');
                        }
                        if(placeholder) placeholder.classList.add('d-none');
                    } else {
                        if(foto) foto.classList.add('d-none');
                        if(placeholder) placeholder.classList.remove('d-none');
                    }
                });
            }

            const endpoints = {
                provinces: 'https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json',
                regencies: (provinceId) => `https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`,
                districts: (regencyId) => `https://www.emsifa.com/api-wilayah-indonesia/api/districts/${regencyId}.json`,
                villages: (districtId) => `https://www.emsifa.com/api-wilayah-indonesia/api/villages/${districtId}.json`,
            };

            const selects = {
                provinsi: document.getElementById('provinsi'),
                kabupaten: document.getElementById('kabupaten_kota'),
                kecamatan: document.getElementById('kecamatan'),
                kelurahan: document.getElementById('kelurahan')
            };

            const inputs = {
                provinsi: document.getElementById('province_code'),
                kabupaten: document.getElementById('regency_code'),
                kecamatan: document.getElementById('district_code'),
                kelurahan: document.getElementById('village_code')
            };

            const selected = {
                provinsi: '{{ old("province_code", $dapur->province_code) }}',
                kabupaten: '{{ old("regency_code", $dapur->regency_code) }}',
                kecamatan: '{{ old("district_code", $dapur->district_code) }}',
                kelurahan: '{{ old("village_code", $dapur->village_code) }}'
            };

            async function fetchOptions(url, selectElement, valueField = 'id', textField = 'name', selectedValue = null) {
                try {
                    selectElement.disabled = true;
                    const firstOption = selectElement.options[0];
                    selectElement.innerHTML = '';
                    selectElement.appendChild(firstOption);

                    const response = await fetch(url);
                    if (!response.ok) throw new Error('Network response was not ok');
                    const data = await response.json();

                    data.sort((a, b) => a[textField].localeCompare(b[textField]));

                    data.forEach(item => {
                        const option = new Option(item[textField], item[textField]);
                        option.dataset.code = item[valueField];
                        if (selectedValue && (item[valueField] === selectedValue || item[textField] === selectedValue)) {
                            option.selected = true;
                            const level = selectElement.id === 'provinsi' ? 'provinsi' :
                                         selectElement.id === 'kabupaten_kota' ? 'kabupaten' :
                                         selectElement.id === 'kecamatan' ? 'kecamatan' : 'kelurahan';
                            inputs[level].value = item[valueField];
                        }
                        selectElement.add(option);
                    });
                    
                    selectElement.disabled = false;
                    return data;
                } catch (error) {
                    console.error('Error fetching data:', error);
                    alert('Gagal mengambil data wilayah. Silakan coba lagi nanti.');
                }
            }

            function updateSelection(selectLevel, targetElementCode) {
                selects[selectLevel].addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const code = selectedOption.dataset.code || '';
                    inputs[targetElementCode].value = code;

                    if (selectLevel === 'provinsi') {
                        resetSelects(['kabupaten', 'kecamatan', 'kelurahan']);
                        if (code) {
                            fetchOptions(endpoints.regencies(code), selects.kabupaten).then(() => {
                                selects.kabupaten.dispatchEvent(new Event('change'));
                            });
                        }
                    } else if (selectLevel === 'kabupaten') {
                        resetSelects(['kecamatan', 'kelurahan']);
                        if (code) {
                            fetchOptions(endpoints.districts(code), selects.kecamatan).then(() => {
                                selects.kecamatan.dispatchEvent(new Event('change'));
                            });
                        }
                    } else if (selectLevel === 'kecamatan') {
                        resetSelects(['kelurahan']);
                        if (code) {
                            fetchOptions(endpoints.villages(code), selects.kelurahan);
                        }
                    }
                });
            }

            function resetSelects(levels) {
                levels.forEach(level => {
                    const select = selects[level];
                    const firstOption = select.options[0];
                    select.innerHTML = '';
                    select.appendChild(firstOption);
                    select.disabled = true;
                    inputs[level].value = '';
                });
            }

            updateSelection('provinsi', 'provinsi');
            updateSelection('kabupaten', 'kabupaten');
            updateSelection('kecamatan', 'kecamatan');
            updateSelection('kelurahan', 'kelurahan');

            fetchOptions(endpoints.provinces, selects.provinsi, 'id', 'name', selected.provinsi)
                .then(() => {
                    const provinceSelected = selects.provinsi.options[selects.provinsi.selectedIndex];
                    const provCode = provinceSelected?.dataset?.code || selected.provinsi;
                    
                    if (provCode) {
                        return fetchOptions(endpoints.regencies(provCode), selects.kabupaten, 'id', 'name', selected.kabupaten);
                    }
                })
                .then(data => {
                    if (data) {
                        const regencySelected = selects.kabupaten.options[selects.kabupaten.selectedIndex];
                        const regCode = regencySelected?.dataset?.code || selected.kabupaten;
                        
                        if (regCode) {
                            return fetchOptions(endpoints.districts(regCode), selects.kecamatan, 'id', 'name', selected.kecamatan);
                        }
                    }
                })
                .then(data => {
                    if (data) {
                        const districtSelected = selects.kecamatan.options[selects.kecamatan.selectedIndex];
                        const distCode = districtSelected?.dataset?.code || selected.kecamatan;
                        
                        if (distCode) {
                            return fetchOptions(endpoints.villages(distCode), selects.kelurahan, 'id', 'name', selected.kelurahan);
                        }
                    }
                });
        });
    </script>
    @endpush
@endsection
