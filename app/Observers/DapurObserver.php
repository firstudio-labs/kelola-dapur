<?php

namespace App\Observers;

use App\Models\Dapur;
use Database\Seeders\AccountingCategorySeeder;

class DapurObserver
{
    public function created(Dapur $dapur): void
    {
        AccountingCategorySeeder::seedForDapur($dapur->id_dapur);
    }
}
