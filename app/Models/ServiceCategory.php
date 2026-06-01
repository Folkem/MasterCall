<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $icon
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ServiceCategory extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'description'];

    public function masters(): BelongsToMany
    {
        return $this->belongsToMany(MasterProfile::class, 'category_master_profile', 'category_id', 'master_profile_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'category_id');
    }
}
