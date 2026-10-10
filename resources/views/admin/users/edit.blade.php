@extends('layouts.admin')
@section('title', $user->name)
@section('content')
<p><a href="{{ route('admin.users.index') }}">← Felhasználók</a></p>
<h1>{{ $user->name }}</h1>
<p class="muted">{{ $user->email }} · regisztrált: {{ $user->created_at->format('Y.m.d.') }}
  @if ($user->requested_resident) · megadott lakó: <b>{{ $user->requested_resident }}</b> {{ $user->requested_relation ? '(' . $user->requested_relation . ')' : '' }}@endif
</p>
<form method="post" action="{{ route('admin.users.update', $user) }}" class="form">
  @csrf @method('put')
  <div class="row">
    <label>Név <input name="name" value="{{ old('name', $user->name) }}" required></label>
    <label>Telefon <input name="phone" value="{{ old('phone', $user->phone) }}"></label>
    <label>Állapot
      <select name="status">
        @foreach (['pending' => 'Jóváhagyásra vár', 'approved' => 'Jóváhagyva', 'blocked' => 'Letiltva'] as $k => $v)
          <option value="{{ $k }}" @selected($user->status === $k)>{{ $v }}</option>
        @endforeach
      </select>
    </label>
    <label>Szerep
      <select name="role">
        <option value="relative" @selected($user->role === 'relative')>Hozzátartozó</option>
        <option value="admin" @selected($user->role === 'admin')>Admin</option>
      </select>
    </label>
  </div>
  <fieldset>
    <legend>Lakók, akiknek adatait láthatja</legend>
    @forelse ($residents as $r)
      @php $linked = $user->residents->firstWhere('id', $r->id); @endphp
      <div class="resident-row">
        <label class="check"><input type="checkbox" name="residents[]" value="{{ $r->id }}" @checked($linked)> {{ $r->name }} @if(!$r->active)<small class="muted">(inaktív)</small>@endif</label>
        <label class="inline">kapcsolat <input name="relation[{{ $r->id }}]" value="{{ $linked?->pivot->relation ?? ($linked ? '' : $user->requested_relation) }}" placeholder="pl. lánya"></label>
      </div>
    @empty
      <p class="muted">Még nincs lakó felvéve. <a href="{{ route('admin.residents.create') }}">Új lakó</a></p>
    @endforelse
  </fieldset>
  <button class="btn btn--primary" type="submit">Mentés</button>
</form>
@unless ($user->is(auth()->user()))
<form class="danger-zone" method="post" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Biztosan törli ezt a felhasználót?')">
  @csrf @method('delete')<button class="btn btn--danger">Felhasználó törlése</button>
</form>
@endunless
@endsection
