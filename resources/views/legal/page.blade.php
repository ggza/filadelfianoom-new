@extends('layouts.page')
@section('title', $section->title() . ' – Filadelfia Noom')

@section('page')
<article class="legal" aria-labelledby="h-legal">
  <h1 id="h-legal" class="h-display">{{ $section->title() }}</h1>
  <div class="prose legal__body">{!! $section->body() !!}</div>
  @if ($page === 'cookies')
    <p><button type="button" class="pill" data-cookie-settings>{{ __('site.cookie_settings') }}</button></p>
  @endif
</article>
@endsection
