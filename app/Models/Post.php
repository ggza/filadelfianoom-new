<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title_ro', 'title_hu', 'body_ro', 'body_hu', 'image', 'published_at'];

    protected $casts = ['published_at' => 'datetime'];

    public function scopePublished(Builder $q): Builder
    {
        return $q->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /** Az adott nyelven megjeleníthető-e (van címe vagy szövege). */
    public function hasLang(string $lang): bool
    {
        return filled($this->{"title_$lang"}) || filled($this->{"body_$lang"});
    }

    public function title(?string $lang = null): ?string
    {
        $lang = $lang ?: app()->getLocale();
        return $this->{"title_$lang"};
    }

    public function body(?string $lang = null): ?string
    {
        $lang = $lang ?: app()->getLocale();
        return $this->{"body_$lang"};
    }
}
