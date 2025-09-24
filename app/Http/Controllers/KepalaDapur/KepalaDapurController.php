<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Dapur;
use App\Models\StockItem;
use App\Models\ApprovalStockItem;
use App\Models\ApprovalTransaksi;
use App\Models\TransaksiDapur;
use App\Models\MenuMakanan;
use App\Models\TemplateItem;
use App\Models\LaporanKekuranganStock;
use App\Models\SubscriptionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KepalaDapurController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:kepala_dapur']);
    }

    public function dashboard(Request $request, Dapur $dapur)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->isKepalaDapur($dapur->id_dapur)) {
            abort(403, 'Anda tidak memiliki akses ke dashboard ini untuk dapur ini');
        }

        // Get comprehensive dashboard data
        $dashboardData = [
            'user' => $user,
            'dapur' => $dapur,
            'role' => 'kepala_dapur',

            // Enhanced Core Statistics
            'statistics' => $this->getCoreStatistics($dapur),

            // Approval Statistics
            'approvalStats' => $this->getApprovalStatistics($dapur),

            // Stock Health Overview
            'stockHealth' => $this->getStockHealthOverview($dapur),

            // Transaction Performance
            'transactionPerformance' => $this->getTransactionPerformance($dapur),

            // Team Management Overview
            'teamOverview' => $this->getTeamOverview($dapur),

            // Recent Activities Timeline
            'recentActivities' => $this->getRecentActivitiesTimeline($dapur),

            // System Health & Alerts
            'systemAlerts' => $this->getSystemAlerts($dapur),

            // Performance Metrics
            'performanceMetrics' => $this->getPerformanceMetrics($dapur),

            // Quick Actions
            'quickActions' => $this->getEnhancedQuickActions($dapur),

            // Charts Data
            'chartsData' => $this->getChartsData($dapur),

            // Subscription Status
            'subscriptionStatus' => $this->getSubscriptionStatus($dapur),
        ];

        return view('kepaladapur.dashboard.index', $dashboardData);
    }

    /**
     * Get core statistics for dashboard cards
     */
    private function getCoreStatistics(Dapur $dapur): array
    {
        return [
            'pending_approvals' => $this->getPendingApprovalsCount($dapur),
            'pending_transaction_approvals' => $this->getPendingTransactionApprovalsCount($dapur),
            'total_stock_items' => $this->getTotalStockItemsForDapur($dapur),
            'total_stock_value' => $this->getTotalStockForDapur($dapur),
            'monthly_transactions' => $this->getMonthlyTransactionsForDapur($dapur),
            'completed_transactions' => $this->getCompletedTransactionsForDapur($dapur),
            'total_portions_month' => $this->getTotalPortionsThisMonth($dapur),
            'active_menus' => $this->getActiveMenusForDapur($dapur),
            'team_members' => $this->getTotalTeamMembers($dapur),
            'low_stock_alerts' => $this->getLowStockAlertsCount($dapur),
        ];
    }

    /**
     * Get approval statistics with breakdown
     */
    private function getApprovalStatistics(Dapur $dapur): array
    {
        $stockApprovals = ApprovalStockItem::whereHas('stockItem', function ($query) use ($dapur) {
            $query->where('id_dapur', $dapur->id_dapur);
        });

        $transactionApprovals = ApprovalTransaksi::whereHas('transaksiDapur', function ($query) use ($dapur) {
            $query->where('id_dapur', $dapur->id_dapur);
        });

        return [
            'stock_approvals' => [
                'total' => $stockApprovals->count(),
                'pending' => $stockApprovals->where('status', 'pending')->count(),
                'approved' => $stockApprovals->where('status', 'approved')->count(),
                'rejected' => $stockApprovals->where('status', 'rejected')->count(),
                'this_month' => $stockApprovals->whereMonth('created_at', now()->month)->count(),
            ],
            'transaction_approvals' => [
                'total' => $transactionApprovals->count(),
                'pending' => $transactionApprovals->where('status', 'pending')->count(),
                'approved' => $transactionApprovals->where('status', 'approved')->count(),
                'rejected' => $transactionApprovals->where('status', 'rejected')->count(),
                'this_month' => $transactionApprovals->whereMonth('created_at', now()->month)->count(),
            ],
            'approval_performance' => $this->getApprovalPerformanceMetrics($dapur),
        ];
    }

    /**
     * Get detailed stock health overview
     */
    /**
     * Get detailed stock health overview
     */
    private function getStockHealthOverview(Dapur $dapur): array
    {
        $stockItemsQuery = StockItem::where('id_dapur', $dapur->id_dapur)->with('templateItem');

        $totalItems = $stockItemsQuery->count();

        // Stok normal (tersedia sepenuhnya)
        $availableItems = $stockItemsQuery->clone()->where('jumlah', '>', 10)->count();

        // Habis
        $outOfStock = $stockItemsQuery->clone()->where('jumlah', '<=', 0)->count();

        // Rendah (6-10)
        $lowStock = $stockItemsQuery->clone()->where('jumlah', '>', 5)->where('jumlah', '<=', 10)->count();

        // Kritis (1-5)
        $criticalStock = $stockItemsQuery->clone()->where('jumlah', '>', 0)->where('jumlah', '<=', 5)->count();

        // Ambil semua item rendah, kritis, dan habis (<=10, termasuk <=0)
        $lowStockItems = $stockItemsQuery->clone()->where('jumlah', '<=', 10)
            ->orderBy('jumlah', 'asc')
            ->get()
            ->map(function ($item) {
                $status = $item->jumlah <= 0 ? 'out_of_stock' : ($item->jumlah <= 5 ? 'critical' : 'low');
                return [
                    'id' => $item->id_stock_item,
                    'nama_bahan' => $item->templateItem->nama_bahan ?? 'N/A',
                    'jumlah' => $item->jumlah,
                    'satuan' => $item->templateItem->satuan ?? 'N/A',
                    'status' => $status,
                    'last_restock' => $item->tanggal_restok ? Carbon::parse($item->tanggal_restok)->format('d M Y') : 'Belum pernah',
                ];
            })->toArray();

        return [
            'overview' => [
                'total_items' => $totalItems,
                'available_items' => $availableItems,
                'out_of_stock' => $outOfStock,
                'low_stock' => $lowStock,
                'critical_stock' => $criticalStock,
                'stock_health_percentage' => $totalItems > 0 ? round(($availableItems / $totalItems) * 100, 1) : 0,
            ],
            'low_stock_items' => $lowStockItems,
            'stock_trends' => $this->getStockTrends($dapur),
        ];
    }

    /**
     * Get transaction performance data
     */
    private function getTransactionPerformance(Dapur $dapur): array
    {
        $transactions = TransaksiDapur::where('id_dapur', $dapur->id_dapur);

        $thisMonth = $transactions->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        $lastMonth = $transactions->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year);

        $thisMonthCount = $thisMonth->count();
        $lastMonthCount = $lastMonth->count();

        $growthRate = $lastMonthCount > 0 ? (($thisMonthCount - $lastMonthCount) / $lastMonthCount) * 100 : 0;

        return [
            'monthly_summary' => [
                'this_month' => $thisMonthCount,
                'last_month' => $lastMonthCount,
                'growth_rate' => round($growthRate, 1),
                'total_portions_month' => $thisMonth->sum('total_porsi'),
            ],
            'status_breakdown' => [
                'completed' => $transactions->where('status', 'completed')->count(),
                'processing' => $transactions->where('status', 'processing')->count(),
                'cancelled' => $transactions->where('status', 'cancelled')->count(),
            ],
            'weekly_trend' => $this->getWeeklyTransactionTrend($dapur),
            'popular_menus' => $this->getPopularMenus($dapur),
        ];
    }

    /**
     * Get comprehensive team overview
     */
    private function getTeamOverview(Dapur $dapur): array
    {
        $kepalaDapur = $dapur->kepalaDapur()->with('user')->get();
        $adminGudang = $dapur->adminGudang()->with('user')->get();
        $ahliGizi = $dapur->ahliGizi()->with('user')->get();

        return [
            'summary' => [
                'total_members' => $kepalaDapur->count() + $adminGudang->count() + $ahliGizi->count(),
                'kepala_dapur_count' => $kepalaDapur->count(),
                'admin_gudang_count' => $adminGudang->count(),
                'ahli_gizi_count' => $ahliGizi->count(),
            ],
            'members' => [
                'kepala_dapur' => $kepalaDapur,
                'admin_gudang' => $adminGudang,
                'ahli_gizi' => $ahliGizi,
            ],
            'recent_additions' => $this->getRecentTeamAdditions($dapur),
            'team_performance' => $this->getTeamPerformanceMetrics($dapur),
        ];
    }

    /**
     * Get enhanced recent activities timeline
     */
    private function getRecentActivitiesTimeline(Dapur $dapur, int $limit = 15): array
    {
        $activities = collect();

        // Stock approvals activities
        $stockApprovals = ApprovalStockItem::whereHas('stockItem', function ($query) use ($dapur) {
            $query->where('id_dapur', $dapur->id_dapur);
        })
            ->with(['stockItem.templateItem', 'adminGudang.user'])
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($approval) use ($dapur) {
                return [
                    'type' => 'stock_approval',
                    'title' => 'Permintaan Stock ' . ucfirst($approval->status),
                    'description' => ($approval->adminGudang->user->nama ?? 'Admin') . ' mengajukan ' .
                        number_format($approval->jumlah) . ' ' . $approval->satuan . ' ' .
                        ($approval->stockItem->templateItem->nama_bahan ?? 'item'),
                    'user' => $approval->adminGudang->user->nama ?? 'Admin',
                    'status' => $approval->status,
                    'created_at' => $approval->created_at,
                    'icon' => $this->getActivityIcon('stock_approval', $approval->status),
                    'color' => $this->getActivityColor($approval->status),
                    'url' => route('kepala-dapur.approvals.show', [$dapur, $approval]),
                ];
            });

        // Transaction approvals activities
        $transactionApprovals = ApprovalTransaksi::whereHas('transaksiDapur', function ($query) use ($dapur) {
            $query->where('id_dapur', $dapur->id_dapur);
        })
            ->with(['transaksiDapur', 'ahliGizi.user'])
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($approval) use ($dapur) {
                return [
                    'type' => 'transaction_approval',
                    'title' => 'Approval Transaksi ' . ucfirst($approval->status),
                    'description' => ($approval->ahliGizi->user->nama ?? 'Ahli Gizi') . ' mengajukan paket menu ' .
                        number_format($approval->transaksiDapur->total_porsi ?? 0),
                    'user' => $approval->ahliGizi->user->nama ?? 'Ahli Gizi',
                    'status' => $approval->status,
                    'created_at' => $approval->created_at,
                    'icon' => $this->getActivityIcon('transaction_approval', $approval->status),
                    'color' => $this->getActivityColor($approval->status),
                    'url' => route('kepala-dapur.approval-transaksi.show', [$dapur, $approval]),
                ];
            });

        // Recent transactions
        $transactions = TransaksiDapur::where('id_dapur', $dapur->id_dapur)
            ->where('status', ['processing', 'completed', 'cancelled'])
            ->with(['createdBy'])
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($transaction) {
                return [
                    'type' => 'transaction',
                    'title' => 'Transaksi ' . ucfirst($transaction->status),
                    'description' => 'Paket menu ' . number_format($transaction->total_porsi) . ' porsi ' .
                        'oleh ' . ($transaction->createdBy->nama ?? 'System'),
                    'user' => $transaction->createdBy->nama ?? 'System',
                    'status' => $transaction->status,
                    'created_at' => $transaction->created_at,
                    'icon' => $this->getActivityIcon('transaction', $transaction->status),
                    'color' => $this->getActivityColor($transaction->status),
                    'url' => route('ahli-gizi.transaksi.show', $transaction),
                ];
            });

        // Shortage reports
        $shortageReports = LaporanKekuranganStock::whereHas('transaksiDapur', function ($query) use ($dapur) {
            $query->where('id_dapur', $dapur->id_dapur);
        })
            ->with(['transaksiDapur', 'templateItem'])
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($report) use ($dapur) {
                return [
                    'type' => 'shortage_report',
                    'title' => 'Laporan Kekurangan Stock',
                    'description' => 'Kekurangan ' . ($report->templateItem->nama_bahan ?? 'item') . ': ' .
                        number_format($report->jumlah_kurang, 2) . ' ' . $report->satuan,
                    'user' => 'System',
                    'status' => $report->status,
                    'created_at' => $report->created_at,
                    'icon' => $this->getActivityIcon('shortage', $report->status),
                    'color' => $this->getActivityColor($report->status),
                    'url' => route('kepala-dapur.laporan-kekurangan.show', [$dapur, $report->transaksi]),
                ];
            });

        // Merge and sort all activities
        return $activities
            ->merge($stockApprovals)
            ->merge($transactionApprovals)
            ->merge($transactions)
            ->merge($shortageReports)
            ->sortByDesc('created_at')
            ->take($limit)
            ->values()
            ->toArray();
    }

    /**
     * Get system alerts and notifications
     */
    private function getSystemAlerts(Dapur $dapur): array
    {
        $alerts = [];

        // Critical stock alerts
        $criticalStock = $this->getCriticalStockItemsForDapur($dapur);
        if ($criticalStock > 0) {
            $alerts[] = [
                'type' => 'critical',
                'category' => 'stock',
                'title' => 'Stock Kritis',
                'message' => "{$criticalStock} item stock dalam kondisi kritis (≤5 unit)",
                // 'action_text' => 'Lihat Detail',
                'action_url' => route('admin-gudang.stock.index', $dapur) . '?filter=critical',
                'icon' => 'bx-error-circle',
            ];
        }

        // Subscription alerts
        $subscriptionStatus = $this->getSubscriptionStatus($dapur);
        if ($subscriptionStatus['is_expired'] || $subscriptionStatus['is_expiring_soon']) {
            $alerts[] = [
                'type' => $subscriptionStatus['is_expired'] ? 'critical' : 'warning',
                'category' => 'subscription',
                'title' => $subscriptionStatus['is_expired'] ? 'Langganan Berakhir' : 'Langganan Segera Berakhir',
                'message' => $subscriptionStatus['is_expired'] ?
                    'Langganan telah berakhir, beberapa fitur tidak tersedia' :
                    "Langganan berakhir dalam {$subscriptionStatus['days_left']} hari",
                'action_text' => 'Perpanjang Sekarang',
                'action_url' => route('kepala-dapur.subscription.create', $dapur),
                'icon' => 'bx-crown',
            ];
        }

        // High pending approvals
        $pendingApprovals = $this->getPendingApprovalsCount($dapur) + $this->getPendingTransactionApprovalsCount($dapur);
        if ($pendingApprovals >= 5) {
            $alerts[] = [
                'type' => 'warning',
                'category' => 'approval',
                'title' => 'Banyak Approval Tertunda',
                'message' => "{$pendingApprovals} approval menunggu tindakan Anda",
                'action_text' => 'Review Sekarang',
                'action_url' => route('kepala-dapur.approvals.index', $dapur),
                'icon' => 'bx-time-five',
            ];
        }

        return $alerts;
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics(Dapur $dapur): array
    {
        return [
            'approval_performance' => $this->getApprovalPerformanceMetrics($dapur),
            'stock_turnover' => $this->getStockTurnoverMetrics($dapur),
            'efficiency_metrics' => $this->getEfficiencyMetrics($dapur),
            'quality_metrics' => $this->getQualityMetrics($dapur),
        ];
    }

    /**
     * Get enhanced quick actions
     */
    private function getEnhancedQuickActions(Dapur $dapur): array
    {
        $actions = [];

        // Priority actions based on current status
        $pendingStockApprovals = $this->getPendingApprovalsCount($dapur);
        $pendingTransactionApprovals = $this->getPendingTransactionApprovalsCount($dapur);
        $lowStockCount = $this->getLowStockAlertsCount($dapur);
        $subscriptionStatus = $this->getSubscriptionStatus($dapur);

        // Critical actions first
        if ($subscriptionStatus['is_expired']) {
            $actions[] = [
                'id' => 'renew_subscription',
                'title' => 'Perpanjang Langganan',
                'description' => 'Langganan telah berakhir',
                'icon' => 'bx-crown',
                'color' => 'danger',
                'priority' => 1,
                'url' => route('kepala-dapur.subscription.create', $dapur),
                'badge' => 'URGENT',
            ];
        }

        if ($pendingStockApprovals > 0) {
            $actions[] = [
                'id' => 'review_stock_approvals',
                'title' => 'Review Stock Approvals',
                'description' => "{$pendingStockApprovals} permintaan stock menunggu",
                'icon' => 'bx-check-circle',
                'color' => 'warning',
                'priority' => 2,
                'url' => route('kepala-dapur.approvals.index', $dapur),
                'badge' => $pendingStockApprovals,
            ];
        }

        if ($pendingTransactionApprovals > 0) {
            $actions[] = [
                'id' => 'review_transaction_approvals',
                'title' => 'Review Transaksi',
                'description' => "{$pendingTransactionApprovals} transaksi menunggu approval",
                'icon' => 'bx-receipt',
                'color' => 'info',
                'priority' => 3,
                'url' => route('kepala-dapur.approval-transaksi.index', $dapur),
                'badge' => $pendingTransactionApprovals,
            ];
        }

        if ($lowStockCount > 0) {
            $actions[] = [
                'id' => 'check_low_stock',
                'title' => 'Cek Stock Rendah',
                'description' => "{$lowStockCount} item stock rendah",
                'icon' => 'bx-error',
                'color' => 'danger',
                'priority' => 2,
                'url' => route('kepala-dapur.stock.index', $dapur) . '?filter=low_stock',
                'badge' => $lowStockCount,
            ];
        }

        // Standard management actions
        $actions = array_merge($actions, [
            [
                'id' => 'manage_users',
                'title' => 'Kelola Tim',
                'description' => 'Tambah atau edit anggota tim',
                'icon' => 'bx-group',
                'color' => 'primary',
                'priority' => 5,
                'url' => route('kepala-dapur.users.index', $dapur),
                'badge' => null,
            ],
            [
                'id' => 'manage_templates',
                'title' => 'Template Bahan',
                'description' => 'Kelola template bahan baku',
                'icon' => 'bx-package',
                'color' => 'info',
                'priority' => 6,
                'url' => route('kepala-dapur.template-items.index'),
                'badge' => null,
            ],
            [
                'id' => 'view_menus',
                'title' => 'Lihat Menu',
                'description' => 'Daftar menu yang tersedia',
                'icon' => 'bx-food-menu',
                'color' => 'secondary',
                'priority' => 7,
                'url' => route('kepala-dapur.menu-makanan.index'),
                'badge' => null,
            ],
            [
                'id' => 'view_reports',
                'title' => 'Lihat Laporan',
                'description' => 'Laporan kekurangan stock',
                'icon' => 'bx-bar-chart',
                'color' => 'success',
                'priority' => 8,
                'url' => route('kepala-dapur.laporan-kekurangan.index'),
                'badge' => null,
            ],
        ]);

        // Sort by priority
        usort($actions, function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });

        return $actions;
    }

    /**
     * Get charts data for dashboard
     */
    private function getChartsData(Dapur $dapur): array
    {
        return [
            'monthly_transactions' => $this->getMonthlyTransactionChartData($dapur),
            'stock_status_pie' => $this->getStockStatusPieData($dapur),
            'approval_trend' => $this->getApprovalTrendData($dapur),
            'team_workload' => $this->getTeamWorkloadData($dapur),
        ];
    }

    // Helper methods for various metrics...

    private function getPendingApprovalsCount(Dapur $dapur): int
    {
        return ApprovalStockItem::whereHas('stockItem', function ($query) use ($dapur) {
            $query->where('id_dapur', $dapur->id_dapur);
        })->where('status', 'pending')->count();
    }

    private function getPendingTransactionApprovalsCount(Dapur $dapur): int
    {
        return ApprovalTransaksi::whereHas('transaksiDapur', function ($query) use ($dapur) {
            $query->where('id_dapur', $dapur->id_dapur);
        })->where('status', 'pending')->count();
    }

    private function getTotalStockForDapur(Dapur $dapur): float
    {
        return StockItem::where('id_dapur', $dapur->id_dapur)->sum('jumlah');
    }

    private function getTotalStockItemsForDapur(Dapur $dapur): int
    {
        return StockItem::where('id_dapur', $dapur->id_dapur)->where('jumlah', '>', 0)->count();
    }

    private function getMonthlyTransactionsForDapur(Dapur $dapur): int
    {
        return TransaksiDapur::where('id_dapur', $dapur->id_dapur)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    private function getCompletedTransactionsForDapur(Dapur $dapur): int
    {
        return TransaksiDapur::where('id_dapur', $dapur->id_dapur)
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->count();
    }

    private function getTotalPortionsThisMonth(Dapur $dapur): int
    {
        return TransaksiDapur::where('id_dapur', $dapur->id_dapur)
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->sum('total_porsi');
    }

    private function getActiveMenusForDapur(Dapur $dapur): int
    {
        return MenuMakanan::where('created_by_dapur_id', $dapur->id_dapur)
            ->where('is_active', true)
            ->count();
    }

    private function getTotalTeamMembers(Dapur $dapur): int
    {
        return $dapur->kepalaDapur()->count() +
            $dapur->adminGudang()->count() +
            $dapur->ahliGizi()->count();
    }

    private function getLowStockAlertsCount(Dapur $dapur): int
    {
        return StockItem::where('id_dapur', $dapur->id_dapur)
            ->where('jumlah', '>', 0)
            ->where('jumlah', '<=', 10)
            ->count();
    }

    private function getCriticalStockItemsForDapur(Dapur $dapur): int
    {
        return StockItem::where('id_dapur', $dapur->id_dapur)
            ->where('jumlah', '>', 0)
            ->where('jumlah', '<=', 5)
            ->count();
    }

    private function getSubscriptionStatus(Dapur $dapur): array
    {
        if (!$dapur->subscription_end) {
            return [
                'status' => 'unknown',
                'days_left' => null,
                'end_date' => null,
                'is_expired' => false,
                'is_expiring_soon' => false
            ];
        }

        $daysLeft = now()->diffInDays($dapur->subscription_end, false);
        $isExpired = $daysLeft < 0;
        $isExpiringSoon = $daysLeft <= 7 && $daysLeft >= 0;

        return [
            'status' => $isExpired ? 'expired' : ($isExpiringSoon ? 'expiring' : 'active'),
            'days_left' => $daysLeft,
            'end_date' => $dapur->subscription_end,
            'is_expired' => $isExpired,
            'is_expiring_soon' => $isExpiringSoon
        ];
    }

    private function getActivityIcon(string $type, string $status): string
    {
        $icons = [
            'stock_approval' => ['pending' => 'bx-time', 'approved' => 'bx-check-circle', 'rejected' => 'bx-x-circle'],
            'transaction_approval' => ['pending' => 'bx-receipt', 'approved' => 'bx-check', 'rejected' => 'bx-x'],
            'shortage' => ['pending' => 'bx-error', 'resolved' => 'bx-check'],
        ];

        return $icons[$type][$status] ?? 'bx-circle';
    }

    private function getActivityColor(string $status): string
    {
        $colors = [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'completed' => 'success',
            'processing' => 'info',
            'cancelled' => 'danger',
            'resolved' => 'success',
        ];

        return $colors[$status] ?? 'secondary';
    }

    // Additional helper methods implementation

    private function getApprovalPerformanceMetrics(Dapur $dapur): array
    {
        $stockApprovals = ApprovalStockItem::whereHas('stockItem', function ($query) use ($dapur) {
            $query->where('id_dapur', $dapur->id_dapur);
        });

        $total = $stockApprovals->count();
        $approved = $stockApprovals->where('status', 'approved')->count();
        $avgResponseTime = $stockApprovals->whereNotNull('approved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_hours')
            ->first()->avg_hours ?? 0;

        return [
            'total_requests' => $total,
            'approval_rate' => $total > 0 ? round(($approved / $total) * 100, 1) : 0,
            'avg_response_time_hours' => round($avgResponseTime, 1),
            'pending_count' => $stockApprovals->where('status', 'pending')->count(),
        ];
    }

    private function getStockTrends(Dapur $dapur): array
    {
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $restockCount = ApprovalStockItem::whereHas('stockItem', function ($query) use ($dapur) {
                $query->where('id_dapur', $dapur->id_dapur);
            })
                ->where('status', 'approved')
                ->whereDate('approved_at', $date)
                ->count();

            $last7Days->push([
                'date' => $date->format('M d'),
                'restock_count' => $restockCount
            ]);
        }

        return $last7Days->toArray();
    }

    private function getWeeklyTransactionTrend(Dapur $dapur): array
    {
        $weeklyData = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $transactionCount = TransaksiDapur::where('id_dapur', $dapur->id_dapur)
                ->whereDate('created_at', $date)
                ->count();

            $weeklyData->push([
                'date' => $date->format('M d'),
                'transaction_count' => $transactionCount
            ]);
        }

        return $weeklyData->toArray();
    }

    private function getPopularMenus(Dapur $dapur): array
    {
        return DB::table('detail_transaksi_dapur')
            ->join('transaksi_dapur', 'detail_transaksi_dapur.id_transaksi', '=', 'transaksi_dapur.id_transaksi')
            ->join('menu_makanan', 'detail_transaksi_dapur.id_menu', '=', 'menu_makanan.id_menu')
            ->where('transaksi_dapur.id_dapur', $dapur->id_dapur)
            ->where('transaksi_dapur.status', 'completed')
            ->whereMonth('transaksi_dapur.created_at', now()->month)
            ->selectRaw('menu_makanan.nama_menu, SUM(detail_transaksi_dapur.jumlah_porsi) as total_porsi')
            ->groupBy('menu_makanan.id_menu', 'menu_makanan.nama_menu')
            ->orderByDesc('total_porsi')
            ->take(5)
            ->get()
            ->toArray();
    }

    private function getRecentTeamAdditions(Dapur $dapur): array
    {
        $recentMembers = collect();

        // Recent Kepala Dapur
        $recentKD = $dapur->kepalaDapur()->with('user')
            ->where('created_at', '>=', now()->subDays(30))
            ->get()
            ->map(function ($member) {
                return [
                    'name' => $member->user->nama,
                    'role' => 'Kepala Dapur',
                    'joined_at' => $member->created_at,
                ];
            });

        // Recent Admin Gudang
        $recentAG = $dapur->adminGudang()->with('user')
            ->where('created_at', '>=', now()->subDays(30))
            ->get()
            ->map(function ($member) {
                return [
                    'name' => $member->user->nama,
                    'role' => 'Admin Gudang',
                    'joined_at' => $member->created_at,
                ];
            });

        // Recent Ahli Gizi
        $recentAhliGizi = $dapur->ahliGizi()->with('user')
            ->where('created_at', '>=', now()->subDays(30))
            ->get()
            ->map(function ($member) {
                return [
                    'name' => $member->user->nama,
                    'role' => 'Ahli Gizi',
                    'joined_at' => $member->created_at,
                ];
            });

        return $recentMembers->merge($recentKD)
            ->merge($recentAG)
            ->merge($recentAhliGizi)
            ->sortByDesc('joined_at')
            ->take(5)
            ->values()
            ->toArray();
    }

    private function getTeamPerformanceMetrics(Dapur $dapur): array
    {
        // Admin Gudang Performance
        $adminPerformance = $dapur->adminGudang()
            ->with(['approvalStockItems' => function ($query) {
                $query->whereMonth('created_at', now()->month);
            }])
            ->get()
            ->map(function ($admin) {
                return [
                    'name' => $admin->user->nama ?? 'N/A',
                    'requests_made' => $admin->approvalStockItems->count(),
                    'role' => 'Admin Gudang'
                ];
            });

        // Ahli Gizi Performance
        $ahliGiziPerformance = $dapur->ahliGizi()
            ->with(['approvalTransaksi' => function ($query) {
                $query->whereMonth('created_at', now()->month);
            }])
            ->get()
            ->map(function ($ahliGizi) {
                return [
                    'name' => $ahliGizi->user->nama ?? 'N/A',
                    'transactions_created' => $ahliGizi->approvalTransaksi->count(),
                    'role' => 'Ahli Gizi'
                ];
            });

        return $adminPerformance->merge($ahliGiziPerformance)->toArray();
    }

    private function getStockTurnoverMetrics(Dapur $dapur): array
    {
        $totalItems = StockItem::where('id_dapur', $dapur->id_dapur)->count();
        $restockedThisMonth = StockItem::where('id_dapur', $dapur->id_dapur)
            ->whereHas('approvalStockItems', function ($query) {
                $query->where('status', 'approved')
                    ->whereMonth('approved_at', now()->month);
            })->count();

        return [
            'turnover_rate' => $totalItems > 0 ? round(($restockedThisMonth / $totalItems) * 100, 1) : 0,
            'items_restocked' => $restockedThisMonth,
            'total_items' => $totalItems,
        ];
    }

    private function getEfficiencyMetrics(Dapur $dapur): array
    {
        $completedTransactions = TransaksiDapur::where('id_dapur', $dapur->id_dapur)
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->count();

        $totalTransactions = TransaksiDapur::where('id_dapur', $dapur->id_dapur)
            ->whereMonth('created_at', now()->month)
            ->count();

        return [
            'completion_rate' => $totalTransactions > 0 ? round(($completedTransactions / $totalTransactions) * 100, 1) : 0,
            'completed_transactions' => $completedTransactions,
            'total_transactions' => $totalTransactions,
        ];
    }

    private function getQualityMetrics(Dapur $dapur): array
    {
        $rejectedApprovals = ApprovalStockItem::whereHas('stockItem', function ($query) use ($dapur) {
            $query->where('id_dapur', $dapur->id_dapur);
        })->where('status', 'rejected')->whereMonth('created_at', now()->month)->count();

        $totalApprovals = ApprovalStockItem::whereHas('stockItem', function ($query) use ($dapur) {
            $query->where('id_dapur', $dapur->id_dapur);
        })->whereMonth('created_at', now()->month)->count();

        return [
            'rejection_rate' => $totalApprovals > 0 ? round(($rejectedApprovals / $totalApprovals) * 100, 1) : 0,
            'quality_score' => $totalApprovals > 0 ? round(100 - (($rejectedApprovals / $totalApprovals) * 100), 1) : 100,
        ];
    }

    private function getMonthlyTransactionChartData(Dapur $dapur): array
    {
        $monthlyData = collect();
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = TransaksiDapur::where('id_dapur', $dapur->id_dapur)
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();

            $monthlyData->push([
                'month' => $month->format('M Y'),
                'count' => $count
            ]);
        }

        return $monthlyData->toArray();
    }

    private function getStockStatusPieData(Dapur $dapur): array
    {
        $available = StockItem::where('id_dapur', $dapur->id_dapur)->where('jumlah', '>', 10)->count();
        $low = StockItem::where('id_dapur', $dapur->id_dapur)->where('jumlah', '>', 0)->where('jumlah', '<=', 10)->count();
        $outOfStock = StockItem::where('id_dapur', $dapur->id_dapur)->where('jumlah', '<=', 0)->count();

        return [
            'available' => $available,
            'low_stock' => $low,
            'out_of_stock' => $outOfStock,
        ];
    }

    private function getApprovalTrendData(Dapur $dapur): array
    {
        $trendData = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $approvals = ApprovalStockItem::whereHas('stockItem', function ($query) use ($dapur) {
                $query->where('id_dapur', $dapur->id_dapur);
            })
                ->whereDate('created_at', $date)
                ->count();

            $trendData->push([
                'date' => $date->format('M d'),
                'approvals' => $approvals
            ]);
        }

        return $trendData->toArray();
    }

    private function getTeamWorkloadData(Dapur $dapur): array
    {
        $workloadData = [];

        // Admin Gudang workload
        $adminWorkload = $dapur->adminGudang()->with('user')->get()->map(function ($admin) {
            $pendingRequests = $admin->approvalStockItems()->where('status', 'pending')->count();
            return [
                'name' => $admin->user->nama ?? 'N/A',
                'workload' => $pendingRequests,
                'role' => 'Admin Gudang'
            ];
        });

        // Ahli Gizi workload
        $ahliGiziWorkload = $dapur->ahliGizi()->with('user')->get()->map(function ($ahliGizi) use ($dapur) {
            $pendingTransactions = TransaksiDapur::where('id_dapur', $dapur->id_dapur)
                ->where('created_by', $ahliGizi->user->id_user ?? 0)
                ->where('status', 'draft')
                ->count();
            return [
                'name' => $ahliGizi->user->nama ?? 'N/A',
                'workload' => $pendingTransactions,
                'role' => 'Ahli Gizi'
            ];
        });

        return $adminWorkload->merge($ahliGiziWorkload)->toArray();
    }
}
