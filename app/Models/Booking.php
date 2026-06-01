<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $client_id
 * @property int $master_id
 * @property int|null $service_id
 * @property int $category_id
 * @property string $address
 * @property Carbon $scheduled_at
 * @property string $description
 * @property float|null $price
 * @property OrderStatus $status
 * @property string|null $stripe_session_id
 * @property string|null $master_note
 * @property Carbon|null $started_at
 * @property Carbon|null $completed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Booking extends Model
{
    protected $fillable = [
        'client_id',
        'master_id',
        'service_id',
        'category_id',
        'address',
        'scheduled_at',
        'description',
        'price',
        'status',
        'stripe_session_id',
        'master_note',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'price' => 'decimal:2',
            'status' => OrderStatus::class,
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function master(): BelongsTo
    {
        return $this->belongsTo(User::class, 'master_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function workReport(): HasOne
    {
        return $this->hasOne(WorkReport::class);
    }

    public function canBeCancelledByClient(): bool
    {
        return in_array($this->status, [OrderStatus::Pending, OrderStatus::Accepted, OrderStatus::Confirmed])
            && $this->scheduled_at->isFuture();
    }
}
