<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    public function __construct(private Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'message' => "Нове замовлення #{$this->booking->id} від {$this->booking->client->name}",
            'url' => route('cabinet.orders.show', $this->booking),
        ];
    }
}
