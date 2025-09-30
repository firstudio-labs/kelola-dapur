<?php

use App\Http\Controllers\KepalaDapur\KepalaDapurController;
use App\Http\Controllers\AdminGudang\AdminGudangController;
use App\Http\Controllers\AdminGudang\StockItemController as AdminGudangStockItemController;
use App\Http\Controllers\AhliGizi\AhliGiziController;
use App\Http\Controllers\AhliGizi\MenuMakananController as AhliGiziMenuMakananController;
use App\Http\Controllers\AhliGizi\TransaksiDapurController as AhliGiziTransaksiDapurController;
use App\Http\Controllers\AhliGizi\LaporanController as AhliGiziLaporanController;
use App\Http\Controllers\Api\WilayahController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\DashboardController;
use App\Http\Controllers\KepalaDapur\MenuMakananController as KepalaDapurMenuMakananController;
use App\Http\Controllers\KepalaDapur\TemplateItemController as KepalaDapurTemplateItemController;
use App\Http\Controllers\KepalaDapur\UserController as KepalaDapurUserController;
use App\Http\Controllers\KepalaDapur\LaporanKekuranganStockController as KepalaDapurLaporanKekuranganStockController;
use App\Http\Controllers\KepalaDapur\ApprovalStockItemController as KepalaDapurApprovalStockItemController;
use App\Http\Controllers\KepalaDapur\ApprovalTransaksiController as KepalaDapurApprovalTransaksiController;
use App\Http\Controllers\KepalaDapur\StockItemController;
use App\Http\Controllers\KepalaDapur\SubscriptionController;
// use App\Http\Controllers\SuperAdmin\ApprovalStockItemController;
use App\Http\Controllers\SuperAdmin\BahanMenuController;
use App\Http\Controllers\SuperAdmin\DapurController;
use App\Http\Controllers\SuperAdmin\MenuMakananController;
use App\Http\Controllers\SuperAdmin\PromoCodeController;
use App\Http\Controllers\SuperAdmin\SubscriptionPackageController;
use App\Http\Controllers\SuperAdmin\SubscriptionRequestController;
// use App\Http\Controllers\SuperAdmin\StockItemController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\TemplateItemController;
use Illuminate\Support\Facades\Auth;
// use App\Http\Controllers\SuperAdmin\UserController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    // Ambil paket subscription yang aktif
    $subscriptionPackages = \App\Models\SubscriptionPackage::where('is_active', true)
        ->orderBy('harga', 'asc')
        ->get();

    return view('welcome.index', compact('subscriptionPackages'));
})->name('welcome');

Route::get('/welcome', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    // Ambil paket subscription yang aktif
    $subscriptionPackages = \App\Models\SubscriptionPackage::where('is_active', true)
        ->orderBy('harga', 'asc')
        ->get();

    return view('welcome.index', compact('subscriptionPackages'));
})->name('welcome');

// Guest
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Register
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// API
Route::prefix('api/wilayah')->name('api.wilayah.')->group(function () {
    Route::get('/provinces', [WilayahController::class, 'getProvinces'])->name('provinces');
    Route::get('/regencies/{provinceId}', [WilayahController::class, 'getRegencies'])->name('regencies');
    Route::get('/districts/{regencyId}', [WilayahController::class, 'getDistricts'])->name('districts');
    Route::get('/villages/{districtId}', [WilayahController::class, 'getVillages'])->name('villages');
    Route::post('/clear-cache', [WilayahController::class, 'clearCache'])->name('clear-cache');
});

