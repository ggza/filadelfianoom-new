@extends('layouts.admin')
@section('title', $resident->exists ? $resident->name : 'Új lakó')
@section('content')
<p><a href="{{ $resident->exists ? route('admin.residents.show', $resident) : route('admin.residents.index') }}">← Vissza</a></p>
<h1>{{ $resident->exists ? 'Lakó adatai' : 'Új lakó' }}</h1>
<form method="post" class="form" action="{{ $resident->exists ? route('admin.residents.update', $resident) : route('admin.residents.store') }}">
  @csrf @if ($resident->exists) @method('put') @endif
  <div class="row">
    <label>Név <input name="name" value="{{ old('name', $resident->name) }}" required></label>
    <label>Szoba <input name="room" value="{{ old('room', $resident->room) }}"></label>
    <label>Beköltözés <input type="date" name="moved_in_at" value="{{ old('moved_in_at', optional($resident->moved_in_at)->toDateString()) }}"></label>
    <label class="check"><input type="checkbox" name="active" value="1" @checked(old('active', $resident->active))> Aktív</label>
  </div>
  <button class="btn btn--primary" type="submit">Mentés</button>
</form>
@if ($resident->exists)
<form class="danger-zone" method="post" action="{{ route('admin.residents.destroy', $resident) }}" onsubmit="return confirm('A lakó minden híre és fotója is törlődik. Biztosan?')">
  @csrf @method('delete')<button class="btn btn--danger">Lakó törlése</button>
</form>
@endif
@endsection
