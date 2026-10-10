<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResidentPhoto extends Model
{
    protected $fillable = ['resident_id', 'resident_update_id', 'path', 'thumb', 'caption'];

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }
}
