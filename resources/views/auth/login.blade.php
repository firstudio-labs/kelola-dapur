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
            content="width=device-width, initial-scale=1.0, maximum-scale=1.0"
        />
        <title>Login - Kelola Dapur</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
            rel="stylesheet"
        />

        <!-- Icons -->
        <link
            rel="stylesheet"
            href="{{ asset("admin") }}/assets/vendor/fonts/boxicons.css"
        />

        <!-- Core CSS -->
        <link
            rel="stylesheet"
            href="{{ asset("admin") }}/assets/vendor/css/core.css"
        />
        <link
            rel="stylesheet"
            href="{{ asset("admin") }}/assets/vendor/css/theme-default.css"
        />
        <link
            rel="stylesheet"
            href="{{ asset("admin") }}/assets/css/demo.css"
        />

        <!-- Vendors CSS -->
        <link
            rel="stylesheet"
            href="{{ asset("admin") }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css"
        />

        <!-- Page CSS -->
        <link
            rel="stylesheet"
            href="{{ asset("admin") }}/assets/vendor/css/pages/page-auth.css"
        />

        <!-- Helpers -->
        <script src="{{ asset("admin") }}/assets/vendor/js/helpers.js"></script>
        <script src="{{ asset("admin") }}/assets/js/config.js"></script>

        <!-- hCaptcha -->
        <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
    </head>
    <body style="background-color: #3758F9">
        <div class="container-xxl">
            <div
                class="authentication-wrapper authentication-basic container-p-y"
            >
                <div class="authentication-inner">
                    <!-- Card -->
                    <div class="card">
                        <div class="card-body">
                            <!-- Logo -->
                            <div class="app-brand justify-content-center mb-4">
                                <a href="/welcome" class="app-brand-link gap-2">
                                    <span class="app-brand-logo demo">
                                        <img
                                            src="{{ asset("logo_kelola_dapur_black.png") }}"
                                            alt="Logo"
                                            style="height: 60px"
                                        />
                                    </span>
                                    {{-- <span class="demo fw-bolder fs-2">
                                        Kelola Dapur
                                    </span> --}}
                                </a>
                            </div>

                            <!-- Alert Section -->
                            @if (session("success"))
                                <div
                                    class="alert alert-success alert-dismissible fade show"
                                    role="alert"
                                >
                                    {{ session("success") }}
                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="alert"
                                    ></button>
                                </div>
                            @endif

                            @if (session("error"))
                                <div
                                    class="alert alert-danger alert-dismissible fade show"
                                    role="alert"
                                >
                                    {{ session("error") }}
                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="alert"
                                    ></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div
                                    class="alert alert-danger alert-dismissible fade show"
                                    role="alert"
                                >
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="alert"
                                    ></button>
                                </div>
                            @endif

                            <h4 class="mb-2">Selamat Datang</h4>
                            <p class="mb-4">
                                Silakan login untuk menikmati layanan kami
                            </p>

                            <form
                                id="formAuthentication"
                                class="mb-3"
                                action="{{ route("login") }}"
                                method="POST"
                            >
                                @csrf
                                <!-- Username/Email -->
                                <div class="mb-3">
                                    <label for="login" class="form-label">
                                        Username atau Email
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control @error('login') is-invalid @enderror"
                                        id="login"
                                        name="login"
                                        placeholder="Masukkan username atau email"
                                        value="{{ old("login") }}"
                                        autofocus
                                    />
                                    @error('login')
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
                                            class="form-control @error('password') is-invalid @enderror"
                                            name="password"
                                            placeholder="********"
                                            aria-describedby="password"
                                        />
                                        <span
                                            class="input-group-text cursor-pointer"
                                        >
                                            <i class="bx bx-hide"></i>
                                        </span>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- hCaptcha -->
                                <div class="mb-3">
                                    <div class="h-captcha" data-sitekey="{{ config('services.hcaptcha.site_key', env('HCAPTCHA_SITE_KEY')) }}"></div>
                                    @error('h-captcha-response')
                                        <div class="text-danger mt-1 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Remember Me -->
                                {{-- <div class="mb-3">
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="remember"
                                            name="remember"
                                            {{ old('remember') ? 'checked' : '' }}
                                        />
                                        <label class="form-check-label" for="remember">
                                            Ingat saya
                                        </label>
                                    </div>
                                </div> --}}

                                <!-- Submit -->
                                <div class="mb-3">
                                    <button
                                        class="btn btn-primary d-grid w-100"
                                        type="submit"
                                        id="loginBtn"
                                    >
                                        <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                                        Sign in
                                    </button>
                                </div>
                            </form>

                            <p class="text-center">
                                <span>Belum punya akun?</span>
                                <a href="{{ route("register") }}">
                                    <span>Daftar di sini</span>
                                </a>
                            </p>
                        </div>
                    </div>
                    <!-- /Card -->
                </div>
            </div>
        </div>

        <!-- Core JS -->
        <script src="{{ asset("admin") }}/assets/vendor/libs/jquery/jquery.js"></script>
        <script src="{{ asset("admin") }}/assets/vendor/libs/popper/popper.js"></script>
        <script src="{{ asset("admin") }}/assets/vendor/js/bootstrap.js"></script>
        <script src="{{ asset("admin") }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
        <script src="{{ asset("admin") }}/assets/vendor/js/menu.js"></script>
        <script src="{{ asset("admin") }}/assets/js/main.js"></script>

        <script>
            // Handle form submission
            document.getElementById('formAuthentication').addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('loginBtn');
                const spinner = submitBtn.querySelector('.spinner-border');
                
                // Show loading state
                submitBtn.disabled = true;
                spinner.classList.remove('d-none');
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Logging in...';
            });

            // Reset button state on page load (in case of validation errors)
            window.addEventListener('load', function() {
                const submitBtn = document.getElementById('loginBtn');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Sign in';
            });
        </script>
    </body>
</html>