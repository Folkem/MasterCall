<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $phone
 * @property Role $role
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read MasterProfile|null $masterProfile
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'is_active' => 'boolean',
        ];
    }

    public function masterProfile(): HasOne
    {
        return $this->hasOne(MasterProfile::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'master_id');
    }

    public function clientOrders(): HasMany
    {
        return $this->hasMany(Booking::class, 'client_id');
    }

    public function masterOrders(): HasMany
    {
        return $this->hasMany(Booking::class, 'master_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'client_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'client_id');
    }

    public function isClient(): bool
    {
        return $this->role === Role::Client;
    }

    public function isMaster(): bool
    {
        return $this->role === Role::Master;
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }
}
