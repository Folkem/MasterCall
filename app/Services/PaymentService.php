<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Booking;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCheckoutSession(Booking $booking, string $successUrl, string $cancelUrl): Session
    {
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'uah',
                    'product_data' => [
                        'name' => $booking->service?->name ?? 'Послуга майстра',
                        'description' => "Замовлення #{$booking->id}",
                    ],
                    'unit_amount' => (int) round((float) $booking->price * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'client_reference_id' => (string) $booking->id,
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);

        $booking->update(['stripe_session_id' => $session->id]);

        return $session;
    }

    public function handleSuccess(Booking $booking, string $sessionId, BookingService $bookingService): bool
    {
        if ($booking->status !== OrderStatus::Accepted) {
            return false;
        }

        if ($booking->stripe_session_id !== $sessionId) {
            return false;
        }

        $session = Session::retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            return false;
        }

        $bookingService->confirm($booking);

        return true;
    }
}
