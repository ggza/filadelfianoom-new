{{-- Egyszerű aloldal (belépés, regisztráció, fiók) a nyilvános oldal arculatával --}}
@extends('layouts.site')

@section('body_class', 'page-simple')

@section('mainbar')
  <div class="mainbar">
    <div class="wrap mainbar__in mainbar__in--simple">
      <a class="back-link" href="{{ route('home') }}">← {{ __('account.back_to_site') }}</a>
      <a class="brand" href="{{ route('home') }}" aria-label="{{ __('site.site_name') }}">
        <img src="{{ asset('assets/img/logo.webp') }}" width="180" height="180" alt="">
      </a>
      @auth
        <form method="post" action="{{ route('logout') }}" class="logout-form">@csrf
          <button type="submit" class="nav-link">{{ __('account.logout') }}</button>
        </form>
      @endauth
    </div>
  </div>
@endsection

@section('content')
<main id="main" class="page">
  <div class="wrap page__in">
    @if (session('status'))<p class="note note--ok" role="status">{{ session('status') }}</p>@endif
    @yield('page')
  </div>
</main>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/page.js') }}?v={{ config('app.asset_version') }}" defer></script>
@endpush
