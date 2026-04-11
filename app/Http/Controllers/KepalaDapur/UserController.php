<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\Dapur;
use App\Models\User;
use App\Models\UserRole;
use App\Models\AdminGudang;
use App\Models\AhliGizi;
use App\Models\Produksi;
use App\Models\Akuntan;
use App\Models\Sarpas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $dapur = $request->current_dapur;
            $search = $request->input('search');
            $current_user = auth()->user();

            if (!$current_user->userRole || $current_user->userRole->role_type !== 'kepala_dapur') {
                Log::warning('Unauthorized access attempt to user management', [
                    'user_id' => $current_user->id_user,
                    'role_type' => $current_user->userRole ? $current_user->userRole->role_type : 'none'
                ]);
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengelola user.');
            }

            $users = User::whereHas('userRole', function ($query) use ($dapur) {
                $query->where('id_dapur', $dapur->id_dapur)
                    ->whereIn('role_type', ['admin_gudang', 'ahli_gizi', 'produksi', 'distributor', 'akuntan', 'sarpas']);
            })->when($search, function ($query, $search) {
                return $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->with('userRole')->paginate(10);

            return view('kepaladapur.user.index', compact('users', 'dapur', 'current_user'));
        } catch (Exception $e) {
            Log::error('Failed to load user list', [
                'error' => $e->getMessage(),
                'dapur_id' => $dapur->id_dapur ?? 'not_set',
            ]);
            return redirect()->back()->with('error', 'Gagal memuat daftar user: ' . $e->getMessage());
        }
    }

    public function create(Request $request)
    {
        try {
            $dapur = $request->current_dapur;
            $roles = [
                'admin_gudang' => 'Admin Gudang', 
                'ahli_gizi' => 'Ahli Gizi',
                'produksi' => 'Produksi', 
                'distributor' => 'Distributor',
                'akuntan' => 'Akuntan',
                'sarpas' => 'Sarpas',
            ];

            return view('kepaladapur.user.create', compact('dapur', 'roles'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat form tambah user: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $dapur = $request->current_dapur;

            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role_type' => ['required', Rule::in(['admin_gudang', 'ahli_gizi', 'produksi', 'distributor', 'akuntan', 'sarpas'])],
                'nik_produksi' => 'nullable|string|max:16',
                'nik_ahli_gizi' => 'nullable|string|max:16',
                'nik_akuntan' => 'nullable|string|max:16',
                'nik_sarpas' => 'nullable|string|max:16',
                'nama_lengkap' => 'nullable|string|max:255',
                'kontak_wa' => 'nullable|string|max:20',
                'jabatan' => 'nullable|in:Penanggung jawab,Anggota',
                'pendidikan' => 'nullable|in:SD,SMP,SMA,D1,D2,D3,Sarjana',
                'jenis_kelamin' => 'nullable|in:Pria,Wanita',
                'foto_diri' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'nik_distribusi' => 'nullable|string|max:16',
                'nik_admin_gudang' => 'nullable|string|max:16',
                'pendidikan_terakhir' => 'nullable|string',
                'alamat_detail' => 'nullable|string',
                'province_code' => 'nullable|string',
                'province_name' => 'nullable|string',
                'regency_code' => 'nullable|string',
                'regency_name' => 'nullable|string',
                'district_code' => 'nullable|string',
                'district_name' => 'nullable|string',
                'village_code' => 'nullable|string',
                'village_name' => 'nullable|string',
            ]);

            $user = User::create([
                'nama' => $validated['nama'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_active' => true,
            ]);

            $userRole = UserRole::create([
                'id_user' => $user->id_user,
                'role_type' => $validated['role_type'],
                'id_dapur' => $dapur->id_dapur,
            ]);

            if ($validated['role_type'] === 'admin_gudang') {
                $adminGudangData = [
                    'id_user_role' => $userRole->id_user_role,
                    'id_dapur' => $dapur->id_dapur,
                    'nik_admin_gudang' => $validated['nik_admin_gudang'] ?? null,
                    'nama_lengkap' => $validated['nama_lengkap'] ?? null,
                    'kontak_wa' => $validated['kontak_wa'] ?? null,
                    'jabatan' => $validated['jabatan'] ?? 'Anggota',
                    'pendidikan_terakhir' => $validated['pendidikan_terakhir'] ?? null,
                    'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                    'alamat_detail' => $validated['alamat_detail'] ?? null,
                    'province_code' => $validated['province_code'] ?? null,
                    'province_name' => $validated['province_name'] ?? null,
                    'regency_code' => $validated['regency_code'] ?? null,
                    'regency_name' => $validated['regency_name'] ?? null,
                    'district_code' => $validated['district_code'] ?? null,
                    'district_name' => $validated['district_name'] ?? null,
                    'village_code' => $validated['village_code'] ?? null,
                    'village_name' => $validated['village_name'] ?? null,
                ];

                if ($request->hasFile('foto_diri')) {
                    $image = $request->file('foto_diri');
                    $filename = time() . '_' . uniqid() . '.webp';
                    $path = 'dokumen_admin_gudang/foto_diri/' . $filename;
                    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                    $img = $manager->read($image->getRealPath());
                    if ($img->width() > 1200) $img->scale(width: 1200);
                    Storage::disk('public')->put($path, (string) $img->toWebp(80));
                    $adminGudangData['foto_diri'] = $path;
                }
                AdminGudang::create($adminGudangData);
            } elseif ($validated['role_type'] === 'ahli_gizi') {
                $ahliGiziData = [
                    'id_user_role' => $userRole->id_user_role,
                    'id_dapur' => $dapur->id_dapur,
                    'nik_ahli_gizi' => $validated['nik_ahli_gizi'] ?? null,
                    'jabatan' => $validated['jabatan'] ?? 'Anggota',
                    'pendidikan_terakhir' => $validated['pendidikan_terakhir'] ?? null,
                    'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                    'kontak_wa' => $validated['kontak_wa'] ?? null,
                ];

                if ($request->hasFile('foto_diri')) {
                    $image = $request->file('foto_diri');
                    $filename = time() . '_' . uniqid() . '.webp';
                    $path = 'dokumen_ahli_gizi/foto_diri/' . $filename;
                    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                    $img = $manager->read($image->getRealPath());
                    if ($img->width() > 1200) $img->scale(width: 1200);
                    Storage::disk('public')->put($path, (string) $img->toWebp(80));
                    $ahliGiziData['foto_diri'] = $path;
                }
                AhliGizi::create($ahliGiziData);
            } elseif ($validated['role_type'] === 'produksi') {
                $produksiData = [
                    'id_user_role' => $userRole->id_user_role,
                    'id_dapur' => $dapur->id_dapur,
                    'nik_produksi' => $validated['nik_produksi'] ?? null,
                    'nama_lengkap' => $validated['nama_lengkap'] ?? null,
                    'kontak_wa' => $validated['kontak_wa'] ?? null,
                    'jabatan' => $validated['jabatan'] ?? 'Anggota',
                    'pendidikan' => $validated['pendidikan'] ?? null,
                    'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                    'alamat_detail' => $validated['alamat_detail'] ?? null,
                    'province_code' => $validated['province_code'] ?? null,
                    'province_name' => $validated['province_name'] ?? null,
                    'regency_code' => $validated['regency_code'] ?? null,
                    'regency_name' => $validated['regency_name'] ?? null,
                    'district_code' => $validated['district_code'] ?? null,
                    'district_name' => $validated['district_name'] ?? null,
                    'village_code' => $validated['village_code'] ?? null,
                    'village_name' => $validated['village_name'] ?? null,
                ];

                if ($request->hasFile('foto_diri')) {
                    $image = $request->file('foto_diri');
                    $filename = time() . '_' . uniqid() . '.webp';
                    $path = 'dokumen_produksi/foto_diri/' . $filename;
                    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                    $img = $manager->read($image->getRealPath());
                    if ($img->width() > 1200) $img->scale(width: 1200);
                    Storage::disk('public')->put($path, (string) $img->toWebp(80));
                    $produksiData['foto_diri'] = $path;
                }
                Produksi::create($produksiData);
            } elseif ($validated['role_type'] === 'distributor') {
                $distributorData = [
                    'id_user_role' => $userRole->id_user_role,
                    'id_dapur' => $dapur->id_dapur,
                    'nik_distribusi' => $validated['nik_distribusi'] ?? null,
                    'nama_lengkap' => $validated['nama_lengkap'] ?? null,
                    'kontak_wa' => $validated['kontak_wa'] ?? null,
                    'jabatan' => $validated['jabatan'] ?? 'Anggota',
                    'pendidikan' => $validated['pendidikan'] ?? null,
                    'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                    'alamat_detail' => $validated['alamat_detail'] ?? null,
                    'province_code' => $validated['province_code'] ?? null,
                    'province_name' => $validated['province_name'] ?? null,
                    'regency_code' => $validated['regency_code'] ?? null,
                    'regency_name' => $validated['regency_name'] ?? null,
                    'district_code' => $validated['district_code'] ?? null,
                    'district_name' => $validated['district_name'] ?? null,
                    'village_code' => $validated['village_code'] ?? null,
                    'village_name' => $validated['village_name'] ?? null,
                ];

                if ($request->hasFile('foto_diri')) {
                    $image = $request->file('foto_diri');
                    $filename = time() . '_' . uniqid() . '.webp';
                    $path = 'dokumen_distributor/foto_diri/' . $filename;
                    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                    $img = $manager->read($image->getRealPath());
                    if ($img->width() > 1200) $img->scale(width: 1200);
                    Storage::disk('public')->put($path, (string) $img->toWebp(80));
                    $distributorData['foto_diri'] = $path;
                }
                \App\Models\Distributor::create($distributorData);
            } elseif ($validated['role_type'] === 'akuntan') {
                $akuntanData = [
                    'id_user_role' => $userRole->id_user_role,
                    'id_dapur' => $dapur->id_dapur,
                    'nik_akuntan' => $validated['nik_akuntan'] ?? null,
                    'nama_lengkap' => $validated['nama_lengkap'] ?? null,
                    'kontak_wa' => $validated['kontak_wa'] ?? null,
                    'jabatan' => $validated['jabatan'] ?? 'Anggota',
                    'pendidikan' => $validated['pendidikan'] ?? null,
                    'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                    'alamat_detail' => $validated['alamat_detail'] ?? null,
                    'province_code' => $validated['province_code'] ?? null,
                    'province_name' => $validated['province_name'] ?? null,
                    'regency_code' => $validated['regency_code'] ?? null,
                    'regency_name' => $validated['regency_name'] ?? null,
                    'district_code' => $validated['district_code'] ?? null,
                    'district_name' => $validated['district_name'] ?? null,
                    'village_code' => $validated['village_code'] ?? null,
                    'village_name' => $validated['village_name'] ?? null,
                ];

                if ($request->hasFile('foto_diri')) {
                    $image = $request->file('foto_diri');
                    $filename = time() . '_' . uniqid() . '.webp';
                    $path = 'dokumen_akuntan/foto_diri/' . $filename;
                    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                    $img = $manager->read($image->getRealPath());
                    if ($img->width() > 1200) $img->scale(width: 1200);
                    Storage::disk('public')->put($path, (string) $img->toWebp(80));
                    $akuntanData['foto_diri'] = $path;
                }
                \App\Models\Akuntan::create($akuntanData);
            } elseif ($validated['role_type'] === 'sarpas') {
                $sarpasData = [
                    'id_user_role' => $userRole->id_user_role,
                    'id_dapur' => $dapur->id_dapur,
                    'nik_sarpas' => $validated['nik_sarpas'] ?? null,
                    'nama_lengkap' => $validated['nama_lengkap'] ?? null,
                    'kontak_wa' => $validated['kontak_wa'] ?? null,
                    'jabatan' => $validated['jabatan'] ?? 'Anggota',
                    'pendidikan' => $validated['pendidikan'] ?? null,
                    'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                    'alamat_detail' => $validated['alamat_detail'] ?? null,
                    'province_code' => $validated['province_code'] ?? null,
                    'province_name' => $validated['province_name'] ?? null,
                    'regency_code' => $validated['regency_code'] ?? null,
                    'regency_name' => $validated['regency_name'] ?? null,
                    'district_code' => $validated['district_code'] ?? null,
                    'district_name' => $validated['district_name'] ?? null,
                    'village_code' => $validated['village_code'] ?? null,
                    'village_name' => $validated['village_name'] ?? null,
                ];

                if ($request->hasFile('foto_diri')) {
                    $image = $request->file('foto_diri');
                    $filename = time() . '_' . uniqid() . '.webp';
                    $path = 'dokumen_sarpas/foto_diri/' . $filename;
                    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                    $img = $manager->read($image->getRealPath());
                    if ($img->width() > 1200) $img->scale(width: 1200);
                    Storage::disk('public')->put($path, (string) $img->toWebp(80));
                    $sarpasData['foto_diri'] = $path;
                }
                \App\Models\Sarpas::create($sarpasData);
            }

            return redirect()->route('kepala-dapur.users.index', ['dapur' => $dapur->id_dapur])->with('success', 'User berhasil ditambahkan.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function show(Request $request, Dapur $dapur, User $user)
    {
        Log::info('Show User Attempt - Debug Parameters', [
            'dapur_id' => $dapur->id_dapur,
            'user_id' => $user->id_user,
            'all_route_parameters' => $request->route()->parameters(),
            'url' => $request->fullUrl()
        ]);

        try {

            $user->load('userRole');

            if (!$user->userRole) {
                Log::warning('User has no role', [
                    'user_id' => $user->id_user,
                    'user_name' => $user->nama
                ]);
                return redirect()->back()->with('error', "User {$user->nama} tidak memiliki role yang ditetapkan.");
            }

            if ($user->userRole->id_dapur != $dapur->id_dapur) {
                Log::warning('User not in this dapur', [
                    'user_id' => $user->id_user,
                    'user_dapur_id' => $user->userRole->id_dapur,
                    'current_dapur_id' => $dapur->id_dapur
                ]);
                return redirect()->back()->with('error', "User {$user->nama} tidak memiliki akses ke dapur ini.");
            }

            if (!in_array($user->userRole->role_type, ['admin_gudang', 'ahli_gizi', 'produksi', 'distributor', 'akuntan', 'sarpas'])) {
                Log::warning('User has wrong role type', [
                    'user_id' => $user->id_user,
                    'role_type' => $user->userRole->role_type,
                    'allowed_roles' => ['admin_gudang', 'ahli_gizi', 'produksi', 'distributor', 'akuntan', 'sarpas']
                ]);
                return redirect()->back()->with('error', "User {$user->nama} bukan admin gudang, ahli gizi, produksi, distributor, akuntan, atau sarpas.");
            }

            $adminGudang = null;
            $ahliGizi = null;
            $produksi = null;
            $distributor = null;
            $akuntan = null;
            $sarpas = null;
            if ($user->userRole->role_type === 'admin_gudang') {
                $adminGudang = AdminGudang::where('id_user_role', $user->userRole->id_user_role)->first();
            } elseif ($user->userRole->role_type === 'ahli_gizi') {
                $ahliGizi = AhliGizi::where('id_user_role', $user->userRole->id_user_role)->first();
            } elseif ($user->userRole->role_type === 'produksi') {
                $produksi = Produksi::where('id_user_role', $user->userRole->id_user_role)->first();
            } elseif ($user->userRole->role_type === 'distributor') {
                $distributor = \App\Models\Distributor::where('id_user_role', $user->userRole->id_user_role)->first();
            } elseif ($user->userRole->role_type === 'akuntan') {
                $akuntan = \App\Models\Akuntan::where('id_user_role', $user->userRole->id_user_role)->first();
            } elseif ($user->userRole->role_type === 'sarpas') {
                $sarpas = \App\Models\Sarpas::where('id_user_role', $user->userRole->id_user_role)->first();
            }

            Log::info('User access granted', [
                'user_id' => $user->id_user,
                'user_name' => $user->nama,
                'role_type' => $user->userRole->role_type,
                'dapur_id' => $dapur->id_dapur
            ]);

            return view('kepaladapur.user.show', compact('user', 'dapur', 'adminGudang', 'ahliGizi', 'produksi', 'distributor', 'akuntan', 'sarpas'));
        } catch (Exception $e) {
            Log::error('Failed to show user', [
                'error' => $e->getMessage(),
                'user_id' => $user->id_user ?? 'not_set',
                'dapur_id' => $dapur->id_dapur ?? 'not_set',
            ]);
            return redirect()->back()->with('error', 'Gagal memuat detail user: ' . $e->getMessage());
        }
    }

    public function edit(Request $request, Dapur $dapur, User $user)
    {
        Log::info('Edit User Attempt', [
            'dapur_id' => $dapur->id_dapur,
            'user_id' => $user->id_user,
        ]);

        try {
            $current_user = auth()->user();

            $user->load('userRole');

            if (!$user->userRole) {
                Log::warning('Edit User: User has no role', [
                    'user_id' => $user->id_user,
                    'user_name' => $user->nama
                ]);
                return redirect()->back()->with('error', "User {$user->nama} tidak memiliki role yang ditetapkan.");
            }

            if ($user->userRole->id_dapur != $dapur->id_dapur) {
                Log::warning('Edit User: User not in this dapur', [
                    'user_id' => $user->id_user,
                    'user_dapur_id' => $user->userRole->id_dapur,
                    'current_dapur_id' => $dapur->id_dapur
                ]);
                return redirect()->back()->with('error', "User {$user->nama} tidak memiliki akses ke dapur ini.");
            }

            if (!in_array($user->userRole->role_type, ['admin_gudang', 'ahli_gizi', 'produksi', 'distributor', 'akuntan', 'sarpas'])) {
                Log::warning('Edit User: User has wrong role type', [
                    'user_id' => $user->id_user,
                    'role_type' => $user->userRole->role_type,
                    'allowed_roles' => ['admin_gudang', 'ahli_gizi', 'produksi', 'distributor', 'akuntan', 'sarpas']
                ]);
                return redirect()->back()->with('error', "User {$user->nama} bukan admin gudang, ahli gizi, produksi, distributor, akuntan, atau sarpas.");
            }

            $roles = [
                'admin_gudang' => 'Admin Gudang', 
                'ahli_gizi' => 'Ahli Gizi',
                'produksi' => 'Produksi', 
                'distributor' => 'Distributor',
                'akuntan' => 'Akuntan',
                'sarpas' => 'Sarpas'
            ];

            $adminGudang = null;
            $ahliGizi = null;
            $produksi = null;
            $distributor = null;
            $akuntan = null;
            $sarpas = null;
            if ($user->userRole->role_type === 'admin_gudang') {
                $adminGudang = AdminGudang::where('id_user_role', $user->userRole->id_user_role)->first();
            } elseif ($user->userRole->role_type === 'ahli_gizi') {
                $ahliGizi = AhliGizi::where('id_user_role', $user->userRole->id_user_role)->first();
            } elseif ($user->userRole->role_type === 'produksi') {
                $produksi = Produksi::where('id_user_role', $user->userRole->id_user_role)->first();
            } elseif ($user->userRole->role_type === 'distributor') {
                $distributor = \App\Models\Distributor::where('id_user_role', $user->userRole->id_user_role)->first();
            } elseif ($user->userRole->role_type === 'akuntan') {
                $akuntan = \App\Models\Akuntan::where('id_user_role', $user->userRole->id_user_role)->first();
            } elseif ($user->userRole->role_type === 'sarpas') {
                $sarpas = \App\Models\Sarpas::where('id_user_role', $user->userRole->id_user_role)->first();
            }

            Log::info('Edit User: Access granted', [
                'user_id' => $user->id_user,
                'user_name' => $user->nama,
                'role_type' => $user->userRole->role_type,
                'dapur_id' => $dapur->id_dapur
            ]);

            return view('kepaladapur.user.edit', compact('user', 'dapur', 'roles', 'current_user', 'adminGudang', 'ahliGizi', 'produksi', 'distributor', 'akuntan', 'sarpas'));
        } catch (Exception $e) {
            Log::error('Failed to edit user', [
                'error' => $e->getMessage(),
                'user_id' => $user->id_user ?? 'not_set',
                'dapur_id' => $dapur->id_dapur ?? 'not_set',
            ]);
            return redirect()->back()->with('error', 'Gagal memuat form edit user: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Dapur $dapur, User $user)
    {
        try {

            $user->load('userRole');

            if (!$user->userRole) {
                return redirect()->back()->with('error', "User {$user->nama} tidak memiliki role yang ditetapkan.");
            }

            if ($user->userRole->id_dapur != $dapur->id_dapur) {
                return redirect()->back()->with('error', "User {$user->nama} tidak memiliki akses ke dapur ini.");
            }

            if (!in_array($user->userRole->role_type, ['admin_gudang', 'ahli_gizi', 'produksi', 'distributor', 'akuntan', 'sarpas'])) {
                return redirect()->back()->with('error', "User {$user->nama} bukan admin gudang, ahli gizi, produksi, distributor, akuntan, atau sarpas.");
            }

            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id_user, 'id_user')],
                'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id_user, 'id_user')],
                'password' => 'nullable|string|min:8|confirmed',
                'role_type' => ['required', Rule::in(['admin_gudang', 'ahli_gizi', 'produksi', 'distributor', 'akuntan', 'sarpas'])],
                'is_active' => 'sometimes|boolean',
                'nik_ahli_gizi' => 'nullable|string|max:16',
                'jabatan' => 'nullable|in:Penanggung jawab,Anggota',
                'pendidikan_terakhir' => 'nullable|string',
                'jenis_kelamin' => 'nullable|in:Pria,Wanita',
                'kontak_wa' => 'nullable|string|max:20',
                'alamat_detail' => 'nullable|string',
                'province_code' => 'nullable|string',
                'province_name' => 'nullable|string',
                'regency_code' => 'nullable|string',
                'regency_name' => 'nullable|string',
                'district_code' => 'nullable|string',
                'district_name' => 'nullable|string',
                'village_code' => 'nullable|string',
                'village_name' => 'nullable|string',
                'foto_diri' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'nik_produksi' => 'nullable|string|max:16',
                'nama_lengkap' => 'nullable|string|max:255',
                'pendidikan' => 'nullable|in:SD,SMP,SMA,D1,D2,D3,Sarjana',
                'nik_distribusi' => 'nullable|string|max:16',
                'nik_admin_gudang' => 'nullable|string|max:16',
                'nik_akuntan' => 'nullable|string|max:16',
                'nik_sarpas' => 'nullable|string|max:16',
            ]);

            $user->update([
                'nama' => $validated['nama'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'is_active' => $request->has('is_active') ? $request->boolean('is_active') : false,
            ]);

            if (!empty($validated['password'])) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }

            $userRole = $user->userRole;
            if ($userRole->role_type !== $validated['role_type']) {
                if ($userRole->role_type === 'admin_gudang' && $userRole->adminGudang) {
                    $userRole->adminGudang->delete();
                } elseif ($userRole->role_type === 'ahli_gizi' && $userRole->ahliGizi) {
                    $userRole->ahliGizi->delete();
                } elseif ($userRole->role_type === 'produksi' && $userRole->produksi) {
                    $userRole->produksi->delete();
                } elseif ($userRole->role_type === 'akuntan' && $userRole->akuntan) {
                    $userRole->akuntan->delete();
                } elseif ($userRole->role_type === 'sarpas' && $userRole->sarpas) {
                    $userRole->sarpas->delete();
                }

                $userRole->update(['role_type' => $validated['role_type']]);

                if ($validated['role_type'] === 'admin_gudang') {
                    AdminGudang::create([
                        'id_user_role' => $userRole->id_user_role,
                        'id_dapur' => $dapur->id_dapur,
                    ]);
                } elseif ($validated['role_type'] === 'ahli_gizi') {
                    AhliGizi::create([
                        'id_user_role' => $userRole->id_user_role,
                        'id_dapur' => $dapur->id_dapur,
                    ]);
                } elseif ($validated['role_type'] === 'produksi') {
                    \App\Models\Produksi::create([
                        'id_user_role' => $userRole->id_user_role,
                        'id_dapur' => $dapur->id_dapur,
                    ]);
                } elseif ($validated['role_type'] === 'distributor') {
                    \App\Models\Distributor::create([
                        'id_user_role' => $userRole->id_user_role,
                        'id_dapur' => $dapur->id_dapur,
                    ]);
                } elseif ($validated['role_type'] === 'akuntan') {
                    $akuntan = \App\Models\Akuntan::create([
                        'id_user_role' => $userRole->id_user_role,
                        'id_dapur' => $dapur->id_dapur,
                    ]);
                } elseif ($validated['role_type'] === 'sarpas') {
                    $sarpas = \App\Models\Sarpas::create([
                        'id_user_role' => $userRole->id_user_role,
                        'id_dapur' => $dapur->id_dapur,
                    ]);
                }
            }

            if ($userRole->role_type === 'admin_gudang') {
                $adminGudang = AdminGudang::where('id_user_role', $userRole->id_user_role)->first();
                if ($adminGudang) {
                    $adminGudangProfileData = [
                        'nik_admin_gudang' => $validated['nik_admin_gudang'] ?? null,
                        'nama_lengkap' => $validated['nama_lengkap'] ?? null,
                        'jabatan' => $validated['jabatan'] ?? null,
                        'pendidikan_terakhir' => $validated['pendidikan_terakhir'] ?? null,
                        'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                        'kontak_wa' => $validated['kontak_wa'] ?? null,
                        'alamat_detail' => $validated['alamat_detail'] ?? null,
                        'province_code' => $validated['province_code'] ?? null,
                        'province_name' => $validated['province_name'] ?? null,
                        'regency_code' => $validated['regency_code'] ?? null,
                        'regency_name' => $validated['regency_name'] ?? null,
                        'district_code' => $validated['district_code'] ?? null,
                        'district_name' => $validated['district_name'] ?? null,
                        'village_code' => $validated['village_code'] ?? null,
                        'village_name' => $validated['village_name'] ?? null,
                    ];

                    if ($request->hasFile('foto_diri')) {
                        if ($adminGudang->foto_diri && Storage::disk('public')->exists($adminGudang->foto_diri)) {
                            Storage::disk('public')->delete($adminGudang->foto_diri);
                        }

                        $image = $request->file('foto_diri');
                        $filename = time() . '_' . uniqid() . '.webp';
                        $path = 'dokumen_admin_gudang/foto_diri/' . $filename;
                        
                        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                        $img = $manager->read($image->getRealPath());
                        if ($img->width() > 1200) {
                            $img->scale(width: 1200);
                        }
                        
                        Storage::disk('public')->put($path, (string) $img->toWebp(80));
                        $adminGudangProfileData['foto_diri'] = $path;
                    }

                    $adminGudang->update($adminGudangProfileData);
                }
            } elseif ($userRole->role_type === 'ahli_gizi') {
                $ahliGizi = AhliGizi::where('id_user_role', $userRole->id_user_role)->first();
                if ($ahliGizi) {
                    $ahliGiziProfileData = [
                        'nik_ahli_gizi' => $validated['nik_ahli_gizi'] ?? null,
                        'jabatan' => $validated['jabatan'] ?? null,
                        'pendidikan_terakhir' => $validated['pendidikan_terakhir'] ?? null,
                        'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                        'kontak_wa' => $validated['kontak_wa'] ?? null,
                        'alamat_detail' => $validated['alamat_detail'] ?? null,
                        'province_code' => $validated['province_code'] ?? null,
                        'province_name' => $validated['province_name'] ?? null,
                        'regency_code' => $validated['regency_code'] ?? null,
                        'regency_name' => $validated['regency_name'] ?? null,
                        'district_code' => $validated['district_code'] ?? null,
                        'district_name' => $validated['district_name'] ?? null,
                        'village_code' => $validated['village_code'] ?? null,
                        'village_name' => $validated['village_name'] ?? null,
                    ];

                    if ($request->hasFile('foto_diri')) {
                        if ($ahliGizi->foto_diri && Storage::disk('public')->exists($ahliGizi->foto_diri)) {
                            Storage::disk('public')->delete($ahliGizi->foto_diri);
                        }

                        $image = $request->file('foto_diri');
                        $filename = time() . '_' . uniqid() . '.webp';
                        $path = 'dokumen_ahli_gizi/foto_diri/' . $filename;
                        
                        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                        $img = $manager->read($image->getRealPath());
                        if ($img->width() > 1200) {
                            $img->scale(width: 1200);
                        }
                        
                        Storage::disk('public')->put($path, (string) $img->toWebp(80));
                        $ahliGiziProfileData['foto_diri'] = $path;
                    }

                    $ahliGizi->update($ahliGiziProfileData);
                }
            } elseif ($userRole->role_type === 'produksi') {
                $produksi = Produksi::where('id_user_role', $userRole->id_user_role)->first();
                if ($produksi) {
                    $produksiProfileData = [
                        'nik_produksi' => $validated['nik_produksi'] ?? null,
                        'nama_lengkap' => $validated['nama_lengkap'] ?? null,
                        'jabatan' => $validated['jabatan'] ?? null,
                        'pendidikan' => $validated['pendidikan'] ?? null,
                        'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                        'kontak_wa' => $validated['kontak_wa'] ?? null,
                        'alamat_detail' => $validated['alamat_detail'] ?? null,
                        'province_code' => $validated['province_code'] ?? null,
                        'province_name' => $validated['province_name'] ?? null,
                        'regency_code' => $validated['regency_code'] ?? null,
                        'regency_name' => $validated['regency_name'] ?? null,
                        'district_code' => $validated['district_code'] ?? null,
                        'district_name' => $validated['district_name'] ?? null,
                        'village_code' => $validated['village_code'] ?? null,
                        'village_name' => $validated['village_name'] ?? null,
                    ];

                    if ($request->hasFile('foto_diri')) {
                        if ($produksi->foto_diri && Storage::disk('public')->exists($produksi->foto_diri)) {
                            Storage::disk('public')->delete($produksi->foto_diri);
                        }

                        $image = $request->file('foto_diri');
                        $filename = time() . '_' . uniqid() . '.webp';
                        $path = 'dokumen_produksi/foto_diri/' . $filename;
                        
                        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                        $img = $manager->read($image->getRealPath());
                        if ($img->width() > 1200) {
                            $img->scale(width: 1200);
                        }
                        
                        Storage::disk('public')->put($path, (string) $img->toWebp(80));
                        $produksiProfileData['foto_diri'] = $path;
                    }

                    $produksi->update($produksiProfileData);
                }
            } elseif ($userRole->role_type === 'distributor') {
                $distributor = \App\Models\Distributor::where('id_user_role', $userRole->id_user_role)->first();
                if ($distributor) {
                    $distributorProfileData = [
                        'nik_distribusi' => $validated['nik_distribusi'] ?? null,
                        'nama_lengkap' => $validated['nama_lengkap'] ?? null,
                        'jabatan' => $validated['jabatan'] ?? null,
                        'pendidikan' => $validated['pendidikan'] ?? null,
                        'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                        'kontak_wa' => $validated['kontak_wa'] ?? null,
                        'alamat_detail' => $validated['alamat_detail'] ?? null,
                        'province_code' => $validated['province_code'] ?? null,
                        'province_name' => $validated['province_name'] ?? null,
                        'regency_code' => $validated['regency_code'] ?? null,
                        'regency_name' => $validated['regency_name'] ?? null,
                        'district_code' => $validated['district_code'] ?? null,
                        'district_name' => $validated['district_name'] ?? null,
                        'village_code' => $validated['village_code'] ?? null,
                        'village_name' => $validated['village_name'] ?? null,
                    ];

                    if ($request->hasFile('foto_diri')) {
                        if ($distributor->foto_diri && Storage::disk('public')->exists($distributor->foto_diri)) {
                            Storage::disk('public')->delete($distributor->foto_diri);
                        }

                        $image = $request->file('foto_diri');
                        $filename = time() . '_' . uniqid() . '.webp';
                        $path = 'dokumen_distributor/foto_diri/' . $filename;
                        
                        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                        $img = $manager->read($image->getRealPath());
                        if ($img->width() > 1200) {
                            $img->scale(width: 1200);
                        }
                        
                        Storage::disk('public')->put($path, (string) $img->toWebp(80));
                        $distributorProfileData['foto_diri'] = $path;
                    }

                    $distributor->update($distributorProfileData);
                }
            } elseif ($userRole->role_type === 'akuntan') {
                $akuntan = \App\Models\Akuntan::where('id_user_role', $userRole->id_user_role)->first();
                if ($akuntan) {
                    $akuntanProfileData = [
                        'nik_akuntan' => $validated['nik_akuntan'] ?? null,
                        'nama_lengkap' => $validated['nama_lengkap'] ?? null,
                        'jabatan' => $validated['jabatan'] ?? null,
                        'pendidikan' => $validated['pendidikan'] ?? null,
                        'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                        'kontak_wa' => $validated['kontak_wa'] ?? null,
                        'alamat_detail' => $validated['alamat_detail'] ?? null,
                        'province_code' => $validated['province_code'] ?? null,
                        'province_name' => $validated['province_name'] ?? null,
                        'regency_code' => $validated['regency_code'] ?? null,
                        'regency_name' => $validated['regency_name'] ?? null,
                        'district_code' => $validated['district_code'] ?? null,
                        'district_name' => $validated['district_name'] ?? null,
                        'village_code' => $validated['village_code'] ?? null,
                        'village_name' => $validated['village_name'] ?? null,
                    ];

                    if ($request->hasFile('foto_diri')) {
                        if ($akuntan->foto_diri && Storage::disk('public')->exists($akuntan->foto_diri)) {
                            Storage::disk('public')->delete($akuntan->foto_diri);
                        }

                        $image = $request->file('foto_diri');
                        $filename = time() . '_' . uniqid() . '.webp';
                        $path = 'dokumen_akuntan/foto_diri/' . $filename;
                        
                        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                        $img = $manager->read($image->getRealPath());
                        if ($img->width() > 1200) {
                            $img->scale(width: 1200);
                        }
                        
                        Storage::disk('public')->put($path, (string) $img->toWebp(80));
                        $akuntanProfileData['foto_diri'] = $path;
                    }

                    $akuntan->update($akuntanProfileData);
                }
            } elseif ($userRole->role_type === 'sarpas') {
                $sarpas = \App\Models\Sarpas::where('id_user_role', $userRole->id_user_role)->first();
                if ($sarpas) {
                    $sarpasProfileData = [
                        'nik_sarpas' => $validated['nik_sarpas'] ?? null,
                        'nama_lengkap' => $validated['nama_lengkap'] ?? null,
                        'jabatan' => $validated['jabatan'] ?? null,
                        'pendidikan' => $validated['pendidikan'] ?? null,
                        'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                        'kontak_wa' => $validated['kontak_wa'] ?? null,
                        'alamat_detail' => $validated['alamat_detail'] ?? null,
                        'province_code' => $validated['province_code'] ?? null,
                        'province_name' => $validated['province_name'] ?? null,
                        'regency_code' => $validated['regency_code'] ?? null,
                        'regency_name' => $validated['regency_name'] ?? null,
                        'district_code' => $validated['district_code'] ?? null,
                        'district_name' => $validated['district_name'] ?? null,
                        'village_code' => $validated['village_code'] ?? null,
                        'village_name' => $validated['village_name'] ?? null,
                    ];

                    if ($request->hasFile('foto_diri')) {
                        if ($sarpas->foto_diri && Storage::disk('public')->exists($sarpas->foto_diri)) {
                            Storage::disk('public')->delete($sarpas->foto_diri);
                        }

                        $image = $request->file('foto_diri');
                        $filename = time() . '_' . uniqid() . '.webp';
                        $path = 'dokumen_sarpas/foto_diri/' . $filename;
                        
                        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                        $img = $manager->read($image->getRealPath());
                        if ($img->width() > 1200) {
                            $img->scale(width: 1200);
                        }
                        
                        Storage::disk('public')->put($path, (string) $img->toWebp(80));
                        $sarpasProfileData['foto_diri'] = $path;
                    }

                    $sarpas->update($sarpasProfileData);
                }
            }

            return redirect()->route('kepala-dapur.users.index', ['dapur' => $dapur->id_dapur])->with('success', 'User berhasil diperbarui.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, Dapur $dapur, User $user)
    {
        Log::info('Destroy User Attempt', [
            'dapur_id' => $dapur->id_dapur,
            'user_id' => $user->id_user,
        ]);

        try {

            $user->load('userRole');

            if (!$user->userRole) {
                Log::warning('Destroy User: User has no role', [
                    'user_id' => $user->id_user,
                    'user_name' => $user->nama
                ]);
                return redirect()->back()->with('error', "User {$user->nama} tidak memiliki role yang ditetapkan.");
            }

            if ($user->userRole->id_dapur != $dapur->id_dapur) {
                Log::warning('Destroy User: User not in this dapur', [
                    'user_id' => $user->id_user,
                    'user_dapur_id' => $user->userRole->id_dapur,
                    'current_dapur_id' => $dapur->id_dapur
                ]);
                return redirect()->back()->with('error', "User {$user->nama} tidak memiliki akses ke dapur ini.");
            }

            if (!in_array($user->userRole->role_type, ['admin_gudang', 'ahli_gizi', 'produksi', 'distributor', 'akuntan', 'sarpas'])) {
                Log::warning('Destroy User: User has wrong role type', [
                    'user_id' => $user->id_user,
                    'role_type' => $user->userRole->role_type,
                    'allowed_roles' => ['admin_gudang', 'ahli_gizi', 'produksi', 'distributor', 'akuntan', 'sarpas']
                ]);
                return redirect()->back()->with('error', "User {$user->nama} bukan admin gudang, ahli gizi, produksi, distributor, akuntan, atau sarpas.");
            }

            if ($user->userRole->role_type === 'admin_gudang' && $user->userRole->adminGudang) {
                $user->userRole->adminGudang->delete();
            } elseif ($user->userRole->role_type === 'ahli_gizi' && $user->userRole->ahliGizi) {
                $user->userRole->ahliGizi->delete();
            } elseif ($user->userRole->role_type === 'produksi' && $user->userRole->produksi) {
                $user->userRole->produksi->delete();
            } elseif ($user->userRole->role_type === 'distributor' && $user->userRole->distributor) {
                $user->userRole->distributor->delete();
            } elseif ($user->userRole->role_type === 'akuntan' && $user->userRole->akuntan) {
                $user->userRole->akuntan->delete();
            } elseif ($user->userRole->role_type === 'sarpas' && $user->userRole->sarpas) {
                $user->userRole->sarpas->delete();
            }

            $user->userRole()->delete();
            $user->delete();

            Log::info('User deleted successfully', [
                'user_id' => $user->id_user,
                'user_name' => $user->nama,
                'dapur_id' => $dapur->id_dapur
            ]);

            return redirect()->route('kepala-dapur.users.index', ['dapur' => $dapur->id_dapur])->with('success', 'User berhasil dihapus.');
        } catch (Exception $e) {
            Log::error('Failed to delete user', [
                'error' => $e->getMessage(),
                'user_id' => $user->id_user ?? 'not_set',
                'dapur_id' => $dapur->id_dapur ?? 'not_set',
            ]);
            return redirect()->back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    public function editKepalaDapur(Request $request)
    {
        try {
            $current_user = User::with('userRole')->find(auth()->id());

            if (!$current_user) {
                Log::error('Current user not found', ['user_id' => auth()->id()]);
                return redirect()->route('dashboard')->with('error', 'User tidak ditemukan.');
            }

            Log::info('User role debug', [
                'user_id' => $current_user->id_user,
                'has_user_role' => $current_user->userRole !== null,
                'user_role_data' => $current_user->userRole ? [
                    'role_type' => $current_user->userRole->role_type,
                    'id_dapur' => $current_user->userRole->id_dapur
                ] : 'null'
            ]);

            if (!$current_user->userRole) {
                Log::error('User has no role assigned', [
                    'user_id' => $current_user->id_user,
                    'user_name' => $current_user->nama
                ]);
                return redirect()->route('dashboard')->with('error', 'User tidak memiliki role yang ditetapkan. Silahkan hubungi administrator.');
            }

            if ($current_user->userRole->role_type !== 'kepala_dapur') {
                Log::warning('User is not kepala dapur', [
                    'user_id' => $current_user->id_user,
                    'role_type' => $current_user->userRole->role_type
                ]);
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses untuk mengedit profil kepala dapur.');
            }

            $dapur = Dapur::find($current_user->userRole->id_dapur);

            if (!$dapur) {
                Log::error('Dapur not found for kepala dapur', [
                    'user_id' => $current_user->id_user,
                    'dapur_id' => $current_user->userRole->id_dapur
                ]);
                return redirect()->route('dashboard')->with('error', 'Data dapur tidak ditemukan. Silahkan hubungi administrator.');
            }

            Log::info('Kepala dapur edit profile access granted', [
                'user_id' => $current_user->id_user,
                'user_name' => $current_user->nama,
                'dapur_id' => $dapur->id_dapur,
                'dapur_name' => $dapur->nama_dapur
            ]);

            return view('kepaladapur.user.edit-kepala-dapur', compact('current_user', 'dapur'));
        } catch (Exception $e) {
            Log::error('Failed to load kepala dapur edit form', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id() ?? 'not_set',
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('dashboard')->with('error', 'Gagal memuat form edit profil: ' . $e->getMessage());
        }
    }

    public function updateKepalaDapur(Request $request)
    {
        try {
            $current_user = User::with('userRole')->find(auth()->id());

            if (!$current_user) {
                return redirect()->route('dashboard')->with('error', 'User tidak ditemukan.');
            }

            if (!$current_user->userRole) {
                return redirect()->route('dashboard')->with('error', 'User tidak memiliki role yang ditetapkan.');
            }

            if ($current_user->userRole->role_type !== 'kepala_dapur') {
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses untuk mengedit profil kepala dapur.');
            }

            $dapur = Dapur::find($current_user->userRole->id_dapur);

            if (!$dapur) {
                return redirect()->route('dashboard')->with('error', 'Data dapur tidak ditemukan.');
            }

            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($current_user->id_user, 'id_user')],
                'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($current_user->id_user, 'id_user')],
                'password' => 'nullable|string|min:8|confirmed',
            ]);

            $updateData = [
                'nama' => $validated['nama'],
                'username' => $validated['username'],
                'email' => $validated['email'],
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            User::where('id_user', $current_user->id_user)->update($updateData);

            Log::info('Kepala dapur profile updated successfully', [
                'user_id' => $current_user->id_user,
                'user_name' => $validated['nama'],
                'dapur_id' => $dapur->id_dapur
            ]);

            return redirect()->route('kepala-dapur.edit-profil')
                ->with('success', 'Profil berhasil diperbarui.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (Exception $e) {
            Log::error('Failed to update kepala dapur profile', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id() ?? 'not_set',
            ]);
            return redirect()->back()->with('error', 'Gagal memperbarui profil: ' . $e->getMessage());
        }
    }
}
