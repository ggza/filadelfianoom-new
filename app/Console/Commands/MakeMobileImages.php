<?php

namespace App\Console\Commands;

use App\Models\GalleryImage;
use App\Support\ImageTool;
use Illuminate\Console\Command;
use Throwable;

/**
 * A galéria/hero képek mobilos (max. 800 px széles) változatainak utólagos elkészítése a m/ almappába.
 * Idempotens: a meglévő változatot nem írja felül. Az eredeti képekhez nem nyúl. (Geza, 2026-10-11)
 */
class MakeMobileImages extends Command
{
    protected $signature = 'images:mobile {--dry-run : csak kiírja, mit készítene}';

    protected $description = 'Mobilos képváltozatok (srcset) készítése a meglévő galériaképekhez';

    public function handle(): int
    {
        $made = $skipped = $missing = 0;
        foreach (GalleryImage::orderBy('id')->get() as $img) {
            $src = public_path($img->file);
            $out = public_path(ImageTool::mobilePath($img->file));
            if (! is_file($src)) { $this->warn("hiányzik az eredeti: {$img->file}"); $missing++; continue; }
            if (is_file($out)) { $skipped++; continue; }
            if ($this->option('dry-run')) { $this->line('készülne: ' . ImageTool::mobilePath($img->file)); $made++; continue; }
            try {
                ImageTool::mobileVariantFromFile($src, $out);
                $this->line(sprintf('kész: %s (%d KB → %d KB)', ImageTool::mobilePath($img->file), filesize($src) / 1024, filesize($out) / 1024));
                $made++;
            } catch (Throwable $e) {
                $this->error("{$img->file}: " . $e->getMessage());
            }
        }
        $this->info(($this->option('dry-run') ? 'Készülne' : 'Elkészült') . ": $made, már megvolt: $skipped, hiányzó eredeti: $missing");
        return self::SUCCESS;
    }
}
