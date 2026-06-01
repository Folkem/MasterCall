<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $bio
 * @property string|null $photo_path
 * @property string $city
 * @property int $years_experience
 * @property bool $is_available
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read Collection<ServiceCategory> $categories
 * @property-read Collection<MasterPhoto> $photos
 * @property-read Collection<Service> $services
 */
class MasterProfile extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'photo_path',
        'city',
        'years_experience',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
            'years_experience' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ServiceCategory::class, 'category_master_profile', 'master_profile_id', 'category_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(MasterPhoto::class)->orderBy('sort_order');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'master_id', 'user_id');
    }

    public function photoUrl(): string
    {
        if ($this->photo_path) {
            return Storage::disk('public')->url($this->photo_path);
        }

        return asset('images/placeholder-master.svg');
    }

    public function averageRating(): float
    {
        return round(
            Review::where('master_id', $this->user_id)->avg('rating') ?? 0,
            1
        );
    }

    public function reviewCount(): int
    {
        return Review::where('master_id', $this->user_id)->count();
    }

    public function minServicePrice(): ?float
    {
        return Service::where('master_id', $this->user_id)->min('price');
    }

    public function yearsLabel(): string
    {
        $n = $this->years_experience;
        $lastTwo = $n % 100;
        $lastOne = $n % 10;

        if ($lastTwo >= 11 && $lastTwo <= 19) {
            return "{$n} років";
        }

        return match ($lastOne) {
            1 => "{$n} рік",
            2, 3, 4 => "{$n} роки",
            default => "{$n} років",
        };
    }
}
