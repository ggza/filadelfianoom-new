@extends('layouts.site')

@php
  $sec = fn ($key) => ($s[$key] ?? null) && $s[$key]->visible($lang) ? $s[$key] : null;
  $hasReport = $sec('raport') && __('site.nav_report');
  $n = 0;
  $num = function () use (&$n) { return sprintf('%02d', ++$n); };
  $chev = ['prev' => 'M15 5l-7 7 7 7', 'next' => 'M9 5l7 7-7 7'];
  $admissionErrors = $errors->admission ?? null;
  $openApply = session('admission_sent') || ($admissionErrors && $admissionErrors->any());
  $contactErrors = $errors->contact ?? null;
  $openContact = session('contact_sent') || ($contactErrors && $contactErrors->any());
@endphp

@section('body_class', 'is-intro')

@section('menu_items')
  @if ($sec('fundatia'))<li><a href="#fundatia" data-nav>{{ __('site.nav.fundatia') }}</a></li>@endif
  @if ($sec('servicii'))<li><a href="#servicii" data-nav>{{ __('site.nav.servicii') }}</a></li>@endif
  @if ($gallery->count())<li><a href="#galerie" data-nav>{{ __('site.nav.galerie') }}</a></li>@endif
  @if ($posts->count())<li><a href="#stiri" data-nav>{{ __('site.nav.stiri') }}</a></li>@endif
  @if ($sec('suport'))<li><a href="#suport" data-nav>{{ __('site.nav.suport') }}</a></li>@endif
  @if ($sec('contact'))<li><a href="#contact" data-nav>{{ __('site.nav.contact') }}</a></li>@endif
  @if ($hasReport)<li><button type="button" class="nav-link" data-open="dlg-report">{{ __('site.nav_report') }}</button></li>@endif
@endsection

@section('menu_cta')
  <div class="offcanvas__cta"><button type="button" class="pill pill--big" data-open="dlg-apply">{{ __('site.nav_apply') }}</button></div>
@endsection

@section('content')
<!-- ============ Hero: szlogen-intro, majd fotó-carousel ============ -->
<section class="hero" aria-labelledby="slogan">
  <div class="hero__bgs" aria-hidden="true">
    <div class="hero__bg" style="background-image:url({{ asset('assets/img/hatter-1.webp') }})"></div>
    <div class="hero__bg" style="background-image:url({{ asset('assets/img/hatter-2.webp') }})"></div>
    <div class="hero__veil"></div>
  </div>

  <div class="hero__photos carousel" data-carousel="hero" aria-roledescription="carousel" aria-label="{{ __('site.nav.galerie') }}">
    @foreach ($hero as $i => $img)
      @php [$cap, $capLang] = $img->caption($lang); @endphp
      <figure class="carousel__slide{{ $i === 0 ? ' is-active' : '' }}" aria-roledescription="slide" aria-label="{{ ($i + 1) . ' / ' . $hero->count() }}" @if($i) aria-hidden="true" @endif>
        <img src="{{ asset($img->file) }}" alt="{{ $cap }}" lang="{{ $capLang }}" loading="{{ $i < 2 ? 'eager' : 'lazy' }}" width="{{ $img->width }}" height="{{ $img->height }}">
        @if ($cap)<figcaption lang="{{ $capLang }}">{{ $cap }}</figcaption>@endif
      </figure>
    @endforeach
    <div class="carousel__controls">
      <button type="button" class="round" data-prev aria-label="{{ __('site.prev') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $chev['prev'] }}"/></svg></button>
      <button type="button" class="round" data-toggle aria-label="{{ __('site.pause') }}" data-label-pause="{{ __('site.pause') }}" data-label-play="{{ __('site.play') }}">
        <svg class="i-pause" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5v14M15 5v14"/></svg>
        <svg class="i-play" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5l11 7-11 7z"/></svg>
      </button>
      <button type="button" class="round" data-next aria-label="{{ __('site.next') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $chev['next'] }}"/></svg></button>
    </div>
  </div>

  <h1 class="slogan" id="slogan">
    @foreach (__('site.slogan') as $k => $line)
      <span class="slogan__line slogan__line--{{ $k + 1 }}">{{ $line }}</span>
    @endforeach
  </h1>

  <p class="hero__tag" aria-hidden="true">@foreach (__('site.slogan') as $line)<span>{{ $line }}</span>@endforeach</p>

  <button type="button" class="skip-intro" data-skip-intro>{{ __('site.skip_intro') }}</button>
  <a class="hero__down" href="#fundatia"><span>{{ __('site.down') }}</span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg></a>
