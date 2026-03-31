<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Dapur;
use App\Models\SuperAdmin;
use App\Models\KepalaDapur;
use App\Models\AdminGudang;
use App\Models\AhliGizi;
use App\Models\Produksi;
use App\Models\Distributor;
use App\Models\PenerimaMbg;
use App\Models\Mitra;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $dapur = Dapur::firstOrCreate(
                ['nama_dapur' => 'Dapur Anom'],
                [
                    'alamat' => 'Jl. Anom No. 1, Kota Anom',
                    'status' => 'active',
                ]
            );

            $password = Hash::make('Test1234');

            $roles = [
                'super_admin' => [
                    'username' => 'anomadmin',
                    'nama' => 'Anom Super Admin',
                    'model' => SuperAdmin::class,
                    'needs_dapur' => false,
                ],
                'kepala_dapur' => [
                    'username' => 'anomkepala',
                    'nama' => 'Anom Kepala Dapur',
                    'model' => KepalaDapur::class,
                    'needs_dapur' => true,
                ],
                'admin_gudang' => [
                    'username' => 'anomgudang',
                    'nama' => 'Anom Admin Gudang',
                    'model' => AdminGudang::class,
                    'needs_dapur' => true,
                ],
                'ahli_gizi' => [
                    'username' => 'anomgizi',
                    'nama' => 'Anom Ahli Gizi',
                    'model' => AhliGizi::class,
                    'needs_dapur' => true,
                ],
                'produksi' => [
                    'username' => 'anomproduksi',
                    'nama' => 'Anom Produksi',
                    'model' => Produksi::class,
                    'needs_dapur' => true,
                ],
                'distributor' => [
                    'username' => 'anomdistribusi',
                    'nama' => 'Anom Distributor',
                    'model' => Distributor::class,
                    'needs_dapur' => true,
                ],
                'penerima_mbg' => [
                    'username' => 'anompenerima',
                    'nama' => 'Anom Penerima MBG',
                    'model' => PenerimaMbg::class,
                    'needs_dapur' => true,
                    'extra' => [
                        'status_approval' => 'approved',
                        'jumlah_porsi' => 100,
                        'id_number' => '1234567890',
                        'penanggung_jawab' => 'Anom Penanggung Jawab',
                    ]
                ],
                'mitra' => [
                    'username' => 'anommitra',
                    'nama' => 'Anom Mitra',
                    'model' => Mitra::class,
                    'needs_dapur' => false,
                ],
            ];

            foreach ($roles as $roleType => $data) {
                $user = User::updateOrCreate(
                    ['username' => $data['username']],
                    [
                        'nama' => $data['nama'],
                        'email' => $data['username'] . '@example.com',
                        'password' => $password,
                        'is_active' => true,
                    ]
                );

                $userRole = UserRole::updateOrCreate(
                    ['id_user' => $user->id_user],
                    [
                        'role_type' => $roleType,
                        'id_dapur' => $data['needs_dapur'] ? $dapur->id_dapur : null,
                    ]
                );

                if ($roleType === 'mitra') {
                    $mitra = Mitra::updateOrCreate(
                        ['id_user_role' => $userRole->id_user_role],
                        [
                            'nik_pemilik' => '1234567890123456',
                            'nama_pemilik' => 'Anom Mitra Pemilik',
                            'alamat_detail' => 'Alamat Mitra Anom',
                        ]
                    );
                    
                    $mitra->dapur()->syncWithoutDetaching([$dapur->id_dapur => ['status' => 'approved']]);
                } else {
                    $modelClass = $data['model'];
                    $recordData = ['id_user_role' => $userRole->id_user_role];

                    if ($data['needs_dapur']) {
                        $recordData['id_dapur'] = $dapur->id_dapur;
                    }

                    if (isset($data['extra'])) {
                        $recordData = array_merge($recordData, $data['extra']);
                    }

                    $modelClass::updateOrCreate(
                        ['id_user_role' => $userRole->id_user_role],
                        $recordData
                    );
                }
            }
        });
    }
}
