<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resident extends Model
{
    protected $fillable = ['name', 'room', 'moved_in_at', 'active'];

    protected $casts = ['moved_in_at' => 'date', 'active' => 'boolean'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('relation')->withTimestamps();
    }

    public function updates(): HasMany
    {
        return $this->hasMany(ResidentUpdate::class)->latest();
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ResidentPhoto::class)->latest();
    }
}