</section>

<main id="main">

  @if ($x = $sec('fundatia'))
  <section id="fundatia" class="section section--paper" tabindex="-1" aria-labelledby="h-fundatia">
    <div class="wrap split">
      <div class="split__aside">
        <p class="eyebrow">{{ $num() }} · {{ __('site.nav.fundatia') }}</p>
        <h2 id="h-fundatia" class="h-display">{{ $x->title($lang) }}</h2>
        @if ($arch = $gallery->get(2) ?? $gallery->first())
          @php [$ac, $al] = $arch->caption($lang); @endphp
          <figure class="arch"><img src="{{ asset($arch->file) }}" alt="{{ $ac }}" lang="{{ $al }}" loading="lazy"></figure>
        @endif
      </div>
      <div class="split__main prose prose--lead">{!! $x->body($lang) !!}@include('site.partials.deco-icons', ['icons' => ['otthon', 'sziv', 'apolas', 'level']])</div>
    </div>
  </section>
  @endif

  @if ($x = $sec('servicii'))
  <section id="servicii" class="section section--cream" tabindex="-1" aria-labelledby="h-servicii">
    <div class="wrap">
      <header class="section__head">
        <p class="eyebrow">{{ $num() }} · {{ __('site.nav.servicii') }}</p>
        <h2 id="h-servicii" class="h-display">{{ $x->title($lang) }}</h2>
      </header>
      <div class="prose prose--columns">{!! $x->body($lang) !!}</div>

      @if ($e = $sec('efecte'))
      <div class="effects" id="efecte">
        <h3 class="effects__title">{{ $e->title($lang) }}</h3>
        <div class="effects__body prose">{!! $e->body($lang) !!}@include('site.partials.deco-icons', ['icons' => ['kozosseg', 'sziv', 'nap', 'otthon']])</div>
      </div>
      @endif
    </div>
  </section>
  @endif

  @if ($gallery->count())
  <section id="galerie" class="section section--lilac" tabindex="-1" aria-labelledby="h-galerie">
    <div class="wrap">
      <header class="section__head section__head--center">
        <p class="eyebrow">{{ $num() }}</p>
        <h2 id="h-galerie" class="h-display">{{ __('site.nav.galerie') }}</h2>
      </header>

      <div class="gallery carousel" data-carousel="gallery" aria-roledescription="carousel" aria-label="{{ __('site.nav.galerie') }}">
        <div class="gallery__stage">
          @foreach ($gallery as $i => $img)
            @php [$cap, $capLang] = $img->caption($lang); @endphp
            <figure class="carousel__slide{{ $i === 0 ? ' is-active' : '' }}" aria-roledescription="slide" aria-label="{{ ($i + 1) . ' / ' . $gallery->count() }}" @if($i) aria-hidden="true" @endif>
              <button type="button" class="gallery__zoom" data-lightbox="{{ $i }}" aria-label="{{ __('site.open_img') }}: {{ $cap }}" @if($i) tabindex="-1" @endif>
                <img src="{{ asset($img->file) }}" alt="{{ $cap }}" lang="{{ $capLang }}" loading="lazy" width="{{ $img->width }}" height="{{ $img->height }}">
                <span class="gallery__zoom-ico" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="6"/><path d="M20 20l-4.5-4.5M11 8v6M8 11h6"/></svg></span>
              </button>
              <figcaption lang="{{ $capLang }}"><span class="num">{{ sprintf('%02d', $i + 1) }}</span> {{ $cap }}</figcaption>
            </figure>
          @endforeach
          <button type="button" class="round round--side round--left" data-prev aria-label="{{ __('site.prev') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $chev['prev'] }}"/></svg></button>
          <button type="button" class="round round--side round--right" data-next aria-label="{{ __('site.next') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $chev['next'] }}"/></svg></button>
        </div>
        <ol class="gallery__thumbs">
          @foreach ($gallery as $i => $img)
            <li><button type="button" data-goto="{{ $i }}" aria-label="{{ $i + 1 }}. {{ $img->caption($lang)[0] }}" @if($i === 0) aria-current="true" @endif>
              <img src="{{ asset($img->thumb) }}" alt="" width="480" height="320">
            </button></li>
          @endforeach
        </ol>
        @if (__('site.caption_lang_note') && $gallery->contains(fn ($g) => blank($g->{'caption_' . $lang}) && filled($g->caption_hu)))
          <p class="gallery__note">{{ __('site.caption_lang_note') }}</p>
        @endif
      </div>
    </div>
  </section>
  @endif

  @if ($posts->count())
  <section id="stiri" class="section section--paper" tabindex="-1" aria-labelledby="h-stiri">
    <div class="wrap">
      <header class="section__head">
        <p class="eyebrow">{{ $num() }} · {{ __('site.nav.stiri') }}</p>
        <h2 id="h-stiri" class="h-display">{{ __('site.nav.stiri') }}</h2>
      </header>
      <div class="posts">
        @foreach ($posts as $post)
          <article class="post" id="post-{{ $post->id }}">
            @if ($post->image)<img class="post__img" src="{{ asset($post->image) }}" alt="" loading="lazy">@endif
            <div class="post__in">
              <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('Y.m.d.') }}</time>
              @if ($post->title($lang))<h3>{{ $post->title($lang) }}</h3>@endif
              <div class="prose">{!! $post->body($lang) !!}</div>
            </div>
          </article>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  @if ($x = $sec('suport'))
  <section id="suport" class="section section--paper" tabindex="-1" aria-labelledby="h-suport">
    <div class="wrap split split--reverse">
      <div class="split__main">
        <p class="eyebrow">{{ $num() }} · {{ __('site.nav.suport') }}</p>
        <h2 id="h-suport" class="h-display">{{ $x->title($lang) }}</h2>
        <div class="prose prose--support">{!! $x->body($lang) !!}</div>
      </div>
      <aside class="split__aside donate">
        <a class="donate__card donate__card--pdf" href="{{ asset('assets/docs/formular-230.pdf') }}" target="_blank" rel="noopener">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 2h9l5 5v15H6z"/><path d="M14 2v6h6M9 13h6M9 17h6"/></svg>
          <span><strong>{{ __('site.form230') }}</strong><small>{{ __('site.form230_sub') }}</small></span>
        </a>
        <div class="donate__card donate__iban">
          <p><span>IBAN Lei</span><code>RO19 BTRL RONC RT0P 9698 5901</code>
            <button type="button" class="copy" data-copy="RO19BTRLRONCRT0P96985901" data-done="{{ __('site.copied') }}">{{ __('site.copy') }}</button></p>
          <p><span>IBAN EUR</span><code>RO04 BTRL EURC RT0P 9698 5901</code>
            <button type="button" class="copy" data-copy="RO04BTRLEURCRT0P96985901" data-done="{{ __('site.copied') }}">{{ __('site.copy') }}</button></p>
          <p class="donate__bank">Banca Transilvania Oradea · CF 14258752</p>
        </div>
      </aside>
    </div>
  </section>
  @endif

  @if ($x = $sec('contact'))
  <section id="contact" class="section section--green" tabindex="-1" aria-labelledby="h-contact">
    <div class="wrap">
      <header class="section__head">
        <p class="eyebrow eyebrow--light">{{ $num() }} · {{ __('site.nav.contact') }}</p>
        <h2 id="h-contact" class="h-display">{{ $x->title($lang) }}</h2>
      </header>
      <div class="contact">
        <div class="contact__card prose prose--contact">{!! $x->body($lang) !!}</div>
        <div class="contact__actions">
          <a class="action" href="tel:+40259432020"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 3h4l2 5-2.5 1.5a11 11 0 005 5L15 12l5 2v4a2 2 0 01-2 2A16 16 0 013 5a2 2 0 012-2z"/></svg><span>{{ __('site.call') }}<small>+40 259 432 020</small></span></a>
          <button type="button" class="action" data-open="dlg-contact"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5h18v14H3z"/><path d="M3 6l9 7 9-7"/></svg><span>{{ __('site.write') }}<small>{{ __('site.contact_btn_sub') }}</small></span></button>
          <a class="action" href="https://www.google.com/maps/search/?api=1&amp;query=Filadelfia+Noom+S%C4%83lard+547+Bihor" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s7-6.2 7-12a7 7 0 10-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg><span>{{ __('site.map') }}<small>{{ $lang === 'ro' ? 'Sălard 547-548, Bihor' : 'Szalárd 547-548, Bihor' }}</small></span></a>
          <button type="button" class="action action--form" data-open="dlg-apply"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5M10 13h6M10 17h6"/></svg><span>{{ __('site.nav_apply') }}<small>{{ __('site.tab_form') }}</small></span></button>
        </div>
      </div>
    </div>
  </section>
  @endif
