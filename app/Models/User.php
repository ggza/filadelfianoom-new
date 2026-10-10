<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'phone', 'role', 'status', 'locale', 'requested_resident', 'requested_relation'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function residents(): BelongsToMany
    {
        return $this->belongsToMany(Resident::class)->withPivot('relation')->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /** Jelszó-visszaállító levél a felhasználó nyelvén (a Laravel alap angol levele helyett). */
    public function sendPasswordResetNotification($token): void
    {
        $lang = in_array($this->locale, ['hu', 'ro'], true) ? $this->locale : app()->getLocale();
        $body = __('account.reset_mail_body', [
            'name' => $this->name,
            'url' => route('password.reset', $token),
            'minutes' => config('auth.passwords.users.expire'),
        ], $lang);

        try {
            Mail::raw($body, fn ($m) => $m->to($this->email)->subject(__('account.reset_mail_subject', [], $lang) . ' – Filadelfia Noom'));
        } catch (Throwable $e) {
            Log::warning('Levél nem ment ki (jelszó-visszaállítás): ' . $e->getMessage());
        }
    }

    /** Láthatja-e az adott lakó adatait. */
    public function canSeeResident(Resident $resident): bool
    {
        if ($this->isAdmin()) return true;
        return $this->isApproved() && $this->residents()->whereKey($resident->id)->exists();
    }
}
