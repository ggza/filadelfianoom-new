<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $fillable = ['file', 'thumb', 'width', 'height', 'caption_hu', 'caption_ro', 'in_hero', 'in_gallery', 'sort'];

    protected $casts = ['in_hero' => 'boolean', 'in_gallery' => 'boolean'];

    /** A felirat az aktuális nyelven; ha nincs, a másikon. [szöveg, nyelv] */
    public function caption(?string $lang = null): array
    {
        $lang = $lang ?: app()->getLocale();
        $other = $lang === 'ro' ? 'hu' : 'ro';
        if (filled($this->{"caption_$lang"})) return [$this->{"caption_$lang"}, $lang];
        return [(string) $this->{"caption_$other"}, $other];
    }
}
