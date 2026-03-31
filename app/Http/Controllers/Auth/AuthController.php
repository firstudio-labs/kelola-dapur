<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Dapur;
use App\Models\KepalaDapur;
use App\Models\Mitra;
use App\Models\MitraDapur;
use App\Models\PenerimaMbg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

use function PHPUnit\Framework\returnSelf;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }
        return view('auth.login');
    }

    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        return view('auth.register');
    }

    public function showRegistrationFormPenerima()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        $dapurList = Dapur::where('status', 'active')->orderBy('nama_dapur')->get();
        return view('auth.register_penerima', compact('dapurList'));
    }

    public function showRegistrationFormMitra()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        return view('auth.register_mitra');
    }

    public function registerMitra(Request $request)
    {
        $key = 'register_mitra.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->back()
                ->with('error', "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.")
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $validator = Validator::make($request->all(), [
            'nama'                  => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'username'              => 'required|string|max:255|unique:users,username|regex:/^[a-zA-Z0-9_]+$/',
            'email'                 => 'required|email|max:255|unique:users,email',
            'password'              => [
                'required', 'string', 'min:8', 'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ],
            'nik_pemilik'           => 'required|string|max:20|regex:/^[0-9]+$/',
            'nama_pemilik'          => 'required|string|max:255',
            'province_code'         => 'required|string',
            'province_name'         => 'required|string|max:100',
            'regency_code'          => 'required|string',
            'regency_name'          => 'required|string|max:100',
            'district_code'         => 'nullable|string',
            'district_name'         => 'nullable|string|max:100',
            'village_code'          => 'nullable|string',
            'village_name'          => 'nullable|string|max:100',
            'alamat_detail'         => 'required|string|max:500',
            'h-captcha-response'    => 'required',
            'terms'                 => 'required|accepted',
        ], [
            'nama.required'             => 'Nama lengkap harus diisi',
            'nama.regex'                => 'Nama hanya boleh mengandung huruf dan spasi',
            'username.required'         => 'Username harus diisi',
            'username.unique'           => 'Username sudah digunakan',
            'email.required'            => 'Email harus diisi',
            'email.unique'              => 'Email sudah terdaftar',
            'password.required'         => 'Password harus diisi',
            'password.min'              => 'Password minimal 8 karakter',
            'password.regex'            => 'Password harus mengandung minimal 1 huruf kecil, 1 huruf besar, dan 1 angka',
            'password.confirmed'        => 'Konfirmasi password tidak cocok',
            'nik_pemilik.required'      => 'NIK pemilik harus diisi',
            'nik_pemilik.regex'         => 'NIK hanya boleh mengandung angka',
            'nama_pemilik.required'     => 'Nama pemilik harus diisi',
            'province_name.required'    => 'Provinsi harus dipilih',
            'regency_name.required'     => 'Kabupaten/Kota harus dipilih',
            'alamat_detail.required'    => 'Detail alamat harus diisi',
            'h-captcha-response.required' => 'Silakan verifikasi captcha terlebih dahulu',
            'terms.required'            => 'Anda harus menyetujui syarat dan ketentuan',
            'terms.accepted'            => 'Anda harus menyetujui syarat dan ketentuan',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        if (!$this->verifyCaptcha($request->input('h-captcha-response'))) {
            return redirect()->back()
                ->withErrors(['h-captcha-response' => 'Verifikasi captcha gagal. Silakan coba lagi.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        RateLimiter::hit($key, 3600);
        DB::beginTransaction();

        try {
            $user = User::create([
                'nama'      => trim($request->nama),
                'username'  => strtolower(trim($request->username)),
                'email'     => strtolower(trim($request->email)),
                'password'  => Hash::make($request->password),
                'is_active' => true,
            ]);

            // Mitra tidak terikat ke satu dapur di user_roles; id_dapur null
            $userRole = UserRole::create([
                'id_user'   => $user->id_user,
                'role_type' => 'mitra',
                'id_dapur'  => null,
            ]);

            $mitra = Mitra::create([
                'id_user_role'  => $userRole->id_user_role,
                'nik_pemilik'   => $request->nik_pemilik,
                'nama_pemilik'  => trim($request->nama_pemilik),
                'province_code' => $request->province_code,
                'province_name' => $request->province_name,
                'regency_code'  => $request->regency_code,
                'regency_name'  => $request->regency_name,
                'district_code' => $request->district_code,
                'district_name' => $request->district_name,
                'village_code'  => $request->village_code,
                'village_name'  => $request->village_name,
                'alamat_detail' => trim($request->alamat_detail),
            ]);

            DB::commit();
            RateLimiter::clear($key);

            Log::info('Mitra registered', ['user_id' => $user->id_user]);

            Auth::login($user);
            $user->load(['userRole']);

            return redirect()->route('mitra.dashboard')
                ->with('success', 'Registrasi berhasil! Selamat datang di Kelola Dapur sebagai Mitra.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Mitra registration failed', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.')
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    public function registerPenerima(Request $request)
    {
        $key = 'register_penerima.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->back()
                ->with('error', "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.")
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $validator = Validator::make($request->all(), [
            'nama'                 => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'username'             => 'required|string|max:255|unique:users,username|regex:/^[a-zA-Z0-9_]+$/',
            'email'                => 'required|email|max:255|unique:users,email',
            'password'             => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
            ],
            'id_dapur'             => 'required|exists:dapur,id_dapur',
            'id_type'              => 'required|in:nik,nisn,no_registrasi',
            'id_number'            => 'required|string|max:50',
            'penanggung_jawab'     => 'required|string|max:255',
            'province_code'        => 'required|string',
            'province_name'        => 'required|string|max:100',
            'regency_code'         => 'required|string',
            'regency_name'         => 'required|string|max:100',
            'district_code'        => 'nullable|string',
            'district_name'        => 'nullable|string|max:100',
            'village_code'         => 'nullable|string',
            'village_name'         => 'nullable|string|max:100',
            'alamat_detail'        => 'required|string|max:500',
            'latitude'             => 'nullable|numeric|between:-90,90',
            'longitude'            => 'nullable|numeric|between:-180,180',
            'link_gmaps'           => 'nullable|url|max:500',
            'foto_lokasi'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'h-captcha-response'   => 'required',
            'terms'                => 'required|accepted',
        ], [
            'nama.required'             => 'Nama lengkap harus diisi',
            'nama.regex'                => 'Nama hanya boleh mengandung huruf dan spasi',
            'username.required'         => 'Username harus diisi',
            'username.unique'           => 'Username sudah digunakan',
            'email.required'            => 'Email harus diisi',
            'email.unique'              => 'Email sudah terdaftar',
            'password.required'         => 'Password harus diisi',
            'password.min'              => 'Password minimal 8 karakter',
            'password.regex'            => 'Password harus mengandung minimal 1 huruf kecil, 1 huruf besar, dan 1 angka',
            'password.confirmed'        => 'Konfirmasi password tidak cocok',
            'id_dapur.required'         => 'Silakan pilih Dapur SPPG tujuan',
            'id_dapur.exists'           => 'Dapur SPPG tidak ditemukan',
            'id_number.required'        => 'Nomor identitas harus diisi',
            'penanggung_jawab.required' => 'Penanggung jawab harus diisi',
            'province_name.required'    => 'Provinsi harus dipilih',
            'regency_name.required'     => 'Kabupaten/Kota harus dipilih',
            'alamat_detail.required'    => 'Detail alamat harus diisi',
            'h-captcha-response.required' => 'Silakan verifikasi captcha terlebih dahulu',
            'terms.required'            => 'Anda harus menyetujui syarat dan ketentuan',
            'terms.accepted'            => 'Anda harus menyetujui syarat dan ketentuan',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->except('password', 'password_confirmation'));
        }

        if (!$this->verifyCaptcha($request->input('h-captcha-response'))) {
            return redirect()->back()
                ->withErrors(['h-captcha-response' => 'Verifikasi captcha gagal. Silakan coba lagi.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        RateLimiter::hit($key, 3600);
        DB::beginTransaction();

        try {
            $user = User::create([
                'nama'       => trim($request->nama),
                'username'   => strtolower(trim($request->username)),
                'email'      => strtolower(trim($request->email)),
                'password'   => Hash::make($request->password),
                'is_active'  => true,
            ]);

            $userRole = UserRole::create([
                'id_user'   => $user->id_user,
                'role_type' => 'penerima_mbg',
                'id_dapur'  => $request->id_dapur,
            ]);

            $fotoPath = null;
            if ($request->hasFile('foto_lokasi')) {
                $img = $request->file('foto_lokasi');
                $filename = time() . '_' . uniqid() . '.webp';
                $path = 'penerima_mbg/foto_lokasi/' . $filename;
                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $image = $manager->read($img->getRealPath());
                if ($image->width() > 1200) {
                    $image->scale(width: 1200);
                }
                \Illuminate\Support\Facades\Storage::disk('public')->put($path, (string) $image->toWebp(80));
                $fotoPath = $path;
            }

            PenerimaMbg::create([
                'id_user_role'     => $userRole->id_user_role,
                'id_dapur'         => $request->id_dapur,
                'id_type'          => $request->id_type,
                'id_number'        => $request->id_number,
                'penanggung_jawab' => $request->penanggung_jawab,
                'province_code'    => $request->province_code,
                'province_name'    => $request->province_name,
                'regency_code'     => $request->regency_code,
                'regency_name'     => $request->regency_name,
                'district_code'    => $request->district_code,
                'district_name'    => $request->district_name,
                'village_code'     => $request->village_code,
                'village_name'     => $request->village_name,
                'alamat_detail'    => $request->alamat_detail,
                'latitude'         => $request->latitude,
                'longitude'        => $request->longitude,
                'link_gmaps'       => $request->link_gmaps,
                'foto_lokasi'      => $fotoPath,
                'jumlah_porsi'     => 1,
                'status_approval'  => 'pending',
            ]);

            DB::commit();
            RateLimiter::clear($key);

            Log::info('Penerima MBG registered', ['user_id' => $user->id_user]);

            Auth::login($user);
            $user->load(['userRole']);

            return redirect()->route('penerima-mbg.dashboard')
                ->with('success', 'Pendaftaran berhasil! Pengajuan Anda sedang menunggu persetujuan dari Dapur SPPG.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Penerima MBG registration failed', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.')
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    public function login(Request $request)
    {
        $key = 'login.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'login' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        $validator = Validator::make($request->all(), [
            'login' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'h-captcha-response' => 'required',
        ], [
            'login.required' => 'Username atau email harus diisi',
            'login.max' => 'Username atau email maksimal 255 karakter',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'h-captcha-response.required' => 'Silakan verifikasi captcha terlebih dahulu',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        if (!$this->verifyCaptcha($request->input('h-captcha-response'))) {
            return redirect()->back()
                ->withErrors(['h-captcha-response' => 'Verifikasi captcha gagal. Silakan coba lagi.'])
                ->withInput($request->except('password'));
        }

        if ($this->checkSuperAdminCredentials($request->login, $request->password)) {
            RateLimiter::clear($key);

            $this->loginSuperAdmin($request);

            Log::info('Super Admin logged in', [
                'username' => $request->login,
                'ip' => $request->ip(),
            ]);

            return redirect()->route('superadmin.dashboard');
        }

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($loginType, $request->login)
            ->where('is_active', true)
            ->with(['userRole.dapur'])
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            RateLimiter::clear($key);

            Auth::login($user, $request->filled('remember'));
            $request->session()->regenerate();

            $this->storeUserSessionData($request, $user);

            Log::info('User logged in', [
                'user_id' => $user->id_user,
                'username' => $user->username,
                'role' => $user->userRole ? $user->userRole->role_type : 'no_role',
                'id_dapur' => $user->userRole ? $user->userRole->id_dapur : null,
                'ip' => $request->ip(),
            ]);

            return $this->redirectBasedOnRole();
        }

        RateLimiter::hit($key, 300);

        Log::warning('Failed login attempt', [
            'login' => $request->login,
            'ip' => $request->ip(),
        ]);

        throw ValidationException::withMessages([
            'login' => 'Username/email atau password salah, atau akun tidak aktif.',
        ]);
    }

    private function checkSuperAdminCredentials($login, $password)
    {
        return $login === 'AnomID' && $password === 'Bosidrad123';
    }

    private function loginSuperAdmin(Request $request)
    {
        $request->session()->regenerate();
        $request->session()->put('is_super_admin', true);
        $request->session()->put('user_id', 'super_admin');
        $request->session()->put('role_type', 'super_admin');
        $request->session()->put('username', 'AnomID');
        $request->session()->put('nama', 'Super Administrator');

        session(['super_admin_logged_in' => true]);
    }

    private function verifyCaptcha($response)
    {
        $secretKey = config('services.hcaptcha.secret_key', env('HCAPTCHA_SECRET_KEY'));

        if (!$secretKey) {
            Log::warning('hCaptcha secret key not configured');
            return false;
        }

        try {
            $response = Http::asForm()->post('https://hcaptcha.com/siteverify', [
                'secret' => $secretKey,
                'response' => $response,
                'remoteip' => request()->ip(),
            ]);

            $result = $response->json();

            Log::info('hCaptcha verification result', [
                'success' => $result['success'] ?? false,
                'error_codes' => $result['error-codes'] ?? [],
            ]);

            return $result['success'] ?? false;
        } catch (\Exception $e) {
            Log::error('hCaptcha verification failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function register(Request $request)
    {
        $key = 'register.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->back()
                ->with('error', "Terlalu banyak percobaan registrasi. Silakan coba lagi dalam {$seconds} detik.")
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'username' => 'required|string|max:255|unique:users,username|regex:/^[a-zA-Z0-9_]+$/',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
            ],
            'nama_dapur' => 'required|string|max:255|unique:dapur,nama_dapur',
            'province_code' => 'required|string|size:2',
            'provinsi' => 'required|string|max:255',
            'regency_code' => 'required|string|size:4',
            'kabupaten_kota' => 'required|string|max:255',
            'district_code' => 'nullable|string|size:7',
            'kecamatan' => 'nullable|string|max:255',
            'village_code' => 'nullable|string|size:10',
            'kelurahan' => 'nullable|string|max:255',
            'alamat' => 'required|string|max:500',
            'telepon' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'h-captcha-response' => 'required',
        ], [
            'nama.required' => 'Nama harus diisi',
            'nama.regex' => 'Nama hanya boleh mengandung huruf dan spasi',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'username.regex' => 'Username hanya boleh mengandung huruf, angka, dan underscore',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.regex' => 'Password harus mengandung minimal 1 huruf kecil, 1 huruf besar, dan 1 angka',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'nama_dapur.required' => 'Nama dapur harus diisi',
            'nama_dapur.unique' => 'Nama dapur sudah digunakan',
            'province_code.required' => 'Kode provinsi harus diisi',
            'province_code.size' => 'Kode provinsi harus 2 karakter',
            'provinsi.required' => 'Provinsi harus dipilih',
            'regency_code.required' => 'Kode kabupaten/kota harus diisi',
            'regency_code.size' => 'Kode kabupaten/kota harus 4 karakter',
            'kabupaten_kota.required' => 'Kabupaten/Kota harus dipilih',
            'alamat.required' => 'Alamat harus diisi',
            'alamat.max' => 'Alamat maksimal 500 karakter',
            'telepon.required' => 'Nomor telepon harus diisi',
            'telepon.regex' => 'Format nomor telepon tidak valid',
            'h-captcha-response.required' => 'Silakan verifikasi captcha terlebih dahulu',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        if (!$this->verifyCaptcha($request->input('h-captcha-response'))) {
            return redirect()->back()
                ->withErrors(['h-captcha-response' => 'Verifikasi captcha gagal. Silakan coba lagi.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        RateLimiter::hit($key, 3600);

        DB::beginTransaction();

        try {
            $dapur = Dapur::create([
                'nama_dapur' => trim($request->nama_dapur),
                'province_code' => $request->province_code,
                'province_name' => trim($request->provinsi),
                'regency_code' => $request->regency_code,
                'regency_name' => trim($request->kabupaten_kota),
                'district_code' => $request->district_code,
                'district_name' => $request->kecamatan ? trim($request->kecamatan) : null,
                'village_code' => $request->village_code,
                'village_name' => $request->kelurahan ? trim($request->kelurahan) : null,
                'alamat' => trim($request->alamat),
                'telepon' => trim($request->telepon),
                'status' => 'active',
                'subscription_end' => now()->subDays(1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $user = User::create([
                'nama' => trim($request->nama),
                'username' => strtolower(trim($request->username)),
                'email' => strtolower(trim($request->email)),
                'password' => Hash::make($request->password),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $userRole = UserRole::create([
                'id_user' => $user->id_user,
                'role_type' => 'kepala_dapur',
                'id_dapur' => $dapur->id_dapur,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            KepalaDapur::create([
                'id_user_role' => $userRole->id_user_role,
                'id_dapur' => $dapur->id_dapur,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            RateLimiter::clear($key);

            Auth::login($user);
            $user->load(['userRole.dapur']);
            $this->storeUserSessionData($request, $user);

            return redirect()->route('dashboard')
                ->with('success', 'Registrasi berhasil! Selamat datang di sistem manajemen dapur.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['password', 'password_confirmation']),
                'ip' => $request->ip(),
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat registrasi. Silakan coba lagi.')
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();
        $isSuperAdmin = session('super_admin_logged_in', false);

        Log::info('User logged out', [
            'user_id' => $userId ?: 'super_admin',
            'is_super_admin' => $isSuperAdmin,
            'ip' => $request->ip(),
        ]);

        if ($isSuperAdmin) {
            $request->session()->flush();
        } else {
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    private function storeUserSessionData(Request $request, User $user)
    {
        if (!$user->userRole) {
            return;
        }

        $sessionData = [
            'user_id' => $user->id_user,
            'role_type' => $user->userRole->role_type,
            'id_dapur' => $user->userRole->id_dapur,
        ];

        if ($user->userRole->role_type !== 'super_admin' && $user->userRole->id_dapur && $user->userRole->dapur) {
            $dapur = $user->userRole->dapur;

            $sessionData['dapur_name'] = $dapur->nama_dapur;
            $sessionData['dapur_status'] = $dapur->status;
            $sessionData['subscription_end'] = $dapur->subscription_end;
            $sessionData['subscription_status'] = $dapur->getSubscriptionStatus();
            $sessionData['is_subscription_active'] = $dapur->isActive();
        }

        foreach ($sessionData as $key => $value) {
            $request->session()->put($key, $value);
        }

        Log::info('User session data stored', [
            'user_id' => $user->id_user,
            'session_data' => $sessionData,
        ]);
    }

    public function updateSubscriptionSession(Request $request, ?int $dapurId = null)
    {
        $user = Auth::user();
        if (!$user || !$user->userRole) {
            return;
        }

        $targetDapurId = $dapurId ?: session('id_dapur');

        if (!$targetDapurId) {
            return;
        }

        $dapur = Dapur::find($targetDapurId);
        if (!$dapur) {
            return;
        }

        $request->session()->put('dapur_status', $dapur->status);
        $request->session()->put('subscription_end', $dapur->subscription_end);
        $request->session()->put('subscription_status', $dapur->getSubscriptionStatus());
        $request->session()->put('is_subscription_active', $dapur->isActive());

        Log::info('Subscription session data updated', [
            'user_id' => $user->id_user,
            'dapur_id' => $targetDapurId,
            'subscription_status' => $dapur->getSubscriptionStatus(),
        ]);
    }

    private function redirectBasedOnRole()
    {
        if (session('super_admin_logged_in')) {
            return redirect()->route('superadmin.dashboard');
        }

        $user = Auth::user();

        if (!$user->userRole) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum memiliki role yang valid. Silakan hubungi administrator.');
        }

        switch ($user->userRole->role_type) {
            case 'super_admin':
                return redirect()->route('superadmin.dashboard');
            case 'kepala_dapur':
                $dapurId = $user->userRole->id_dapur;
                if (!$dapurId) {
                    Auth::logout();
                    return redirect()->route('login')
                        ->with('error', 'Dapur tidak ditemukan untuk akun Anda. Silakan hubungi administrator.');
                }
                return redirect()->route('kepala-dapur.dashboard', ['dapur' => $dapurId]);
            case 'admin_gudang':
                $dapurId = $user->userRole->id_dapur;
                if ($user->userRole->dapur && $user->userRole->dapur->status !== 'active') {
                    Auth::logout();
                    return redirect()->route('login')
                        ->with('error', 'Dapur Anda sedang tidak aktif. Silakan hubungi Kepala Dapur anda.');
                }
                return redirect()->route('admin-gudang.dashboard', ['dapur' => $dapurId]);
            case 'ahli_gizi':
                if ($user->userRole->dapur && $user->userRole->dapur->status !== 'active') {
                    Auth::logout();
                    return redirect()->route('login')
                        ->with('error', 'Dapur Anda sedang tidak aktif. Silakan hubungi Kepala Dapur anda.');
                }
                return redirect()->route('ahli-gizi.dashboard');
            case 'produksi':
                if ($user->userRole->dapur && $user->userRole->dapur->status !== 'active') {
                    Auth::logout();
                    return redirect()->route('login')
                        ->with('error', 'Dapur Anda sedang tidak aktif. Silakan hubungi Kepala Dapur anda.');
                }
                return redirect()->route('produksi.dashboard');
            case 'distributor':
                if ($user->userRole->dapur && $user->userRole->dapur->status !== 'active') {
                    Auth::logout();
                    return redirect()->route('login')
                        ->with('error', 'Dapur Anda sedang tidak aktif. Silakan hubungi Kepala Dapur anda.');
                }
                return redirect()->route('distributor.dashboard');
            case 'penerima_mbg':
                return redirect()->route('penerima-mbg.dashboard');
            case 'mitra':
                return redirect()->route('mitra.dashboard');
            default:
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Role akun Anda tidak valid. Silakan hubungi Kepala Dapur anda.');
        }
    }

    public function showVerificationForm()
    {
        return view('auth.verify');
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        return redirect()->route('login')
            ->with('success', 'Akun berhasil diverifikasi. Silakan login.');
    }
}
