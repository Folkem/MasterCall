<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $booking_id
 * @property int $sender_id
 * @property string $body
 * @property Carbon|null $read_at
 * @property Carbon|null $created_at
 */
class Message extends Model
{
    protected $fillable = ['booking_id', 'sender_id', 'body', 'read_at'];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function formattedTime(): string
    {
        if ($this->created_at->isToday()) {
            return $this->created_at->format('H:i');
        }
        if ($this->created_at->isYesterday()) {
            return 'Вчора '.$this->created_at->format('H:i');
        }

        return $this->created_at->format('d.m.Y H:i');
    }
}
