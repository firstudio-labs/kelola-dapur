@extends('template_kepala_dapur.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <nav class="d-flex align-items-center mb-2">
                        <a href="{{ route('dashboard') }}" class="text-muted me-2">
                            <i class="bx bx-home-alt me-1"></i>Dashboard
                        </a>
                        <i class="bx bx-chevron-right me-2"></i>
                        <a href="{{ route('kepala-dapur.users.index', ['dapur' => $dapur]) }}" class="text-muted me-2">Kelola User</a>
                        <i class="bx bx-chevron-right me-2"></i>
                        <span class="text-dark">Edit {{ $user->nama }}</span>
                    </nav>
                    <h4 class="mb-1">Edit User</h4>
                    <p class="mb-0 text-muted">Perbarui detail user</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('kepala-dapur.users.update', ['dapur' => $dapur, 'user' => $user]) }}" method="POST" class="row g-4" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="col-12">
                    <h5 class="card-title mb-0">Informasi User</h5>
                    <div class="row g-4 mt-2">
                        
                        <div class="col-md-6">
                            <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="nama" 
                                   id="nama" 
                                   required
                                   class="form-control @error('nama') is-invalid @enderror"
                                   placeholder="Contoh: John Doe"
                                   value="{{ old('nama', $user->nama) }}">
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="username" 
                                   id="username" 
                                   required
                                   class="form-control @error('username') is-invalid @enderror"
                                   placeholder="Contoh: johndoe"
                                   value="{{ old('username', $user->username) }}">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   required
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="Contoh: john@example.com"
                                   value="{{ old('email', $user->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="role_type" class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role_type" 
                                    id="role_type" 
                                    required
                                    class="form-select @error('role_type') is-invalid @enderror">
                                <option value="">Pilih Role</option>
                                @foreach ($roles as $value => $label)
                                    <option value="{{ $value }}" {{ old('role_type', $user->userRole->role_type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('role_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="is_active" class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Aktif</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label">Password Baru (Opsional)</label>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Kosongkan jika tidak ingin ubah">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation" 
                                   class="form-control @error('password_confirmation') is-invalid @enderror"
                                   placeholder="Ulangi password baru">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-12" id="admin_gudang_section" style="display: {{ old('role_type', $user->userRole->role_type) === 'admin_gudang' ? 'block' : 'none' }};">
                    <hr class="my-3">
                    <h5 class="card-title mb-3">Informasi Spesifik Admin Gudang</h5>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="nik_admin_gudang" class="form-label">NIK Logistik</label>
                            <input type="text" 
                                   name="nik_admin_gudang" 
                                   id="nik_admin_gudang"
                                   class="form-control @error('nik_admin_gudang') is-invalid @enderror"
                                   placeholder="Contoh: 3171234567890001"
                                   value="{{ old('nik_admin_gudang', $adminGudang->nik_admin_gudang ?? '') }}">
                            @error('nik_admin_gudang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="nama_lengkap_admin" class="form-label">Nama Lengkap Logistik</label>
                            <input type="text" 
                                   name="nama_lengkap" 
                                   id="nama_lengkap_admin"
                                   class="form-control @error('nama_lengkap') is-invalid @enderror"
                                   placeholder="Nama lengkap sesuai KTP"
                                   value="{{ old('nama_lengkap', $adminGudang->nama_lengkap ?? '') }}">
                            @error('nama_lengkap')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="jabatan_admin" class="form-label">Jabatan</label>
                            <select name="jabatan" id="jabatan_admin" class="form-select @error('jabatan') is-invalid @enderror">
                                <option value="">Pilih Jabatan</option>
                                <option value="Penanggung jawab" {{ old('jabatan', $adminGudang->jabatan ?? '') === 'Penanggung jawab' ? 'selected' : '' }}>Penanggung jawab</option>
                                <option value="Anggota" {{ old('jabatan', $adminGudang->jabatan ?? '') === 'Anggota' ? 'selected' : '' }}>Anggota</option>
                            </select>
                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="kontak_wa_admin" class="form-label">Nomor WhatsApp / Telepon</label>
                            <input type="text" 
                                   name="kontak_wa" 
                                   id="kontak_wa_admin"
                                   class="form-control @error('kontak_wa') is-invalid @enderror"
                                   placeholder="Contoh: 08123456789"
                                   value="{{ old('kontak_wa', $adminGudang->kontak_wa ?? '') }}">
                            @error('kontak_wa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="jenis_kelamin_admin" class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin_admin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Pria" {{ old('jenis_kelamin', $adminGudang->jenis_kelamin ?? '') === 'Pria' ? 'selected' : '' }}>Pria</option>
                                <option value="Wanita" {{ old('jenis_kelamin', $adminGudang->jenis_kelamin ?? '') === 'Wanita' ? 'selected' : '' }}>Wanita</option>
                            </select>
                            @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="pendidikan_admin" class="form-label">Pendidikan Terakhir</label>
                            <select name="pendidikan_terakhir" id="pendidikan_admin" class="form-select @error('pendidikan_terakhir') is-invalid @enderror">
                                <option value="">Pilih Pendidikan</option>
                                <option value="SD" {{ old('pendidikan_terakhir', $adminGudang->pendidikan_terakhir ?? '') === 'SD' ? 'selected' : '' }}>SD</option>
                                <option value="SMP" {{ old('pendidikan_terakhir', $adminGudang->pendidikan_terakhir ?? '') === 'SMP' ? 'selected' : '' }}>SMP</option>
                                <option value="SMA" {{ old('pendidikan_terakhir', $adminGudang->pendidikan_terakhir ?? '') === 'SMA' ? 'selected' : '' }}>SMA</option>
                                <option value="D1" {{ old('pendidikan_terakhir', $adminGudang->pendidikan_terakhir ?? '') === 'D1' ? 'selected' : '' }}>D1</option>
                                <option value="D2" {{ old('pendidikan_terakhir', $adminGudang->pendidikan_terakhir ?? '') === 'D2' ? 'selected' : '' }}>D2</option>
                                <option value="D3" {{ old('pendidikan_terakhir', $adminGudang->pendidikan_terakhir ?? '') === 'D3' ? 'selected' : '' }}>D3</option>
                                <option value="Sarjana" {{ old('pendidikan_terakhir', $adminGudang->pendidikan_terakhir ?? '') === 'Sarjana' ? 'selected' : '' }}>Sarjana (S1/S2/S3)</option>
                            </select>
                            @error('pendidikan_terakhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="foto_diri_admin" class="form-label">Foto Diri</label>
                            <input type="file" 
                                   name="foto_diri" 
                                   id="foto_diri_admin"
                                   accept="image/*"
                                   class="form-control @error('foto_diri') is-invalid @enderror">
                            @error('foto_diri')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <div class="mt-3">
                                <img id="image_preview_admin" 
                                     src="{{ ($adminGudang ?? false) && $adminGudang->foto_diri ? Storage::url($adminGudang->foto_diri) : '#' }}" 
                                     alt="Preview Foto Diri" 
                                     class="img-fluid rounded border" 
                                     style="max-height: 200px; object-fit: cover; {{ ($adminGudang ?? false) && $adminGudang->foto_diri ? 'display: block;' : 'display: none;' }}">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-12" id="ahli_gizi_section" style="display: {{ old('role_type', $user->userRole->role_type) === 'ahli_gizi' ? 'block' : 'none' }};">
                    <hr class="my-3">
                    <h5 class="card-title mb-3">Informasi Spesifik Ahli Gizi</h5>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="nik_ahli_gizi" class="form-label">NIK Ahli Gizi</label>
                            <input type="text" 
                                   name="nik_ahli_gizi" 
                                   id="nik_ahli_gizi"
                                   class="form-control @error('nik_ahli_gizi') is-invalid @enderror"
                                   placeholder="Contoh: 3171234567890001"
                                   value="{{ old('nik_ahli_gizi', $ahliGizi->nik_ahli_gizi ?? '') }}">
                            @error('nik_ahli_gizi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <select name="jabatan" id="jabatan" class="form-select @error('jabatan') is-invalid @enderror">
                                <option value="">Pilih Jabatan</option>
                                <option value="Penanggung jawab" {{ old('jabatan', $ahliGizi->jabatan ?? '') === 'Penanggung jawab' ? 'selected' : '' }}>Penanggung jawab</option>
                                <option value="Anggota" {{ old('jabatan', $ahliGizi->jabatan ?? '') === 'Anggota' ? 'selected' : '' }}>Anggota</option>
                            </select>
                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="kontak_wa" class="form-label">Nomor WhatsApp / Telepon</label>
                            <input type="text" 
                                   name="kontak_wa" 
                                   id="kontak_wa"
                                   class="form-control @error('kontak_wa') is-invalid @enderror"
                                   placeholder="Contoh: 08123456789"
                                   value="{{ old('kontak_wa', $ahliGizi->kontak_wa ?? '') }}">
                            @error('kontak_wa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Pria" {{ old('jenis_kelamin', $ahliGizi->jenis_kelamin ?? '') === 'Pria' ? 'selected' : '' }}>Pria</option>
                                <option value="Wanita" {{ old('jenis_kelamin', $ahliGizi->jenis_kelamin ?? '') === 'Wanita' ? 'selected' : '' }}>Wanita</option>
                            </select>
                            @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="pendidikan_terakhir" class="form-label">Pendidikan Terakhir</label>
                            <select name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-select @error('pendidikan_terakhir') is-invalid @enderror">
                                <option value="">Pilih Pendidikan</option>
                                <option value="SD" {{ old('pendidikan_terakhir', $ahliGizi->pendidikan_terakhir ?? '') === 'SD' ? 'selected' : '' }}>SD</option>
                                <option value="SMP" {{ old('pendidikan_terakhir', $ahliGizi->pendidikan_terakhir ?? '') === 'SMP' ? 'selected' : '' }}>SMP</option>
                                <option value="SMA" {{ old('pendidikan_terakhir', $ahliGizi->pendidikan_terakhir ?? '') === 'SMA' ? 'selected' : '' }}>SMA</option>
                                <option value="D1" {{ old('pendidikan_terakhir', $ahliGizi->pendidikan_terakhir ?? '') === 'D1' ? 'selected' : '' }}>D1</option>
                                <option value="D2" {{ old('pendidikan_terakhir', $ahliGizi->pendidikan_terakhir ?? '') === 'D2' ? 'selected' : '' }}>D2</option>
                                <option value="D3" {{ old('pendidikan_terakhir', $ahliGizi->pendidikan_terakhir ?? '') === 'D3' ? 'selected' : '' }}>D3</option>
                                <option value="Sarjana" {{ old('pendidikan_terakhir', $ahliGizi->pendidikan_terakhir ?? '') === 'Sarjana' ? 'selected' : '' }}>Sarjana (S1/S2/S3)</option>
                            </select>
                            @error('pendidikan_terakhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="foto_diri" class="form-label">Foto Diri</label>
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
                                     src="{{ ($ahliGizi ?? false) && $ahliGizi->foto_diri ? Storage::url($ahliGizi->foto_diri) : '#' }}" 
                                     alt="Preview Foto Diri" 
                                     class="img-fluid rounded border" 
                                     style="max-height: 200px; object-fit: cover; {{ ($ahliGizi ?? false) && $ahliGizi->foto_diri ? 'display: block;' : 'display: none;' }}">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-12" id="address_section" style="display: {{ in_array(old('role_type', $user->userRole->role_type), ['admin_gudang', 'ahli_gizi', 'produksi', 'distributor']) ? 'block' : 'none' }};">
                    <hr class="my-3">
                    <h5 class="card-title mb-3">Informasi Alamat</h5>
                    <div class="row g-4">
                        <input type="hidden" name="province_code" id="province_code" value="{{ old('province_code', $adminGudang->province_code ?? ($ahliGizi->province_code ?? ($produksi->province_code ?? ($distributor->province_code ?? '')))) }}">
                        <input type="hidden" name="regency_code" id="regency_code" value="{{ old('regency_code', $adminGudang->regency_code ?? ($ahliGizi->regency_code ?? ($produksi->regency_code ?? ($distributor->regency_code ?? '')))) }}">
                        <input type="hidden" name="district_code" id="district_code" value="{{ old('district_code', $adminGudang->district_code ?? ($ahliGizi->district_code ?? ($produksi->district_code ?? ($distributor->district_code ?? '')))) }}">
                        <input type="hidden" name="village_code" id="village_code" value="{{ old('village_code', $adminGudang->village_code ?? ($ahliGizi->village_code ?? ($produksi->village_code ?? ($distributor->village_code ?? '')))) }}">

                        <input type="hidden" name="province_name" id="provinsi_name" value="{{ old('province_name', $adminGudang->province_name ?? ($ahliGizi->province_name ?? ($produksi->province_name ?? ($distributor->province_name ?? '')))) }}">
                        <input type="hidden" name="regency_name" id="kabupaten_name" value="{{ old('regency_name', $adminGudang->regency_name ?? ($ahliGizi->regency_name ?? ($produksi->regency_name ?? ($distributor->regency_name ?? '')))) }}">
                        <input type="hidden" name="district_name" id="kecamatan_name" value="{{ old('district_name', $adminGudang->district_name ?? ($ahliGizi->district_name ?? ($produksi->district_name ?? ($distributor->district_name ?? '')))) }}">
                        <input type="hidden" name="village_name" id="kelurahan_name" value="{{ old('village_name', $adminGudang->village_name ?? ($ahliGizi->village_name ?? ($produksi->village_name ?? ($distributor->village_name ?? '')))) }}">

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
                            <label for="alamat_detail" class="form-label">Alamat Lengkap (Keterangan)</label>
                            <textarea name="alamat_detail" 
                                      id="alamat_detail" 
                                      class="form-control @error('alamat_detail') is-invalid @enderror" 
                                      rows="6" 
                                      placeholder="Nama jalan, gedung, no rumah, RT/RW, patokan">{{ old('alamat_detail', $adminGudang->alamat_detail ?? ($ahliGizi->alamat_detail ?? ($produksi->alamat_detail ?? ($distributor->alamat_detail ?? '')))) }}</textarea>
                            @error('alamat_detail')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-12" id="produksi_section" style="display: {{ old('role_type', $user->userRole->role_type) === 'produksi' ? 'block' : 'none' }};">

                    <hr class="my-3">
                    <h5 class="card-title mb-3">Informasi Spesifik Produksi</h5>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="nik_produksi" class="form-label">NIK Produksi</label>
                            <input type="text" 
                                   name="nik_produksi" 
                                   id="nik_produksi"
                                   class="form-control @error('nik_produksi') is-invalid @enderror"
                                   placeholder="Contoh: 3171234567890001"
                                   value="{{ old('nik_produksi', $produksi->nik_produksi ?? '') }}">
                            @error('nik_produksi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap Produksi</label>
                            <input type="text" 
                                   name="nama_lengkap" 
                                   id="nama_lengkap"
                                   class="form-control @error('nama_lengkap') is-invalid @enderror"
                                   placeholder="Nama lengkap sesuai KTP"
                                   value="{{ old('nama_lengkap', $produksi->nama_lengkap ?? '') }}">
                            @error('nama_lengkap')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="jabatan_prod" class="form-label">Jabatan</label>
                            <select name="jabatan" id="jabatan_prod" class="form-select @error('jabatan') is-invalid @enderror">
                                <option value="">Pilih Jabatan</option>
                                <option value="Penanggung jawab" {{ old('jabatan', $produksi->jabatan ?? '') === 'Penanggung jawab' ? 'selected' : '' }}>Penanggung jawab</option>
                                <option value="Anggota" {{ old('jabatan', $produksi->jabatan ?? '') === 'Anggota' ? 'selected' : '' }}>Anggota</option>
                            </select>
                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="kontak_wa_prod" class="form-label">Nomor WhatsApp / Telepon</label>
                            <input type="text" 
                                   name="kontak_wa" 
                                   id="kontak_wa_prod"
                                   class="form-control @error('kontak_wa') is-invalid @enderror"
                                   placeholder="Contoh: 08123456789"
                                   value="{{ old('kontak_wa', $produksi->kontak_wa ?? '') }}">
                            @error('kontak_wa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="jenis_kelamin_prod" class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin_prod" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Pria" {{ old('jenis_kelamin', $produksi->jenis_kelamin ?? '') === 'Pria' ? 'selected' : '' }}>Pria</option>
                                <option value="Wanita" {{ old('jenis_kelamin', $produksi->jenis_kelamin ?? '') === 'Wanita' ? 'selected' : '' }}>Wanita</option>
                            </select>
                            @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="pendidikan_prod" class="form-label">Pendidikan</label>
                            <select name="pendidikan" id="pendidikan_prod" class="form-select @error('pendidikan') is-invalid @enderror">
                                <option value="">Pilih Pendidikan</option>
                                <option value="SD" {{ old('pendidikan', $produksi->pendidikan ?? '') === 'SD' ? 'selected' : '' }}>SD</option>
                                <option value="SMP" {{ old('pendidikan', $produksi->pendidikan ?? '') === 'SMP' ? 'selected' : '' }}>SMP</option>
                                <option value="SMA" {{ old('pendidikan', $produksi->pendidikan ?? '') === 'SMA' ? 'selected' : '' }}>SMA</option>
                                <option value="D1" {{ old('pendidikan', $produksi->pendidikan ?? '') === 'D1' ? 'selected' : '' }}>D1</option>
                                <option value="D2" {{ old('pendidikan', $produksi->pendidikan ?? '') === 'D2' ? 'selected' : '' }}>D2</option>
                                <option value="D3" {{ old('pendidikan', $produksi->pendidikan ?? '') === 'D3' ? 'selected' : '' }}>D3</option>
                                <option value="Sarjana" {{ old('pendidikan', $produksi->pendidikan ?? '') === 'Sarjana' ? 'selected' : '' }}>Sarjana (S1/S2/S3)</option>
                            </select>
                            @error('pendidikan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="foto_diri_prod" class="form-label">Foto Diri</label>
                            <input type="file" 
                                   name="foto_diri" 
                                   id="foto_diri_prod"
                                   accept="image/*"
                                   class="form-control @error('foto_diri') is-invalid @enderror">
                            @error('foto_diri')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <div class="mt-3">
                                <img id="image_preview_prod" 
                                     src="{{ ($produksi ?? false) && $produksi->foto_diri ? Storage::url($produksi->foto_diri) : '#' }}" 
                                     alt="Preview Foto Diri" 
                                     class="img-fluid rounded border" 
                                     style="max-height: 200px; object-fit: cover; {{ ($produksi ?? false) && $produksi->foto_diri ? 'display: block;' : 'display: none;' }}">
                            </div>
                        </div>

                    </div>
                </div>
                
                <div class="col-12" id="distributor_section" style="display: {{ old('role_type', $user->userRole->role_type) === 'distributor' ? 'block' : 'none' }};">
                    <hr class="my-3">
                    <h5 class="card-title mb-3">Informasi Spesifik Distributor</h5>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="nik_distribusi" class="form-label">NIK Distributor</label>
                            <input type="text" 
                                   name="nik_distribusi" 
                                   id="nik_distribusi"
                                   class="form-control @error('nik_distribusi') is-invalid @enderror"
                                   placeholder="NIK Distributor"
                                   value="{{ old('nik_distribusi', $distributor->nik_distribusi ?? '') }}">
                            @error('nik_distribusi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="nama_lengkap_dist" class="form-label">Nama Lengkap Distributor</label>
                            <input type="text" 
                                   name="nama_lengkap" 
                                   id="nama_lengkap_dist"
                                   class="form-control @error('nama_lengkap') is-invalid @enderror"
                                   placeholder="Nama lengkap"
                                   value="{{ old('nama_lengkap', $distributor->nama_lengkap ?? '') }}">
                            @error('nama_lengkap')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="jabatan_dist" class="form-label">Jabatan</label>
                            <select name="jabatan" id="jabatan_dist" class="form-select @error('jabatan') is-invalid @enderror">
                                <option value="">Pilih Jabatan</option>
                                <option value="Penanggung jawab" {{ old('jabatan', $distributor->jabatan ?? '') === 'Penanggung jawab' ? 'selected' : '' }}>Penanggung jawab</option>
                                <option value="Anggota" {{ old('jabatan', $distributor->jabatan ?? '') === 'Anggota' ? 'selected' : '' }}>Anggota</option>
                            </select>
                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="kontak_wa_dist" class="form-label">Nomor WhatsApp / Telepon</label>
                            <input type="text" 
                                   name="kontak_wa" 
                                   id="kontak_wa_dist"
                                   class="form-control @error('kontak_wa') is-invalid @enderror"
                                   placeholder="Contoh: 08123456789"
                                   value="{{ old('kontak_wa', $distributor->kontak_wa ?? '') }}">
                            @error('kontak_wa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="jenis_kelamin_dist" class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin_dist" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Pria" {{ old('jenis_kelamin', $distributor->jenis_kelamin ?? '') === 'Pria' ? 'selected' : '' }}>Pria</option>
                                <option value="Wanita" {{ old('jenis_kelamin', $distributor->jenis_kelamin ?? '') === 'Wanita' ? 'selected' : '' }}>Wanita</option>
                            </select>
                            @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="pendidikan_dist" class="form-label">Pendidikan</label>
                            <select name="pendidikan" id="pendidikan_dist" class="form-select @error('pendidikan') is-invalid @enderror">
                                <option value="">Pilih Pendidikan</option>
                                <option value="SD" {{ old('pendidikan', $distributor->pendidikan ?? '') === 'SD' ? 'selected' : '' }}>SD</option>
                                <option value="SMP" {{ old('pendidikan', $distributor->pendidikan ?? '') === 'SMP' ? 'selected' : '' }}>SMP</option>
                                <option value="SMA" {{ old('pendidikan', $distributor->pendidikan ?? '') === 'SMA' ? 'selected' : '' }}>SMA</option>
                                <option value="D1" {{ old('pendidikan', $distributor->pendidikan ?? '') === 'D1' ? 'selected' : '' }}>D1</option>
                                <option value="D2" {{ old('pendidikan', $distributor->pendidikan ?? '') === 'D2' ? 'selected' : '' }}>D2</option>
                                <option value="D3" {{ old('pendidikan', $distributor->pendidikan ?? '') === 'D3' ? 'selected' : '' }}>D3</option>
                                <option value="Sarjana" {{ old('pendidikan', $distributor->pendidikan ?? '') === 'Sarjana' ? 'selected' : '' }}>Sarjana (S1/S2/S3)</option>
                            </select>
                            @error('pendidikan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="foto_diri_dist" class="form-label">Foto Diri</label>
                            <input type="file" 
                                   name="foto_diri" 
                                   id="foto_diri_dist"
                                   accept="image/*"
                                   class="form-control @error('foto_diri') is-invalid @enderror">
                            @error('foto_diri')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <div class="mt-3">
                                <img id="image_preview_dist" 
                                     src="{{ ($distributor ?? false) && $distributor->foto_diri ? Storage::url($distributor->foto_diri) : '#' }}" 
                                     alt="Preview Foto Diri" 
                                     class="img-fluid rounded border" 
                                     style="max-height: 200px; object-fit: cover; {{ ($distributor ?? false) && $distributor->foto_diri ? 'display: block;' : 'display: none;' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('kepala-dapur.users.index', ['dapur' => $dapur]) }}" class="btn btn-label-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="alert alert-info alert-dismissible" role="alert">
        <h6 class="alert-heading mb-2">Instruksi Edit User</h6>
        <ul class="mb-0">
            <li>Username dan email harus unik.</li>
            <li>Kosongkan password jika tidak ingin mengubah.</li>
            <li>Perubahan role akan menampilkan/menyembunyikan form tambahan terkait (seperti form spesifik Ahli Gizi).</li>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
    // Role Switch Logic
    const roleSelect = document.getElementById('role_type');
    const adminGudangSection = document.getElementById('admin_gudang_section');
    const ahliGiziSection = document.getElementById('ahli_gizi_section');
    const produksiSection = document.getElementById('produksi_section');
    const distributorSection = document.getElementById('distributor_section');
    const addressSection = document.getElementById('address_section');

    function toggleSections() {
        const role = roleSelect.value;
        
        // Hide all first
        adminGudangSection.style.display = 'none';
        ahliGiziSection.style.display = 'none';
        produksiSection.style.display = 'none';
        distributorSection.style.display = 'none';
        addressSection.style.display = 'none';
        
        // Disable all inputs in sections to avoid submitting unused fields with duplicate names
        adminGudangSection.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
        ahliGiziSection.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
        produksiSection.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
        distributorSection.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
        addressSection.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);

        if (role === 'admin_gudang') {
            adminGudangSection.style.display = 'block';
            adminGudangSection.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
            addressSection.style.display = 'block';
            addressSection.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
        } else if (role === 'ahli_gizi') {
            ahliGiziSection.style.display = 'block';
            ahliGiziSection.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
            addressSection.style.display = 'block';
            addressSection.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
        } else if (role === 'produksi') {
            produksiSection.style.display = 'block';
            produksiSection.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
            addressSection.style.display = 'block';
            addressSection.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
        } else if (role === 'distributor') {
            distributorSection.style.display = 'block';
            distributorSection.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
            addressSection.style.display = 'block';
            addressSection.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
        }
    }

    roleSelect.addEventListener('change', toggleSections);
    toggleSections(); // Call on load

    // Foto Preview Logic
    const fotoInputAdmin = document.getElementById('foto_diri_admin');
    const imagePreviewAdmin = document.getElementById('image_preview_admin');

    const fotoInput = document.getElementById('foto_diri');
    const imagePreview = document.getElementById('image_preview');

    const fotoInputProd = document.getElementById('foto_diri_prod');
    const imagePreviewProd = document.getElementById('image_preview_prod');

    const fotoInputDist = document.getElementById('foto_diri_dist');
    const imagePreviewDist = document.getElementById('image_preview_dist');

    function handleImagePreview(input, preview, defaultUrl) {
        if (input) {
            input.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                } else {
                    if (defaultUrl && defaultUrl !== '#') {
                        preview.src = defaultUrl;
                    } else {
                        preview.style.display = 'none';
                        preview.src = '#';
                    }
                }
            });
        }
    }

    handleImagePreview(fotoInputAdmin, imagePreviewAdmin, "{{ ($adminGudang ?? false) && $adminGudang->foto_diri ? Storage::url($adminGudang->foto_diri) : '#' }}");
    handleImagePreview(fotoInput, imagePreview, "{{ ($ahliGizi ?? false) && $ahliGizi->foto_diri ? Storage::url($ahliGizi->foto_diri) : '#' }}");
    handleImagePreview(fotoInputProd, imagePreviewProd, "{{ ($produksi ?? false) && $produksi->foto_diri ? Storage::url($produksi->foto_diri) : '#' }}");
    handleImagePreview(fotoInputDist, imagePreviewDist, "{{ ($distributor ?? false) && $distributor->foto_diri ? Storage::url($distributor->foto_diri) : '#' }}");

    // Initialize Select2
    $('.select2').select2({
        theme: 'default',
        width: '100%'
    });

    // API Wilayah Logic
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