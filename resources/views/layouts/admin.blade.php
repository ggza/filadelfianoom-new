<!doctype html>
<html lang="hu">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Adminisztráció') – Filadelfia Noom</title>
<link rel="icon" href="{{ asset('assets/img/favicon.png') }}">
<link rel="stylesheet" href="{{ config('app.font_url') }}">
<link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v={{ config('app.asset_version') }}">
@stack('head')
</head>
<body class="admin" data-idle-minutes="{{ config('app.admin_idle_minutes') }}" data-idle-ping="{{ route('admin.keepalive') }}" data-idle-logout="{{ route('logout') }}" data-idle-login="{{ route('login', ['idle' => 1]) }}">
<header class="admin-top">
  <a class="admin-brand" href="{{ route('admin.dashboard') }}"><img src="{{ asset('assets/img/logo.webp') }}" alt=""> ADMIN</a>
  <nav class="admin-nav" aria-label="Admin menü">
    @php $r = request()->route()->getName(); @endphp
    <a href="{{ route('admin.dashboard') }}" @if($r === 'admin.dashboard') aria-current="page" @endif>Áttekintés</a>
    <a href="{{ route('admin.sections.index') }}" @if(str_starts_with($r, 'admin.sections')) aria-current="page" @endif>Szövegek</a>
    <a href="{{ route('admin.gallery.index') }}" @if(str_starts_with($r, 'admin.gallery')) aria-current="page" @endif>Galéria</a>
    <a href="{{ route('admin.posts.index') }}" @if(str_starts_with($r, 'admin.posts')) aria-current="page" @endif>Bejegyzések</a>
    <a href="{{ route('admin.admissions.index') }}" @if(str_starts_with($r, 'admin.admissions')) aria-current="page" @endif>Jelentkezések</a>
    <a href="{{ route('admin.messages.index') }}" @if(str_starts_with($r, 'admin.messages')) aria-current="page" @endif>Üzenetek</a>
    <a href="{{ route('admin.residents.index') }}" @if(str_starts_with($r, 'admin.residents')) aria-current="page" @endif>Lakók</a>
    <a href="{{ route('admin.users.index') }}" @if(str_starts_with($r, 'admin.users')) aria-current="page" @endif>Felhasználók</a>
  </nav>
  <div class="admin-me">
    <a href="{{ route('home') }}" target="_blank" rel="noopener">Főoldal ↗</a>
    <form method="post" action="{{ route('logout') }}">@csrf<button type="submit">Kilépés</button></form>
  </div>
</header>
<main class="admin-main">
  @if (session('status'))<p class="flash" role="status">{{ session('status') }}</p>@endif
  @if ($errors->any())
    <div class="flash flash--err" role="alert"><ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif
  @yield('content')
</main>
<dialog id="idle-dialog" class="idle-dialog" aria-labelledby="idle-title">
  <h2 id="idle-title">Hamarosan kiléptetjük</h2>
  <p>Tétlenség miatt <strong id="idle-countdown">2:00</strong> múlva automatikusan kilépünk. A nem mentett módosítások elvesznek.</p>
  <div class="idle-dialog__actions">
    <button type="button" class="btn btn--primary" id="idle-stay">Maradok bejelentkezve</button>
    <button type="button" class="btn" id="idle-leave">Kilépés most</button>
  </div>
</dialog>
<script src="{{ asset('assets/js/admin-idle.js') }}?v={{ config('app.asset_version') }}" defer></script>
@stack('scripts')
</body>
</html>
