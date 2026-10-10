<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Kapcsolatfelvételi üzenet. A személyes adatok titkosítva kerülnek az adatbázisba (APP_KEY). */
class ContactMessage extends Model
{
    protected $fillable = ['locale', 'name', 'email', 'phone', 'message', 'status', 'admin_note'];

    protected $casts = [
        'name' => 'encrypted',
        'email' => 'encrypted',
        'phone' => 'encrypted',
        'message' => 'encrypted',
        'admin_note' => 'encrypted',
    ];

    public const STATUSES = ['new', 'answered', 'closed'];

    public const LABELS = ['new' => 'Új', 'answered' => 'Megválaszolva', 'closed' => 'Lezárt'];
}
