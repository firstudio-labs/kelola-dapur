<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the old single-column unique index on period_id if it still exists.
        // The correct constraint is the composite (period_id, cash_account_id) already added
        // by the previous migration 2026_04_08_022022_add_cash_account_id_to_accounting_balances.
        try {
            DB::statement('ALTER TABLE accounting_balances DROP INDEX accounting_balances_period_id_unique');
        } catch (\Exception $e) {
            // Already removed — safe to ignore
        }
    }

    public function down(): void
    {
        // Restore the single-column unique index (not recommended but provided for completeness)
        try {
            DB::statement('ALTER TABLE accounting_balances ADD UNIQUE INDEX accounting_balances_period_id_unique (period_id)');
        } catch (\Exception $e) {}
    }
};
