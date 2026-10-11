<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $fillable = ['file', 'thumb', 'width', 'height', 'caption_hu', 'caption_ro', 'in_hero', 'in_gallery', 'sort'];

    protected $casts = ['in_hero' => 'boolean', 'in_gallery' => 'boolean'];

    /**
     * srcset a mobilos (m/) és a nagy változattal; ha a mobilos fájl nincs meg a szerveren, null
     * (ilyenkor csak az eredeti kép töltődik be, nem lesz törött kép).
     */
    public function srcset(): ?string
    {
        $m = \App\Support\ImageTool::mobilePath($this->file);
        if (! is_file(public_path($m))) return null;
        [$mw] = @getimagesize(public_path($m)) ?: [\App\Support\ImageTool::MOBILE_W];
        return asset($m) . " {$mw}w, " . asset($this->file) . ' ' . ($this->width ?: 1600) . 'w';
    }

    /** A felirat az aktuális nyelven; ha nincs, a másikon. [szöveg, nyelv] */
    public function caption(?string $lang = null): array
    {
        $lang = $lang ?: app()->getLocale();
        $other = $lang === 'ro' ? 'hu' : 'ro';
        if (filled($this->{"caption_$lang"})) return [$this->{"caption_$lang"}, $lang];
        return [(string) $this->{"caption_$other"}, $other];
    }
}
