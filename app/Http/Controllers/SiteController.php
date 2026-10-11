<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\ContactMessage;
use App\Models\GalleryImage;
use App\Models\Post;
use App\Models\Section;
use App\Rules\Turnstile;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function home()
    {
        $lang = app()->getLocale();
        $sections = Section::all()->keyBy('key');
        $images = GalleryImage::orderBy('sort')->orderBy('id')->get();
        $posts = Post::published()->latest('published_at')->get()->filter(fn ($p) => $p->hasLang($lang))->take(6);

        return view('site.home', [
            'lang' => $lang,
            's' => $sections,
            'hero' => $images->where('in_hero', true)->values(),
            'gallery' => $images->where('in_gallery', true)->values(),
            'posts' => $posts,
        ]);
    }

    public function storeAdmission(Request $request)
    {
        $data = $request->validateWithBag('admission', [
            'representative' => 'required|string|max:1000',
            'resident' => 'required|string|max:1000',
            'diseases' => 'nullable|string|max:2000',
            'payment_method' => 'nullable|string|max:500',
            'preferred_date' => 'nullable|date',
            'phone' => 'required|string|max:60',
            'email' => 'nullable|email|max:190',
            'consent' => 'accepted', // kifejezett hozzájárulás (egészségügyi adatok)
            'website' => 'prohibited', // rejtett csapda a robotoknak
            'cf-turnstile-response' => [new Turnstile],
        ]);
        unset($data['website'], $data['consent'], $data['cf-turnstile-response']);

        $admission = Admission::create($data + ['locale' => app()->getLocale(), 'source' => 'web']);

        // a jelentkezés adatai titkosítva tárolódnak, ezért a levélbe csak a hivatkozás kerül
        $this->notifyAdmin(
            'Új jelentkezés',
            "Új jelentkezés érkezett a weboldalon (nyelv: " . strtoupper($admission->locale) . ").\n\n"
            . "Az adatok az admin felületen olvashatók:\n" . route('admin.admissions.show', $admission),
        );

        // a képviselő adatai mezőben CNP és cím is lehet, ezért a visszaigazolás név nélkül szól
        $this->acknowledge($admission->email, '', 'admission');

        return redirect()->to(url('/') . '?sent=1#jelentkezes')->with('admission_sent', true);
    }

    public function storeContact(Request $request)
    {
        $data = $request->validateWithBag('contact', [
            'name' => 'required|string|max:190',
            'email' => 'required|email|max:190',
            'phone' => 'nullable|string|max:60',
            'message' => 'required|string|max:5000',
            'consent' => 'accepted',
            'website' => 'prohibited', // rejtett csapda a robotoknak
            'cf-turnstile-response' => [new Turnstile],
        ]);
        unset($data['website'], $data['consent'], $data['cf-turnstile-response']);

        $msg = ContactMessage::create($data + ['locale' => app()->getLocale()]);

        $this->notifyAdmin(
            'Új üzenet',
            "Új üzenet a weboldalról\n\nNév: {$msg->name}\nE-mail: {$msg->email}\nTelefon: " . ($msg->phone ?: '–')
            . "\nNyelv: " . strtoupper($msg->locale) . "\n\n{$msg->message}\n\n" . route('admin.messages.show', $msg),
            $msg->email,
            $msg->name,
        );

        $this->acknowledge($msg->email, $msg->name, 'contact');

        return redirect()->to(url('/') . '?sent=contact#contact')->with('contact_sent', true);
    }
}