</main>

<!-- ============ Modal: Instituţionalizare / Jelentkezés ============ -->
<dialog id="dlg-apply" class="modal modal--wide" aria-labelledby="dlg-apply-title" @if($openApply) data-autoopen="form" @endif>
  <div class="modal__head">
    <h2 id="dlg-apply-title">{{ __('site.nav_apply') }}</h2>
    <button type="button" class="modal__close" data-close aria-label="{{ __('site.close') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
  </div>
  <div class="tabs" role="tablist" aria-label="{{ __('site.nav_apply') }}">
    <button type="button" role="tab" id="tab-steps" aria-controls="panel-steps" aria-selected="true">{{ __('site.tab_steps') }}</button>
    <button type="button" role="tab" id="tab-docs" aria-controls="panel-docs" aria-selected="false" tabindex="-1">{{ __('site.tab_docs') }}</button>
    <button type="button" role="tab" id="tab-form" aria-controls="panel-form" aria-selected="false" tabindex="-1">{{ __('site.tab_form') }}</button>
  </div>
  <div class="modal__body">
    <div role="tabpanel" id="panel-steps" aria-labelledby="tab-steps" tabindex="0">
      @if ($x = $sec('jelentkezes'))
        <h3 class="modal__sub">{{ $x->title($lang) }}</h3>
        <div class="prose prose--steps">{!! $x->body($lang) !!}</div>
      @endif
      <p class="steps-more"><button type="button" class="pill pill--big" data-tab-go="form">{{ __('site.tab_form') }} →</button></p>
    </div>
    <div role="tabpanel" id="panel-docs" aria-labelledby="tab-docs" tabindex="0" hidden>
      @if ($x = $sec('dokumentumok'))
        <h3 class="modal__sub">{{ $x->title($lang) }}</h3>
        <div class="prose prose--checklist">{!! $x->body($lang) !!}</div>
      @endif
    </div>
    <div role="tabpanel" id="panel-form" aria-labelledby="tab-form" tabindex="0" hidden>
      @if (session('admission_sent'))
        <p class="note note--ok" role="status">{{ __('site.form_sent') }}</p>
      @else
      <form class="apply-form" method="post" action="{{ route('admission.store') }}">
        @csrf
        @if ($admissionErrors && $admissionErrors->any())
          <p class="note note--err" role="alert">{{ __('site.form_errors') }}</p>
        @endif
        @foreach (['representative' => 'text', 'resident' => 'text', 'diseases' => 'text', 'payment_method' => 'text', 'preferred_date' => 'date', 'phone' => 'tel', 'email' => 'email'] as $field => $type)
          <div class="field @if($admissionErrors && $admissionErrors->has($field)) field--err @endif">
            <label for="f-{{ $field }}">{{ __('admission.' . $field) }}@if(in_array($field, ['representative', 'resident', 'phone'])) <span aria-hidden="true">*</span>@endif</label>
            <input id="f-{{ $field }}" name="{{ $field }}" type="{{ $type }}" value="{{ old($field) }}"
              @if(in_array($field, ['representative', 'resident', 'phone'])) required @endif
              @if($field === 'email') autocomplete="email" @elseif($field === 'phone') autocomplete="tel" @endif>
            @if ($admissionErrors && $admissionErrors->has($field))<small class="field__err">{{ $admissionErrors->first($field) }}</small>@endif
          </div>
        @endforeach
        <div class="hp" aria-hidden="true"><label>Website <input name="website" tabindex="-1" autocomplete="off"></label></div>
        @include('partials.turnstile', ['bag' => $admissionErrors])
        <p class="note">{{ __('site.privacy') }}</p>
        <div class="apply-form__foot">
          <button type="submit" class="pill pill--big">{{ __('site.submit') }}</button>
        </div>
      </form>
      @endif
    </div>
  </div>
