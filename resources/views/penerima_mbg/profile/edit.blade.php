@extends('template_penerima_mbg.layout')
@section('title', 'Edit Profil')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Edit Profil Saya</h4>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('penerima-mbg.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Informasi Akun</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $user->nama) }}" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Dapur SPPG Tujuan <span class="text-danger">*</span></label>
                        <select name="id_dapur" class="form-select @error('id_dapur') is-invalid @enderror" required>
                            <option value="">-- Pilih Dapur SPPG --</option>
                            @foreach($dapurList as $dapur)
                                <option value="{{ $dapur->id_dapur }}" {{ old('id_dapur', $penerima->id_dapur) == $dapur->id_dapur ? 'selected' : '' }}>
                                    {{ $dapur->nama_dapur }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_dapur')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Identitas</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Jenis Identitas <span class="text-danger">*</span></label>
                        <select name="id_type" class="form-select @error('id_type') is-invalid @enderror" required>
                            <option value="nik" {{ old('id_type', $penerima->id_type) == 'nik' ? 'selected' : '' }}>NIK</option>
                            <option value="nisn" {{ old('id_type', $penerima->id_type) == 'nisn' ? 'selected' : '' }}>NISN</option>
                            <option value="no_registrasi" {{ old('id_type', $penerima->id_type) == 'no_registrasi' ? 'selected' : '' }}>No. Registrasi</option>
                        </select>
                        @error('id_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nomor Identitas <span class="text-danger">*</span></label>
                        <input type="text" name="id_number" class="form-control @error('id_number') is-invalid @enderror" value="{{ old('id_number', $penerima->id_number) }}" required>
                        @error('id_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nama Penanggung Jawab (Sesuai KTP) <span class="text-danger">*</span></label>
                        <input type="text" name="penanggung_jawab" class="form-control @error('penanggung_jawab') is-invalid @enderror" value="{{ old('penanggung_jawab', $penerima->penanggung_jawab) }}" required>
                        @error('penanggung_jawab')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Alamat Lokasi MBG</h5>
                <input type="hidden" name="province_code" id="province_code" value="{{ old('province_code', $penerima->province_code) }}">
                <input type="hidden" name="regency_code" id="regency_code" value="{{ old('regency_code', $penerima->regency_code) }}">
                <input type="hidden" name="district_code" id="district_code" value="{{ old('district_code', $penerima->district_code) }}">
                <input type="hidden" name="village_code" id="village_code" value="{{ old('village_code', $penerima->village_code) }}">
                <input type="hidden" name="province_name" id="province_name_val" value="{{ old('province_name', $penerima->province_name) }}">
                <input type="hidden" name="regency_name" id="regency_name_val" value="{{ old('regency_name', $penerima->regency_name) }}">
                <input type="hidden" name="district_name" id="district_name_val" value="{{ old('district_name', $penerima->district_name) }}">
                <input type="hidden" name="village_name" id="village_name_val" value="{{ old('village_name', $penerima->village_name) }}">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                        <select id="provinsi_select" class="form-select">
                            <option value="">-- Pilih Provinsi --</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kabupaten/Kota <span class="text-danger">*</span></label>
                        <select id="kabupaten_select" class="form-select" disabled>
                            <option value="">-- Pilih Kabupaten/Kota --</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kecamatan</label>
                        <select id="kecamatan_select" class="form-select" disabled>
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Desa/Kelurahan</label>
                        <select id="desa_select" class="form-select" disabled>
                            <option value="">-- Pilih Desa/Kelurahan --</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Detail Alamat <span class="text-danger">*</span></label>
                        <textarea name="alamat_detail" class="form-control @error('alamat_detail') is-invalid @enderror" rows="3" required>{{ old('alamat_detail', $penerima->alamat_detail) }}</textarea>
                        @error('alamat_detail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Lokasi & Foto</h5>
                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">Link Google Maps</label>
                        <input type="url" name="link_gmaps" class="form-control @error('link_gmaps') is-invalid @enderror" value="{{ old('link_gmaps', $penerima->link_gmaps) }}" placeholder="https://maps.google.com/...">
                        @error('link_gmaps')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Foto Lokasi</label>
                        @if($penerima->foto_lokasi)
                            <div class="mb-2">
                                <img src="{{ Storage::url($penerima->foto_lokasi) }}" alt="Foto Lokasi" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        @endif
                        <input type="file" name="foto_lokasi" class="form-control @error('foto_lokasi') is-invalid @enderror" accept="image/*">
                        @error('foto_lokasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Simpan Profil</button>
            <a href="{{ route('penerima-mbg.dashboard') }}" class="btn btn-label-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection
@push('scripts')
<script>
const BASE = 'https://ibnux.github.io/data-indonesia/';
const savedProv = '{{ old('province_code', $penerima->province_code) }}';
const savedKab = '{{ old('regency_code', $penerima->regency_code) }}';
const savedKec = '{{ old('district_code', $penerima->district_code) }}';
const savedDesa = '{{ old('village_code', $penerima->village_code) }}';

function populateSelect(select, data, valueKey, labelKey, savedVal) {
    select.innerHTML = '<option value="">-- Pilih --</option>';
    data.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item[valueKey];
        opt.text = item[labelKey];
        if (item[valueKey] == savedVal) opt.selected = true;
        select.appendChild(opt);
    });
    select.disabled = false;
}

fetch(`${BASE}provinsi.json`)
    .then(r => r.json())
    .then(data => {
        populateSelect(document.getElementById('provinsi_select'), data, 'id', 'nama', savedProv);
        if (savedProv) {
            fetch(`${BASE}kabupaten/${savedProv}.json`).then(r => r.json()).then(d => {
                populateSelect(document.getElementById('kabupaten_select'), d, 'id', 'nama', savedKab);
                if (savedKab) {
                    fetch(`${BASE}kecamatan/${savedKab}.json`).then(r => r.json()).then(d => {
                        populateSelect(document.getElementById('kecamatan_select'), d, 'id', 'nama', savedKec);
                        if (savedKec) {
                            fetch(`${BASE}kelurahan/${savedKec}.json`).then(r => r.json()).then(d => {
                                populateSelect(document.getElementById('desa_select'), d, 'id', 'nama', savedDesa);
                            });
                        }
                    });
                }
            });
        }
    });

document.getElementById('provinsi_select').addEventListener('change', function() {
    const id = this.options[this.selectedIndex]?.value;
    const name = this.options[this.selectedIndex]?.text;
    document.getElementById('province_code').value = id;
    document.getElementById('province_name_val').value = id ? name : '';
    ['kabupaten_select','kecamatan_select','desa_select'].forEach(s => { document.getElementById(s).innerHTML='<option value="">-- Pilih --</option>'; document.getElementById(s).disabled=true; });
    ['regency_code','district_code','village_code','regency_name_val','district_name_val','village_name_val'].forEach(f => document.getElementById(f).value='');
    if (id) fetch(`${BASE}kabupaten/${id}.json`).then(r=>r.json()).then(d=>populateSelect(document.getElementById('kabupaten_select'),d,'id','nama',''));
});

document.getElementById('kabupaten_select').addEventListener('change', function() {
    const id = this.options[this.selectedIndex]?.value;
    const name = this.options[this.selectedIndex]?.text;
    document.getElementById('regency_code').value = id;
    document.getElementById('regency_name_val').value = id ? name : '';
    ['kecamatan_select','desa_select'].forEach(s => { document.getElementById(s).innerHTML='<option value="">-- Pilih --</option>'; document.getElementById(s).disabled=true; });
    ['district_code','village_code','district_name_val','village_name_val'].forEach(f => document.getElementById(f).value='');
    if (id) fetch(`${BASE}kecamatan/${id}.json`).then(r=>r.json()).then(d=>populateSelect(document.getElementById('kecamatan_select'),d,'id','nama',''));
});

document.getElementById('kecamatan_select').addEventListener('change', function() {
    const id = this.options[this.selectedIndex]?.value;
    const name = this.options[this.selectedIndex]?.text;
    document.getElementById('district_code').value = id;
    document.getElementById('district_name_val').value = id ? name : '';
    document.getElementById('desa_select').innerHTML='<option value="">-- Pilih --</option>'; document.getElementById('desa_select').disabled=true;
    ['village_code','village_name_val'].forEach(f => document.getElementById(f).value='');
    if (id) fetch(`${BASE}kelurahan/${id}.json`).then(r=>r.json()).then(d=>populateSelect(document.getElementById('desa_select'),d,'id','nama',''));
});

document.getElementById('desa_select').addEventListener('change', function() {
    const id = this.options[this.selectedIndex]?.value;
    const name = this.options[this.selectedIndex]?.text;
    document.getElementById('village_code').value = id;
    document.getElementById('village_name_val').value = id ? name : '';
});
</script>
@endpush
