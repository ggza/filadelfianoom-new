@php
  $lang = app()->getLocale();
  $other = $lang === 'ro' ? 'hu' : 'ro';
  $v = '?v=' . config('app.asset_version', '1');
@endphp
<!doctype html>
<html lang="{{ $lang }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', __('site.title'))</title>
<meta name="description" content="{{ __('site.site_name') }} – Sălard / Szalárd, Bihor">
<link rel="icon" href="{{ asset('assets/img/favicon.png') }}">
<link rel="alternate" hreflang="ro" href="{{ url()->current() }}?lang=ro">
<link rel="alternate" hreflang="hu" href="{{ url()->current() }}?lang=hu">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="{{ config('app.font_url') }}">
<link rel="stylesheet" href="{{ asset('assets/css/site.css') . $v }}">
<link rel="stylesheet" href="{{ asset('assets/css/app.css') . $v }}">
@stack('head')
</head>
<body class="@yield('body_class')">
<a class="skip-link" href="#main">{{ __('site.skip_link') }}</a>

@php
  // intézménynév: az első két szó után a logó („Filadelfia Noom [logó] Idősek Otthona”)
  $nameParts = preg_split('/\s+/u', trim(__('site.topbar_name')), 3);
  $nameHead = implode(' ', array_slice($nameParts, 0, 2));
  $nameTail = $nameParts[2] ?? '';
@endphp
<header class="site-header" id="top">
  <div class="topbar">
    <div class="wrap topbar__in">
      <nav class="lang" aria-label="{{ __('site.lang_label') }}">
        @foreach (['ro', 'hu'] as $l)
          <a href="?lang={{ $l }}" hreflang="{{ $l }}" lang="{{ $l }}" @if($l === $lang) aria-current="true" @endif
             title="{{ $l === 'ro' ? 'Română' : 'Magyar' }}">{{ strtoupper($l) }}</a>
        @endforeach
      </nav>
      <a class="topbar__name" href="{{ route('home') }}" aria-label="{{ __('site.topbar_name') }}">
        <span aria-hidden="true">{{ $nameHead }}</span>
        <img class="topbar__logo" src="{{ asset('assets/img/logo.webp') }}" width="180" height="180" alt="">
        <span aria-hidden="true">{{ $nameTail }}</span>
      </a>
      <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-menu" aria-label="{{ __('site.menu') }}">
        <span class="menu-toggle__bars" aria-hidden="true"></span>
      </button>
    </div>
  </div>
</header>

{{-- beúszó menü: minden menüpont, felhívás gomb, alul elérhetőség és belépés --}}
<div class="offcanvas" id="site-menu" role="dialog" aria-modal="true" aria-label="{{ __('site.menu') }}" hidden>
  <div class="offcanvas__backdrop" data-menu-close></div>
  <div class="offcanvas__panel" tabindex="-1">
    <div class="offcanvas__head">
      <span class="offcanvas__title">{{ __('site.menu') }}</span>
      <button type="button" class="offcanvas__close" data-menu-close aria-label="{{ __('site.close') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
    </div>
    <nav class="offcanvas__nav" aria-label="{{ __('site.menu') }}">
      <ul>
        @hasSection('menu_items')
          @yield('menu_items')
        @else
          <li><a href="{{ route('home') }}">{{ __('account.back_to_site') }}</a></li>
        @endif
      </ul>
    </nav>
    @yield('menu_cta')
    <div class="offcanvas__foot">
      <a href="tel:+40259432020"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/></svg>+40 259 432 020</a>
      <a href="mailto:filadelfianoom@gmail.com"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>filadelfianoom@gmail.com</a>
      @auth
        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('account') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/></svg>{{ auth()->user()->isAdmin() ? __('account.admin') : __('site.my_account') }}</a>
        <form method="post" action="{{ route('logout') }}">@csrf<button type="submit"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 4h4v16h-4M10 8l-4 4 4 4M6 12h10"/></svg>{{ __('account.logout') }}</button></form>
      @else
        <a href="{{ route('login') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/></svg>{{ __('site.account') }}</a>
      @endauth
    </div>
  </div>
</div>

@yield('content')

<footer class="site-footer">
  <div class="wrap site-footer__in">
    <img class="site-footer__logo" src="{{ asset('assets/img/logo.webp') }}" width="72" height="72" alt="">
    <div class="site-footer__imprint prose">{!! $imprint ?? '' !!}</div>
    <ul class="site-footer__links">
      @yield('footer_links')
      <li><a href="?lang={{ $other }}" hreflang="{{ $other }}" lang="{{ $other }}">{{ $other === 'ro' ? 'Română' : 'Magyar' }}</a></li>
      <li><a href="#top">{{ __('site.to_top') }} ↑</a></li>
    </ul>
  </div>
</footer>

<script src="{{ asset('assets/js/menu.js') . $v }}" defer></script>
@stack('scripts')
</body>
</html>
