<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop unique index if exists
        try {
            DB::statement('ALTER TABLE accounting_balances DROP INDEX accounting_balances_period_id_unique');
        } catch (\Exception $e) {
            // Index might not exist or different name
        }

        // 2. Add column if not exists
        if (!Schema::hasColumn('accounting_balances', 'cash_account_id')) {
            Schema::table('accounting_balances', function (Blueprint $table) {
                $table->unsignedBigInteger('cash_account_id')->nullable()->after('period_id');
                $table->foreign('cash_account_id', 'fk_acc_balances_cash_account')->references('id')->on('cash_accounts')->onDelete('cascade');
            });
        }

        // 3. Migrate data
        $balances = DB::table('accounting_balances')->whereNull('cash_account_id')->get();
        foreach ($balances as $balance) {
            $period = DB::table('accounting_periods')->where('id', $balance->period_id)->first();
            if ($period) {
                $firstAccount = DB::table('cash_accounts')->where('id_dapur', $period->id_dapur)->first();
                if ($firstAccount) {
                    DB::table('accounting_balances')
                        ->where('id', $balance->id)
                        ->update(['cash_account_id' => $firstAccount->id]);
                }
            }
        }

        // 4. Add composite unique index if not exists
        try {
            DB::statement('ALTER TABLE accounting_balances ADD UNIQUE INDEX acc_balances_period_cash_unique (period_id, cash_account_id)');
        } catch (\Exception $e) {
            // Already exists or duplicate entry if data migration failed
        }
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE accounting_balances DROP INDEX acc_balances_period_cash_unique');
        } catch (\Exception $e) {}

        if (Schema::hasColumn('accounting_balances', 'cash_account_id')) {
            Schema::table('accounting_balances', function (Blueprint $table) {
                $table->dropForeign('fk_acc_balances_cash_account');
                $table->dropColumn('cash_account_id');
            });
        }

        try {
            DB::statement('ALTER TABLE accounting_balances ADD UNIQUE INDEX accounting_balances_period_id_unique (period_id)');
        } catch (\Exception $e) {}
    }
};
