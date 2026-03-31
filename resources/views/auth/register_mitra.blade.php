<!DOCTYPE html>
<html
    lang="en"
    class="light-style customizer-hide"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="{{ asset("admin") }}/assets/"
    data-template="vertical-menu-template-free"
>
    <head>
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
        />
        <title>Daftar Mitra - Kelola Dapur</title>

        <link rel="icon" type="image/x-icon" href="{{ asset("env") }}/logoo.png" />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset("admin") }}/assets/vendor/fonts/boxicons.css" />
        <link rel="stylesheet" href="{{ asset("admin") }}/assets/vendor/css/core.css" class="template-customizer-core-css" />
        <link rel="stylesheet" href="{{ asset("admin") }}/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
        <link rel="stylesheet" href="{{ asset("admin") }}/assets/css/demo.css" />
        <link rel="stylesheet" href="{{ asset("admin") }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
        <link rel="stylesheet" href="{{ asset("admin") }}/assets/vendor/css/pages/page-auth.css" />

        <script src="{{ asset("admin") }}/assets/vendor/js/helpers.js"></script>
        <script src="{{ asset("admin") }}/assets/js/config.js"></script>
        <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
    </head>

    <body style="background-color: #3758F9">
        <div class="container-xxl">
            <div class="authentication-wrapper authentication-basic container-p-y">
                <div class="authentication-inner py-4" style="max-width: 800px;">
                    <div class="card">
                        <div class="card-body">

                            <div class="app-brand justify-content-center">
                                <a href="/welcome" class="app-brand-link gap-2">
                                    <span class="app-brand-logo demo">
                                        <img src="{{ asset("logo_kelola_dapur_black.png") }}" alt="Logo" style="height: 60px" />
                                    </span>
                                </a>
                            </div>

                            @if (session("success"))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session("success") }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if (session("error"))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session("error") }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <h4 class="mb-2 text-center">Bergabung dengan Kelola Dapur</h4>
                            <p class="mb-3 text-center">Pilih peran Anda untuk memulai pendaftaran</p>

                            <div class="row g-2 mb-4">
                                <div class="col-4">
                                    <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 py-2">
                                        <i class="bx bx-store me-1"></i><small>Kepala Dapur</small>
                                    </a>
                                </div>
                                <div class="col-4">
                                    <a href="{{ route('daftar-mitra') }}" class="btn btn-primary w-100 py-2">
                                        <i class="bx bx-buildings me-1"></i><small>Mitra</small>
                                    </a>
                                </div>
                                <div class="col-4">
                                    <a href="{{ route('daftar-mbg') }}" class="btn btn-outline-primary w-100 py-2">
                                        <i class="bx bx-user me-1"></i><small>Penerima MBG</small>
                                    </a>
                                </div>
                            </div>

                            <div class="divider mb-4">
                                <div class="divider-text">Detail Pendaftaran Mitra</div>
                            </div>

                            <form id="formAuthentication" class="mb-3" action="{{ route("daftar-mitra.post") }}" method="POST">
                                @csrf

                                {{-- Hidden wilayah codes --}}
                                <input type="hidden" name="province_code" id="province_code" />
                                <input type="hidden" name="regency_code" id="regency_code" />
                                <input type="hidden" name="district_code" id="district_code" />
                                <input type="hidden" name="village_code" id="village_code" />

                                {{-- ========== AKUN ========== --}}
                                <div class="divider my-3">
                                    <div class="divider-text text-muted small"><i class="bx bx-user-circle me-1"></i>Informasi Akun</div>
                                </div>

                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control @error("nama") is-invalid @enderror"
                                        id="nama" name="nama"
                                        placeholder="Masukkan nama lengkap"
                                        value="{{ old("nama") }}" required autofocus />
                                    @error("nama")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error("username") is-invalid @enderror"
                                            id="username" name="username"
                                            placeholder="Pilih username unik"
                                            value="{{ old("username") }}" required />
                                        @error("username")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email"
                                            class="form-control @error("email") is-invalid @enderror"
                                            id="email" name="email"
                                            placeholder="Masukkan email"
                                            value="{{ old("email") }}" required />
                                        @error("email")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="password"
                                                class="form-control @error("password") is-invalid @enderror"
                                                name="password" placeholder="Min. 8 karakter" required />
                                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                        </div>
                                        @error("password")
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="password_confirmation">Konfirmasi Password <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="password_confirmation"
                                                class="form-control"
                                                name="password_confirmation" placeholder="Ulangi password" required />
                                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- ========== DATA PEMILIK ========== --}}
                                <div class="divider my-3">
                                    <div class="divider-text text-muted small"><i class="bx bx-id-card me-1"></i>Data Pemilik</div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="nik_pemilik" class="form-label">NIK Pemilik <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error("nik_pemilik") is-invalid @enderror"
                                            id="nik_pemilik" name="nik_pemilik"
                                            placeholder="16 digit NIK"
                                            maxlength="20"
                                            value="{{ old("nik_pemilik") }}" required />
                                        @error("nik_pemilik")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nama_pemilik" class="form-label">Nama Pemilik <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error("nama_pemilik") is-invalid @enderror"
                                            id="nama_pemilik" name="nama_pemilik"
                                            placeholder="Nama sesuai KTP"
                                            value="{{ old("nama_pemilik") }}" required />
                                        @error("nama_pemilik")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- ========== ALAMAT PEMILIK ========== --}}
                                <div class="divider my-3">
                                    <div class="divider-text text-muted small"><i class="bx bx-map me-1"></i>Alamat Pemilik</div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="provinsi" class="form-label">Provinsi <span class="text-danger">*</span></label>
                                        <select id="provinsi" name="province_name"
                                            class="form-select @error("province_name") is-invalid @enderror" required>
                                            <option value="">Pilih Provinsi</option>
                                        </select>
                                        @error("province_name")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="kabupaten_kota" class="form-label">Kabupaten/Kota <span class="text-danger">*</span></label>
                                        <select id="kabupaten_kota" name="regency_name"
                                            class="form-select @error("regency_name") is-invalid @enderror" required>
                                            <option value="">Pilih Kabupaten/Kota</option>
                                        </select>
                                        @error("regency_name")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="kecamatan" class="form-label">Kecamatan</label>
                                        <select id="kecamatan" name="district_name"
                                            class="form-select @error("district_name") is-invalid @enderror">
                                            <option value="">Pilih Kecamatan (Opsional)</option>
                                        </select>
                                        @error("district_name")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="kelurahan" class="form-label">Kelurahan/Desa</label>
                                        <select id="kelurahan" name="village_name"
                                            class="form-select @error("village_name") is-invalid @enderror">
                                            <option value="">Pilih Kelurahan/Desa (Opsional)</option>
                                        </select>
                                        @error("village_name")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="alamat_detail" class="form-label">Detail Alamat <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error("alamat_detail") is-invalid @enderror"
                                        id="alamat_detail" name="alamat_detail" rows="2"
                                        placeholder="No. Jalan, RT/RW, dll." required>{{ old("alamat_detail") }}</textarea>
                                    @error("alamat_detail")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>



                                {{-- ========== CAPTCHA & TERMS ========== --}}
                                <div class="mb-3">
                                    <div class="h-captcha"
                                         data-sitekey="{{ config('services.hcaptcha.site_key', env('HCAPTCHA_SITE_KEY')) }}"
                                         data-theme="light"
                                         data-size="normal">
                                    </div>
                                    @error('h-captcha-response')
                                        <div class="text-danger mt-2">
                                            <small>{{ $message }}</small>
                                        </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input @error('terms') is-invalid @enderror"
                                            type="checkbox" name="terms" id="terms" required>
                                        <label class="form-check-label" for="terms">
                                            Saya menyetujui <a href="javascript:void(0);">Aturan &amp; Ketentuan Layanan</a> yang berlaku.
                                        </label>
                                        @error('terms')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <button id="registerBtn" class="btn btn-primary d-grid w-100"
                                    style="background-color: #26355d; border-color: #26355d;" type="submit">
                                    <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                                    Daftar Sebagai Mitra
                                </button>
                            </form>

                            <p class="text-center">
                                <span class="text-black">Sudah punya akun?</span>
                                <a href="{{ route("login") }}" class="text-primary">
                                    <span>Masuk di sini</span>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="{{ asset("admin") }}/assets/vendor/libs/jquery/jquery.js"></script>
        <script src="{{ asset("admin") }}/assets/vendor/libs/popper/popper.js"></script>
        <script src="{{ asset("admin") }}/assets/vendor/js/bootstrap.js"></script>
        <script src="{{ asset("admin") }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
        <script src="{{ asset("admin") }}/assets/vendor/js/menu.js"></script>
        <script src="{{ asset("admin") }}/assets/js/main.js"></script>

        <script>
            $(document).ready(function () {
                // Form submit loading
                $('#formAuthentication').on('submit', function() {
                    const btn = $('#registerBtn');
                    btn.prop('disabled', true);
                    btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Mendaftar...');
                });

                // === Wilayah cascading ===
                $.ajax({
                    url: '{{ route("api.wilayah.provinces") }}',
                    method: 'GET',
                    success: function (res) {
                        if (res.success) {
                            res.data.forEach(function (p) {
                                $('#provinsi').append(`<option value="${p.name}" data-id="${p.id}">${p.name}</option>`);
                            });
                        }
                    }
                });

                $('#provinsi').on('change', function () {
                    let pId = $(this).find('option:selected').data('id');
                    $('#province_code').val(pId);
                    $('#kabupaten_kota').html('<option value="">Pilih Kabupaten/Kota</option>');
                    $('#kecamatan').html('<option value="">Pilih Kecamatan (Opsional)</option>');
                    $('#kelurahan').html('<option value="">Pilih Kelurahan/Desa (Opsional)</option>');
                    $('#regency_code,#district_code,#village_code').val('');
                    if (pId) {
                        $.ajax({
                            url: '{{ route("api.wilayah.regencies", "") }}/' + pId,
                            success: function (res) {
                                if (res.success) {
                                    res.data.forEach(function (r) {
                                        $('#kabupaten_kota').append(`<option value="${r.name}" data-id="${r.id}">${r.name}</option>`);
                                    });
                                }
                            }
                        });
                    }
                });

                $('#kabupaten_kota').on('change', function () {
                    let rId = $(this).find('option:selected').data('id');
                    $('#regency_code').val(rId);
                    $('#kecamatan').html('<option value="">Pilih Kecamatan (Opsional)</option>');
                    $('#kelurahan').html('<option value="">Pilih Kelurahan/Desa (Opsional)</option>');
                    $('#district_code,#village_code').val('');
                    if (rId) {
                        $.ajax({
                            url: '{{ route("api.wilayah.districts", "") }}/' + rId,
                            success: function (res) {
                                if (res.success) {
                                    res.data.forEach(function (d) {
                                        $('#kecamatan').append(`<option value="${d.name}" data-id="${d.id}">${d.name}</option>`);
                                    });
                                }
                            }
                        });
                    }
                });

                $('#kecamatan').on('change', function () {
                    let dId = $(this).find('option:selected').data('id');
                    $('#district_code').val(dId);
                    $('#kelurahan').html('<option value="">Pilih Kelurahan/Desa (Opsional)</option>');
                    $('#village_code').val('');
                    if (dId) {
                        $.ajax({
                            url: '{{ route("api.wilayah.villages", "") }}/' + dId,
                            success: function (res) {
                                if (res.success) {
                                    res.data.forEach(function (v) {
                                        $('#kelurahan').append(`<option value="${v.name}" data-id="${v.id}">${v.name}</option>`);
                                    });
                                }
                            }
                        });
                    }
                });

                $('#kelurahan').on('change', function () {
                    $('#village_code').val($(this).find('option:selected').data('id'));
                });


            });
        </script>
    </body>
</html>
