<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class ReviewService
{
    public function store(User $client, User $master, int $rating, ?string $comment): Review
    {
        $hasCompleted = Booking::where('client_id', $client->id)
            ->where('master_id', $master->id)
            ->where('status', OrderStatus::Completed)
            ->exists();

        if (! $hasCompleted) {
            throw ValidationException::withMessages([
                'review' => 'Ви можете залишити відгук лише після завершеного замовлення.',
            ]);
        }

        return Review::updateOrCreate(
            ['client_id' => $client->id, 'master_id' => $master->id],
            ['rating' => $rating, 'comment' => $comment]
        );
    }
}
