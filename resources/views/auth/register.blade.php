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
        <title>Registrasi - Kelola Dapur</title>

        <link
            rel="icon"
            type="image/x-icon"
            href="{{ asset("env") }}/logoo.png"
        />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
            rel="stylesheet"
        />
        <link
            rel="stylesheet"
            href="{{ asset("admin") }}/assets/vendor/fonts/boxicons.css"
        />
        <link
            rel="stylesheet"
            href="{{ asset("admin") }}/assets/vendor/css/core.css"
            class="template-customizer-core-css"
        />
        <link
            rel="stylesheet"
            href="{{ asset("admin") }}/assets/vendor/css/theme-default.css"
            class="template-customizer-theme-css"
        />
        <link
            rel="stylesheet"
            href="{{ asset("admin") }}/assets/css/demo.css"
        />
        <link
            rel="stylesheet"
            href="{{ asset("admin") }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css"
        />
        <link
            rel="stylesheet"
            href="{{ asset("admin") }}/assets/vendor/css/pages/page-auth.css"
        />

        <script src="{{ asset("admin") }}/assets/vendor/js/helpers.js"></script>
        <script src="{{ asset("admin") }}/assets/js/config.js"></script>
    </head>

    <body style="background-color: #26355d">
        <div class="container-xxl">
            <div
                class="authentication-wrapper authentication-basic container-p-y"
            >
                <div class="authentication-inner">
                    <div class="card">
                        <div class="card-body">
                            <!-- Logo -->
                            <div class="app-brand justify-content-center">
                                <a href="/welcome" class="app-brand-link gap-2">
                                    <span class="app-brand-logo demo">
                                        <img
                                            src="{{ asset("logo.png") }}"
                                            alt="Logo"
                                            style="height: 60px"
                                        />
                                    </span>
                                    <span class="demo text-body fw-bolder fs-2">
                                        Kelola Dapur
                                    </span>
                                </a>
                            </div>
                            <!-- /Logo -->

                            <h4 class="mb-2">
                                Bergabung dengan Kelola Dapur 🚀
                            </h4>
                            <p class="mb-4">
                                Daftar sekarang untuk mengelola MBG dapur anda
                                sebagai Kepala Dapur
                            </p>

                            <form
                                id="formAuthentication"
                                class="mb-3"
                                action="{{ route("register") }}"
                                method="POST"
                            >
                                @csrf

                                <!-- Hidden codes -->
                                <input
                                    type="hidden"
                                    name="province_code"
                                    id="province_code"
                                />
                                <input
                                    type="hidden"
                                    name="regency_code"
                                    id="regency_code"
                                />
                                <input
                                    type="hidden"
                                    name="district_code"
                                    id="district_code"
                                />
                                <input
                                    type="hidden"
                                    name="village_code"
                                    id="village_code"
                                />

                                <!-- Nama Lengkap -->
                                <div class="mb-3">
                                    <label for="nama" class="form-label">
                                        Nama Lengkap
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control @error("nama") is-invalid @enderror"
                                        id="nama"
                                        name="nama"
                                        placeholder="Masukkan nama lengkap"
                                        value="{{ old("nama") }}"
                                        required
                                        autofocus
                                    />
                                    @error("nama")
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Username -->
                                <div class="mb-3">
                                    <label for="username" class="form-label">
                                        Username
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control @error("username") is-invalid @enderror"
                                        id="username"
                                        name="username"
                                        placeholder="Pilih username unik"
                                        value="{{ old("username") }}"
                                        required
                                    />
                                    @error("username")
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        Email
                                    </label>
                                    <input
                                        type="email"
                                        class="form-control @error("email") is-invalid @enderror"
                                        id="email"
                                        name="email"
                                        placeholder="Masukkan email"
                                        value="{{ old("email") }}"
                                        required
                                    />
                                    @error("email")
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Nama Dapur -->
                                <div class="mb-3">
                                    <label for="nama_dapur" class="form-label">
                                        Nama Dapur
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control @error("nama_dapur") is-invalid @enderror"
                                        id="nama_dapur"
                                        name="nama_dapur"
                                        placeholder="Masukkan nama dapur"
                                        value="{{ old("nama_dapur") }}"
                                        required
                                    />
                                    @error("nama_dapur")
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Provinsi -->
                                <div class="mb-3">
                                    <label for="provinsi" class="form-label">
                                        Provinsi
                                    </label>
                                    <select
                                        id="provinsi"
                                        name="provinsi"
                                        class="form-select @error("provinsi") is-invalid @enderror"
                                        required
                                    >
                                        <option value="">Pilih Provinsi</option>
                                    </select>
                                    @error("provinsi")
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Kabupaten/Kota -->
                                <div class="mb-3">
                                    <label
                                        for="kabupaten_kota"
                                        class="form-label"
                                    >
                                        Kabupaten/Kota
                                    </label>
                                    <select
                                        id="kabupaten_kota"
                                        name="kabupaten_kota"
                                        class="form-select @error("kabupaten_kota") is-invalid @enderror"
                                        required
                                    >
                                        <option value="">
                                            Pilih Kabupaten/Kota
                                        </option>
                                    </select>
                                    @error("kabupaten_kota")
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Kecamatan -->
                                <div class="mb-3">
                                    <label for="kecamatan" class="form-label">
                                        Kecamatan
                                    </label>
                                    <select
                                        id="kecamatan"
                                        name="kecamatan"
                                        class="form-select @error("kecamatan") is-invalid @enderror"
                                    >
                                        <option value="">
                                            Pilih Kecamatan (Opsional)
                                        </option>
                                    </select>
                                    @error("kecamatan")
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Kelurahan/Desa -->
                                <div class="mb-3">
                                    <label for="kelurahan" class="form-label">
                                        Kelurahan/Desa
                                    </label>
                                    <select
                                        id="kelurahan"
                                        name="kelurahan"
                                        class="form-select @error("kelurahan") is-invalid @enderror"
                                    >
                                        <option value="">
                                            Pilih Kelurahan/Desa (Opsional)
                                        </option>
                                    </select>
                                    @error("kelurahan")
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Alamat -->
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">
                                        Alamat
                                    </label>
                                    <textarea
                                        class="form-control @error("alamat") is-invalid @enderror"
                                        id="alamat"
                                        name="alamat"
                                        placeholder="Masukkan alamat lengkap"
                                        required
                                    >
{{ old("alamat") }}</textarea
                                    >
                                    @error("alamat")
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Telepon -->
                                <div class="mb-3">
                                    <label for="telepon" class="form-label">
                                        Telepon
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control @error("telepon") is-invalid @enderror"
                                        id="telepon"
                                        name="telepon"
                                        placeholder="Masukkan nomor telepon"
                                        value="{{ old("telepon") }}"
                                        required
                                    />
                                    @error("telepon")
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="mb-3 form-password-toggle">
                                    <label class="form-label" for="password">
                                        Password
                                    </label>
                                    <div class="input-group input-group-merge">
                                        <input
                                            type="password"
                                            id="password"
                                            class="form-control @error("password") is-invalid @enderror"
                                            name="password"
                                            placeholder="••••••••••••"
                                            required
                                        />
                                        <span
                                            class="input-group-text cursor-pointer"
                                        >
                                            <i class="bx bx-hide"></i>
                                        </span>
                                        @error("password")
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Konfirmasi Password -->
                                <div class="mb-3 form-password-toggle">
                                    <label
                                        class="form-label"
                                        for="password_confirmation"
                                    >
                                        Konfirmasi Password
                                    </label>
                                    <div class="input-group input-group-merge">
                                        <input
                                            type="password"
                                            id="password_confirmation"
                                            class="form-control"
                                            name="password_confirmation"
                                            placeholder="••••••••••••"
                                            required
                                        />
                                        <span
                                            class="input-group-text cursor-pointer"
                                        >
                                            <i class="bx bx-hide"></i>
                                        </span>
                                    </div>
                                </div>

                                <button
                                    class="btn btn-primary d-grid w-100"
                                    style="
                                        background-color: #26355d;
                                        border-color: #26355d;
                                    "
                                >
                                    Daftar Sekarang
                                </button>
                            </form>

                            <p class="text-center">
                                <span class="text-black">
                                    Sudah punya akun?
                                </span>
                                <a
                                    href="{{ route("login") }}"
                                    class="text-primary"
                                >
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
                // Fetch provinces
                $.ajax({
                    url: '{{ route("api.wilayah.provinces") }}',
                    method: 'GET',
                    success: function (response) {
                        if (response.success) {
                            let provinces = response.data;
                            let provinceSelect = $('#provinsi');
                            provinces.forEach(function (province) {
                                provinceSelect.append(
                                    `<option value="${province.name}" data-id="${province.id}">${province.name}</option>`,
                                );
                            });
                        }
                    },
                    error: function () {
                        alert('Gagal memuat data provinsi');
                    },
                });

                // Fetch regencies when province is selected
                $('#provinsi').on('change', function () {
                    let provinceId = $(this).find('option:selected').data('id');
                    let provinceName = $(this).val();
                    $('#province_code').val(provinceId);
                    let regencySelect = $('#kabupaten_kota');
                    let districtSelect = $('#kecamatan');
                    let villageSelect = $('#kelurahan');
                    regencySelect.html(
                        '<option value="">Pilih Kabupaten/Kota</option>',
                    );
                    districtSelect.html(
                        '<option value="">Pilih Kecamatan (Opsional)</option>',
                    );
                    villageSelect.html(
                        '<option value="">Pilih Kelurahan/Desa (Opsional)</option>',
                    );
                    $('#regency_code').val('');
                    $('#district_code').val('');
                    $('#village_code').val('');

                    if (provinceId) {
                        $.ajax({
                            url:
                                '{{ route("api.wilayah.regencies", "") }}/' +
                                provinceId,
                            method: 'GET',
                            success: function (response) {
                                if (response.success) {
                                    let regencies = response.data;
                                    regencies.forEach(function (regency) {
                                        regencySelect.append(
                                            `<option value="${regency.name}" data-id="${regency.id}">${regency.name}</option>`,
                                        );
                                    });
                                }
                            },
                            error: function () {
                                alert('Gagal memuat data kabupaten/kota');
                            },
                        });
                    }
                });

                // Fetch districts when regency is selected
                $('#kabupaten_kota').on('change', function () {
                    let regencyId = $(this).find('option:selected').data('id');
                    let regencyName = $(this).val();
                    $('#regency_code').val(regencyId);
                    let districtSelect = $('#kecamatan');
                    let villageSelect = $('#kelurahan');
                    districtSelect.html(
                        '<option value="">Pilih Kecamatan (Opsional)</option>',
                    );
                    villageSelect.html(
                        '<option value="">Pilih Kelurahan/Desa (Opsional)</option>',
                    );
                    $('#district_code').val('');
                    $('#village_code').val('');

                    if (regencyId) {
                        $.ajax({
                            url:
                                '{{ route("api.wilayah.districts", "") }}/' +
                                regencyId,
                            method: 'GET',
                            success: function (response) {
                                if (response.success) {
                                    let districts = response.data;
                                    districts.forEach(function (district) {
                                        districtSelect.append(
                                            `<option value="${district.name}" data-id="${district.id}">${district.name}</option>`,
                                        );
                                    });
                                }
                            },
                            error: function () {
                                alert('Gagal memuat data kecamatan');
                            },
                        });
                    }
                });

                // Fetch villages when district is selected
                $('#kecamatan').on('change', function () {
                    let districtId = $(this).find('option:selected').data('id');
                    let districtName = $(this).val();
                    $('#district_code').val(districtId);
                    let villageSelect = $('#kelurahan');
                    villageSelect.html(
                        '<option value="">Pilih Kelurahan/Desa (Opsional)</option>',
                    );
                    $('#village_code').val('');

                    if (districtId) {
                        $.ajax({
                            url:
                                '{{ route("api.wilayah.villages", "") }}/' +
                                districtId,
                            method: 'GET',
                            success: function (response) {
                                if (response.success) {
                                    let villages = response.data;
                                    villages.forEach(function (village) {
                                        villageSelect.append(
                                            `<option value="${village.name}" data-id="${village.id}">${village.name}</option>`,
                                        );
                                    });
                                }
                            },
                            error: function () {
                                alert('Gagal memuat data kelurahan/desa');
                            },
                        });
                    }
                });

                // Set village code when village is selected
                $('#kelurahan').on('change', function () {
                    let villageId = $(this).find('option:selected').data('id');
                    $('#village_code').val(villageId);
                });
            });
        </script>
    </body>
</html>
