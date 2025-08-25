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
    </head>
    <body style="background-color: #26355d">
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
                                <a href="/" class="app-brand-link gap-2">
                                    <span class="app-brand-logo demo">
                                        <img
                                            src="{{ asset("logo.png") }}"
                                            alt="Logo"
                                            style="height: 60px"
                                        />
                                    </span>
                                    <span class="demo fw-bolder fs-2">
                                        Kelola Dapur
                                    </span>
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
                                Silakan login untuk mengakses dashboard
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
                                        class="form-control"
                                        id="login"
                                        name="login"
                                        placeholder="Masukkan username atau email"
                                        value="{{ old("login") }}"
                                        autofocus
                                    />
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
                                            class="form-control"
                                            name="password"
                                            placeholder="••••••••"
                                            aria-describedby="password"
                                        />
                                        <span
                                            class="input-group-text cursor-pointer"
                                        >
                                            <i class="bx bx-hide"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div class="mb-3">
                                    <button
                                        class="btn btn-primary d-grid w-100"
                                        type="submit"
                                    >
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
    </body>
</html>
