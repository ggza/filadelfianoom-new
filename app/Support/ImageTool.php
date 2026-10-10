<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Feltöltött képek átméretezése GD-vel: egy nagy (max. 1600 px) és egy kicsi (480×320, vágva) változat.
 * WebP-be ment, ha a PHP tudja, különben JPEG-be.
 */
class ImageTool
{
    public const MAX = 1600;
    public const THUMB_W = 480;
    public const THUMB_H = 320;

    /**
     * @return array{file:string, thumb:string, width:int, height:int}  útvonalak a $baseDir-hez képest
     */
    public static function store(UploadedFile $upload, string $baseDir, string $relPrefix = ''): array
    {
        $src = self::load($upload->getRealPath());
        $ext = function_exists('imagewebp') ? 'webp' : 'jpg';
        $name = now()->format('Ymd-His') . '-' . Str::lower(Str::random(6)) . '.' . $ext;

        if (! is_dir("$baseDir/kicsi")) mkdir("$baseDir/kicsi", 0775, true);

        [$w, $h] = [imagesx($src), imagesy($src)];
        $scale = min(1, self::MAX / max($w, $h));
        $big = self::resample($src, (int) round($w * $scale), (int) round($h * $scale));
        self::save($big, "$baseDir/$name", $ext);

        // kicsi kép: középre vágva 3:2-re
        $ratio = self::THUMB_W / self::THUMB_H;
        if ($w / $h > $ratio) { $cw = (int) round($h * $ratio); $ch = $h; $cx = (int) (($w - $cw) / 2); $cy = 0; }
        else { $cw = $w; $ch = (int) round($w / $ratio); $cx = 0; $cy = (int) (($h - $ch) / 2); }
        $thumb = imagecreatetruecolor(self::THUMB_W, self::THUMB_H);
        imagecopyresampled($thumb, $src, 0, 0, $cx, $cy, self::THUMB_W, self::THUMB_H, $cw, $ch);
        self::save($thumb, "$baseDir/kicsi/$name", $ext);

        $out = ['file' => $relPrefix . $name, 'thumb' => $relPrefix . 'kicsi/' . $name, 'width' => imagesx($big), 'height' => imagesy($big)];
        imagedestroy($src); imagedestroy($big); imagedestroy($thumb);
        return $out;
    }

    private static function load(string $path)
    {
        $info = @getimagesize($path);
        if (! $info) throw new RuntimeException('Nem kép.');
        $img = match ($info[2]) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_WEBP => imagecreatefromwebp($path),
            IMAGETYPE_GIF => imagecreatefromgif($path),
            default => throw new RuntimeException('Nem támogatott képformátum.'),
        };
        // telefonos fotók elforgatása az EXIF alapján
        if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($path);
            $o = $exif['Orientation'] ?? 1;
            if ($o == 3) $img = imagerotate($img, 180, 0);
            elseif ($o == 6) $img = imagerotate($img, -90, 0);
            elseif ($o == 8) $img = imagerotate($img, 90, 0);
        }
        return $img;
    }

    private static function resample($src, int $w, int $h)
    {
        $dst = imagecreatetruecolor($w, $h);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, imagesx($src), imagesy($src));
        return $dst;
    }

    private static function save($img, string $path, string $ext): void
    {
        $ok = $ext === 'webp' ? imagewebp($img, $path, 80) : imagejpeg($img, $path, 82);
        if (! $ok) throw new RuntimeException('A kép mentése nem sikerült.');
    }
}
