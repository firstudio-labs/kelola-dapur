<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserRole;
use App\Models\SuperAdmin;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            // Create User
            $superAdminUser = User::create([
                'nama' => 'Super Admin Anom',
                'username' => 'anomadmin',
                'email' => 'anomadmin@example.com',
                'password' => Hash::make('test1234'),
                'is_active' => true,
            ]);

            // Create User Role
            $userRole = UserRole::create([
                'id_user' => $superAdminUser->id_user,
                'role_type' => 'super_admin',
                'id_dapur' => null,
            ]);

            // Create Super Admin
            SuperAdmin::create([
                'id_user_role' => $userRole->id_user_role,
            ]);
        });
    }
}
