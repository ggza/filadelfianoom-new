<?php

namespace Database\Seeders;

use App\Models\GalleryImage;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Kezdő tartalom a saját változatból: a régi oldal szövegei (RO/HU), a galéria és egy admin felhasználó.
 * Többször is futtatható: a meglévő sorokat nem duplikálja.
 */
class DatabaseSeeder extends Seeder
{
    /** saját változat kulcsa => Laravel szekciókulcs, admin felirat, sorrend */
    private const SECTIONS = [
        'rolunk'       => ['fundatia', 'Az Alapítványról', 10],
        'munkank'      => ['servicii', 'Szolgáltatások', 20],
        'hatas'        => ['efecte', 'A szociális gondoskodás hatása', 30],
        'tamogatas'    => ['suport', 'Támogatás', 40],
        'kapcsolat'    => ['contact', 'Elérhetőség', 50],
        'jelentkezes'  => ['jelentkezes', 'Jelentkezés – lépések', 60],
        'dokumentumok' => ['dokumentumok', 'Jelentkezés – dokumentumok', 70],
        'raport'       => ['raport', 'Éves jelentés (Raport anual)', 80],
        'impresszum'   => ['impresszum', 'Impresszum (lábléc)', 90],
    ];

    public function run(): void
    {
        $this->sections();
        $this->gallery();
        $this->admin();
    }

    private function sections(): void
    {
        $dir = database_path('seeders/data');
        $ro = json_decode(File::get("$dir/ro.json"), true);
        $hu = json_decode(File::get("$dir/hu.json"), true);
        $fix = fn (?string $html) => $html === null ? null
            : str_replace('../sites/default/files/paypal-button-kicsi.png', 'assets/docs/paypal.png', $html);

        foreach (self::SECTIONS as $old => [$key, $label, $sort]) {
            if (Section::where('key', $key)->exists()) continue;
            $r = $ro[$old] ?? null;
            // magyar szöveg csak ott, ahol a régi oldalon is volt magyar változat (lang = hu)
            $h = isset($hu[$old]) && ($hu[$old]['lang'] ?? 'hu') === 'hu' ? $hu[$old] : null;
            // az impresszumnak nem volt magyar változata: mindkét nyelven a román szöveg látszik
            if ($old === 'impresszum') $h = $r;

            Section::create([
                'key' => $key,
                'admin_label' => $label,
                'title_ro' => $r['title'] ?? null,
                'title_hu' => $h['title'] ?? null,
                'body_ro' => $fix($r['html'] ?? null),
                'body_hu' => $fix($h['html'] ?? null),
                'show_ro' => true,
                'show_hu' => $h !== null,
                'sort' => $sort,
            ]);
        }
    }

    private function gallery(): void
    {
        if (GalleryImage::exists()) return;

        $src = database_path('seeders/data/galeria');
        $dst = public_path('uploads/galeria');
        File::ensureDirectoryExists("$dst/kicsi");

        $meta = json_decode(File::get(database_path('seeders/data/galeria.json')), true)['images'];
        // háttérképek a hero-képváltóban: minden fotó után felváltva (Geza kérése, 2026-10-08)
        $bgs = ['assets/img/hatter-1.webp', 'assets/img/hatter-2.webp'];
        $sort = 0;

        foreach ($meta as $i => $img) {
            File::copy("$src/{$img['file']}", "$dst/{$img['file']}");
            File::copy("$src/kicsi/{$img['file']}", "$dst/kicsi/{$img['file']}");
            GalleryImage::create([
                'file' => "uploads/galeria/{$img['file']}",
                'thumb' => "uploads/galeria/kicsi/{$img['file']}",
                'width' => $img['w'], 'height' => $img['h'],
                'caption_hu' => $img['caption_hu'], 'caption_ro' => $img['caption_ro'],
                'in_hero' => true, 'in_gallery' => true,
                'sort' => $sort += 10,
            ]);

            {
                $bg = $bgs[$i % count($bgs)];
                [$w, $h] = @getimagesize(public_path($bg)) ?: [1920, 1300];
                GalleryImage::create([
                    'file' => $bg, 'thumb' => $bg, 'width' => $w, 'height' => $h,
                    'caption_hu' => 'A kastély és a park', 'caption_ro' => 'Castelul și parcul',
                    'in_hero' => true, 'in_gallery' => false,
                    'sort' => $sort += 10,
                ]);
            }
        }
    }

    private function admin(): void
    {
        if (User::where('role', 'admin')->exists()) return;

        $email = env('ADMIN_EMAIL', 'admin@filadelfianoom.ro');
        $password = env('ADMIN_PASSWORD') ?: Str::password(14, symbols: false);

        User::create([
            'name' => env('ADMIN_NAME', 'Admin'),
            'email' => $email,
            'password' => $password,
            'role' => 'admin',
            'status' => 'approved',
            'locale' => 'hu',
        ]);

        $this->command?->info("Admin: $email / $password");
    }
}
