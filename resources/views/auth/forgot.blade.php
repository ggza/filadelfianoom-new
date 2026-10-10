@extends('layouts.page')
@section('title', __('account.forgot_title') . ' – Filadelfia Noom')

@section('page')
<section class="auth-card" aria-labelledby="h-forgot">
  <h1 id="h-forgot" class="h-display">{{ __('account.forgot_title') }}</h1>
  <p class="auth-card__intro">{{ __('account.forgot_intro') }}</p>
  <form method="post" action="{{ route('password.email') }}" class="stack-form">
    @csrf
    <div class="field @error('email') field--err @enderror">
      <label for="email">{{ __('account.email') }}</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email">
      @error('email')<small class="field__err">{{ $message }}</small>@enderror
    </div>
    @include('partials.turnstile', ['bag' => $errors])
    <button type="submit" class="pill pill--big">{{ __('account.forgot_submit') }}</button>
  </form>
  <p class="auth-card__alt"><a href="{{ route('login') }}">{{ __('account.login') }}</a></p>
</section>
@endsection
