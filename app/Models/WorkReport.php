<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $booking_id
 * @property int $master_id
 * @property int $client_id
 * @property string $content
 */
class WorkReport extends Model
{
    protected $fillable = ['booking_id', 'master_id', 'client_id', 'content'];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function master(): BelongsTo
    {
        return $this->belongsTo(User::class, 'master_id');
    }
}
