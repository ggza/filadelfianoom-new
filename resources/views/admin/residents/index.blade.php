@extends('layouts.admin')
@section('title', 'Lakók')
@section('content')
<h1>Lakók <a class="btn btn--primary" href="{{ route('admin.residents.create') }}">+ Új lakó</a></h1>
<p class="muted">A lakókhoz feltöltött hírek és fotók csak a hozzájuk rendelt, jóváhagyott hozzátartozóknak látszanak.</p>
<table class="table">
  <thead><tr><th>Név</th><th>Szoba</th><th>Hozzátartozók</th><th>Hírek</th><th>Fotók</th><th></th></tr></thead>
  <tbody>
  @forelse ($residents as $r)
    <tr @if(!$r->active) class="row--muted" @endif>
      <td><b>{{ $r->name }}</b> @if(!$r->active)<small>(inaktív)</small>@endif</td>
      <td>{{ $r->room }}</td><td>{{ $r->users_count }}</td><td>{{ $r->updates_count }}</td><td>{{ $r->photos_count }}</td>
      <td><a class="btn" href="{{ route('admin.residents.show', $r) }}">Megnyitás</a></td>
    </tr>
  @empty
    <tr><td colspan="6" class="muted">Még nincs lakó.</td></tr>
  @endforelse
  </tbody>
</table>
@endsection
