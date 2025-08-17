<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WilayahController extends Controller
{
    private const API_BASE_URL = 'https://emsifa.github.io/api-wilayah-indonesia/api';
    private const BACKUP_API_BASE_URL = 'https://wilayah.id/api';
    private const CACHE_DURATION = 24 * 60 * 60;

    public function getProvinces()
    {
        try {
            $provinces = Cache::remember('wilayah.provinces', self::CACHE_DURATION, function () {
                $response = Http::timeout(15)->get(self::API_BASE_URL . '/provinces.json');

                if ($response->successful()) {
                    return $response->json();
                }

                $backupResponse = Http::timeout(15)->get(self::BACKUP_API_BASE_URL . '/provinces.json');
                if ($backupResponse->successful()) {
                    $data = $backupResponse->json();
                    return $data['data'] ?? $data;
                }

                return $this->getFallbackProvinces();
            });

            return response()->json([
                'success' => true,
                'data' => $provinces
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching provinces', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => true,
                'data' => $this->getFallbackProvinces()
            ]);
        }
    }

    public function getRegencies($provinceId)
    {
        try {
            $regencies = Cache::remember("wilayah.regencies.{$provinceId}", self::CACHE_DURATION, function () use ($provinceId) {
                $response = Http::timeout(15)->get(self::API_BASE_URL . "/regencies/{$provinceId}.json");

                if ($response->successful()) {
                    return $response->json();
                }

                $backupResponse = Http::timeout(15)->get(self::BACKUP_API_BASE_URL . "/regencies/{$provinceId}.json");
                if ($backupResponse->successful()) {
                    $data = $backupResponse->json();
                    return $data['data'] ?? $data;
                }

                return [];
            });

            return response()->json([
                'success' => true,
                'data' => $regencies
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching regencies', [
                'province_id' => $provinceId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data kabupaten/kota',
                'data' => []
            ]);
        }
    }

    public function getDistricts($regencyId)
    {
        try {
            $districts = Cache::remember("wilayah.districts.{$regencyId}", self::CACHE_DURATION, function () use ($regencyId) {
                $response = Http::timeout(15)->get(self::API_BASE_URL . "/districts/{$regencyId}.json");

                if ($response->successful()) {
                    return $response->json();
                }

                $backupResponse = Http::timeout(15)->get(self::BACKUP_API_BASE_URL . "/districts/{$regencyId}.json");
                if ($backupResponse->successful()) {
                    $data = $backupResponse->json();
                    return $data['data'] ?? $data;
                }

                return [];
            });

            return response()->json([
                'success' => true,
                'data' => $districts
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching districts', [
                'regency_id' => $regencyId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data kecamatan',
                'data' => []
            ]);
        }
    }

    public function getVillages($districtId)
    {
        try {
            $villages = Cache::remember("wilayah.villages.{$districtId}", self::CACHE_DURATION, function () use ($districtId) {
                $response = Http::timeout(15)->get(self::API_BASE_URL . "/villages/{$districtId}.json");

                if ($response->successful()) {
                    return $response->json();
                }

                $backupResponse = Http::timeout(15)->get(self::BACKUP_API_BASE_URL . "/villages/{$districtId}.json");
                if ($backupResponse->successful()) {
                    $data = $backupResponse->json();
                    return $data['data'] ?? $data;
                }

                return [];
            });

            return response()->json([
                'success' => true,
                'data' => $villages
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching villages', [
                'district_id' => $districtId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data kelurahan/desa',
                'data' => []
            ]);
        }
    }

    public function clearCache()
    {
        try {
            Cache::forget('wilayah.provinces');

            $cacheKeys = [];
            for ($i = 11; $i <= 96; $i++) {
                $cacheKeys[] = "wilayah.regencies.{$i}";
            }

            foreach ($cacheKeys as $key) {
                Cache::forget($key);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cache berhasil dibersihkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membersihkan cache'
            ]);
        }
    }

    private function getFallbackProvinces(): array
    {
        return [
            ['id' => '11', 'name' => 'Aceh'],
            ['id' => '12', 'name' => 'Sumatera Utara'],
            ['id' => '13', 'name' => 'Sumatera Barat'],
            ['id' => '14', 'name' => 'Riau'],
            ['id' => '15', 'name' => 'Jambi'],
            ['id' => '16', 'name' => 'Sumatera Selatan'],
            ['id' => '17', 'name' => 'Bengkulu'],
            ['id' => '18', 'name' => 'Lampung'],
            ['id' => '19', 'name' => 'Kepulauan Bangka Belitung'],
            ['id' => '21', 'name' => 'Kepulauan Riau'],
            ['id' => '31', 'name' => 'DKI Jakarta'],
            ['id' => '32', 'name' => 'Jawa Barat'],
            ['id' => '33', 'name' => 'Jawa Tengah'],
            ['id' => '34', 'name' => 'Daerah Istimewa Yogyakarta'],
            ['id' => '35', 'name' => 'Jawa Timur'],
            ['id' => '36', 'name' => 'Banten'],
            ['id' => '51', 'name' => 'Bali'],
            ['id' => '52', 'name' => 'Nusa Tenggara Barat'],
            ['id' => '53', 'name' => 'Nusa Tenggara Timur'],
            ['id' => '61', 'name' => 'Kalimantan Barat'],
            ['id' => '62', 'name' => 'Kalimantan Tengah'],
            ['id' => '63', 'name' => 'Kalimantan Selatan'],
            ['id' => '64', 'name' => 'Kalimantan Timur'],
            ['id' => '65', 'name' => 'Kalimantan Utara'],
            ['id' => '71', 'name' => 'Sulawesi Utara'],
            ['id' => '72', 'name' => 'Sulawesi Tengah'],
            ['id' => '73', 'name' => 'Sulawesi Selatan'],
            ['id' => '74', 'name' => 'Sulawesi Tenggara'],
            ['id' => '75', 'name' => 'Gorontalo'],
            ['id' => '76', 'name' => 'Sulawesi Barat'],
            ['id' => '81', 'name' => 'Maluku'],
            ['id' => '82', 'name' => 'Maluku Utara'],
            ['id' => '91', 'name' => 'Papua'],
            ['id' => '92', 'name' => 'Papua Barat'],
            ['id' => '93', 'name' => 'Papua Selatan'],
            ['id' => '94', 'name' => 'Papua Tengah'],
            ['id' => '95', 'name' => 'Papua Pegunungan'],
            ['id' => '96', 'name' => 'Papua Barat Daya'],
        ];
    }
}
