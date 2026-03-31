<?php

namespace Database\Seeders;

use App\Models\SuperAdmin;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
        $this->call([
            TemplateItemsSeeder::class,
            SuperAdminSeeder::class,
            RoleUserSeeder::class,
            PrasaranaSeeder::class,
            RoleUserSeeder::class,
        ]);
    }
}