</dialog>

@if ($hasReport)
<dialog id="dlg-report" class="modal" aria-labelledby="dlg-report-title">
  <div class="modal__head">
    <h2 id="dlg-report-title">{{ $s['raport']->title($lang) }}</h2>
    <button type="button" class="modal__close" data-close aria-label="{{ __('site.close') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
  </div>
  <div class="modal__body prose prose--report">{!! $s['raport']->body($lang) !!}</div>
</dialog>
@endif

<!-- ============ Modal: kapcsolatfelvételi űrlap ============ -->
<dialog id="dlg-contact" class="modal" aria-labelledby="dlg-contact-title" @if($openContact) data-autoopen @endif>
  <div class="modal__head">
    <h2 id="dlg-contact-title">{{ __('site.contact_title') }}</h2>
    <button type="button" class="modal__close" data-close aria-label="{{ __('site.close') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
  </div>
  <div class="modal__body">
    @if (session('contact_sent'))
      <p class="note note--ok" role="status">{{ __('site.contact_sent') }}</p>
    @else
    <form class="apply-form" method="post" action="{{ route('contact.store') }}">
      @csrf
      <p class="note">{{ __('site.contact_intro') }}</p>
      @if ($contactErrors && $contactErrors->any())
        <p class="note note--err" role="alert">{{ __('site.form_errors') }}</p>
      @endif
      @foreach (['name' => 'text', 'email' => 'email', 'phone' => 'tel', 'message' => 'textarea'] as $field => $type)
        <div class="field @if($contactErrors && $contactErrors->has($field)) field--err @endif">
          <label for="c-{{ $field }}">{{ __('site.contact_' . $field) }}@if($field !== 'phone') <span aria-hidden="true">*</span>@endif</label>
          @if ($type === 'textarea')
            <textarea id="c-{{ $field }}" name="{{ $field }}" rows="5" maxlength="5000" required>{{ old($field) }}</textarea>
          @else
            <input id="c-{{ $field }}" name="{{ $field }}" type="{{ $type }}" value="{{ old($field) }}" @if($field !== 'phone') required @endif
              autocomplete="{{ ['name' => 'name', 'email' => 'email', 'phone' => 'tel'][$field] }}">
          @endif
          @if ($contactErrors && $contactErrors->has($field))<small class="field__err">{{ $contactErrors->first($field) }}</small>@endif
        </div>
      @endforeach
      <div class="field field--check @if($contactErrors && $contactErrors->has('consent')) field--err @endif">
        <label><input type="checkbox" name="consent" value="1" required @checked(old('consent'))> <span>{{ __('site.contact_consent') }}</span></label>
      </div>
      <div class="hp" aria-hidden="true"><label>Website <input name="website" tabindex="-1" autocomplete="off"></label></div>
      @include('partials.turnstile', ['bag' => $contactErrors])
      <div class="apply-form__foot">
        <button type="submit" class="pill pill--big">{{ __('site.submit') }}</button>
      </div>
    </form>
    @endif
  </div>
