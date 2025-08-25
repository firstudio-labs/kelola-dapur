@extends("template_kepala_dapur.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <nav class="d-flex align-items-center mb-2">
                            <a
                                href="{{ route("dashboard") }}"
                                class="text-muted me-2"
                            >
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Edit Profil</span>
                        </nav>
                        <h4 class="mb-1">Edit Profil Kepala Dapur</h4>
                        <p class="mb-0 text-muted">
                            Perbarui informasi profil Anda
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="card mb-4">
            <div class="card-body">
                @if (session("success"))
                    <div
                        class="alert alert-success alert-dismissible"
                        role="alert"
                    >
                        {{ session("success") }}
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close"
                        ></button>
                    </div>
                @endif

                @if (session("error"))
                    <div
                        class="alert alert-danger alert-dismissible"
                        role="alert"
                    >
                        {{ session("error") }}
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close"
                        ></button>
                    </div>
                @endif

                <form
                    action="{{ route("kepala-dapur.update-profil") }}"
                    method="POST"
                    class="row g-4"
                >
                    @csrf
                    @method("PUT")

                    <!-- Profile Information -->
                    <div class="col-12">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-user me-2 text-primary"></i>
                            Informasi Profil
                        </h5>
                        <div class="row g-4 mt-2">
                            <!-- Nama -->
                            <div class="col-md-6">
                                <label for="nama" class="form-label">
                                    Nama Lengkap
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    {{--
                                        <span class="input-group-text">
                                        <i class="bx bx-user"></i>
                                        </span>
                                    --}}
                                    <input
                                        type="text"
                                        name="nama"
                                        id="nama"
                                        required
                                        class="form-control @error("nama") is-invalid @enderror"
                                        placeholder="Masukkan nama lengkap"
                                        value="{{ old("nama", $current_user->nama) }}"
                                    />
                                </div>
                                @error("nama")
                                    <div class="form-text text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Username -->
                            <div class="col-md-6">
                                <label for="username" class="form-label">
                                    Username
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    {{--
                                        <span class="input-group-text">
                                        <i class="bx bx-at"></i>
                                        </span>
                                    --}}
                                    <input
                                        type="text"
                                        name="username"
                                        id="username"
                                        required
                                        class="form-control @error("username") is-invalid @enderror"
                                        placeholder="Masukkan username"
                                        value="{{ old("username", $current_user->username) }}"
                                    />
                                </div>
                                @error("username")
                                    <div class="form-text text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    Email
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    {{--
                                        <span class="input-group-text">
                                        <i class="bx bx-envelope"></i>
                                        </span>
                                    --}}
                                    <input
                                        type="email"
                                        name="email"
                                        id="email"
                                        required
                                        class="form-control @error("email") is-invalid @enderror"
                                        placeholder="Masukkan email"
                                        value="{{ old("email", $current_user->email) }}"
                                    />
                                </div>
                                @error("email")
                                    <div class="form-text text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Role (Read-only) -->
                            <div class="col-md-6">
                                <label for="role_display" class="form-label">
                                    Role
                                </label>
                                <div class="input-group">
                                    {{--
                                        <span class="input-group-text">
                                        <i class="bx bx-shield"></i>
                                        </span>
                                    --}}
                                    <input
                                        type="text"
                                        id="role_display"
                                        class="form-control"
                                        value="Kepala Dapur"
                                        readonly
                                    />
                                </div>
                            </div>

                            <!-- Dapur (Read-only) -->
                            <div class="col-md-6">
                                <label for="dapur_display" class="form-label">
                                    Dapur
                                </label>
                                <div class="input-group">
                                    {{--
                                        <span class="input-group-text">
                                        <i class="bx bx-store"></i>
                                        </span>
                                    --}}
                                    <input
                                        type="text"
                                        id="dapur_display"
                                        class="form-control"
                                        value="{{ $dapur->nama_dapur }}"
                                        readonly
                                    />
                                </div>
                            </div>

                            <!-- Status (Read-only) -->
                            <div class="col-md-6">
                                <label for="status_display" class="form-label">
                                    Status
                                </label>
                                <div class="input-group">
                                    {{--
                                        <span class="input-group-text">
                                        <i
                                        class="bx bx-check-circle text-success"
                                        ></i>
                                        </span>
                                    --}}
                                    <input
                                        type="text"
                                        id="status_display"
                                        class="form-control"
                                        value="{{ $current_user->is_active ? "Aktif" : "Tidak Aktif" }}"
                                        readonly
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" />

                    <!-- Password Change Section -->
                    <div class="col-12">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-lock me-2 text-warning"></i>
                            Ubah Password
                        </h5>
                        <p class="text-muted small mt-2">
                            Kosongkan jika tidak ingin mengubah password
                        </p>

                        <div class="row g-4 mt-2">
                            <!-- Password Baru -->
                            <div class="col-md-6">
                                <label for="password" class="form-label">
                                    Password Baru
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bx bx-lock"></i>
                                    </span>
                                    <input
                                        type="password"
                                        name="password"
                                        id="password"
                                        class="form-control @error("password") is-invalid @enderror"
                                        placeholder="Masukkan password baru"
                                    />
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary"
                                        id="togglePassword1"
                                    >
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                                @error("password")
                                    <div class="form-text text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <div class="form-text">Minimal 8 karakter</div>
                            </div>

                            <!-- Konfirmasi Password -->
                            <div class="col-md-6">
                                <label
                                    for="password_confirmation"
                                    class="form-label"
                                >
                                    Konfirmasi Password Baru
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bx bx-lock"></i>
                                    </span>
                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        id="password_confirmation"
                                        class="form-control @error("password_confirmation") is-invalid @enderror"
                                        placeholder="Ulangi password baru"
                                    />
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary"
                                        id="togglePassword2"
                                    >
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                                @error("password_confirmation")
                                    <div class="form-text text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="col-12 mt-4">
                        <div
                            class="d-flex justify-content-between align-items-center"
                        >
                            <a
                                href="{{ route("dashboard") }}"
                                class="btn btn-outline-secondary"
                            >
                                <i class="bx bx-arrow-back me-2"></i>
                                Kembali ke Dashboard
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-2"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Alert -->
        <div class="alert alert-info alert-dismissible" role="alert">
            <h6 class="alert-heading mb-2">
                <i class="bx bx-info-circle me-2"></i>
                Informasi Penting
            </h6>
            <ul class="mb-0">
                <li>Username dan email harus unik dalam sistem.</li>
                <li>Pastikan email yang digunakan masih aktif.</li>
                <li>Password baru minimal 8 karakter untuk keamanan.</li>
                <li>
                    Perubahan profil akan langsung berlaku setelah disimpan.
                </li>
            </ul>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle password visibility
            function togglePasswordVisibility(inputId, toggleButtonId) {
                const passwordInput = document.getElementById(inputId);
                const toggleButton = document.getElementById(toggleButtonId);
                const icon = toggleButton.querySelector('i');

                toggleButton.addEventListener('click', function () {
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.remove('bx-hide');
                        icon.classList.add('bx-show');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.remove('bx-show');
                        icon.classList.add('bx-hide');
                    }
                });
            }

            // Apply to both password fields
            togglePasswordVisibility('password', 'togglePassword1');
            togglePasswordVisibility(
                'password_confirmation',
                'togglePassword2',
            );

            // Avatar upload functionality
            const fileInput = document.getElementById('upload');
            const resetButton = document.querySelector('.account-image-reset');
            const avatarImg = document.getElementById('uploadedAvatar');

            if (fileInput) {
                fileInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            avatarImg.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            if (resetButton) {
                resetButton.addEventListener('click', function () {
                    avatarImg.src =
                        '{{ asset("admin/assets/img/avatars/1.png") }}';
                    fileInput.value = '';
                });
            }

            // Form validation
            const form = document.querySelector('form');
            const passwordInput = document.getElementById('password');
            const passwordConfirmInput = document.getElementById(
                'password_confirmation',
            );

            if (form) {
                form.addEventListener('submit', function (e) {
                    // Check password confirmation
                    if (
                        passwordInput.value &&
                        passwordInput.value !== passwordConfirmInput.value
                    ) {
                        e.preventDefault();
                        alert('Password dan konfirmasi password tidak sama!');
                        passwordConfirmInput.focus();
                        return false;
                    }

                    // Check password length
                    if (passwordInput.value && passwordInput.value.length < 8) {
                        e.preventDefault();
                        alert('Password minimal 8 karakter!');
                        passwordInput.focus();
                        return false;
                    }
                });
            }
        });
    </script>

    <style>
        .account-file-input {
            display: none;
        }

        .avatar img {
            object-fit: cover;
        }

        .input-group-text {
            background-color: var(--bs-gray-100);
            border-color: var(--bs-border-color);
        }

        .card {
            box-shadow: 0 0.25rem 1rem rgba(161, 172, 184, 0.15);
            border: 0;
        }

        .btn-primary {
            background: linear-gradient(45deg, #696cff, #5a67ff);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #5a67ff, #4b63ff);
            transform: translateY(-1px);
        }

        .alert {
            border: 0;
            border-radius: 0.5rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--bs-gray-700);
        }
    </style>
@endsection
