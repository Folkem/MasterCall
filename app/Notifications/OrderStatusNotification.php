<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    public function __construct(
        private Booking $booking,
        private string $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'message' => $this->message,
            'url' => $notifiable->isMaster()
                ? route('cabinet.orders.show', $this->booking)
                : route('account.orders.show', $this->booking),
        ];
    }
}