</dialog>

<dialog id="dlg-lightbox" class="lightbox" aria-label="{{ __('site.nav.galerie') }}">
  <figure class="lightbox__fig"><img alt=""><figcaption></figcaption></figure>
  <button type="button" class="round lightbox__prev" data-lb-prev aria-label="{{ __('site.prev') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $chev['prev'] }}"/></svg></button>
  <button type="button" class="round lightbox__next" data-lb-next aria-label="{{ __('site.next') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $chev['next'] }}"/></svg></button>
  <button type="button" class="round lightbox__close" data-close aria-label="{{ __('site.close') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
</dialog>
@endsection

@section('footer_links')
  <li><button type="button" data-open="dlg-apply">{{ __('site.nav_apply') }}</button></li>
  <li><button type="button" data-open="dlg-apply" data-tab="docs">{{ __('site.nav_docs') }}</button></li>
  @if ($hasReport)<li><button type="button" data-open="dlg-report">{{ __('site.nav_report') }}</button></li>@endif
@endsection

@push('scripts')
<script>
window.FILA_GALLERY = @json($gallery->map(fn ($img) => ['src' => asset($img->file), 'caption' => $img->caption($lang)[0], 'lang' => $img->caption($lang)[1]])->values());
</script>
<script src="{{ asset('assets/js/site.js') }}?v={{ config('app.asset_version') }}" defer></script>
<script src="{{ asset('assets/js/app.js') }}?v={{ config('app.asset_version') }}" defer></script>
@endpush
