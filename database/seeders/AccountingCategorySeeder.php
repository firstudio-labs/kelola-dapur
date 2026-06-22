<?php

namespace Database\Seeders;

use App\Models\AccountingCategory;
use App\Models\Dapur;
use Illuminate\Database\Seeder;

class AccountingCategorySeeder extends Seeder
{
    public static function defaultCategories(): array
    {
        return [
            [
                'name'         => 'Pembelian Bahan Baku',
                'type'         => 'expense',
                'group'        => 'biaya_bahan_baku',
                'is_tax'       => false,
                'is_protected' => true,
            ],
            [
                'name'         => 'Pembelian Bahan Baku',
                'type'         => 'income',
                'group'        => 'dana_bahan_baku',
                'is_tax'       => false,
                'is_protected' => true,
            ],
        ];
    }

    public function run(): void
    {
        foreach (Dapur::all() as $dapur) {
            self::seedForDapur($dapur->id_dapur);
        }
    }

    public static function seedForDapur(int $dapurId): void
    {
        foreach (self::defaultCategories() as $category) {
            AccountingCategory::firstOrCreate(
                [
                    'id_dapur' => $dapurId,
                    'group'    => $category['group'],
                    'type'     => $category['type'],
                ],
                array_merge($category, ['id_dapur' => $dapurId])
            );
        }
    }
}
