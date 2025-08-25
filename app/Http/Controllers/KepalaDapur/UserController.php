<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\Dapur;
use App\Models\User;
use App\Models\UserRole;
use App\Models\AdminGudang;
use App\Models\AhliGizi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
                    ->whereIn('role_type', ['admin_gudang', 'ahli_gizi']);
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
            $roles = ['admin_gudang' => 'Admin Gudang', 'ahli_gizi' => 'Ahli Gizi'];

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
                'role_type' => ['required', Rule::in(['admin_gudang', 'ahli_gizi'])],
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
                AdminGudang::create([
                    'id_user_role' => $userRole->id_user_role,
                    'id_dapur' => $dapur->id_dapur,
                ]);
            } elseif ($validated['role_type'] === 'ahli_gizi') {
                AhliGizi::create([
                    'id_user_role' => $userRole->id_user_role,
                    'id_dapur' => $dapur->id_dapur,
                ]);
            }

            return redirect()->route('kepala-dapur.users.index', ['dapur' => $dapur->id_dapur])->with('success', 'User berhasil ditambahkan.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function show(Request $request, $dapurId, $userId)
    {
        Log::info('Show User Attempt - Debug Parameters', [
            'dapurId_parameter' => $dapurId,
            'userId_parameter' => $userId,
            'all_route_parameters' => $request->route()->parameters(),
            'current_dapur_from_request' => $request->current_dapur ? $request->current_dapur->id_dapur : 'not_set',
            'url' => $request->fullUrl()
        ]);

        try {
            $dapur = $request->current_dapur;

            $user = User::where('id_user', $userId)->first();

            if (!$user) {
                Log::warning('User not found', ['user_id' => $userId]);
                return redirect()->back()->with('error', "User dengan ID {$userId} tidak ditemukan.");
            }

            $user->load('userRole');

            if (!$user->userRole) {
                Log::warning('User has no role', [
                    'user_id' => $userId,
                    'user_name' => $user->nama
                ]);
                return redirect()->back()->with('error', "User {$user->nama} tidak memiliki role yang ditetapkan.");
            }

            if ($user->userRole->id_dapur != $dapur->id_dapur) {
                Log::warning('User not in this dapur', [
                    'user_id' => $userId,
                    'user_dapur_id' => $user->userRole->id_dapur,
                    'current_dapur_id' => $dapur->id_dapur
                ]);
                return redirect()->back()->with('error', "User {$user->nama} tidak memiliki akses ke dapur ini.");
            }

            if (!in_array($user->userRole->role_type, ['admin_gudang', 'ahli_gizi'])) {
                Log::warning('User has wrong role type', [
                    'user_id' => $userId,
                    'role_type' => $user->userRole->role_type,
                    'allowed_roles' => ['admin_gudang', 'ahli_gizi']
                ]);
                return redirect()->back()->with('error', "User {$user->nama} bukan admin gudang atau ahli gizi.");
            }

            Log::info('User access granted', [
                'user_id' => $userId,
                'user_name' => $user->nama,
                'role_type' => $user->userRole->role_type,
                'dapur_id' => $dapur->id_dapur
            ]);

            return view('kepaladapur.user.show', compact('user', 'dapur'));
        } catch (Exception $e) {
            Log::error('Failed to show user', [
                'error' => $e->getMessage(),
                'user_id' => $userId ?? 'not_set',
                'dapur_id' => $request->current_dapur->id_dapur ?? 'not_set',
            ]);
            return redirect()->back()->with('error', 'Gagal memuat detail user: ' . $e->getMessage());
        }
    }

    public function edit(Request $request, $dapurId, $userId)
    {
        Log::info('Edit User Attempt', [
            'dapurId_parameter' => $dapurId,
            'userId_parameter' => $userId,
            'dapur_id' => $request->current_dapur->id_dapur ?? 'not_set',
        ]);

        try {
            $dapur = $request->current_dapur;
            $current_user = auth()->user();

            $user = User::where('id_user', $userId)->first();

            if (!$user) {
                Log::warning('Edit User: User not found', [
                    'user_id' => $userId
                ]);
                return redirect()->back()->with('error', "User dengan ID {$userId} tidak ditemukan.");
            }

            $user->load('userRole');

            if (!$user->userRole) {
                Log::warning('Edit User: User has no role', [
                    'user_id' => $userId,
                    'user_name' => $user->nama
                ]);
                return redirect()->back()->with('error', "User {$user->nama} tidak memiliki role yang ditetapkan.");
            }

            if ($user->userRole->id_dapur != $dapur->id_dapur) {
                Log::warning('Edit User: User not in this dapur', [
                    'user_id' => $userId,
                    'user_dapur_id' => $user->userRole->id_dapur,
                    'current_dapur_id' => $dapur->id_dapur
                ]);
                return redirect()->back()->with('error', "User {$user->nama} tidak memiliki akses ke dapur ini.");
            }

            if (!in_array($user->userRole->role_type, ['admin_gudang', 'ahli_gizi'])) {
                Log::warning('Edit User: User has wrong role type', [
                    'user_id' => $userId,
                    'role_type' => $user->userRole->role_type,
                    'allowed_roles' => ['admin_gudang', 'ahli_gizi']
                ]);
                return redirect()->back()->with('error', "User {$user->nama} bukan admin gudang atau ahli gizi.");
            }

            $roles = ['admin_gudang' => 'Admin Gudang', 'ahli_gizi' => 'Ahli Gizi'];

            Log::info('Edit User: Access granted', [
                'user_id' => $userId,
                'user_name' => $user->nama,
                'role_type' => $user->userRole->role_type,
                'dapur_id' => $dapur->id_dapur
            ]);

            return view('kepaladapur.user.edit', compact('user', 'dapur', 'roles', 'current_user'));
        } catch (Exception $e) {
            Log::error('Failed to edit user', [
                'error' => $e->getMessage(),
                'user_id' => $userId ?? 'not_set',
                'dapur_id' => $request->current_dapur->id_dapur ?? 'not_set',
            ]);
            return redirect()->back()->with('error', 'Gagal memuat form edit user: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $dapurId, $userId)
    {
        try {
            $dapur = $request->current_dapur;

            $user = User::where('id_user', $userId)->first();

            if (!$user) {
                return redirect()->back()->with('error', "User dengan ID {$userId} tidak ditemukan.");
            }

            $user->load('userRole');

            if (!$user->userRole) {
                return redirect()->back()->with('error', "User {$user->nama} tidak memiliki role yang ditetapkan.");
            }

            if ($user->userRole->id_dapur != $dapur->id_dapur) {
                return redirect()->back()->with('error', "User {$user->nama} tidak memiliki akses ke dapur ini.");
            }

            if (!in_array($user->userRole->role_type, ['admin_gudang', 'ahli_gizi'])) {
                return redirect()->back()->with('error', "User {$user->nama} bukan admin gudang atau ahli gizi.");
            }

            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id_user, 'id_user')],
                'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id_user, 'id_user')],
                'password' => 'nullable|string|min:8|confirmed',
                'role_type' => ['required', Rule::in(['admin_gudang', 'ahli_gizi'])],
                'is_active' => 'boolean',
            ]);

            $user->update([
                'nama' => $validated['nama'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            if (!empty($validated['password'])) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }

            $userRole = $user->userRole;
            if ($userRole->role_type !== $validated['role_type']) {
                if ($userRole->role_type === 'admin_gudang') {
                    $userRole->adminGudang()->delete();
                } elseif ($userRole->role_type === 'ahli_gizi') {
                    $userRole->ahliGizi()->delete();
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
                }
            }

            return redirect()->route('kepala-dapur.users.index', ['dapur' => $dapur->id_dapur])->with('success', 'User berhasil diperbarui.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $dapurId, $userId)
    {
        Log::info('Destroy User Attempt', [
            'dapurId_parameter' => $dapurId,
            'userId_parameter' => $userId,
            'dapur_id' => $request->current_dapur->id_dapur ?? 'not_set',
        ]);

        try {
            $dapur = $request->current_dapur;

            $user = User::where('id_user', $userId)->first();

            if (!$user) {
                Log::warning('Destroy User: User not found', [
                    'user_id' => $userId
                ]);
                return redirect()->back()->with('error', "User dengan ID {$userId} tidak ditemukan.");
            }

            $user->load('userRole');

            if (!$user->userRole) {
                Log::warning('Destroy User: User has no role', [
                    'user_id' => $userId,
                    'user_name' => $user->nama
                ]);
                return redirect()->back()->with('error', "User {$user->nama} tidak memiliki role yang ditetapkan.");
            }

            if ($user->userRole->id_dapur != $dapur->id_dapur) {
                Log::warning('Destroy User: User not in this dapur', [
                    'user_id' => $userId,
                    'user_dapur_id' => $user->userRole->id_dapur,
                    'current_dapur_id' => $dapur->id_dapur
                ]);
                return redirect()->back()->with('error', "User {$user->nama} tidak memiliki akses ke dapur ini.");
            }

            if (!in_array($user->userRole->role_type, ['admin_gudang', 'ahli_gizi'])) {
                Log::warning('Destroy User: User has wrong role type', [
                    'user_id' => $userId,
                    'role_type' => $user->userRole->role_type,
                    'allowed_roles' => ['admin_gudang', 'ahli_gizi']
                ]);
                return redirect()->back()->with('error', "User {$user->nama} bukan admin gudang atau ahli gizi.");
            }

            if ($user->userRole->role_type === 'admin_gudang') {
                $user->userRole->adminGudang()->delete();
            } elseif ($user->userRole->role_type === 'ahli_gizi') {
                $user->userRole->ahliGizi()->delete();
            }

            $user->userRole()->delete();
            $user->delete();

            Log::info('User deleted successfully', [
                'user_id' => $userId,
                'user_name' => $user->nama,
                'dapur_id' => $dapur->id_dapur
            ]);

            return redirect()->route('kepala-dapur.users.index', ['dapur' => $dapur->id_dapur])->with('success', 'User berhasil dihapus.');
        } catch (Exception $e) {
            Log::error('Failed to delete user', [
                'error' => $e->getMessage(),
                'user_id' => $userId ?? 'not_set',
                'dapur_id' => $request->current_dapur->id_dapur ?? 'not_set',
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
