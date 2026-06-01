<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
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
            'message' => "Нове повідомлення по замовленню #{$this->booking->id}",
            'url' => $notifiable->isMaster()
                ? route('cabinet.orders.show', $this->booking)
                : route('account.orders.show', $this->booking),
        ];
    }
}
