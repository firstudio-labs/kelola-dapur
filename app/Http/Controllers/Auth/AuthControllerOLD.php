<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Dapur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthControllerOLD extends Controller
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

        $dapurList = Dapur::where('status', 'active')->get();
        return view('auth.register', compact('dapurList'));
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Username atau email harus diisi',
            'password.required' => 'Password harus diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType => $request->login,
            'password' => $request->password,
            'is_active' => true
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return $this->redirectBasedOnRole();
        }

        throw ValidationException::withMessages([
            'login' => 'Username/email atau password salah, atau akun tidak aktif.',
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:kepala_dapur,admin_gudang,ahli_gizi',
            'id_dapur' => 'required|exists:dapur,id_dapur',
        ], [
            'nama.required' => 'Nama harus diisi',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required' => 'Role harus dipilih',
            'role.in' => 'Role tidak valid',
            'id_dapur.required' => 'Dapur harus dipilih',
            'id_dapur.exists' => 'Dapur tidak ditemukan',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        try {
            $user = User::create([
                'nama' => $request->nama,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => true,
            ]);

            $this->assignRole($user, $request->role, $request->id_dapur);

            Auth::login($user);

            return redirect()->route('dashboard')->with('success', 'Registrasi berhasil! Selamat datang.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat registrasi. Silakan coba lagi.')
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah logout.');
    }

    private function assignRole(User $user, string $role, int $dapurId)
    {
        switch ($role) {
            case 'kepala_dapur':
                $user->kepalaDapur()->create(['id_dapur' => $dapurId]);
                break;
            case 'admin_gudang':
                $user->adminGudang()->create(['id_dapur' => $dapurId]);
                break;
            case 'ahli_gizi':
                $user->ahliGizi()->create(['id_dapur' => $dapurId]);
                break;
        }
    }

    private function redirectBasedOnRole()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        if (
            $user->kepalaDapur()->exists() ||
            $user->adminGudang()->exists() ||
            $user->ahliGizi()->exists()
        ) {
            return redirect()->route('dashboard');
        }

        Auth::logout();
        return redirect()->route('login')->with('error', 'Akun Anda belum memiliki role yang valid.');
    }
}
