@extends('layouts.page')
@section('title', __('account.register') . ' – Filadelfia Noom')

@section('page')
<section class="auth-card auth-card--wide" aria-labelledby="h-reg">
  <h1 id="h-reg" class="h-display">{{ __('account.register') }}</h1>
  <p class="auth-card__intro">{{ __('account.register_intro') }}</p>
  <form method="post" action="{{ route('register') }}" class="stack-form stack-form--2">
    @csrf
    @foreach ([
      ['name', 'text', 'name', true],
      ['email', 'email', 'email', true],
      ['phone', 'tel', 'tel', false],
      ['resident_name', 'text', 'off', true],
      ['relation', 'text', 'off', false],
    ] as [$f, $type, $ac, $req])
      <div class="field @error($f) field--err @enderror">
        <label for="{{ $f }}">{{ __('account.' . $f) }}@if($req) <span aria-hidden="true">*</span>@endif</label>
        <input id="{{ $f }}" name="{{ $f }}" type="{{ $type }}" value="{{ old($f) }}" autocomplete="{{ $ac }}" @if($req) required @endif>
        @error($f)<small class="field__err">{{ $message }}</small>@enderror
      </div>
    @endforeach
    <div class="field @error('password') field--err @enderror">
      <label for="password">{{ __('account.password') }} *</label>
      <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password">
      @error('password')<small class="field__err">{{ $message }}</small>@enderror
    </div>
    <div class="field">
      <label for="password_confirmation">{{ __('account.password_confirm') }} *</label>
      <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password">
    </div>
    <label class="check check--full @error('consent') field--err @enderror"><input type="checkbox" name="consent" value="1" required @checked(old('consent'))> {{ __('account.consent') }} <a href="{{ route('legal.privacy') }}" target="_blank" rel="noopener">{{ __('site.privacy_link') }}</a></label>
    @include('partials.turnstile', ['bag' => $errors, 'class' => 'field--full'])
    <div class="stack-form__foot"><button type="submit" class="pill pill--big">{{ __('account.register') }}</button></div>
  </form>
  <p class="auth-card__alt">{{ __('account.have_account') }} <a href="{{ route('login') }}">{{ __('account.login') }}</a></p>
</section>
@endsection
