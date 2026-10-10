@extends('layouts.page')
@section('title', __('account.login') . ' – Filadelfia Noom')

@section('page')
<section class="auth-card" aria-labelledby="h-login">
  <h1 id="h-login" class="h-display">{{ __('account.login') }}</h1>
  @if (request()->boolean('idle'))<p class="note" role="status">{{ __('account.idle_logout') }}</p>@endif
  <form method="post" action="{{ route('login') }}" class="stack-form">
    @csrf
    <div class="field @error('email') field--err @enderror">
      <label for="email">{{ __('account.email') }}</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email">
      @error('email')<small class="field__err">{{ $message }}</small>@enderror
    </div>
    <div class="field">
      <label for="password">{{ __('account.password') }}</label>
      <input id="password" name="password" type="password" required autocomplete="current-password">
    </div>
    <label class="check"><input type="checkbox" name="remember" value="1"> {{ __('account.remember') }}</label>
    <p class="auth-card__forgot"><a href="{{ route('password.request') }}">{{ __('account.forgot_link') }}</a></p>
    <button type="submit" class="pill pill--big">{{ __('account.login') }}</button>
  </form>
  <p class="auth-card__alt">{{ __('account.no_account') }} <a href="{{ route('register') }}">{{ __('account.register') }}</a></p>
</section>
@endsection
