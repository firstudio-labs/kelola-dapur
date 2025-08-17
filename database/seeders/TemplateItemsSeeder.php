<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplateItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'nama_bahan' => 'Tepung Terigu',
                'satuan' => 'kg',
                'keterangan' => 'Tepung terigu serbaguna untuk berbagai masakan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Beras',
                'satuan' => 'kg',
                'keterangan' => 'Beras putih kualitas premium',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Minyak Goreng',
                'satuan' => 'liter',
                'keterangan' => 'Minyak kelapa sawit untuk menggoreng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Gula Pasir',
                'satuan' => 'kg',
                'keterangan' => 'Gula pasir putih',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Telur',
                'satuan' => 'pcs',
                'keterangan' => 'Telur ayam kampung',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Susu Cair',
                'satuan' => 'liter',
                'keterangan' => 'Susu cair full cream',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Tomat',
                'satuan' => 'kg',
                'keterangan' => 'Tomat segar untuk saus dan salad',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Bawang Merah',
                'satuan' => 'kg',
                'keterangan' => 'Bawang merah lokal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Daging Ayam',
                'satuan' => 'kg',
                'keterangan' => 'Daging ayam potong segar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Saus Tomat',
                'satuan' => 'botol',
                'keterangan' => 'Saus tomat kemasan 250ml',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('template_items')->insert($items);
    }
}
