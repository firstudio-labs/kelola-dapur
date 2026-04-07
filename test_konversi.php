<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::whereHas('userRole', function($q) { $q->where('role', 'ahli_gizi'); })->first();
\Illuminate\Support\Facades\Auth::login($user);

$controller = new \App\Http\Controllers\AhliGizi\TransaksiDapurController();
$menu = \App\Models\MenuMakanan::where('is_active', true)->has('bahanMenu')->first();
if($menu) {
    echo $controller->getMenuDetail($menu)->getContent();
} else {
    echo "No menu found";
}