// Authenticated
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/switch/{dapur}', [DashboardController::class, 'switchDapur'])->name('dashboard.switch-dapur');

    // Redirect dashboard
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/api/template-items/search', function (Request $request) {
        $search = $request->get('search', '');
        $templates = \App\Models\TemplateItem::where('nama_bahan', 'like', '%' . $search . '%')
            ->where('is_active', true)
            ->select('id_template_item', 'nama_bahan', 'satuan')
            ->limit(10)
            ->get();

        return response()->json($templates);
    })->name('api.template-items.search');

    Route::get('/api/stock-info/{dapur}/{templateItem}', function ($dapurId, $templateItemId) {
        $stockItem = \App\Models\StockItem::where('id_dapur', $dapurId)
            ->where('id_template_item', $templateItemId)
            ->first();

        return response()->json([
            'stock_tersedia' => $stockItem ? $stockItem->jumlah_stock : 0,
            'satuan' => $stockItem ? $stockItem->templateItem->satuan : '-',
            'status' => $stockItem ? 'available' : 'not_found'
        ]);
    })->name('api.stock-info');


    Route::get('/api/dapur/options', function (Request $request) {
        $search = $request->get('search', '');

        $dapurs = \App\Models\Dapur::where('status', 'active')
            ->when($search, function ($query, $search) {
                return $query->where('nama_dapur', 'like', '%' . $search . '%');
            })
            ->select('id_dapur', 'nama_dapur')
            ->orderBy('nama_dapur', 'asc')
            ->limit(20)
            ->get();

        return response()->json($dapurs);
    })->name('api.dapur.options');

    // Route untuk mendapatkan statistik menu per dapur
    Route::get('/api/menu-makanan/statistics', function (Request $request) {
        $dapurId = $request->get('dapur_id');

        $stats = \App\Models\MenuMakanan::getStatsByDapur($dapurId);

        return response()->json($stats);
    })->name('api.menu-makanan.statistics');

    // Route untuk mendapatkan menu berdasarkan dapur (AJAX)
    Route::get('/api/menu-makanan/by-dapur/{dapur}', function ($dapurId) {
        $menus = \App\Models\MenuMakanan::getMenusByDapur($dapurId, 50);

        return response()->json([
            'success' => true,
            'data' => $menus->map(function ($menu) {
                return [
                    'id' => $menu->id_menu,
                    'nama_menu' => $menu->nama_menu,
                    'kategori' => $menu->kategori,
                    'is_active' => $menu->is_active,
                    'gambar_url' => $menu->gambar_url,
                    'total_bahan' => $menu->bahanMenu->count(),
                    'created_at' => $menu->created_at->format('d M Y')
                ];
            })
        ]);
    })->name('api.menu-makanan.by-dapur');
    // Kepala Dapur Dashboard
    // Route::middleware(['role:kepala_dapur', 'dapur.access:kepala_dapur'])
    //     ->get('/kepala-dapur/dashboard/{dapur}', [KepalaDapurController::class, 'dashboard'])
    //     ->name('kepala-dapur.dashboard');

    // Admin Gudang Dashboard
    // Route::middleware(['role:admin_gudang', 'dapur.access:admin_gudang'])
    //     ->get('/admin-gudang/dashboard/{dapur}', [AdminGudangController::class, 'dashboard'])
    //     ->name('admin-gudang.dashboard');

    // Ahli Gizi Dashboard
    // Route::middleware(['role:ahli_gizi', 'dapur.access:ahli_gizi'])
    //     ->get('/ahli-gizi/dashboard/{dapur}', [AhliGiziController::class, 'dashboard'])
    //     ->name('ahli-gizi.dashboard');
});


