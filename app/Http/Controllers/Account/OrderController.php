<?php

namespace App\Http\Controllers\Account;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    public function index(Request $request): View
    {
        $query = Booking::with(['master', 'master.masterProfile', 'service', 'category'])
            ->where('client_id', auth()->id())
            ->orderByDesc('created_at');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15)->withQueryString();
        $statuses = OrderStatus::cases();

        return view('account.orders.index', compact('orders', 'statuses'));
    }

    public function create(Request $request): View
    {
        $master = User::findOrFail($request->master);
        abort_unless($master->isMaster() && $master->is_active, 404);

        $service = $request->filled('service') ? Service::find($request->service) : null;
        $categories = ServiceCategory::all();

        return view('account.orders.create', compact('master', 'service', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'master_id' => ['required', 'exists:users,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'category_id' => ['required', 'exists:service_categories,id'],
            'address' => ['required', 'string', 'max:500'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'description' => ['required', 'string', 'max:2000'],
        ], [
            'master_id.required' => 'Майстер обов\'язковий.',
            'category_id.required' => 'Категорія обов\'язкова.',
            'address.required' => 'Адреса обов\'язкова.',
            'scheduled_at.required' => 'Дата та час обов\'язкові.',
            'scheduled_at.after' => 'Дата має бути у майбутньому.',
            'description.required' => 'Опис роботи обов\'язковий.',
        ]);

        $master = User::findOrFail($validated['master_id']);
        abort_unless($master->isMaster() && $master->is_active, 404);

        $service = isset($validated['service_id']) ? Service::find($validated['service_id']) : null;

        $booking = Booking::create([
            'client_id' => auth()->id(),
            'master_id' => $validated['master_id'],
            'service_id' => $validated['service_id'] ?? null,
            'category_id' => $validated['category_id'],
            'address' => $validated['address'],
            'scheduled_at' => $validated['scheduled_at'],
            'description' => $validated['description'],
            'price' => $service?->price,
            'status' => OrderStatus::Pending,
        ]);

        $master->notify(new NewOrderNotification($booking));

        return redirect()->route('account.orders.show', $booking)->with('success', 'Замовлення успішно створено!');
    }

    public function show(Booking $order): View
    {
        abort_unless($order->client_id === auth()->id(), 403);

        $order->load(['master', 'master.masterProfile', 'service', 'category', 'workReport', 'messages.sender']);

        $messages = $order->messages;
        $messages->where('sender_id', '!=', auth()->id())->each(function ($msg) {
            if (is_null($msg->read_at)) {
                $msg->update(['read_at' => now()]);
            }
        });

        $existingReview = Review::where('client_id', auth()->id())
            ->where('master_id', $order->master_id)
            ->first();

        return view('account.orders.show', compact('order', 'existingReview'));
    }

    public function cancel(Booking $order): RedirectResponse
    {
        abort_unless($order->client_id === auth()->id(), 403);

        $this->bookingService->cancel($order);

        return redirect()->route('account.orders.show', $order)->with('success', 'Замовлення скасовано.');
    }
}
