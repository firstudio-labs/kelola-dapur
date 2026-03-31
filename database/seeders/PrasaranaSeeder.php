<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriPrasarana;
use App\Models\ItemPrasarana;

class PrasaranaSeeder extends Seeder
{
    
    public function run()
    {
        $data = [
            'Bangunan' => [
                'Ruang Kantor dan Front Office',
                'Ruang Penyimpanan Kering',
                'Ruang Penyimpanan Basah',
                'Alat Pendingin',
                'Dapur Masak',
                'Ruang Pengemasan',
                'Tempat Cuci',
                'Penampungan Sampah',
                'Ruang Parkir'
            ],
            'Alat Masak dan Makan' => [
                'Ompreng',
                'Kompor / Sejenisnya',
                'Alat Dapur',
                'Meja Pengemasan'
            ],
            'Perkantoran' => [
                'Meja Kursi Kantor',
                'Almari Arsip',
                'Komputer dan Printer',
                'ATK'
            ],
            'Keamanan' => [
                'Apar',
                'CCTV'
            ],
            'Kendaraan' => [
                'Mobil Operasional',
                'Mobil Logistik dan Distribusi',
                'Kendaraan Roda 2 dan Roda 3'
            ],
        ];

        foreach ($data as $kategoriName => $items) {
            $kategori = KategoriPrasarana::updateOrCreate(
                ['nama_kategori' => $kategoriName],
                ['is_active' => true, 'is_default' => true]
            );

            foreach ($items as $itemName) {
                ItemPrasarana::updateOrCreate(
                    [
                        'id_kategori' => $kategori->id_kategori,
                        'nama_item' => $itemName
                    ],
                    ['is_active' => true, 'is_default' => true]
                );
            }
        }
    }
}
