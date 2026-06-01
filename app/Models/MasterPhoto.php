<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $master_profile_id
 * @property string $photo_path
 * @property int $sort_order
 */
class MasterPhoto extends Model
{
    protected $fillable = ['master_profile_id', 'photo_path', 'sort_order'];

    public function masterProfile(): BelongsTo
    {
        return $this->belongsTo(MasterProfile::class);
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->photo_path);
    }
}
