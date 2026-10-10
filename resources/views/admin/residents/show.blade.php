@extends('layouts.admin')
@section('title', $resident->name)
@section('content')
<p><a href="{{ route('admin.residents.index') }}">← Lakók</a></p>
<h1>{{ $resident->name }} <a class="btn" href="{{ route('admin.residents.edit', $resident) }}">Adatok szerkesztése</a></h1>
<p class="muted">Szoba: {{ $resident->room ?: '–' }} · Hozzátartozók: {{ $resident->users->map(fn ($u) => $u->name . ($u->pivot->relation ? ' (' . $u->pivot->relation . ')' : ''))->join(', ') ?: 'még nincs' }}</p>

<section class="panel">
  <h2>Új hír / fotók a hozzátartozóknak</h2>
  <form method="post" action="{{ route('admin.residents.updates.store', $resident) }}" enctype="multipart/form-data" class="form">
    @csrf
    <label>Cím <input name="title" maxlength="255"></label>
    <label>Szöveg <textarea name="body" rows="4"></textarea></label>
    <div class="row">
      <label>Fotók <input type="file" name="photos[]" accept="image/*" multiple></label>
      <label>Fotó felirata <input name="caption"></label>
    </div>
    <button class="btn btn--primary" type="submit">Közzététel</button>
  </form>
</section>

<h2>Eddigi hírek</h2>
@forelse ($resident->updates as $u)
  <article class="panel">
    <p class="muted">{{ $u->created_at->format('Y.m.d. H:i') }}</p>
    @if ($u->title)<h3>{{ $u->title }}</h3>@endif
    @if ($u->body)<p>{!! nl2br(e($u->body)) !!}</p>@endif
    <div class="gal-grid gal-grid--small">
      @foreach ($u->photos as $p)
        <div class="gal-item">
          <img src="{{ route('account.photo', [$p, 'thumb']) }}" alt="">
          <form method="post" action="{{ route('admin.residents.photos.destroy', $p) }}" onsubmit="return confirm('Törli a fotót?')">@csrf @method('delete')<button class="btn btn--danger btn--small">Fotó törlése</button></form>
        </div>
      @endforeach
    </div>
    <form method="post" action="{{ route('admin.residents.updates.destroy', $u) }}" onsubmit="return confirm('Törli a hírt és a fotóit?')">@csrf @method('delete')<button class="btn btn--danger btn--small">Hír törlése</button></form>
  </article>
@empty
  <p class="muted">Még nincs hír.</p>
@endforelse
@endsection
