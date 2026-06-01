<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Booking::with(['client', 'master', 'service', 'category'])
            ->orderByDesc('created_at');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('client', fn ($uq) => $uq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('master', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        $orders = $query->paginate(20)->withQueryString();
        $statuses = OrderStatus::cases();

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    public function updateStatus(Request $request, Booking $order): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:'.implode(',', array_column(OrderStatus::cases(), 'value'))],
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Статус оновлено.');
    }
}
