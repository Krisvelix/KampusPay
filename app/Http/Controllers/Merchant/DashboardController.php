<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $merchant = auth()->user()->merchant;

        $totalProducts      = $merchant?->products()->count() ?? 0;
        $totalStock         = $merchant?->products()->sum('stock') ?? 0;
        $availableProducts  = $merchant?->products()->where('is_available', true)->count() ?? 0;
        $outOfStockCount    = $merchant?->products()->where('stock', '<=', 0)->count() ?? 0;
        $totalOrders        = $merchant?->orders()->count() ?? 0;
        $pendingOrdersCount = $merchant?->orders()->where('status', 'pending')->count() ?? 0;
        $totalRevenue       = $merchant?->orders()->where('status', 'completed')->sum('total_amount') ?? 0;
        $recentOrders       = $merchant ? $merchant->orders()->with('customer')->latest()->take(5)->get() : collect();
        $recentProducts     = $merchant ? $merchant->products()->latest()->take(5)->get() : collect();

        return view('merchant.dashboard', compact(
            'merchant',
            'totalProducts',
            'totalStock',
            'availableProducts',
            'outOfStockCount',
            'totalOrders',
            'pendingOrdersCount',
            'totalRevenue',
            'recentOrders',
            'recentProducts'
        ));
    }
}
