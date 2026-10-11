<?php

namespace App\Http\Controllers;

use App\Models\Section;

/**
 * Adatvédelmi és süti-tájékoztató. A szöveg a „Szövegek” adminban szerkeszthető szekció;
 * első megnyitáskor a resources/views/legal/defaults alatti alapszövegből jön létre (migráció nem kell).
 */
class LegalController extends Controller
{
    private const PAGES = [
        'privacy' => ['key' => 'adatvedelem', 'label' => 'Adatvédelmi tájékoztató', 'title_hu' => 'Adatvédelmi tájékoztató', 'title_ro' => 'Politica de confidențialitate', 'sort' => 900],
        'cookies' => ['key' => 'sutik', 'label' => 'Sütitájékoztató', 'title_hu' => 'Sütitájékoztató', 'title_ro' => 'Politica de cookie-uri', 'sort' => 910],
    ];

    public function privacy()
    {
        return $this->show('privacy');
    }

    public function cookies()
    {
        return $this->show('cookies');
    }

    private function show(string $page)
    {
        $p = self::PAGES[$page];
        $vars = [
            'sessionCookie' => config('session.cookie'),
            'sessionMinutes' => config('session.lifetime'),
            'trustDays' => config('app.admin_mfa_trust_days'),
        ];

        $section = Section::firstOrCreate(['key' => $p['key']], [
            'admin_label' => $p['label'],
            'title_hu' => $p['title_hu'],
            'title_ro' => $p['title_ro'],
            'body_hu' => view("legal.defaults.{$page}_hu", $vars)->render(),
            'body_ro' => view("legal.defaults.{$page}_ro", $vars)->render(),
            'show_hu' => true,
            'show_ro' => true,
            'sort' => $p['sort'],
        ]);

        abort_unless($section->visible(), 404);

        return view('legal.page', ['section' => $section, 'page' => $page]);
    }
}
