@extends('template_distributor.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pengaturan /</span> Profil Saya</h4>

@if(session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <div class="col-md-12">
        <form action="{{ route('distributor.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="card mb-4">
                <h5 class="card-header">Detail Profil</h5>
                
                <div class="card-body text-center">
                    <div class="d-flex align-items-start align-items-sm-center justify-content-center gap-4">
                        <img src="{{ $distributor && $distributor->foto_diri ? Storage::url($distributor->foto_diri) : asset('admin/assets/img/avatars/1.png') }}" 
                             alt="user-avatar" 
                             class="d-block rounded border" 
                             height="150" 
                             width="150" 
                             id="uploadedAvatar" 
                             style="object-fit: cover;" />
                        <div class="button-wrapper text-start">
                            <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                <span class="d-none d-sm-block">Unggah Foto Baru</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input type="file" id="upload" name="foto_diri" class="account-file-input" hidden accept="image/png, image/jpeg, image/jpg, image/webp" />
                            </label>
                            <p class="text-muted mb-0">Format JPG, PNG, atau WEBP. Ukuran Maks 2MB.</p>
                            @error('foto_diri')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <hr class="my-0" />
                <div class="card-body">
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="nama" class="form-label">Nama Panggilan / Singkat</label>
                            <input class="form-control" type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" />
                            @error('nama') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <input class="form-control" type="text" name="username" id="username" value="{{ old('username', $user->username) }}" />
                            @error('username') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input class="form-control" type="text" id="email" name="email" value="{{ old('email', $user->email) }}" />
                            @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="kontak_wa" class="form-label">Nomor WhatsApp</label>
                            <input class="form-control" type="text" id="kontak_wa" name="kontak_wa" value="{{ old('kontak_wa', $distributor->kontak_wa) }}" />
                            @error('kontak_wa') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="password" class="form-label">Password Baru (Kosongkan jika tidak ganti)</label>
                            <input class="form-control" type="password" id="password" name="password" placeholder="········" />
                            @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" placeholder="········" />
                        </div>
                    </div>
                </div>
                <hr class="my-0" />
                <div class="card-body">
                    <h5 class="mb-4">Informasi Tambahan Distributor</h5>
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="nik_distribusi" class="form-label">NIK (Nomor Induk Kependudukan)</label>
                            <input class="form-control" type="text" id="nik_distribusi" name="nik_distribusi" value="{{ old('nik_distribusi', $distributor->nik_distribusi) }}" />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap Sesuai KTP</label>
                            <input class="form-control" type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $distributor->nama_lengkap) }}" />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="pendidikan" class="form-label">Pendidikan Terakhir</label>
                            <select name="pendidikan" id="pendidikan" class="form-select">
                                <option value="">Pilih Pendidikan</option>
                                @foreach(['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'Sarjana'] as $edu)
                                    <option value="{{ $edu }}" {{ old('pendidikan', $distributor->pendidikan) == $edu ? 'selected' : '' }}>{{ $edu }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="form-select">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Pria" {{ old('jenis_kelamin', $distributor->jenis_kelamin) == 'Pria' ? 'selected' : '' }}>Pria</option>
                                <option value="Wanita" {{ old('jenis_kelamin', $distributor->jenis_kelamin) == 'Wanita' ? 'selected' : '' }}>Wanita</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="my-0" />
                <div class="card-body">
                    <h5 class="mb-4">Informasi Alamat</h5>
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Provinsi</label>
                            <select id="provinsi_select" class="form-select select2-location">
                                <option value="">Pilih Provinsi</option>
                            </select>
                            <input type="hidden" name="province_code" id="province_code" value="{{ old('province_code', $distributor->province_code) }}">
                            <input type="hidden" name="province_name" id="province_name" value="{{ old('province_name', $distributor->province_name) }}">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Kabupaten/Kota</label>
                            <select id="kabupaten_select" class="form-select select2-location" disabled>
                                <option value="">Pilih Kabupaten/Kota</option>
                            </select>
                            <input type="hidden" name="regency_code" id="regency_code" value="{{ old('regency_code', $distributor->regency_code) }}">
                            <input type="hidden" name="regency_name" id="regency_name" value="{{ old('regency_name', $distributor->regency_name) }}">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Kecamatan</label>
                            <select id="kecamatan_select" class="form-select select2-location" disabled>
                                <option value="">Pilih Kecamatan</option>
                            </select>
                            <input type="hidden" name="district_code" id="district_code" value="{{ old('district_code', $distributor->district_code) }}">
                            <input type="hidden" name="district_name" id="district_name" value="{{ old('district_name', $distributor->district_name) }}">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Desa/Kelurahan</label>
                            <select id="kelurahan_select" class="form-select select2-location" disabled>
                                <option value="">Pilih Desa/Kelurahan</option>
                            </select>
                            <input type="hidden" name="village_code" id="village_code" value="{{ old('village_code', $distributor->village_code) }}">
                            <input type="hidden" name="village_name" id="village_name" value="{{ old('village_name', $distributor->village_name) }}">
                        </div>
                        <div class="mb-3 col-12">
                            <label for="alamat_detail" class="form-label">Alamat Lengkap (Keterangan)</label>
                            <textarea name="alamat_detail" id="alamat_detail" class="form-control" rows="3">{{ old('alamat_detail', $distributor->alamat_detail) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-end mt-3">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single { height: 38px; border: 1px solid #d9dee3; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px; color: #697a8d; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Foto Preview
    const uploadInput = document.getElementById('upload');
    const avatarImg = document.getElementById('uploadedAvatar');
    uploadInput.onchange = evt => {
        const [file] = uploadInput.files;
        if (file) avatarImg.src = URL.createObjectURL(file);
    }

    // API Wilayah
    const provSel = $('#provinsi_select'), kabSel = $('#kabupaten_select'), kecSel = $('#kecamatan_select'), kelSel = $('#kelurahan_select');
    const provC = $('#province_code'), provN = $('#province_name'), regC = $('#regency_code'), regN = $('#regency_name'), distC = $('#district_code'), distN = $('#district_name'), villC = $('#village_code'), villN = $('#village_name');

    $('.select2-location').select2({ width: '100%' });

    // Load Provinces
    fetch('https://ibnux.github.io/data-indonesia/provinsi.json')
        .then(r => r.json()).then(data => {
            let opts = '<option value="">Pilih Provinsi</option>';
            data.forEach(p => opts += `<option value="${p.id}" data-nama="${p.nama}" ${provC.val() == p.id ? 'selected' : ''}>${p.nama}</option>`);
            provSel.html(opts);
            if(provC.val()) loadKab(provC.val(), regC.val());
        });

    provSel.on('change', function(){
        const opt = $(this).find(':selected');
        provC.val(opt.val()); provN.val(opt.data('nama'));
        reset(kabSel,'Pilih Kabupaten/Kota'); reset(kecSel,'Pilih Kecamatan'); reset(kelSel,'Pilih Desa/Kelurahan');
        regC.val(''); regN.val(''); distC.val(''); distN.val(''); villC.val(''); villN.val('');
        if(opt.val()) loadKab(opt.val());
    });

    function loadKab(id, selId=null){
        kabSel.prop('disabled',true).html('<option>Memuat...</option>');
        fetch(`https://ibnux.github.io/data-indonesia/kabupaten/${id}.json`)
            .then(r=>r.json()).then(data => {
                let opts = '<option value="">Pilih Kabupaten/Kota</option>';
                data.forEach(k => opts += `<option value="${k.id}" data-nama="${k.nama}" ${selId == k.id ? 'selected' : ''}>${k.nama}</option>`);
                kabSel.prop('disabled',false).html(opts);
                if(selId && distC.val()) loadKec(selId, distC.val());
            });
    }

    kabSel.on('change', function(){
        const opt = $(this).find(':selected');
        regC.val(opt.val()); regN.val(opt.data('nama'));
        reset(kecSel,'Pilih Kecamatan'); reset(kelSel,'Pilih Desa/Kelurahan');
        distC.val(''); distN.val(''); villC.val(''); villN.val('');
        if(opt.val()) loadKec(opt.val());
    });

    function loadKec(id, selId=null){
        kecSel.prop('disabled',true).html('<option>Memuat...</option>');
        fetch(`https://ibnux.github.io/data-indonesia/kecamatan/${id}.json`)
            .then(r=>r.json()).then(data => {
                let opts = '<option value="">Pilih Kecamatan</option>';
                data.forEach(k => opts += `<option value="${k.id}" data-nama="${k.nama}" ${selId == k.id ? 'selected' : ''}>${k.nama}</option>`);
                kecSel.prop('disabled',false).html(opts);
                if(selId && villC.val()) loadKel(selId, villC.val());
            });
    }

    kecSel.on('change', function(){
        const opt = $(this).find(':selected');
        distC.val(opt.val()); distN.val(opt.data('nama'));
        reset(kelSel,'Pilih Desa/Kelurahan');
        villC.val(''); villN.val('');
        if(opt.val()) loadKel(opt.val());
    });

    function loadKel(id, selId=null){
        kelSel.prop('disabled',true).html('<option>Memuat...</option>');
        fetch(`https://ibnux.github.io/data-indonesia/kelurahan/${id}.json`)
            .then(r=>r.json()).then(data => {
                let opts = '<option value="">Pilih Desa/Kelurahan</option>';
                data.forEach(k => opts += `<option value="${k.id}" data-nama="${k.nama}" ${selId == k.id ? 'selected' : ''}>${k.nama}</option>`);
                kelSel.prop('disabled',false).html(opts);
            });
    }

    kelSel.on('change', function(){
        const opt = $(this).find(':selected');
        villC.val(opt.val()); villN.val(opt.data('nama'));
    });

    function reset(s, t){ s.html(`<option value="">${t}</option>`).prop('disabled', true); }
});
</script>
@endpush
