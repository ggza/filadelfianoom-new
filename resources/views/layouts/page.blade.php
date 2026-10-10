{{-- Egyszerű aloldal (belépés, regisztráció, fiók) a nyilvános oldal arculatával --}}
@extends('layouts.site')

@section('body_class', 'page-simple')

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