//========== Super Admin Only Routes ==========
Route::middleware(['auth', 'super.admin.only'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

        // Dapur Management
        Route::prefix('dapur')->name('dapur.')->group(function () {
            Route::get('/', [DapurController::class, 'dapurIndex'])->name('index');
            Route::get('/create', [DapurController::class, 'dapurCreate'])->name('create');
            Route::post('/', [DapurController::class, 'dapurStore'])->name('store');
            Route::get('/{dapur}', [DapurController::class, 'dapurShow'])->name('show');
            Route::get('/{dapur}/edit', [DapurController::class, 'dapurEdit'])->name('edit');
            Route::put('/{dapur}', [DapurController::class, 'dapurUpdate'])->name('update');
            Route::delete('/{dapur}', [DapurController::class, 'dapurDestroy'])->name('destroy');
        });

        // User Management
        // Route::prefix('users')->name('users.')->group(function () {
        //     Route::get('/', [UserController::class, 'userIndex'])->name('index');
        //     Route::get('/create', [UserController::class, 'userCreate'])->name('create');
        //     Route::post('/', [UserController::class, 'userStore'])->name('store');
        //     Route::get('/{user}', [UserController::class, 'userShow'])->name('show');
        //     Route::get('/{user}/edit', [UserController::class, 'userEdit'])->name('edit');
        //     Route::put('/{user}', [UserController::class, 'userUpdate'])->name('update');
        //     Route::delete('/{user}', [UserController::class, 'userDestroy'])->name('destroy');
        //     // Role Assignment
        //     Route::post('/{user}/assign-role', [SuperAdminController::class, 'assignRole'])->name('assign-role');
        //     Route::delete('/{user}/remove-role', [SuperAdminController::class, 'removeRole'])->name('remove-role');
        // });

        // Template Items
        Route::prefix('template-items')->name('template-items.')->group(function () {
            Route::get('/', [TemplateItemController::class, 'index'])->name('index');
            Route::get('/create', [TemplateItemController::class, 'create'])->name('create');
            Route::post('/', [TemplateItemController::class, 'store'])->name('store');
            Route::get('/{templateItem}/edit', [TemplateItemController::class, 'edit'])->name('edit');
            Route::put('/{templateItem}', [TemplateItemController::class, 'update'])->name('update');
            Route::get('/{templateItem}', [TemplateItemController::class, 'show'])->name('show');
            Route::delete('/{templateItem}', [TemplateItemController::class, 'destroy'])->name('destroy');
        });

        // Menu Makanan
        Route::prefix('menu-makanan')->name('menu-makanan.')->group(function () {
            Route::get('/', [MenuMakananController::class, 'index'])->name('index');
            Route::get('/create', [MenuMakananController::class, 'create'])->name('create');
            Route::post('/', [MenuMakananController::class, 'store'])->name('store');
            Route::get('/{menuMakanan}', [MenuMakananController::class, 'show'])->name('show');
            Route::get('/{menuMakanan}/edit', [MenuMakananController::class, 'edit'])->name('edit');
            Route::put('/{menuMakanan}', [MenuMakananController::class, 'update'])->name('update');
            Route::patch('/{menuMakanan}/toggle-status', [MenuMakananController::class, 'toggleStatus'])->name('toggleStatus');
            Route::delete('/{menuMakanan}', [MenuMakananController::class, 'destroy'])->name('destroy');
            Route::post('/{menuMakanan}/check-stock', [MenuMakananController::class, 'checkStock'])->name('check-stock');
            Route::get('/active-menus', [MenuMakananController::class, 'getActiveMenus'])->name('active-menus');
            Route::get('/{menu}/menu-details', [MenuMakananController::class, 'getMenuDetails'])->name('menu-details');
            Route::get('/{menu}/ingredient-details', [MenuMakananController::class, 'getIngredientDetails'])->name('ingredient-details');
        });
        Route::prefix('bahan-menu')->name('bahan-menu.')->group(function () {
            Route::get('/create', [BahanMenuController::class, 'create'])->name('create');
            Route::post('/', [BahanMenuController::class, 'store'])->name('store');
            Route::get('/{bahanMenu}/edit', [BahanMenuController::class, 'edit'])->name('edit');
            Route::put('/{bahanMenu}', [BahanMenuController::class, 'update'])->name('update');
            Route::delete('/{bahanMenu}', [BahanMenuController::class, 'destroy'])->name('destroy');
            Route::put('/menu/{menu}/bulk-update', [BahanMenuController::class, 'bulkUpdate'])->name('bulk-update');
        });

        Route::prefix('subscription-packages')->name('subscription-packages.')->group(function () {
            Route::get('/', [SubscriptionPackageController::class, 'index'])->name('index');
            Route::get('/create', [SubscriptionPackageController::class, 'create'])->name('create');
            Route::post('/', [SubscriptionPackageController::class, 'store'])->name('store');
            Route::get('/{subscriptionPackage}', [SubscriptionPackageController::class, 'show'])->name('show');
            Route::get('/{subscriptionPackage}/edit', [SubscriptionPackageController::class, 'edit'])->name('edit');
            Route::put('/{subscriptionPackage}', [SubscriptionPackageController::class, 'update'])->name('update');
            Route::delete('/{subscriptionPackage}', [SubscriptionPackageController::class, 'destroy'])->name('destroy');
            Route::patch('/{subscriptionPackage}/toggle-status', [SubscriptionPackageController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Promo Codes
        Route::prefix('promo-codes')->name('promo-codes.')->group(function () {
            Route::get('/', [PromoCodeController::class, 'index'])->name('index');
            Route::get('/create', [PromoCodeController::class, 'create'])->name('create');
            Route::post('/', [PromoCodeController::class, 'store'])->name('store');
            Route::get('/{promoCode}', [PromoCodeController::class, 'show'])->name('show');
            Route::get('/{promoCode}/edit', [PromoCodeController::class, 'edit'])->name('edit');
            Route::put('/{promoCode}', [PromoCodeController::class, 'update'])->name('update');
            Route::delete('/{promoCode}', [PromoCodeController::class, 'destroy'])->name('destroy');
            Route::patch('/{promoCode}/toggle-status', [PromoCodeController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Subscription Requests (Approval)
        Route::prefix('subscription-requests')->name('subscription-requests.')->group(function () {
            Route::get('/', [SubscriptionRequestController::class, 'index'])->name('index');
            Route::get('/pending', [SubscriptionRequestController::class, 'pending'])->name('pending');
            Route::get('/approved', [SubscriptionRequestController::class, 'approved'])->name('approved');
            Route::get('/rejected', [SubscriptionRequestController::class, 'rejected'])->name('rejected');
            Route::get('/{subscriptionRequest}', [SubscriptionRequestController::class, 'show'])->name('show');
            Route::post('/{subscriptionRequest}/approve', [SubscriptionRequestController::class, 'approve'])->name('approve');
            Route::post('/{subscriptionRequest}/reject', [SubscriptionRequestController::class, 'reject'])->name('reject');
            Route::post('/bulk-action', [SubscriptionRequestController::class, 'bulkAction'])->name('bulk-action');
        });

        // API untuk promo code validation
        Route::post('/api/validate-promo', [PromoCodeController::class, 'validatePromo'])->name('api.validate-promo');

        // Route::prefix('stock-items')->name('stock-items.')->group(function () {
        //     Route::get('/', [StockItemController::class, 'index'])->name('index');
        //     Route::get('/create', [StockItemController::class, 'create'])->name('create');
        //     Route::post('/', [StockItemController::class, 'store'])->name('store');
        //     Route::get('/{stockItem}', [StockItemController::class, 'show'])->name('show');
        //     Route::get('/{stockItem}/edit', [StockItemController::class, 'edit'])->name('edit');
        //     Route::put('/{stockItem}', [StockItemController::class, 'update'])->name('update');
        //     Route::delete('/{stockItem}', [StockItemController::class, 'destroy'])->name('destroy');
        // });

        // Route::prefix('approval')->name('approvals.')->group(function () {
        //     Route::get('/', [ApprovalStockItemController::class, 'index'])->name('index');
        //     Route::get('/create', [ApprovalStockItemController::class, 'create'])->name('create');
        //     Route::post('/', [ApprovalStockItemController::class, 'store'])->name('store');
        //     Route::get('/{approvalStockItem}', [ApprovalStockItemController::class, 'show'])->name('show');
        //     Route::get('/{approvalStockItem}/edit', [ApprovalStockItemController::class, 'edit'])->name('edit');
        //     Route::put('/{approvalStockItem}', [ApprovalStockItemController::class, 'update'])->name('update');
        //     Route::delete('/{approvalStockItem}', [ApprovalStockItemController::class, 'destroy'])->name('destroy');
        //     Route::post('/{approvalStockItem}/approve', [ApprovalStockItemController::class, 'approve'])->name('approve');
        //     Route::post('/{approvalStockItem}/reject', [ApprovalStockItemController::class, 'reject'])->name('reject');
        // });
    });


//========== Kepala Dapur Dengan ID Dapur Routes ==========
Route::middleware(['auth', 'dapur.access:kepala_dapur', 'check.subscription'])
    ->prefix('kepala-dapur/dapur/{dapur}')
    ->name('kepala-dapur.')
    ->group(function () {

        // Dashboard - Always accessible
        Route::get('/dashboard', [KepalaDapurController::class, 'dashboard'])->name('dashboard');

        // Stok
        Route::prefix('stock')->name('stock.')->group(function () {
            Route::get('/', [StockItemController::class, 'index'])->name('index');
        });

        // Invoice
        Route::get('/subscription/{subscriptionRequest}/invoice', [SubscriptionController::class, 'invoice'])
            ->name('subscription.invoice');

        // Users Management - Requires active subscription
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [KepalaDapurUserController::class, 'index'])->name('index');
            Route::get('/create', [KepalaDapurUserController::class, 'create'])->name('create');
            Route::post('/', [KepalaDapurUserController::class, 'store'])->name('store');
            Route::get('/{user}', [KepalaDapurUserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [KepalaDapurUserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [KepalaDapurUserController::class, 'update'])->name('update');
            Route::delete('/{user}', [KepalaDapurUserController::class, 'destroy'])->name('destroy');
            Route::get('/edit-kepala-dapur/', [KepalaDapurUserController::class, 'editKepalaDapur'])->name('edit-kepala-dapur');
            Route::put('/edit-kepala-dapur/', [KepalaDapurUserController::class, 'updateKepalaDapur'])->name('update-kepala-dapur');
        });

        // Approval Stock Item - Requires active subscription
        Route::prefix('approvals')->name('approvals.')->group(function () {
            Route::get('/', [KepalaDapurApprovalStockItemController::class, 'index'])->name('index');
            Route::get('/{approval}', [KepalaDapurApprovalStockItemController::class, 'show'])->name('show');
            Route::post('/{approval}/approve', [KepalaDapurApprovalStockItemController::class, 'approve'])->name('approve');
            Route::post('/{approval}/reject', [KepalaDapurApprovalStockItemController::class, 'reject'])->name('reject');
            Route::post('/bulk-action', [KepalaDapurApprovalStockItemController::class, 'bulkAction'])->name('bulk-action');
        });

        // Approval Transaksi
        Route::prefix('approval-transaksi')->name('approval-transaksi.')->group(function () {
            Route::get('/', [KepalaDapurApprovalTransaksiController::class, 'index'])->name('index');
            Route::get('/{approval}', [KepalaDapurApprovalTransaksiController::class, 'show'])->name('show');
            Route::post('/{approval}/setujui', [KepalaDapurApprovalTransaksiController::class, 'approve'])->name('approve');
            Route::post('/{approval}/tolak', [KepalaDapurApprovalTransaksiController::class, 'reject'])->name('reject');
            Route::post('/bulk-action', [KepalaDapurApprovalTransaksiController::class, 'bulkAction'])->name('bulk-action');
        });

        // Subscription Management - Always accessible
        Route::prefix('subscription')->name('subscription.')->group(function () {
            Route::get('/', [SubscriptionController::class, 'index'])->name('index');
            Route::get('/create', [SubscriptionController::class, 'create'])->name('create');
            Route::get('/choose-package', [SubscriptionController::class, 'choosePackage'])->name('choose-package');
            Route::post('/process-payment', [SubscriptionController::class, 'processPayment'])->name('process-payment');
            Route::post('/calculate-price', [SubscriptionController::class, 'calculatePrice'])->name('calculate-price');
            Route::get('/{subscriptionRequest}', [SubscriptionController::class, 'show'])->name('show');
            Route::delete('/{subscriptionRequest}', [SubscriptionController::class, 'cancel'])->name('cancel');
        });
    });



//========== Admin Gudang Dengan ID Dapur Routes ==========
Route::middleware(['auth', 'dapur.access:admin_gudang', 'check.subscription'])
    ->prefix('dapur/{dapur}')
    ->name('admin-gudang.')
    ->group(function () {

        // Dashboard - Always accessible
        Route::get('/dashboard', [AdminGudangController::class, 'dashboard'])->name('dashboard');

        // Stock - Requires active subscription
        Route::prefix('stock')->name('stock.')->group(function () {
            Route::get('/', [AdminGudangStockItemController::class, 'index'])->name('index');
            Route::get('/{stockItem}', [AdminGudangStockItemController::class, 'show'])->name('show');
            Route::post('/{stockItem}/request', [AdminGudangStockItemController::class, 'requestStock'])->name('request');
            Route::get('/export/csv', [AdminGudangStockItemController::class, 'export'])->name('export');
        });
    });



//========== Ahli Gizi Dengan ID Dapur Routes ==========
Route::middleware(['auth', 'dapur.access:ahli_gizi', 'check.subscription'])
    ->prefix('dapur/{dapur}')
    ->name('ahli-gizi.')
    ->group(function () {
        // Dashboard and routes will be handled by check.subscription middleware
    });


//TODO ========== General Role Check Kepala Dapur Routes  ==========
Route::middleware(['auth', 'role:kepala_dapur', 'check.subscription'])->prefix('kepala-dapur')->name('kepala-dapur.')->group(function () {

    // Template Items - Requires active subscription
    Route::prefix('template-items')->name('template-items.')->group(function () {
        Route::get('/', [KepalaDapurTemplateItemController::class, 'index'])->name('index');
        Route::get('/create', [KepalaDapurTemplateItemController::class, 'create'])->name('create');
        Route::post('/', [KepalaDapurTemplateItemController::class, 'store'])->name('store');
        Route::get('/{templateItem}', [KepalaDapurTemplateItemController::class, 'show'])->name('show');
        Route::get('/{templateItem}/edit', [KepalaDapurTemplateItemController::class, 'edit'])->name('edit');
        Route::put('/{templateItem}', [KepalaDapurTemplateItemController::class, 'update'])->name('update');
        Route::delete('/{templateItem}', [KepalaDapurTemplateItemController::class, 'destroy'])->name('destroy');
        Route::get('/search', [KepalaDapurTemplateItemController::class, 'getTemplateItems'])->name('search');
    });

    // Menu Makanan - Requires active subscription
    Route::prefix('menu-makanan')->name('menu-makanan.')->group(function () {
        Route::get('/', [KepalaDapurMenuMakananController::class, 'index'])->name('index');
        Route::get('/{menuMakanan}', [KepalaDapurMenuMakananController::class, 'show'])->name('show');
        Route::get('/active-menus', [KepalaDapurMenuMakananController::class, 'getActiveMenus'])->name('active-menus');
        Route::post('/{menuMakanan}/check-stock', [KepalaDapurMenuMakananController::class, 'checkStock'])->name('check-stock');
    });



    // Laporan Kekurangan Stock - Requires active subscription
    Route::prefix('laporan-kekurangan')->name('laporan-kekurangan.')->group(function () {
        Route::get('/', [KepalaDapurLaporanKekuranganStockController::class, 'index'])->name('index');
        Route::get('/{transaksi}', [KepalaDapurLaporanKekuranganStockController::class, 'show'])->name('show');
        Route::post('/{transaksi}/selesaikan', [KepalaDapurLaporanKekuranganStockController::class, 'resolve'])->name('resolve');
        Route::post('/bulk-selesaikan', [KepalaDapurLaporanKekuranganStockController::class, 'bulkResolve'])->name('bulk-resolve');
        Route::get('/ringkasan/bulanan', [KepalaDapurLaporanKekuranganStockController::class, 'summary'])->name('summary');
        Route::get('/laporan-kekurangan/{transaksi}/export-pdf', [KepalaDapurLaporanKekuranganStockController::class, 'exportKekuranganPdf'])->name('export-pdf');
        Route::get('/laporan-kekurangan/{transaksi}/export-csv', [KepalaDapurLaporanKekuranganStockController::class, 'exportKekuranganCsv'])->name('export-csv');
    });



    // Profile routes - Always accessible
    Route::get('/edit-profil', [KepalaDapurUserController::class, 'editKepalaDapur'])->name('edit-profil');
    Route::put('/edit-profil', [KepalaDapurUserController::class, 'updateKepalaDapur'])->name('update-profil');

    // Route::get('/shortage-reports', [KepalaDapurApprovalTransaksiController::class, 'shortageReports'])->name('shortage-reports');
    // Route::post('/shortage-reports/{report}/resolve', [KepalaDapurApprovalTransaksiController::class, 'resolveShortage'])->name('resolve-shortage');
});

//========== General Role Check Routes ==========
Route::middleware(['auth', 'role:admin_gudang'])->group(function () {
    // Route::get('/admin-gudang/dashboard', [AdminGudangController::class, 'dashboard']);
});


//========== General Role Check Routes ==========
Route::middleware(['auth', 'role:ahli_gizi', 'check.subscription'])->prefix('ahli-gizi')->name('ahli-gizi.')->group(function () {

    // Dashboard - Always accessible
    Route::get('/dashboard', [AhliGiziController::class, 'dashboard'])->name('dashboard');

    // Menu Makanan - Requires active subscription
    Route::prefix('menu-makanan')->name('menu-makanan.')->group(function () {
        Route::get('/', [AhliGiziMenuMakananController::class, 'index'])->name('index');
        Route::get('/create', [AhliGiziMenuMakananController::class, 'create'])->name('create');
        Route::post('/', [AhliGiziMenuMakananController::class, 'store'])->name('store');
        Route::get('/{menuMakanan}', [AhliGiziMenuMakananController::class, 'show'])->name('show');
        Route::get('/{menuMakanan}/edit', [AhliGiziMenuMakananController::class, 'edit'])->name('edit');
        Route::put('/{menuMakanan}', [AhliGiziMenuMakananController::class, 'update'])->name('update');
        Route::delete('/{menuMakanan}', [AhliGiziMenuMakananController::class, 'destroy'])->name('destroy');
        Route::patch('/{menuMakanan}/toggle-status', [AhliGiziMenuMakananController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{menuMakanan}/check-stock', [AhliGiziMenuMakananController::class, 'checkStock'])->name('check-stock');
        Route::get('/menu/{menuMakanan}/detail', [AhliGiziMenuMakananController::class, 'detail'])->name('menu.detail');

        // API Routes AJAX - Requires active subscription
        Route::get('/api/active-menus', [AhliGiziMenuMakananController::class, 'getActiveMenus'])->name('active-menus');
        Route::get('/api/search', [AhliGiziMenuMakananController::class, 'searchMenus'])->name('search-api');
        Route::get('/api/menu/{menuMakanan}/detail', [AhliGiziTransaksiDapurController::class, 'getMenuDetail'])->name('get-menu-detail');
        Route::get('/menu/{menuMakanan}/detail', [AhliGiziMenuMakananController::class, 'detail'])->name('menu.detail');
        Route::get('/api/menu/{menu}/bahan', [AhliGiziMenuMakananController::class, 'getIngredientDetails'])->name('api.menu.ingredients');
    });

    // Input Paket Menu - Requires active subscription
    Route::prefix('input-paket-menu')->name('transaksi.')->group(function () {
        Route::get('/', [AhliGiziTransaksiDapurController::class, 'index'])->name('index');
        Route::get('/buat-paket-baru', [AhliGiziTransaksiDapurController::class, 'create'])->name('create');
        Route::post('/simpan-paket-baru', [AhliGiziTransaksiDapurController::class, 'store'])->name('store');
        Route::get('/{transaksi}/input-porsi-besar', [AhliGiziTransaksiDapurController::class, 'editPorsiBesar'])->name('edit-porsi-besar');
        Route::put('/{transaksi}/simpan-porsi-besar', [AhliGiziTransaksiDapurController::class, 'updatePorsiBesar'])->name('update-porsi-besar');
        Route::get('/{transaksi}/input-porsi-kecil', [AhliGiziTransaksiDapurController::class, 'editPorsiKecil'])->name('edit-porsi-kecil');
        Route::put('/{transaksi}/simpan-porsi-kecil', [AhliGiziTransaksiDapurController::class, 'updatePorsiKecil'])->name('update-porsi-kecil');
        Route::get('/{transaksi}/preview-paket', [AhliGiziTransaksiDapurController::class, 'preview'])->name('preview');

        Route::post('/{transaksi}/ajukan-persetujuan', [AhliGiziTransaksiDapurController::class, 'submitApproval'])->name('submit-approval');
        Route::post('/{transaksi}/laporkan-kekurangan', [AhliGiziTransaksiDapurController::class, 'createShortageReport'])->name('create-shortage-report');

        Route::get('/{transaksi}/detail', [AhliGiziTransaksiDapurController::class, 'show'])->name('show');
        Route::delete('/{transaksi}/hapus', [AhliGiziTransaksiDapurController::class, 'destroy'])->name('destroy');
        Route::post('/{transaksi}/check-stock', [AhliGiziTransaksiDapurController::class, 'checkStockAvailability'])->name('check-stock-api');
    });


    // Laporan Kekurangan Stock - Requires active subscription
    Route::prefix('laporan-saya')->name('laporan.')->group(function () {
        Route::get('/', [AhliGiziLaporanController::class, 'index'])->name('index');
        Route::get('/{laporan}/detail', [AhliGiziLaporanController::class, 'show'])->name('show');
        Route::get('/transaksi-dengan-kekurangan', [AhliGiziLaporanController::class, 'transaksiWithShortage'])->name('transaksi-with-shortage');
        Route::get('/export/csv', [AhliGiziLaporanController::class, 'export'])->name('export');
        Route::get('/api/dashboard-summary', [AhliGiziLaporanController::class, 'getSummaryJson'])->name('dashboard-summary-api');
        Route::get('/api/monthly-trend', [AhliGiziLaporanController::class, 'getMonthlyTrend'])->name('monthly-trend-api');
        Route::get('/ringkasan-dashboard', [AhliGiziLaporanController::class, 'dashboardSummary'])->name('dashboard-summary');
    });

    // ===== MONITORING DAN TRACKING - Requires active subscription =====
    Route::get('/tracking-paket', [AhliGiziTransaksiDapurController::class, 'trackingStatus'])->name('tracking-status');
    Route::get('/statistik-performa', [AhliGiziLaporanController::class, 'performanceStats'])->name('performance-stats');
});



Route::middleware(['auth', 'role:kepala_dapur,admin_gudang,ahli_gizi'])->group(function () {});
