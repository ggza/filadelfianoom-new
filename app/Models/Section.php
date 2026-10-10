<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['key', 'admin_label', 'title_ro', 'title_hu', 'body_ro', 'body_hu', 'show_ro', 'show_hu', 'sort'];

    protected $casts = ['show_ro' => 'boolean', 'show_hu' => 'boolean'];

    public function title(?string $lang = null): ?string
    {
        $lang = $lang ?: app()->getLocale();
        return $this->{"title_$lang"};
    }

    public function body(?string $lang = null): ?string
    {
        $lang = $lang ?: app()->getLocale();
        $html = $this->{"body_$lang"};
        // a szövegben tárolt relatív "assets/..." képhivatkozások a telepítés helyétől függetlenül működjenek
        return $html === null ? null : str_replace('src="assets/', 'src="' . asset('assets') . '/', $html);
    }

    public function visible(?string $lang = null): bool
    {
        $lang = $lang ?: app()->getLocale();
        return (bool) $this->{"show_$lang"} && filled($this->body($lang));
    }
}
