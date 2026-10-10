@extends('layouts.admin')
@section('title', $post->exists ? 'Bejegyzés szerkesztése' : 'Új bejegyzés')
@section('content')
<p><a href="{{ route('admin.posts.index') }}">← Bejegyzések</a></p>
<h1>{{ $post->exists ? 'Bejegyzés szerkesztése' : 'Új bejegyzés' }}</h1>
<form method="post" enctype="multipart/form-data" class="form"
      action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}">
  @csrf @if ($post->exists) @method('put') @endif
  <div class="row">
    <label class="check"><input type="checkbox" name="published" value="1" @checked(old('published', (bool) $post->published_at))> Közzétéve</label>
    <label>Megjelenés ideje <input type="datetime-local" name="published_at" value="{{ old('published_at', optional($post->published_at)->format('Y-m-d\TH:i')) }}"></label>
    <label>Kép <input type="file" name="image" accept="image/*"></label>
    @if ($post->image)
      <span class="thumb-inline"><img src="{{ asset($post->image) }}" alt=""> <label class="check"><input type="checkbox" name="remove_image" value="1"> Kép eltávolítása</label></span>
    @endif
  </div>
  <div class="cols">
    @foreach (['ro' => 'Román', 'hu' => 'Magyar'] as $l => $name)
      <fieldset>
        <legend>{{ $name }}</legend>
        <label>Cím <input name="title_{{ $l }}" value="{{ old("title_$l", $post->{"title_$l"}) }}"></label>
        <label>Szöveg <textarea class="rich" id="body_{{ $l }}" name="body_{{ $l }}">{{ old("body_$l", $post->{"body_$l"}) }}</textarea></label>
      </fieldset>
    @endforeach
  </div>
  <p class="muted">Ha az egyik nyelven üresen hagyja, a bejegyzés azon a nyelven nem jelenik meg.</p>
  <button class="btn btn--primary" type="submit">Mentés</button>
</form>
@include('admin.partials-editor')
@endsection
