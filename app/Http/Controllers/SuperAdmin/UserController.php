<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Dapur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function userIndex()
    {
        $users = User::with(['superAdmin', 'kepalaDapur.dapur', 'adminGudang.dapur', 'ahliGizi.dapur'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('superadmin.users.index', compact('users'));
    }

    public function userCreate()
    {
        $dapurList = Dapur::where('status', 'active')->get();
        return view('superadmin.users.create', compact('dapurList'));
    }

    public function userStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:super_admin,kepala_dapur,admin_gudang,ahli_gizi',
            'id_dapur' => 'required_unless:role,super_admin|exists:dapur,id_dapur',
            'is_active' => 'required|boolean',
        ], [
            'nama.required' => 'Nama harus diisi',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'role.required' => 'Role harus dipilih',
            'id_dapur.required_unless' => 'Dapur harus dipilih untuk role selain Super Admin',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => $request->is_active,
        ]);

        // Assign role
        $this->assignUserRole($user, $request->role, $request->id_dapur);

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function userShow(User $user)
    {
        $user->load(['superAdmin', 'kepalaDapur.dapur', 'adminGudang.dapur', 'ahliGizi.dapur']);

        $roles = [];
        if ($user->superAdmin) {
            $roles[] = ['type' => 'Super Admin', 'dapur' => null];
        }
        foreach ($user->kepalaDapur as $kd) {
            $roles[] = ['type' => 'Kepala Dapur', 'dapur' => $kd->dapur];
        }
        foreach ($user->adminGudang as $ag) {
            $roles[] = ['type' => 'Admin Gudang', 'dapur' => $ag->dapur];
        }
        foreach ($user->ahliGizi as $ag) {
            $roles[] = ['type' => 'Ahli Gizi', 'dapur' => $ag->dapur];
        }

        return view('superadmin.users.show', compact('user', 'roles'));
    }

    public function userEdit(User $user)
    {
        $dapurList = Dapur::where('status', 'active')->get();
        $user->load(['superAdmin', 'kepalaDapur', 'adminGudang', 'ahliGizi']);

        return view('superadmin.users.edit', compact('user', 'dapurList'));
    }

    public function userUpdate(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id_user . ',id_user',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id_user . ',id_user',
            'password' => 'nullable|string|min:8',
            'is_active' => 'required|boolean',
        ], [
            'nama.required' => 'Nama harus diisi',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 8 karakter',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'is_active' => $request->is_active,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User berhasil diperbarui');
    }

    public function userDestroy(User $user)
    {
        if ($user->id_user === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        $user->delete();

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User berhasil dihapus');
    }

    public function assignRole(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:super_admin,kepala_dapur,admin_gudang,ahli_gizi',
            'id_dapur' => 'required_unless:role,super_admin|exists:dapur,id_dapur',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $this->assignUserRole($user, $request->role, $request->id_dapur);

        return redirect()->back()->with('success', 'Role berhasil ditambahkan');
    }

    public function removeRole(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'role_type' => 'required|in:super_admin,kepala_dapur,admin_gudang,ahli_gizi',
            'role_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        switch ($request->role_type) {
            case 'super_admin':
                $user->superAdmin()->delete();
                break;
            case 'kepala_dapur':
                $user->kepalaDapur()->where('id_kepala_dapur', $request->role_id)->delete();
                break;
            case 'admin_gudang':
                $user->adminGudang()->where('id_admin_gudang', $request->role_id)->delete();
                break;
            case 'ahli_gizi':
                $user->ahliGizi()->where('id_ahli_gizi', $request->role_id)->delete();
                break;
        }

        return redirect()->back()->with('success', 'Role berhasil dihapus');
    }

    private function assignUserRole(User $user, string $role, ?int $dapurId)
    {
        switch ($role) {
            case 'super_admin':
                if (!$user->superAdmin) {
                    $user->superAdmin()->create([]);
                }
                break;
            case 'kepala_dapur':
                if (!$user->kepalaDapur()->where('id_dapur', $dapurId)->exists()) {
                    $user->kepalaDapur()->create(['id_dapur' => $dapurId]);
                }
                break;
            case 'admin_gudang':
                if (!$user->adminGudang()->where('id_dapur', $dapurId)->exists()) {
                    $user->adminGudang()->create(['id_dapur' => $dapurId]);
                }
                break;
            case 'ahli_gizi':
                if (!$user->ahliGizi()->where('id_dapur', $dapurId)->exists()) {
                    $user->ahliGizi()->create(['id_dapur' => $dapurId]);
                }
                break;
        }
    }
}
