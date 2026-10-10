@extends('layouts.page')
@section('title', __('site.my_account') . ' – Filadelfia Noom')

@section('page')
<header class="account-head">
  <p class="eyebrow">{{ __('site.my_account') }}</p>
  <h1 class="h-display">{{ __('account.welcome', ['name' => $user->name]) }}</h1>
  @if ($user->isAdmin())<p><a class="pill" href="{{ route('admin.dashboard') }}">{{ __('account.admin') }}</a></p>@endif
</header>

@if (! $user->isApproved() && ! $user->isAdmin())
  <div class="notice-card">
    <h2>{{ __('account.pending_title') }}</h2>
    <p>{{ __('account.pending_body') }}</p>
  </div>
@elseif ($residents->isEmpty())
  <div class="notice-card"><p>{{ __('account.no_residents') }}</p></div>
@else
  @foreach ($residents as $r)
    <section class="resident" aria-labelledby="r-{{ $r->id }}">
      <header class="resident__head">
        <h2 id="r-{{ $r->id }}">{{ $r->name }}</h2>
        @if ($r->room)<p>{{ __('account.room') }}: {{ $r->room }}</p>@endif
      </header>

      <h3 class="resident__sub">{{ __('account.updates') }}</h3>
      @forelse ($r->updates as $u)
        <article class="update">
          <time datetime="{{ $u->created_at->toDateString() }}">{{ $u->created_at->format('Y.m.d. H:i') }}</time>
          @if ($u->title)<h4>{{ $u->title }}</h4>@endif
          @if ($u->body)<p>{!! nl2br(e($u->body)) !!}</p>@endif
          @if ($u->photos->count())
            <div class="photo-grid">
              @foreach ($u->photos as $p)
                <a href="{{ route('account.photo', $p) }}" target="_blank" rel="noopener">
                  <img src="{{ route('account.photo', [$p, 'thumb']) }}" alt="{{ $p->caption }}" loading="lazy" width="240" height="160">
                </a>
              @endforeach
            </div>
          @endif
        </article>
      @empty
        <p class="muted">{{ __('account.no_updates') }}</p>
      @endforelse
    </section>
  @endforeach
@endif

<details class="pw-card" @if($errors->password->any()) open @endif>
  <summary>{{ __('account.change_password') }}</summary>
  <form method="post" action="{{ route('account.password') }}" class="stack-form">
    @csrf @method('put')
    @foreach ([
      ['current_password', 'current-password', __('account.current_password')],
      ['password', 'new-password', __('account.new_password')],
      ['password_confirmation', 'new-password', __('account.password_confirm')],
    ] as [$f, $ac, $label])
      <div class="field @if($errors->password->has($f)) field--err @endif">
        <label for="{{ $f }}">{{ $label }}</label>
        <input id="{{ $f }}" name="{{ $f }}" type="password" autocomplete="{{ $ac }}" required>
        @if($errors->password->has($f))<small class="field__err">{{ $errors->password->first($f) }}</small>@endif
      </div>
    @endforeach
    <button class="pill" type="submit">{{ __('account.save') }}</button>
  </form>
</details>
@endsection
