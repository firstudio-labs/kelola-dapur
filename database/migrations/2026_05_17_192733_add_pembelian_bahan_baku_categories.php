<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Dapur;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $dapurs = Dapur::all();
        $categories = [];

        foreach ($dapurs as $dapur) {
            $categories[] = [
                'id_dapur' => $dapur->id_dapur,
                'name' => 'Pembelian Bahan Baku',
                'type' => 'expense',
                'group' => 'biaya_bahan_baku',
                'is_tax' => false,
                'is_protected' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $categories[] = [
                'id_dapur' => $dapur->id_dapur,
                'name' => 'Pembelian Bahan Baku',
                'type' => 'income',
                'group' => 'dana_bahan_baku',
                'is_tax' => false,
                'is_protected' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($categories)) {
            DB::table('accounting_categories')->insert($categories);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('accounting_categories')
            ->whereIn('name', ['Pembelian Bahan Baku', 'Retur/Pembelian Bahan Baku (Pemasukan)'])
            ->delete();
    }
};
