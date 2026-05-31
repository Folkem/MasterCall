<?php

namespace App\Http\Controllers\Cabinet;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Notifications\OrderStatusNotification;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    public function index(Request $request): View
    {
        $query = Booking::with(['client', 'service', 'category'])
            ->where('master_id', auth()->id())
            ->orderByDesc('created_at');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15)->withQueryString();
        $statuses = OrderStatus::cases();

        return view('cabinet.orders.index', compact('orders', 'statuses'));
    }

    public function show(Booking $order): View
    {
        abort_unless($order->master_id === auth()->id(), 403);

        $order->load(['client', 'service', 'category', 'workReport', 'messages.sender']);

        $messages = $order->messages;
        $messages->where('sender_id', '!=', auth()->id())->each(function ($msg) {
            if (is_null($msg->read_at)) {
                $msg->update(['read_at' => now()]);
            }
        });

        return view('cabinet.orders.show', compact('order'));
    }

    public function accept(Request $request, Booking $order): RedirectResponse
    {
        abort_unless($order->master_id === auth()->id(), 403);

        $request->validate([
            'price' => ['required', 'numeric', 'min:1'],
            'master_note' => ['nullable', 'string', 'max:1000'],
        ], [
            'price.required' => 'Ціна обов\'язкова.',
            'price.numeric' => 'Ціна має бути числом.',
            'price.min' => 'Ціна має бути більше 0.',
        ]);

        try {
            $this->bookingService->accept($order, (float) $request->price, $request->master_note);
        } catch (ValidationException $e) {
            return redirect()->route('cabinet.orders.show', $order)
                ->with('error', collect($e->errors())->flatten()->first());
        }

        $order->client->notify(new OrderStatusNotification($order, 'Замовлення прийнято майстром'));

        return redirect()->route('cabinet.orders.show', $order)->with('success', 'Замовлення прийнято.');
    }

    public function decline(Request $request, Booking $order): RedirectResponse
    {
        abort_unless($order->master_id === auth()->id(), 403);

        $request->validate([
            'master_note' => ['required', 'string', 'max:1000'],
        ], [
            'master_note.required' => 'Причина відмови обов\'язкова.',
        ]);

        try {
            $this->bookingService->decline($order, $request->master_note);
        } catch (ValidationException $e) {
            return redirect()->route('cabinet.orders.show', $order)
                ->with('error', collect($e->errors())->flatten()->first());
        }

        $order->client->notify(new OrderStatusNotification($order, 'Замовлення відхилено'));

        return redirect()->route('cabinet.orders.show', $order)->with('success', 'Замовлення відхилено.');
    }

    public function start(Booking $order): RedirectResponse
    {
        abort_unless($order->master_id === auth()->id(), 403);

        try {
            $this->bookingService->start($order);
        } catch (ValidationException $e) {
            return redirect()->route('cabinet.orders.show', $order)
                ->with('error', collect($e->errors())->flatten()->first());
        }

        return redirect()->route('cabinet.orders.show', $order)->with('success', 'Виконання розпочато.');
    }

    public function complete(Booking $order): RedirectResponse
    {
        abort_unless($order->master_id === auth()->id(), 403);

        try {
            $this->bookingService->complete($order);
        } catch (ValidationException $e) {
            return redirect()->route('cabinet.orders.show', $order)
                ->with('error', collect($e->errors())->flatten()->first());
        }

        $order->client->notify(new OrderStatusNotification($order, 'Замовлення виконано'));

        return redirect()->route('cabinet.orders.show', $order)->with('success', 'Замовлення завершено.');
    }
}
