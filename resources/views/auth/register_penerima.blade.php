<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Penerima MBG - Kelola Dapur</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendor/css/core.css">
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendor/css/theme-default.css">
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/demo.css">
    <script src="{{ asset('admin') }}/assets/vendor/js/helpers.js"></script>
    <script src="{{ asset('admin') }}/assets/js/config.js"></script>
</head>
<body>
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4" style="max-width: 700px; margin: auto;">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="app-brand justify-content-center mb-2">
                        <span class="app-brand-text fw-bolder fs-4">Daftar <span class="text-primary">Kelola Dapur</span></span>
                    </div>
                    <p class="mb-3 text-center text-muted small">Pilih peran Anda untuk memulai pendaftaran</p>

                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 py-2">
                                <i class="bx bx-store me-1"></i> <small>Kepala Dapur</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('daftar-mbg') }}" class="btn btn-primary w-100 py-2">
                                <i class="bx bx-user me-1"></i> <small>Penerima MBG</small>
                            </a>
                        </div>
                    </div>

                    <div class="divider mb-4">
                        <div class="divider-text">Formulir Pendaftaran Penerima MBG</div>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form id="formPenerima" action="{{ route('daftar-mbg.post') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <h6 class="fw-semibold pb-2 border-bottom mb-3">Informasi Akun</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required>
                                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" required minlength="8">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Dapur SPPG Tujuan <span class="text-danger">*</span></label>
                                <select name="id_dapur" class="form-select @error('id_dapur') is-invalid @enderror" required>
                                    <option value="">-- Pilih Dapur SPPG --</option>
                                    @foreach($dapurList as $d)
                                        <option value="{{ $d->id_dapur }}" {{ old('id_dapur') == $d->id_dapur ? 'selected' : '' }}>{{ $d->nama_dapur }}</option>
                                    @endforeach
                                </select>
                                @error('id_dapur')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <h6 class="fw-semibold pb-2 border-bottom mb-3">Identitas Penerima</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Jenis Identitas <span class="text-danger">*</span></label>
                                <select name="id_type" class="form-select" required>
                                    <option value="nik" {{ old('id_type') == 'nik' ? 'selected' : '' }}>NIK</option>
                                    <option value="nisn" {{ old('id_type') == 'nisn' ? 'selected' : '' }}>NISN</option>
                                    <option value="no_registrasi" {{ old('id_type') == 'no_registrasi' ? 'selected' : '' }}>No. Registrasi</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nomor Identitas <span class="text-danger">*</span></label>
                                <input type="text" name="id_number" class="form-control @error('id_number') is-invalid @enderror" value="{{ old('id_number') }}" required>
                                @error('id_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Penanggung Jawab <span class="text-danger">*</span></label>
                                <input type="text" name="penanggung_jawab" class="form-control @error('penanggung_jawab') is-invalid @enderror" value="{{ old('penanggung_jawab') }}" required>
                                @error('penanggung_jawab')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <h6 class="fw-semibold pb-2 border-bottom mb-3">Alamat Lokasi MBG</h6>
                        <input type="hidden" name="province_code" id="prov_code">
                        <input type="hidden" name="province_name" id="prov_name">
                        <input type="hidden" name="regency_code" id="kab_code">
                        <input type="hidden" name="regency_name" id="kab_name">
                        <input type="hidden" name="district_code" id="kec_code">
                        <input type="hidden" name="district_name" id="kec_name">
                        <input type="hidden" name="village_code" id="desa_code">
                        <input type="hidden" name="village_name" id="desa_name">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                                <select id="sel_prov" class="form-select"><option value="">-- Pilih Provinsi --</option></select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kabupaten/Kota <span class="text-danger">*</span></label>
                                <select id="sel_kab" class="form-select" disabled><option value="">-- Pilih Kabupaten/Kota --</option></select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kecamatan</label>
                                <select id="sel_kec" class="form-select" disabled><option value="">-- Pilih Kecamatan --</option></select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Desa/Kelurahan</label>
                                <select id="sel_desa" class="form-select" disabled><option value="">-- Pilih Desa/Kelurahan --</option></select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Detail Alamat <span class="text-danger">*</span></label>
                                <textarea name="alamat_detail" class="form-control @error('alamat_detail') is-invalid @enderror" rows="3" required>{{ old('alamat_detail') }}</textarea>
                                @error('alamat_detail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <h6 class="fw-semibold pb-2 border-bottom mb-3">Titik Lokasi & Foto</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Latitude</label>
                                <input type="text" name="latitude" class="form-control" value="{{ old('latitude') }}" placeholder="-7.12345">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Longitude</label>
                                <input type="text" name="longitude" class="form-control" value="{{ old('longitude') }}" placeholder="110.12345">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Link Google Maps</label>
                                <input type="url" name="link_gmaps" class="form-control" value="{{ old('link_gmaps') }}" placeholder="https://maps.google.com/...">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Foto Lokasi</label>
                                <input type="file" name="foto_lokasi" class="form-control @error('foto_lokasi') is-invalid @enderror" accept="image/*">
                                @error('foto_lokasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-send me-1"></i> Kirim Pendaftaran
                        </button>
                        <p class="text-center mt-3 text-muted">
                            Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('admin') }}/assets/vendor/libs/jquery/jquery.js"></script>
<script src="{{ asset('admin') }}/assets/vendor/libs/popper/popper.js"></script>
<script src="{{ asset('admin') }}/assets/vendor/js/bootstrap.js"></script>
<script>
const BASE = 'https://ibnux.github.io/data-indonesia/';
function fill(sel, data, valK, nameK) {
    sel.innerHTML = '<option value="">-- Pilih --</option>';
    data.forEach(d => { const o = document.createElement('option'); o.value=d[valK]; o.text=d[nameK]; sel.appendChild(o); });
    sel.disabled = false;
}
fetch(BASE + 'provinsi.json').then(r=>r.json()).then(d=>fill(document.getElementById('sel_prov'),d,'id','nama'));

document.getElementById('sel_prov').addEventListener('change', function() {
    const id=this.value, name=this.options[this.selectedIndex]?.text;
    document.getElementById('prov_code').value=id; document.getElementById('prov_name').value=id?name:'';
    ['sel_kab','sel_kec','sel_desa'].forEach(s=>{document.getElementById(s).innerHTML='<option value="">-- Pilih --</option>';document.getElementById(s).disabled=true;});
    if(id) fetch(BASE+'kabupaten/'+id+'.json').then(r=>r.json()).then(d=>fill(document.getElementById('sel_kab'),d,'id','nama'));
});
document.getElementById('sel_kab').addEventListener('change', function() {
    const id=this.value, name=this.options[this.selectedIndex]?.text;
    document.getElementById('kab_code').value=id; document.getElementById('kab_name').value=id?name:'';
    ['sel_kec','sel_desa'].forEach(s=>{document.getElementById(s).innerHTML='<option value="">-- Pilih --</option>';document.getElementById(s).disabled=true;});
    if(id) fetch(BASE+'kecamatan/'+id+'.json').then(r=>r.json()).then(d=>fill(document.getElementById('sel_kec'),d,'id','nama'));
});
document.getElementById('sel_kec').addEventListener('change', function() {
    const id=this.value, name=this.options[this.selectedIndex]?.text;
    document.getElementById('kec_code').value=id; document.getElementById('kec_name').value=id?name:'';
    document.getElementById('sel_desa').innerHTML='<option value="">-- Pilih --</option>'; document.getElementById('sel_desa').disabled=true;
    if(id) fetch(BASE+'kelurahan/'+id+'.json').then(r=>r.json()).then(d=>fill(document.getElementById('sel_desa'),d,'id','nama'));
});
document.getElementById('sel_desa').addEventListener('change', function() {
    document.getElementById('desa_code').value=this.value; document.getElementById('desa_name').value=this.value?this.options[this.selectedIndex]?.text:'';
});
</script>
</body>
</html>
