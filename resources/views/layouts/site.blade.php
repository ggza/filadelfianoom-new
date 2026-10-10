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

<header class="site-header" id="top">
  <div class="topbar">
    <div class="wrap topbar__in">
      <p class="topbar__name">{{ __('site.topbar_name') }}</p>
      <span class="topbar__contact">
        <a href="tel:+40259432020">+40 259 432 020</a>
        <a href="mailto:filadelfianoom@gmail.com">filadelfianoom@gmail.com</a>
      </span>
      <span class="topbar__tools">
        @auth
          <a class="account-btn" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('account') }}">
            <svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/></svg>
            <span>{{ auth()->user()->isAdmin() ? __('account.admin') : __('site.my_account') }}</span>
          </a>
        @else
          <a class="account-btn" href="{{ route('login') }}">
            <svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/></svg>
            <span>{{ __('site.account') }}</span>
          </a>
        @endauth
        <nav class="lang" aria-label="{{ __('site.lang_label') }}">
          @foreach (['ro', 'hu'] as $l)
            <a href="?lang={{ $l }}" hreflang="{{ $l }}" lang="{{ $l }}" @if($l === $lang) aria-current="true" @endif
               title="{{ $l === 'ro' ? 'Română' : 'Magyar' }}">{{ strtoupper($l) }}</a>
          @endforeach
        </nav>
      </span>
    </div>
  </div>
  @yield('mainbar')
</header>

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

@stack('scripts')
</body>
</html>
