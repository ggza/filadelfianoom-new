@extends('layouts.admin')
@section('title', 'Jelentkezés')
@section('content')
<p><a href="{{ route('admin.admissions.index') }}">← Jelentkezések</a></p>
<h1>Jelentkezés – {{ $admission->created_at->format('Y.m.d. H:i') }}</h1>
<dl class="dl">
  @foreach (['representative', 'resident', 'diseases', 'payment_method', 'preferred_date', 'phone', 'email'] as $f)
    <dt>{{ __('admission.' . $f, [], 'hu') }}</dt>
    <dd>{{ $f === 'preferred_date' ? optional($admission->preferred_date)->format('Y.m.d.') : $admission->$f }}</dd>
  @endforeach
  <dt>Űrlap nyelve</dt><dd>{{ strtoupper($admission->locale) }}</dd>
  @if ($admission->source === 'excel')<dt>Forrás</dt><dd>Régi weboldal űrlapja (Excel-import), sorszám: {{ $admission->legacy_serial }}</dd>@endif
</dl>
<form method="post" action="{{ route('admin.admissions.update', $admission) }}" class="form panel">
  @csrf @method('put')
  <label>Állapot
    <select name="status">
      @foreach (['new' => 'Új', 'in_progress' => 'Folyamatban', 'closed' => 'Lezárt'] as $k => $v)
        <option value="{{ $k }}" @selected($admission->status === $k)>{{ $v }}</option>
      @endforeach
    </select>
  </label>
  <label>Belső megjegyzés <textarea name="admin_note" rows="4">{{ $admission->admin_note }}</textarea></label>
  <button class="btn btn--primary" type="submit">Mentés</button>
</form>
<form class="danger-zone" method="post" action="{{ route('admin.admissions.destroy', $admission) }}" onsubmit="return confirm('Biztosan véglegesen törli ezt a jelentkezést?')">
  @csrf @method('delete')<button class="btn btn--danger">Jelentkezés törlése</button>
</form>
@endsection
