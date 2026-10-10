@extends('layouts.page')
@section('title', __('account.reset_title') . ' – Filadelfia Noom')

@section('page')
<section class="auth-card" aria-labelledby="h-reset">
  <h1 id="h-reset" class="h-display">{{ __('account.reset_title') }}</h1>
  <form method="post" action="{{ route('password.update') }}" class="stack-form">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="field @error('email') field--err @enderror">
      <label for="email">{{ __('account.email') }}</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email">
      @error('email')<small class="field__err">{{ $message }}</small>@enderror
    </div>
    <div class="field @error('password') field--err @enderror">
      <label for="password">{{ __('account.new_password') }}</label>
      <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password">
      @error('password')<small class="field__err">{{ $message }}</small>@enderror
    </div>
    <div class="field">
      <label for="password_confirmation">{{ __('account.password_confirm') }}</label>
      <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password">
    </div>
    <button type="submit" class="pill pill--big">{{ __('account.save') }}</button>
  </form>
</section>
@endsection
