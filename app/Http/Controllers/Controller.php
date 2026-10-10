<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

abstract class Controller
{
    // egyszerű szöveges levél; hiba esetén csak naplóz, a műveletet nem akasztja meg
    protected function sendMail(string $to, string $subject, string $body, ?string $replyEmail = null, ?string $replyName = null): bool
    {
        try {
            Mail::raw($body, function ($m) use ($to, $subject, $replyEmail, $replyName) {
                $m->to($to)->subject($subject . ' – Filadelfia Noom');
                if ($replyEmail) {
                    $m->replyTo($replyEmail, $replyName);
                }
            });
            return true;
        } catch (Throwable $e) {
            Log::warning("Levél nem ment ki ({$subject}): " . $e->getMessage());
            return false;
        }
    }

    // értesítő levél az adminnak (CONTACT_TO)
    protected function notifyAdmin(string $subject, string $body, ?string $replyEmail = null, ?string $replyName = null): void
    {
        if ($to = config('app.contact_to')) {
            $this->sendMail($to, $subject, $body, $replyEmail, $replyName);
        }
    }
}
