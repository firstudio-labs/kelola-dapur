<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('admin') }}/assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Registrasi - Kelola Dapur</title>

    <link rel="icon" type="image/x-icon" href="{{asset('env')}}/logoo.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendor/css/theme-default.css"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/demo.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendor/css/pages/page-auth.css" />

    <script src="{{ asset('admin') }}/assets/vendor/js/helpers.js"></script>
    <script src="{{ asset('admin') }}/assets/js/config.js"></script>
</head>

<body style="background-color: #26355d">
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="/" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">
                                    <img src="{{asset('env')}}/logoo.png" alt="Logo" style="height: 60px;">
                                </span>
                                <span class="app-brand-text demo text-body fw-bolder">Dapur</span>
                            </a>
                        </div>
                        <!-- /Logo -->

                        <h4 class="mb-2">Bergabung dengan Kelola Dapur 🚀</h4>
                        <p class="mb-4">Daftar sekarang untuk mengelola MBG dapur anda</p>

                        <form id="formAuthentication" class="mb-3" action="{{ route('register') }}" method="POST">
                            @csrf

                            <!-- Nama Lengkap -->
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                    id="nama" name="nama" placeholder="Masukkan nama lengkap"
                                    value="{{ old('nama') }}" required autofocus />
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    id="username" name="username" placeholder="Pilih username unik"
                                    value="{{ old('username') }}" required />
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" placeholder="Masukkan email"
                                    value="{{ old('email') }}" required />
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Role -->
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select id="role" name="role"
                                    class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="">Pilih role Anda</option>
                                    <option value="kepala_dapur" {{ old('role') == 'kepala_dapur' ? 'selected' : '' }}>
                                        👨‍🍳 Kepala Dapur</option>
                                    <option value="admin_gudang" {{ old('role') == 'admin_gudang' ? 'selected' : '' }}>
                                        📦 Admin Gudang</option>
                                    <option value="ahli_gizi" {{ old('role') == 'ahli_gizi' ? 'selected' : '' }}>
                                        🥗 Ahli Gizi</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Dapur -->
                            <div class="mb-3">
                                <label for="id_dapur" class="form-label">Dapur</label>
                                <select id="id_dapur" name="id_dapur"
                                    class="form-select @error('id_dapur') is-invalid @enderror" required>
                                    <option value="">Pilih dapur tempat kerja</option>
                                    @foreach($dapurList as $dapur)
                                        <option value="{{ $dapur->id_dapur }}" {{ old('id_dapur') == $dapur->id_dapur ? 'selected' : '' }}>
                                            {{ $dapur->nama_dapur }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_dapur')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        name="password" placeholder="••••••••••••" required />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Konfirmasi Password -->
                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password_confirmation" class="form-control"
                                        name="password_confirmation" placeholder="••••••••••••" required />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>

                            <button class="btn btn-primary d-grid w-100"
                                style="background-color: #26355d; border-color: #26355d;">Daftar Sekarang</button>
                        </form>

                        <p class="text-center">
                            <span class="text-black">Sudah punya akun?</span>
                            <a href="{{ route('login') }}" class="text-primary">
                                <span>Masuk di sini</span>
                            </a>
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('admin') }}/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/libs/popper/popper.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/js/bootstrap.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/js/menu.js"></script>
    <script src="{{ asset('admin') }}/assets/js/main.js"></script>
</body>
</html>
