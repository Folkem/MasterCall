<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $ordersToday = Booking::whereDate('created_at', today())->count();

        $revenueThisMonth = Booking::whereIn('status', [
            OrderStatus::Confirmed->value,
            OrderStatus::InProgress->value,
            OrderStatus::Completed->value,
        ])->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('price');

        $totalMasters = User::where('role', Role::Master->value)->count();
        $totalClients = User::where('role', Role::Client->value)->count();

        $topMasters = User::where('role', Role::Master->value)
            ->withCount(['masterOrders as completed_orders_count' => fn ($q) => $q->where('status', OrderStatus::Completed->value)])
            ->orderByDesc('completed_orders_count')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'ordersToday', 'revenueThisMonth', 'totalMasters', 'totalClients', 'topMasters'
        ));
    }
}
