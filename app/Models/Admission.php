<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Beköltözési jelentkezés. A személyes adatok titkosítva kerülnek az adatbázisba (APP_KEY). */
class Admission extends Model
{
    protected $fillable = [
        'locale', 'representative', 'resident', 'diseases', 'payment_method',
        'preferred_date', 'phone', 'email', 'status', 'admin_note', 'source', 'legacy_serial',
    ];

    protected $casts = [
        'representative' => 'encrypted',
        'resident' => 'encrypted',
        'diseases' => 'encrypted',
        'payment_method' => 'encrypted',
        'phone' => 'encrypted',
        'email' => 'encrypted',
        'admin_note' => 'encrypted',
        'preferred_date' => 'date',
    ];

    public const STATUSES = ['new', 'in_progress', 'closed'];
}
