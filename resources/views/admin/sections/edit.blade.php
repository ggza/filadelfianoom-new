@extends('layouts.admin')
@section('title', $section->admin_label)
@section('content')
<p><a href="{{ route('admin.sections.index') }}">← Szövegek</a></p>
<h1>{{ $section->admin_label }}</h1>
<form method="post" action="{{ route('admin.sections.update', $section) }}" class="form">
  @csrf @method('put')
  <div class="cols">
    @foreach (['ro' => 'Román', 'hu' => 'Magyar'] as $l => $name)
      <fieldset>
        <legend>{{ $name }}</legend>
        <label class="check"><input type="checkbox" name="show_{{ $l }}" value="1" @checked($section->{"show_$l"})> Látható a {{ strtolower($name) }} oldalon</label>
        <label>Cím <input name="title_{{ $l }}" value="{{ old("title_$l", $section->{"title_$l"}) }}"></label>
        <label>Szöveg <textarea class="rich" id="body_{{ $l }}" name="body_{{ $l }}">{{ old("body_$l", $section->{"body_$l"}) }}</textarea></label>
      </fieldset>
    @endforeach
  </div>
  <p class="muted">Tipp: a lila felsorolásjel a bekezdés elején egy <code>&lt;span class="ico"&gt;</code>; a „Kód” gombbal látható.</p>
  <button class="btn btn--primary" type="submit">Mentés</button>
</form>
@include('admin.partials-editor')
@endsection
