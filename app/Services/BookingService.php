<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Booking;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function accept(Booking $booking, float $price, ?string $note = null): void
    {
        $this->ensureStatus($booking, OrderStatus::Pending);

        $booking->update([
            'status' => OrderStatus::Accepted,
            'price' => $price,
            'master_note' => $note,
        ]);
    }

    public function decline(Booking $booking, string $note): void
    {
        $this->ensureStatuses($booking, [OrderStatus::Pending, OrderStatus::Accepted, OrderStatus::Confirmed]);

        $booking->update([
            'status' => OrderStatus::Declined,
            'master_note' => $note,
        ]);
    }

    public function cancel(Booking $booking): void
    {
        $this->ensureStatuses($booking, [OrderStatus::Pending, OrderStatus::Accepted, OrderStatus::Confirmed]);

        if ($booking->scheduled_at->isPast()) {
            throw ValidationException::withMessages([
                'status' => 'Не можна скасувати замовлення після запланованої дати.',
            ]);
        }

        $booking->update(['status' => OrderStatus::Cancelled]);
    }

    public function confirm(Booking $booking): void
    {
        $this->ensureStatus($booking, OrderStatus::Accepted);

        $booking->update(['status' => OrderStatus::Confirmed]);
    }

    public function start(Booking $booking): void
    {
        $this->ensureStatus($booking, OrderStatus::Confirmed);

        $booking->update([
            'status' => OrderStatus::InProgress,
            'started_at' => now(),
        ]);
    }

    public function complete(Booking $booking): void
    {
        $this->ensureStatus($booking, OrderStatus::InProgress);

        $booking->update([
            'status' => OrderStatus::Completed,
            'completed_at' => now(),
        ]);
    }

    private function ensureStatus(Booking $booking, OrderStatus $expected): void
    {
        if ($booking->status !== $expected) {
            throw ValidationException::withMessages([
                'status' => 'Недопустима дія для поточного статусу замовлення.',
            ]);
        }
    }

    private function ensureStatuses(Booking $booking, array $expected): void
    {
        if (! in_array($booking->status, $expected)) {
            throw ValidationException::withMessages([
                'status' => 'Недопустима дія для поточного статусу замовлення.',
            ]);
        }
    }
}
