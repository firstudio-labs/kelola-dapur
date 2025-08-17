@extends('template_admin.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <nav class="d-flex align-items-center mb-2">
                                <a href="{{ route('superadmin.dashboard') }}" class="text-muted me-2">
                                    <i class="bx bx-home-alt me-1"></i>Dashboard
                                </a>
                                <i class="bx bx-chevron-right me-2"></i>
                                <a href="{{ route('superadmin.dapur.index') }}" class="text-muted me-2">
                                    Kelola Dapur
                                </a>
                                <i class="bx bx-chevron-right me-2"></i>
                                <span class="text-dark">Edit Dapur</span>
                            </nav>
                            <h4 class="mb-1">Edit Dapur: {{ $dapur->nama_dapur }}</h4>
                            <p class="mb-0 text-muted">Perbarui informasi dapur</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Form -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('superadmin.dapur.update', $dapur) }}" method="POST" class="row g-3">
                        @csrf
                        @method('PUT')

                        <!-- Nama Dapur -->
                        <div class="col-12">
                            <label for="nama_dapur" class="form-label">
                                Nama Dapur <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="nama_dapur" 
                                   id="nama_dapur" 
                                   required
                                   class="form-control @error('nama_dapur') is-invalid @enderror"
                                   placeholder="Contoh: Dapur Utama Jakarta"
                                   value="{{ old('nama_dapur', $dapur->nama_dapur) }}">
                            @error('nama_dapur')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Wilayah -->
                        <div class="col-md-6">
                            <label for="provinsi" class="form-label">
                                Provinsi <span class="text-danger">*</span>
                            </label>
                            <select name="provinsi" 
                                    id="provinsi" 
                                    required
                                    class="choices-select form-select @error('provinsi') is-invalid @enderror">
                                <option value="">Pilih Provinsi</option>
                            </select>
                            @error('provinsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="kabupaten_kota" class="form-label">
                                Kabupaten/Kota <span class="text-danger">*</span>
                            </label>
                            <select name="kabupaten_kota" 
                                    id="kabupaten_kota" 
                                    required
                                    disabled
                                    class="choices-select form-select @error('kabupaten_kota') is-invalid @enderror">
                                <option value="">Pilih Kabupaten/Kota</option>
                            </select>
                            @error('kabupaten_kota')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Alamat -->
                        <div class="col-12">
                            <label for="alamat" class="form-label">
                                Alamat Lengkap <span class="text-danger">*</span>
                            </label>
                            <textarea name="alamat" 
                                      id="alamat" 
                                      rows="4"
                                      required
                                      class="form-control @error('alamat') is-invalid @enderror"
                                      placeholder="Masukkan alamat lengkap dapur">{{ old('alamat', $dapur->alamat) }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Telepon -->
                        <div class="col-md-6">
                            <label for="telepon" class="form-label">
                                Telepon
                            </label>
                            <input type="text" 
                                   name="telepon" 
                                   id="telepon"
                                   class="form-control @error('telepon') is-invalid @enderror"
                                   placeholder="Contoh: 0211234567"
                                   value="{{ old('telepon', $dapur->telepon) }}">
                            @error('telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Nomor telepon dapur (opsional)</small>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label for="status" class="form-label">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" 
                                    id="status" 
                                    required
                                    class="form-select @error('status') is-invalid @enderror">
                                <option value="">Pilih Status</option>
                                <option value="active" {{ old('status', $dapur->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status', $dapur->status) === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Status dapur dalam sistem</small>
                        </div>

                        <!-- Preview Card -->
                        <div class="col-12">
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Preview</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="bx bx-buildings"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center">
                                                <h6 class="mb-0" id="preview-nama">
                                                    {{ old('nama_dapur', $dapur->nama_dapur) }}
                                                </h6>
                                                <span class="badge ms-2 bg-label-secondary" id="preview-status-badge">
                                                    @if(old('status', $dapur->status) === 'active')
                                                        Aktif
                                                    @elseif(old('status', $dapur->status) === 'inactive')
                                                        Tidak Aktif
                                                    @else
                                                        Status
                                                    @endif
                                                </span>
                                            </div>
                                            <small class="text-muted" id="preview-wilayah">
                                                {{ old('kabupaten_kota', $dapur->kabupaten_kota) ? old('kabupaten_kota', $dapur->kabupaten_kota) . ', ' . old('provinsi', $dapur->provinsi) : 'Wilayah akan ditampilkan di sini' }}
                                            </small><br>
                                            <small class="text-muted" id="preview-alamat">
                                                {{ old('alamat', $dapur->alamat) ?: 'Alamat akan ditampilkan di sini' }}
                                            </small><br>
                                            <small class="text-muted" id="preview-telepon" @if(!old('telepon', $dapur->telepon)) style="display: none;" @endif>
                                                <i class="bx bx-phone me-1"></i>
                                                <span id="preview-telepon-text">{{ old('telepon', $dapur->telepon) }}</span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('superadmin.dapur.index') }}" 
                               class="btn btn-outline-secondary">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="btn btn-primary">
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
                    <li>Ketik untuk mencari provinsi atau kabupaten/kota</li>
                    <li>Perubahan nama dapur harus tetap unik dalam sistem</li>
                    <li>Jika mengubah wilayah, pilih provinsi terlebih dahulu kemudian kabupaten/kota</li>
                    <li>Mengubah status ke "Tidak Aktif" akan menonaktifkan dapur dari sistem</li>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
</div>

<!-- Choices.js CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css">
<script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>

<!-- Custom CSS for Choices.js with Sneat -->
<style>
.choices__inner {
    min-height: 38px;
    border-radius: 0.375rem;
    font-size: 0.875rem;
}
.choices__list--dropdown {
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
.choices__item {
    font-size: 0.875rem;
}
.choices__item--selectable.is-highlighted {
    background-color: #696cff;
    color: #fff;
}
.choices.is-disabled .choices__inner {
    background-color: #f8f9fa;
}
.choices.is-invalid .choices__inner {
    border-color: #dc3545;
}
</style>

<!-- JavaScript for Wilayah API and Live Preview -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const namaDapurInput = document.getElementById('nama_dapur');
    const alamatInput = document.getElementById('alamat');
    const teleponInput = document.getElementById('telepon');
    const statusSelect = document.getElementById('status');
    const provinsiSelect = document.getElementById('provinsi');
    const kabupatenSelect = document.getElementById('kabupaten_kota');
    
    const previewNama = document.getElementById('preview-nama');
    const previewWilayah = document.getElementById('preview-wilayah');
    const previewAlamat = document.getElementById('preview-alamat');
    const previewTelepon = document.getElementById('preview-telepon');
    const previewTeleponText = document.getElementById('preview-telepon-text');
    const previewStatusBadge = document.getElementById('preview-status-badge');

    const currentProvinsi = '{{ old("provinsi", $dapur->provinsi ?? "") }}';
    const currentKabupaten = '{{ old("kabupaten_kota", $dapur->kabupaten_kota ?? "") }}';

    const provinsiChoices = new Choices(provinsiSelect, {
        searchEnabled: true,
        searchPlaceholderValue: 'Ketik untuk mencari provinsi...',
        noResultsText: 'Tidak ada hasil ditemukan',
        noChoicesText: 'Tidak ada pilihan tersedia',
        itemSelectText: 'Klik untuk memilih',
        allowHTML: false,
        shouldSort: false,
        placeholder: true,
        placeholderValue: 'Pilih Provinsi'
    });

    const kabupatenChoices = new Choices(kabupatenSelect, {
        searchEnabled: true,
        searchPlaceholderValue: 'Ketik untuk mencari kabupaten/kota...',
        noResultsText: 'Tidak ada hasil ditemukan',
        noChoicesText: 'Pilih provinsi terlebih dahulu',
        itemSelectText: 'Klik untuk memilih',
        allowHTML: false,
        shouldSort: false,
        placeholder: true,
        placeholderValue: 'Pilih Kabupaten/Kota'
    });

    kabupatenChoices.disable();

    loadProvinsi();

    async function loadProvinsi() {
        try {
            const response = await fetch('/api/wilayah/provinces');
            const result = await response.json();
            
            if (result.success && result.data) {
                const choices = result.data.map(province => ({
                    value: province.name,
                    label: province.name,
                    customProperties: { id: province.id },
                    selected: province.name === currentProvinsi
                }));

                provinsiChoices.setChoices(choices, 'value', 'label', true);

                if (currentProvinsi) {
                    const selectedChoice = choices.find(c => c.value === currentProvinsi);
                    if (selectedChoice && selectedChoice.customProperties) {
                        setTimeout(() => {
                            loadKabupaten(selectedChoice.customProperties.id);
                        }, 100);
                    }
                }
            } else {
                throw new Error('Invalid response format');
            }
        } catch (error) {
            console.error('Error loading provinces:', error);
            showErrorMessage('Gagal memuat data provinsi. Silakan refresh halaman.');
        }
    }

    async function loadKabupaten(provinceId) {
        try {
            kabupatenChoices.clearStore();
            kabupatenChoices.disable();

            const response = await fetch(`/api/wilayah/regencies/${provinceId}`);
            const result = await response.json();
            
            if (result.success && result.data) {
                const choices = result.data.map(regency => ({
                    value: regency.name,
                    label: regency.name,
                    selected: regency.name === currentKabupaten
                }));

                kabupatenChoices.setChoices(choices, 'value', 'label', true);
                kabupatenChoices.enable();
                
                setTimeout(updateWilayahPreview, 100);
            } else {
                throw new Error(result.message || 'Invalid response format');
            }
        } catch (error) {
            console.error('Error loading regencies:', error);
            kabupatenChoices.enable();
            showErrorMessage('Gagal memuat data kabupaten/kota. Silakan coba lagi.');
        }
    }

    function showErrorMessage(message) {
        let errorDiv = document.getElementById('wilayah-error-message');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.id = 'wilayah-error-message';
            errorDiv.className = 'alert alert-danger alert-dismissible mt-3';
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

    provinsiSelect.addEventListener('change', function() {
        const selectedChoice = provinsiChoices.getValue();
        if (selectedChoice && selectedChoice.customProperties && selectedChoice.customProperties.id) {
            loadKabupaten(selectedChoice.customProperties.id);
        } else {
            kabupatenChoices.clearStore();
            kabupatenChoices.disable();
            updateWilayahPreview();
        }
    });

    kabupatenSelect.addEventListener('change', updateWilayahPreview);

    function updateWilayahPreview() {
        const provinsiValue = provinsiChoices.getValue();
        const kabupatenValue = kabupatenChoices.getValue();
        
        const provinsi = provinsiValue ? provinsiValue.value : '';
        const kabupaten = kabupatenValue ? kabupatenValue.value : '';
        
        if (kabupaten && provinsi) {
            previewWilayah.textContent = `${kabupaten}, ${provinsi}`;
        } else if (provinsi) {
            previewWilayah.textContent = provinsi;
        } else {
            previewWilayah.textContent = 'Wilayah akan ditampilkan di sini';
        }
    }

    namaDapurInput.addEventListener('input', function() {
        previewNama.textContent = this.value || 'Nama Dapur';
    });

    alamatInput.addEventListener('input', function() {
        previewAlamat.textContent = this.value || 'Alamat akan ditampilkan di sini';
    });

    teleponInput.addEventListener('input', function() {
        if (this.value) {
            previewTeleponText.textContent = this.value;
            previewTelepon.style.display = 'block';
        } else {
            previewTelepon.style.display = 'none';
        }
    });

    statusSelect.addEventListener('change', function() {
        if (this.value === 'active') {
            previewStatusBadge.textContent = 'Aktif';
            previewStatusBadge.className = 'badge ms-2 bg-label-success';
        } else if (this.value === 'inactive') {
            previewStatusBadge.textContent = 'Tidak Aktif';
            previewStatusBadge.className = 'badge ms-2 bg-label-danger';
        } else {
            previewStatusBadge.textContent = 'Status';
            previewStatusBadge.className = 'badge ms-2 bg-label-secondary';
        }
    });

    setTimeout(() => {
        if (teleponInput.value) {
            teleponInput.dispatchEvent(new Event('input'));
        }
        if (statusSelect.value) {
            statusSelect.dispatchEvent(new Event('change'));
        }
        updateWilayahPreview();
    }, 100);

    function addErrorStyling() {
        if (document.querySelector('.invalid-feedback[for="provinsi"]')) {
            provinsiChoices.containerOuter.element.classList.add('is-invalid');
        }
        if (document.querySelector('.invalid-feedback[for="kabupaten_kota"]')) {
            kabupatenChoices.containerOuter.element.classList.add('is-invalid');
        }
    }

    setTimeout(addErrorStyling, 100);
});
</script>
@endsection