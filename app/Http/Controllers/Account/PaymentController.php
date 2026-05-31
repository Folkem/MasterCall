<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Notifications\OrderStatusNotification;
use App\Services\BookingService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private BookingService $bookingService,
    ) {}

    public function checkout(Booking $order): RedirectResponse
    {
        abort_unless($order->client_id === auth()->id(), 403);
        abort_unless($order->status->value === 'accepted', 403, 'Замовлення не може бути оплачено.');

        $session = $this->paymentService->createCheckoutSession(
            $order,
            route('checkout.success', ['order' => $order->id]).'?session_id={CHECKOUT_SESSION_ID}',
            route('checkout.cancel', ['order' => $order->id]),
        );

        return redirect($session->url);
    }

    public function success(Request $request, Booking $order): RedirectResponse
    {
        abort_unless($order->client_id === auth()->id(), 403);

        $sessionId = $request->query('session_id');

        if (! $sessionId) {
            return redirect()->route('account.orders.show', $order)->with('error', 'Невірний запит оплати.');
        }

        $confirmed = $this->paymentService->handleSuccess($order, $sessionId, $this->bookingService);

        if ($confirmed) {
            $order->master->notify(new OrderStatusNotification($order, 'Замовлення підтверджено клієнтом'));

            return redirect()->route('account.orders.show', $order)->with('success', 'Оплата успішна! Замовлення підтверджено.');
        }

        return redirect()->route('account.orders.show', $order)->with('info', 'Статус замовлення не змінився.');
    }

    public function cancel(Booking $order): RedirectResponse
    {
        abort_unless($order->client_id === auth()->id(), 403);

        return redirect()->route('account.orders.show', $order)->with('info', 'Оплату скасовано. Замовлення залишається активним.');
    }
}
