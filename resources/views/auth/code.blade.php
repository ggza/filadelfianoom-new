@extends('layouts.page')
@section('title', __('account.mfa_title') . ' – Filadelfia Noom')

@section('page')
<section class="auth-card" aria-labelledby="h-code">
  <h1 id="h-code" class="h-display">{{ __('account.mfa_title') }}</h1>
  <p class="auth-card__intro">{{ __('account.mfa_intro') }}</p>
  <form method="post" action="{{ route('login.code') }}" class="stack-form">
    @csrf
    <div class="field @error('code') field--err @enderror">
      <label for="code">{{ __('account.mfa_code') }}</label>
      <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9 ]*" maxlength="12" required autofocus autocomplete="one-time-code">
      @error('code')<small class="field__err">{{ $message }}</small>@enderror
    </div>
    <label class="check"><input type="checkbox" name="trust" value="1"> {{ __('account.mfa_trust', ['days' => $days]) }}</label>
    <button type="submit" class="pill pill--big">{{ __('account.login') }}</button>
  </form>
  <form method="post" action="{{ route('login.code.resend') }}" class="auth-card__alt">
    @csrf
    <button type="submit" class="link-button">{{ __('account.mfa_resend') }}</button>
  </form>
</section>
@endsection
