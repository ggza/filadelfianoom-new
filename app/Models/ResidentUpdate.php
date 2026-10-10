<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResidentUpdate extends Model
{
    protected $fillable = ['resident_id', 'author_id', 'title', 'body'];

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ResidentPhoto::class);
    }
}
